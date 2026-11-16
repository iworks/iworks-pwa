/**
 * Asset pipelines — grunt-parity JS and CSS builds.
 *
 * JS:   assets/scripts/src/{frontend,admin}/*.js → concat → frontend.js / admin.js → terser → *.min.js
 * CSS:  assets/sass/frontend/*.scss → sass → assets/styles/frontend/*.css
 *       → concat → iworks-pwa-frontend.css; every non-min CSS → *.min.css
 */

import { join, relative, resolve, dirname } from "node:path";
import { readdirSync, existsSync, mkdirSync, cpSync, rmSync } from "node:fs";
import { log } from "./consoleHelpers.mjs";
import { config } from "./config.mjs";
import {
  concatFiles,
  sassCompile,
  terserMinify,
  cleanCssMinify,
  writeFileAtomic,
  prettierSources,
  cleanGlob,
} from "./tools.mjs";

const root = resolve(dirname(new URL(import.meta.url).pathname), "..");

/**
 * Version banner used by every built asset (grunt concat/uglify/cssmin parity).
 */
function banner() {
  return (
    `/*! ${config.title} - v${config.version}\n` +
    ` * ${config.homepage}\n` +
    ` * Copyright (c) ${config.buildYear}\n` +
    ` * Licensed ${config.license}\n` +
    ` */`
  );
}

/**
 * JS bundles: concatenated output → ordered source globs (grunt js_files_concat).
 */
const JS_BUNDLES = [
  {
    target: "assets/scripts/frontend.js",
    sources: "assets/scripts/src/frontend/*.js",
  },
  {
    target: "assets/scripts/admin.js",
    sources: "assets/scripts/src/admin/*.js",
  },
];

/**
 * SCSS files to compile: target CSS → source SCSS (grunt css_files_compile).
 */
const SCSS_FILES = [
  {
    css: "assets/styles/frontend/add-to-home-screen.css",
    scss: "assets/sass/frontend/add-to-home-screen.scss",
  },
];

/**
 * JS pipeline: prettier sources → concat each bundle → minify.
 */
export async function buildJs() {
  await prettierSources(JS_BUNDLES.map(({ sources }) => sources));
  for (const { target, sources } of JS_BUNDLES) {
    const glob = new Bun.Glob(sources);
    const files = [];
    for await (const f of glob.scan({ cwd: root, onlyFiles: true })) {
      files.push(join(root, f));
    }
    files.sort();
    const concatTarget = join(root, target);
    const content = await concatFiles(files, banner());
    writeFileAtomic(concatTarget, content);
    log(`js — ${relative(root, concatTarget)}`, "i");

    const minTarget = concatTarget.replace(/\.js$/, ".min.js");
    const minified = await terserMinify(concatTarget, banner());
    writeFileAtomic(minTarget, minified);
    log(`js — ${relative(root, minTarget)}`, "s");
  }
}

/**
 * CSS pipeline: sass compile → concat frontend CSS → minify all non-min CSS.
 */
export async function buildCss() {
  // sass compile (grunt sass task).
  for (const { css, scss } of SCSS_FILES) {
    const compiled = await sassCompile(join(root, scss));
    writeFileAtomic(join(root, css), compiled);
    log(`sass — ${relative(root, join(root, css))}`, "i");
  }

  // concat frontend CSS into the single frontend stylesheet (grunt concat_css).
  const frontendDir = join(root, "assets/styles/frontend");
  const frontendCssFiles = existsSync(frontendDir)
    ? readdirSync(frontendDir)
        .filter((f) => f.endsWith(".css") && !f.endsWith(".min.css"))
        .sort()
        .map((f) => join(frontendDir, f))
    : [];
  if (frontendCssFiles.length) {
    const concatTarget = join(
      root,
      `assets/styles/${config.name}-frontend.css`,
    );
    const content = await concatFiles(frontendCssFiles, banner());
    writeFileAtomic(concatTarget, content);
    log(`css — ${relative(root, concatTarget)}`, "i");
  }

  // minify every non-min CSS file in assets/styles (grunt cssmin).
  const stylesDir = join(root, "assets/styles");
  const cssFiles = existsSync(stylesDir)
    ? readdirSync(stylesDir).filter(
        (f) => f.endsWith(".css") && !f.endsWith(".min.css"),
      )
    : [];
  for (const file of cssFiles) {
    const source = join(stylesDir, file);
    const minified = await cleanCssMinify(
      source,
      await Bun.file(source).text(),
      banner(),
    );
    writeFileAtomic(`${source.slice(0, -4)}.min.css`, minified);
    log(`css — ${relative(root, `${source.slice(0, -4)}.min.css`)}`, "s");
  }
}

/**
 * Full asset build (grunt default task parity, minus clean:temp).
 */
export async function buildAssets() {
  await buildJs();
  await buildCss();
}

/**
 * Prettier pass over the JS/JSON sources (build pipelines run it first).
 */
export async function formatSources() {
  await prettierSources([
    "assets/scripts/src/**/*.js",
    "bundler/**/*.js",
    "bundler/**/*.mjs",
    "*.js",
  ]);
  log("prettier — sources formatted", "s");
}

/**
 * Remove built files (i18n only).
 */
export async function cleanCompiled() {
  await cleanGlob("languages/*.mo");
  log("clean — languages/*.mo removed", "i");
}

/**
 * grunt clean:temp parity — remove temp junk files.
 */
export async function cleanTemp() {
  await cleanGlob("**/*.tmp");
  await cleanGlob("**/.afpDeleted*");
  await cleanGlob("**/.DS_Store");
}
