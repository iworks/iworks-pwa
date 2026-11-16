/**
 * Translations — wp-cli i18n make-pot / update-po / make-mo.
 * Replaces grunt-wp-i18n (makepot) and grunt-potomo.
 */

import { dirname, join, resolve } from "node:path";
import { existsSync, readdirSync, readFileSync, writeFileSync } from "node:fs";
import { log } from "./consoleHelpers.mjs";
import { config } from "./config.mjs";
import { spawnAsync } from "./spawnAsync.mjs";

const root = resolve(dirname(new URL(import.meta.url).pathname), "..");
const WP_CLI_URL =
  "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar";

/**
 * Make sure wp-cli.phar is available; download it when missing.
 */
async function ensureWpCli() {
  const phar = join(root, config.wpCli);
  if (existsSync(phar)) {
    return phar;
  }
  log("wp-cli.phar not found — downloading…", "w");
  const result = await spawnAsync(
    "curl",
    ["-sSfL", WP_CLI_URL, "-o", config.wpCli],
    "wp-cli download",
    false,
  );
  if (result.code !== 0 || !existsSync(phar)) {
    const err = new Error(
      "wp-cli.phar is missing and the download failed. Download it manually from https://wp-cli.org/ and place it in the project root.",
    );
    err.friendly = true;
    throw err;
  }
  return phar;
}

/**
 * Run a wp-cli i18n command.
 *
 * @param {string[]} args Command arguments.
 * @param {string}   label Log label.
 */
async function wpCliI18n(args, label) {
  const phar = await ensureWpCli();
  const result = await spawnAsync(
    "php",
    [config.wpCli, "i18n", ...args, "--no-color"],
    label,
    false,
  );
  if (result.code !== 0) {
    const err = new Error(
      `${label}\n${result.stderr.trim() || result.stdout.trim()}`,
    );
    err.friendly = true;
    throw err;
  }
}

/**
 * Build the .pot file and update all .po files.
 *
 * @param {boolean} makeMo Also compile .mo files (build:github).
 */
export async function makePot(makeMo = false) {
  const potFile = join(config.languagesDir, `${config.textdomain}.pot`);
  const excludes =
    "release,node_modules,.git,.opencode,bundler,tests,.sass-cache";
  const stripDate = (s) => s.replace(/^"POT-Creation-Date:.*$/m, "");
  const potRawBefore = existsSync(potFile)
    ? readFileSync(potFile, "utf8")
    : null;
  const potBefore = potRawBefore === null ? null : stripDate(potRawBefore);
  await wpCliI18n(
    [
      "make-pot",
      ".",
      potFile,
      `--exclude=${excludes}`,
      `--slug=${config.textdomain}`,
    ],
    "make-pot",
  );
  if (
    potBefore !== null &&
    potBefore === stripDate(readFileSync(potFile, "utf8"))
  ) {
    // No content change — restore the previous file so the build doesn't
    // produce a timestamp-only diff.
    writeFileSync(potFile, potRawBefore);
    log("make-pot — no changes (timestamp only), .pot untouched", "i");
  }
  const poFiles = existsSync(join(root, config.languagesDir))
    ? readdirSync(join(root, config.languagesDir)).filter((f) =>
        f.endsWith(".po"),
      )
    : [];
  for (const po of poFiles) {
    await wpCliI18n(
      ["update-po", potFile, join(config.languagesDir, po)],
      `update-po ${po}`,
    );
  }
  if (makeMo) {
    if (poFiles.length) {
      await wpCliI18n(["make-mo", config.languagesDir], "make-mo");
    } else {
      log("make-mo — no .po files found, skipped", "w");
    }
  }
  log(
    `i18n — ${potFile}${poFiles.length ? ` + ${poFiles.length} .po updated` : ""}${makeMo && poFiles.length ? " + .mo compiled" : ""}`,
    "s",
  );
}
