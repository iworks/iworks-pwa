/**
 * WordPress.org Plugin Check (PCP) step for the wporg release build.
 *
 * Copies the staged wporg release directory into the local WordPress
 * plugins directory under the real plugin slug and runs
 * `wp plugin check <slug>` against it, so the results match what
 * WordPress.org's Plugin Checker reports for the released code.
 */

import { basename, dirname, join, resolve } from "node:path";
import {
  cpSync,
  existsSync,
  rmSync,
  symlinkSync,
  writeFileSync,
} from "node:fs";
import { relative } from "node:path";
import { log } from "./consoleHelpers.mjs";
import { spawnAsync } from "./spawnAsync.mjs";

const root = resolve(dirname(new URL(import.meta.url).pathname), "..");

/**
 * Parse Plugin Check CSV output into row objects (file, line, column, type,
 * code, message, docs). The output format is a `FILE: <path>` line per file,
 * followed by a header and one CSV row per finding. Handles quoted fields
 * with embedded commas/quotes.
 *
 * @param {string[]} lines Raw output lines.
 * @return {Array<Object>} Parsed rows.
 */
function parseCsv(lines) {
  const rows = [];
  let currentFile = "";
  for (const raw of lines) {
    const line = raw.trim();
    if (!line) {
      continue;
    }
    if (line.startsWith("FILE:")) {
      currentFile = normalizeFilePath(line.slice(5).trim());
      continue;
    }
    if (line.startsWith("line,column,")) {
      continue;
    }
    if (!/^\d+,/.test(line)) {
      continue;
    }
    const fields = [];
    let field = "";
    let quoted = false;
    for (let i = 0; i < line.length; i++) {
      const char = line[i];
      if (quoted) {
        if (char === '"') {
          if (line[i + 1] === '"') {
            field += '"';
            i++;
          } else {
            quoted = false;
          }
        } else {
          field += char;
        }
      } else if (char === '"') {
        quoted = true;
      } else if (char === ",") {
        fields.push(field);
        field = "";
      } else {
        field += char;
      }
    }
    fields.push(field);
    const [lineNo, column, type, code, message, docs] = fields;
    rows.push({
      file: currentFile,
      line: lineNo,
      column,
      type,
      code,
      message,
      docs,
    });
  }
  return rows;
}

/**
 * Normalize an absolute staging path to a plugin-relative path.
 *
 * @param {string} file Raw path from the FILE: header.
 * @return {string} Plugin-relative path.
 */
