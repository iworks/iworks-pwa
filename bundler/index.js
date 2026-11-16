#!/usr/bin/env bun
/**
 * Bundler entry point — replaces the old Grunt pipeline.
 *
 * Modes:
 *   dev (default) — watcher, rebuilds assets on JS/SCSS change
 *   js            — JS bundles only (concat + minify)
 *   css           — CSS pipeline only (sass + concat + minify)
 *   build         — both flavors: build:github + build:wporg (+ Plugin Check)
 *   build:wporg   — production build → release/wporg/{name}.zip
 *   build:github  — production build + .mo → release/github/{name}.zip
 *   make-pot      — translations only (.pot + .po update)
 *   test          — PHPUnit (tests/php/phpunit.xml)
 *   pcp           — Plugin Check on the wporg build (builds it first if missing)
 *   update-libs   — update iWorks git submodules
 *   notes         — release checklist (grunt `notes` parity)
 */

import { dirname, join, resolve } from "node:path";
import { existsSync, watch } from "node:fs";
import { executionTime, log } from "./consoleHelpers.mjs";
import { ProgressBar } from "./progressBar.mjs";
import { spawnAsync } from "./spawnAsync.mjs";
import { config } from "./config.mjs";
import {
  buildAssets,
  formatSources,
  cleanCompiled,
  cleanTemp,
} from "./pipelines.mjs";
import { makeZip } from "./makeZip.mjs";
import { makePot } from "./makePot.mjs";
import { pluginCheck } from "./pluginCheck.mjs";

const root = resolve(dirname(new URL(import.meta.url).pathname), "..");

/**
 * Friendly error printer — one-liners, no stack traces.
 *
 * @param {*} error Thrown error.
 */
function printError(error) {
  const message = error?.friendly
    ? error.message
    : error?.stack || String(error);
  log(message, "e");
}

/**
 * Production build for one flavor.
 *
 * @param {string} flavor "wporg" or "github".
 */
async function build(flavor) {
  const start = Date.now();
  try {
    const steps = flavor === "wporg" ? 7 : 6;
    const bar = new ProgressBar(steps);
    bar.tick("clean");
    await cleanTemp();
    bar.tick("prettier");
    await formatSources();
    bar.tick("assets");
    await buildAssets();
    bar.tick("i18n");
    await makePot(flavor === "github");
    bar.tick("zip");
    await makeZip(flavor);
    if (flavor === "wporg") {
      bar.tick("plugin-check");
      await pluginCheck(config.name, config.version, {
        // External iWorks submodules are shared libraries maintained
        // upstream — their findings are not this plugin's release blockers.
        excludeDirectories: ["includes/iworks/options", "includes/iworks/rate"],
        ignoreCodes: [
          // "Tested up to" is bumped on the wp.org release checklist, not at
          // every build.
          "outdated_tested_upto_header",
        ],
      });
    }
    bar.tick("done");
    log(
      `BUILD OK (${flavor}) in ${((Date.now() - start) / 1000).toFixed(2)}s`,
      "s",
    );
  } catch (error) {
    log("BUILD FAILED", "e");
    printError(error);
    process.exit(1);
  }
}

/**
 * Dev watcher — rebuild assets on JS/SCSS change (grunt watch parity).
 */
function dev() {
  const exitAfterMs = Bun.env.DEV_EXIT_AFTER_MS || "";
  if (exitAfterMs) {
    setTimeout(() => {
      log(`dev — exiting after ${exitAfterMs} ms (DEV_EXIT_AFTER_MS)`, "i");
      process.exit(0);
    }, Number(exitAfterMs));
  }
  log(
    "dev — watching for changes (no auto browser reload, refresh manually)",
    "i",
  );

  let timer = null;
  let running = false;
  const queue = new Set();

  const JS_DIRS = ["assets/scripts/src/"];
  const SCSS_DIR = "assets/sass/";

  const relevant = (rel) =>
    JS_DIRS.some((dir) => rel.startsWith(dir) && rel.endsWith(".js")) ||
    (rel.startsWith(SCSS_DIR) && rel.endsWith(".scss"));

  const processQueue = async () => {
    timer = null;
    if (running) {
      return;
    }
    running = true;
    const changed = [...queue].filter(relevant);
    queue.clear();
    try {
      const isJs = changed.some(
        (f) => f.startsWith("assets/scripts/src/") && f.endsWith(".js"),
      );
      const isCss = changed.some(
        (f) => f.startsWith("assets/sass/") && f.endsWith(".scss"),
      );
      if (isJs) {
        await buildAssets();
      } else if (isCss) {
        const { buildCss } = await import("./pipelines.mjs");
        await buildCss();
      }
      log(`dev — rebuilt assets (${changed.length} changed)`, "s");
    } catch (error) {
      printError(error);
    } finally {
      running = false;
    }
  };

  watch(root, { recursive: true }, (event, filename) => {
    if (!filename) {
      return;
    }
    const rel = filename.replace(/\\/g, "/");
    if (!relevant(rel)) {
      return;
    }
    queue.add(rel);
    if (!timer) {
      timer = setTimeout(processQueue, 500);
    }
  });
}

