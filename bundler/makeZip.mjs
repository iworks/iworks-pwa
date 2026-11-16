/**
 * Release packaging — staging copy, placeholder replacement, ZIP.
 * Two flavors: wporg (plugin directory) and github (self-updater ZIP).
 */

import { dirname, join, relative, extname, resolve } from "node:path";
import {
  createWriteStream,
  existsSync,
  mkdirSync,
  cpSync,
  readdirSync,
  readFileSync,
  rmSync,
} from "node:fs";
import archiver from "archiver";
import { log } from "./consoleHelpers.mjs";
import {
  config,
  replacePatterns,
  copyRules,
  BINARY_EXTENSIONS,
  bundleName,
} from "./config.mjs";
import { writeFileAtomic } from "./tools.mjs";
import { spawnAsync } from "./spawnAsync.mjs";

const root = resolve(dirname(new URL(import.meta.url).pathname), "..");

/**
 * Walk a directory, pruning heavyweight excluded dirs (.git, node_modules,
 * .sass-cache) for speed; returns relative paths.
 *
 * @param {string} dir Absolute directory.
 * @param {string} base Base for relative paths.
 * @return {string[]} Relative file paths.
 */
const PRUNE = new Set([".git", "node_modules", ".sass-cache"]);
function walk(dir, base = dir) {
  const out = [];
  if (!existsSync(dir)) {
    return out;
  }
  for (const entry of readdirSync(dir, { withFileTypes: true })) {
    if (entry.isDirectory() && PRUNE.has(entry.name)) {
      continue;
    }
    const full = join(dir, entry.name);
    if (entry.isDirectory()) {
      out.push(...walk(full, base));
    } else {
      out.push(relative(base, full));
    }
  }
  return out;
}

/**
 * Apply the ordered copy rules to a relative path (first match wins, like grunt).
 *
 * @param {string} rel Relative file path (no leading ./).
 * @param {Array}  rules Ordered rules from copyRules().
 * @return {boolean} True when the file should be copied.
 */
function included(rel, rules) {
  for (const rule of rules) {
    const glob = new Bun.Glob(rule.glob);
    if (glob.match(rel)) {
      return rule.include;
    }
  }
  return false;
}

/**
 * Apply the placeholder replacement patterns to a text file.
 *
 * @param {string} file Destination file path.
 */
function replacePlaceholders(file) {
  const ext = extname(file).slice(1).toLowerCase();
  if (BINARY_EXTENSIONS.has(ext)) {
    return;
  }
  const patterns = replacePatterns().filter(
    ({ files }) => !files || files.some((f) => file.endsWith(f)),
  );
  let content = readFileSync(file, "utf8");
  for (const { match, replace } of patterns) {
    content = content.replace(match, replace);
  }
  writeFileAtomic(file, content);
}

/**
 * Build one release flavor.
 *
 * @param {string} flavor "wporg" or "github".
 */
export async function makeZip(flavor) {
  const versionDir = join(root, config.releaseDir, flavor, config.version);
  const stagingDir = join(versionDir, config.name);
  const zipPath = join(root, config.releaseDir, flavor, `${config.name}.zip`);

  // Clean previous build (grunt clean:wporg/github).
  rmSync(versionDir, { recursive: true, force: true });
  rmSync(zipPath, { force: true });

  // Copy files passing the ordered rules.
  const rules = copyRules(flavor);
  const files = walk(root).filter((rel) => included(rel, rules));
  if (!existsSync(join(root, `${config.name}.php`))) {
    log("makeZip — main plugin file not found, ZIP may be incomplete", "w");
  }
  mkdirSync(stagingDir, { recursive: true });
  for (const rel of files) {
    const dest = join(stagingDir, rel);
    mkdirSync(dirname(dest), { recursive: true });
    cpSync(join(root, rel), dest);
    replacePlaceholders(dest);
  }
  log(`makeZip — ${files.length} files staged for ${flavor}`, "i");

  // Zip (level 9), wrapper folder {name}/ inside.
  await new Promise((resolveZip, rejectZip) => {
    const output = createWriteStream(zipPath);
    const archive = archiver("zip", { zlib: { level: 9 } });
    output.on("close", resolveZip);
    archive.on("error", rejectZip);
    archive.pipe(output);
    archive.directory(`${versionDir}/`, false);
    archive.finalize();
  });
  log(`makeZip — ${flavor} ZIP ready: ${relative(root, zipPath)}`, "s");
}