function normalizeFilePath(file) {
  // Strip repeated "kpir/" staging prefixes (symlinked copies) so each file
  // is reported once under its plugin-relative path.
  file = file.replace(/^(?:kpir\/)+/, "");
  const match = file.match(/plugins\/[^/]+\/(.*)$/);
  file = match ? match[1] : file;
  // De-duplicate symlinked copies: the check scans both the plugin slug
  // (symlink to the staged release) and the dev directory itself.
  return file.replace(/^dev-kpir\//, "");
}

/**
 * Print a results summary table — totals, per-file breakdown and per-code
 * breakdown, sorted by error count.
 *
 * @param {number} errors     Total error count.
 * @param {number} warnings   Total warning count.
 * @param {Map}    perFile    Map file → {errors, warnings}.
 * @param {Map}    fileCounts Map "file\tcode" → {file, code, errors, warnings}.
 */
function printSummary(errors, warnings, perFile, fileCounts) {
  if (!errors && !warnings) {
    return;
  }
  const byErrorsDesc = (a, b) => b.errors - a.errors || b.warnings - a.warnings;
  const col = (text, width) => {
    text = String(text);
    return text.length > width
      ? `${text.slice(0, width - 1)}…`
      : text.padEnd(width);
  };
  const line = (rule) => `  ${rule.repeat(64)}`;
  log("", "i");
  log(
    `  SUMMARY — ${errors} error(s), ${warnings} warning(s)`,
    errors ? "e" : "w",
  );
  log("", "i");
  // Per-file table.
  log("  By file:", "i");
  console.log(
    `  ${col("FILE", 44)}  ${col("ERRORS", 7)}  ${col("WARNINGS", 9)}`,
  );
  log("─");
  for (const entry of [...perFile.values()].sort(byErrorsDesc)) {
    console.log(
      `  ${col(entry.file, 44)}  ${col(entry.errors, 7)}  ${col(entry.warnings, 9)}`,
    );
  }
  // Per-code table.
  const perCode = new Map();
  for (const { code, errors: e, warnings: w } of fileCounts.values()) {
    const entry = perCode.get(code) || { code, errors: 0, warnings: 0 };
    entry.errors += e;
    entry.warnings += w;
    perCode.set(code, entry);
  }
  log("", "i");
  log("  By check code:", "i");
  console.log(`  ${col("CODE", 56)}  ${col("ERR", 4)}  ${col("WARN", 5)}`);
  log("─");
  for (const entry of [...perCode.values()].sort(byErrorsDesc)) {
    console.log(
      `  ${col(entry.code, 56)}  ${col(entry.errors, 4)}  ${col(entry.warnings, 5)}`,
    );
  }
  log("", "i");
}

/**
 * Run Plugin Check on the staged wporg release.
 *
 * Requires wp-cli.phar (downloaded by postinstall) and the Plugin Check
 * plugin installed in the local WordPress installation. The build fails
 * when any ERROR result is reported; warnings are informational.
 * Set PLUGIN_CHECK_SKIP=1 to skip the check entirely.
 *
 * @param {string} slug Plugin slug (directory name in the release).
 * @param {string} version Plugin version (staging subdirectory).
 * @param {Object} options Optional settings.
 * @param {string[]} options.ignoreCodes PCP result codes to exclude.
 * @return {Promise<{errors: number, warnings: number}>} Result counts.
 */
export async function pluginCheck(slug, version, options = {}) {
  if (Bun.env.PLUGIN_CHECK_SKIP === "1") {
    log("plugin-check — skipped (PLUGIN_CHECK_SKIP=1)", "w");
    return { errors: 0, warnings: 0 };
  }
  const wpCli = join(root, "wp-cli.phar");
  if (!existsSync(wpCli)) {
    log("plugin-check — wp-cli.phar not found, skipped (run bun install)", "w");
    return { errors: 0, warnings: 0 };
  }
  const pluginsDir = Bun.env.PLUGIN_CHECK_PLUGINS_DIR || dirname(root);
  const checkPluginDir = join(pluginsDir, "plugin-check");
  if (!existsSync(checkPluginDir)) {
    log(
      "plugin-check — the Plugin Check plugin is not installed, skipped (https://wordpress.org/plugins/plugin-check/)",
      "w",
    );
    return { errors: 0, warnings: 0 };
  }
  const stagedDir = join(root, "release", "wporg", version, slug);
  if (!existsSync(stagedDir)) {
    const err = new Error(
      `plugin-check — staged directory not found: ${stagedDir}`,
    );
    err.friendly = true;
    throw err;
  }
  // Install the staged release under the real slug, check it, remove it.
  const target = join(pluginsDir, slug);
  rmSync(target, { recursive: true, force: true });
  cpSync(stagedDir, target, { recursive: true });
  let errors = 0;
  let warnings = 0;
  try {
    const args = [
      "-d",
      "error_reporting=225",
      wpCli,
      "plugin",
      "check",
      slug,
      "--categories=plugin_repo",
      "--exclude-directories=" +
        (options.excludeDirectories?.length
          ? options.excludeDirectories.join(",")
          : "includes/iworks/options"),
      "--format=csv",
    ];
    if (options.ignoreCodes?.length) {
      args.push(`--ignore-codes=${options.ignoreCodes.join(",")}`);
    }
    const result = await spawnAsync("php", args, "wp plugin check", false);
    if (result.code !== 0 && !result.stdout.trim()) {
      const err = new Error(
        `plugin-check — wp plugin check failed with no output (exit ${result.code}):\n${result.stderr.split("\n").filter(Boolean).slice(0, 5).join("\n")}`,
      );
      err.friendly = true;
      throw err;
    }
    const lines = result.stdout.split("\n");
    const rows = parseCsv(lines);
    const errorLines = [];
    const fileCounts = new Map();
    const seen = new Set();
    for (const row of rows) {
      const file = row.file || "(unknown)";
      const code = row.code || "(no code)";
      const isError = (row.type || "").toUpperCase() === "ERROR";
      // De-duplicate findings reported for symlinked copies of the same file.
      const dedupeKey = `${row.line}:${row.column}:${code}:${row.message}`;
      if (seen.has(dedupeKey)) {
        continue;
      }
      seen.add(dedupeKey);
      if (isError) {
        errors++;
        errorLines.push(row);
      } else if ((row.type || "").toUpperCase() === "WARNING") {
        warnings++;
      } else {
        continue;
      }
      const key = `${file}\t${code}`;
      const entry = fileCounts.get(key) || {
        file,
        code,
        errors: 0,
        warnings: 0,
      };
      if (isError) {
        entry.errors++;
      } else {
        entry.warnings++;
      }
      fileCounts.set(key, entry);
    }
    // Per-file totals.
    const perFile = new Map();
    for (const { file, errors: e, warnings: w } of fileCounts.values()) {
      const entry = perFile.get(file) || { file, errors: 0, warnings: 0 };
      entry.errors += e;
      entry.warnings += w;
      perFile.set(file, entry);
    }
    if (errorLines.length) {
      log(`plugin-check — ${errors} error(s):`, "e");
      for (const row of errorLines) {
        console.log(
          `  ${row.file || "?"}:${row.line || 0}\t${row.code || "?"}\t${row.message || ""}`,
        );
      }
    }
    if (warnings) {
      log(`plugin-check — ${warnings} warning(s)`, "w");
    }
    printSummary(errors, warnings, perFile, fileCounts);
    // Full results to a log file (gitignored).
    const logFile = join(root, "plugin-check.log");
    const header = `# plugin-check — ${new Date().toISOString()} — ${slug} ${version}\n`;
    writeFileSync(
      logFile,
      header + (result.stdout.trim() || "(no output)") + "\n",
    );
    log(`plugin-check — full output written to plugin-check.log`, "i");
  } finally {
    rmSync(target, { recursive: true, force: true });
  }
  symlinkRelease(pluginsDir, slug, version);
  if (errors) {
    const err = new Error(
      `plugin-check — ${errors} Plugin Check error(s) (PLUGIN_CHECK_SKIP=1 to skip)`,
    );
    err.friendly = true;
    throw err;
  }
  log("plugin-check — passed", "s");
  return { errors, warnings };
}

/**
 * Symlink the just-built wporg release into the WordPress plugins
 * directory, so the local site loads the actual built version.
 *
 * Linux/macOS only (symlinks); skipped with a warning elsewhere or when
 * PLUGIN_SYMLINK_SKIP=1. Never fails the build — symlink problems are
 * reported as warnings.
 *
 * @param {string} pluginsDir Local WordPress plugins directory.
 * @param {string} slug Plugin slug.
 * @param {string} version Plugin version (staging subdirectory).
 * @return {void}
 */
function symlinkRelease(pluginsDir, slug, version) {
  if (Bun.env.PLUGIN_SYMLINK_SKIP === "1") {
    return;
  }
  if (process.platform !== "linux" && process.platform !== "darwin") {
    log(
      `plugin-check — symlink skipped (unsupported platform: ${process.platform})`,
      "w",
    );
    return;
  }
  const stagedDir = join("release", "wporg", version, slug);
  const link = join(pluginsDir, slug);
  try {
    rmSync(link, { recursive: true, force: true });
    symlinkSync(relative(pluginsDir, join(root, stagedDir)), link);
    log(`plugin-check — ${link} → ${stagedDir}`, "s");
  } catch (error) {
    log(`plugin-check — symlink failed: ${error.message}`, "w");
  }
}