/**
 * PHPUnit (grunt `test` parity, jshint dropped).
 */
async function test() {
  const bin = existsSync(join(root, "vendor/bin/phpunit"))
    ? join(root, "vendor/bin/phpunit")
    : "phpunit";
  const result = await spawnAsync(
    bin,
    ["--configuration", "tests/php/phpunit.xml", "--colors=always"],
    "PHPUnit",
    false,
  );
  if (result.stdout.trim()) {
    console.log(result.stdout.trim());
  }
  if (result.code !== 0) {
    log(
      "If phpunit is not installed, run `composer install` or set up a WordPress test environment (see tests/php/bootstrap.php).",
      "w",
    );
    process.exit(result.code);
  }
  log("PHPUnit passed", "s");
}

/**
 * Update the iWorks git submodules (grunt never had this; parity with
 * dev-sierotki). Pulls latest changes for each submodule and fails when any
 * of them cannot be updated.
 */
async function updateLibraries() {
  const submodules = [
    { path: "includes/iworks/options", label: "iWorks Options" },
    { path: "includes/iworks/rate", label: "iWorks Rate" },
  ];
  let failed = false;
  for (const { path, label } of submodules) {
    if (!existsSync(join(root, path))) {
      log(`update-libs — ${path} not found, skipped`, "w");
      continue;
    }
    log(`update-libs — updating ${label} (${path})`, "i");
    const result = await spawnAsync("git", ["-C", path, "pull"], label, false);
    if (result.stdout.trim()) {
      console.log(result.stdout.trim());
    }
    if (result.stderr.trim()) {
      console.log(result.stderr.trim());
    }
    if (result.code !== 0) {
      failed = true;
    }
  }
  if (failed) {
    log("update-libs — one or more submodules failed to update", "e");
    process.exit(1);
  }
  log("update-libs — all iWorks libraries updated", "s");
}

/**
 * Plugin Check (PCP) on the wporg build — builds the wporg ZIP first when
 * it is missing, then runs the check against the staged release.
 */
async function pcp() {
  const zipPath = join(root, config.releaseDir, "wporg", `${config.name}.zip`);
  if (!existsSync(zipPath)) {
    log("pcp — no wporg ZIP found, building first…", "i");
    await build("wporg");
    return;
  }
  try {
    await pluginCheck(config.name, config.version);
  } catch (error) {
    printError(error);
    process.exit(1);
  }
}

/**
 * Release checklist (grunt `notes` task parity).
 */
function notes() {
  log("Release notes", "i");
  const items = [
    "Check FORUM for open threads",
    "REPLY to forum threads + unsubscribe",
    "Update the TRANSLATION files",
    "Generate ARCHIVE",
    "Check ARCHIVE structure - it should be a folder with plugin name",
    "INSTALL on a clean WordPress installation",
    "RELEASE the plugin on WordPress.org!",
    "Add git tag!",
    "RELEASE the plugin on GitHub!",
    "RELEASE the plugin!",
  ];
  items.forEach((item, index) => {
    console.log(`  ${index + 1}. ${item}`);
  });
}

// ─── Mode dispatch ────────────────────────────────────────────────────────────

const mode = process.argv[2] || "dev";

const modes = {
  dev: () => dev(),
  js: () =>
    executionTime("js", async () =>
      (await import("./pipelines.mjs")).buildJs(),
    ),
  css: () =>
    executionTime("css", async () =>
      (await import("./pipelines.mjs")).buildCss(),
    ),
  build: async () => {
    await build("github");
    await build("wporg");
  },
  "build:wporg": () => build("wporg"),
  "build:github": () => build("github"),
  release: async () => {
    await build("wporg");
    await build("github");
    notes();
  },
  pcp: () => pcp(),
  "make-pot": () => executionTime("make-pot", () => makePot(false)),
  test: () => test(),
  "update-libs": () => updateLibraries(),
  notes: () => notes(),
};

if (!modes[mode]) {
  log(
    `Unknown mode "${mode}". Available: ${Object.keys(modes).join(", ")}`,
    "e",
  );
  process.exit(1);
}

try {
  await modes[mode]();
} catch (error) {
  printError(error);
  process.exit(1);
}
