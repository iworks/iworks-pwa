/**
 * In-process tool calls — prettier, sass, terser and clean-css APIs.
 * All dist/source writes are atomic (temp + rename); tool failures are
 * rethrown as friendly one-line errors.
 */

import { dirname, join, relative, resolve } from "node:path";
import {
  mkdirSync,
  renameSync,
  writeFileSync,
  existsSync,
  rmSync,
} from "node:fs";
import { fileURLToPath } from "node:url";
import * as prettier from "prettier";
import { log } from "./consoleHelpers.mjs";

const root = resolve(dirname(fileURLToPath(import.meta.url)), "..");

/**
 * Atomic write: temp file + rename.
 *
 * @param {string} file    Destination path.
 * @param {string} content Text content.
 */
export function writeFileAtomic(file, content) {
  mkdirSync(dirname(file), { recursive: true });
  const tmp = `${file}.tmp`;
  writeFileSync(tmp, content);
  renameSync(tmp, file);
}

/**
 * Friendly error: tool — file\nmessage (no stack trace noise).
 *
 * @param {string} tool Tool name.
 * @param {string} file File being processed.
 * @param {*}      error Thrown error.
 */
function friendly(tool, file, error) {
  const message =
    error && error.formatted
      ? error.formatted
      : error?.message || String(error);
  const err = new Error(
    `${tool} — ${relative(root, file) || file}\n${message}`,
  );
  err.friendly = true;
  throw err;
}

/**
 * Prettier — format a single file in place.
 *
 * @param {string} file File path.
 */
export async function prettierFile(file) {
  try {
    const source = await Bun.file(file).text();
    const formatted = await prettier.format(source, { filepath: file });
    if (formatted !== source) {
      writeFileAtomic(file, formatted);
      log(`prettier — ${relative(root, file)}`, "i");
    }
  } catch (error) {
    friendly("prettier", file, error);
  }
}

/**
 * Prettier — format every JS source file (glob pattern, relative to root).
 *
 * @param {string[]} patterns Glob patterns.
 */
export async function prettierSources(patterns) {
  for (const pattern of patterns) {
    const glob = new Bun.Glob(pattern);
    for await (const file of glob.scan({ cwd: root, onlyFiles: true })) {
      await prettierFile(join(root, file));
    }
  }
}

/**
 * Sass — compile a .scss file into expanded CSS (no source maps).
 *
 * @param {string} file Source .scss path.
 * @return {string} Compiled CSS.
 */
export async function sassCompile(file) {
  try {
    const sass = await import("sass");
    const result = sass.compile(file, {
      style: "expanded",
      sourceMap: false,
      charset: false,
    });
    return result.css;
  } catch (error) {
    friendly("sass", file, error);
  }
}

/**
 * Terser — minify JS source with a version banner (jQuery kept intact).
 *
 * @param {string} file Source JS path.
 * @param {string} banner Banner comment prepended to the output.
 * @return {Promise<string>} Minified JS.
 */
export async function terserMinify(file, banner = "") {
  try {
    const { minify } = await import("terser");
    const source = await Bun.file(file).text();
    const result = await minify(source, {
      mangle: { reserved: ["jQuery"] },
      format: banner ? { preamble: banner } : {},
    });
    return result.code;
  } catch (error) {
    friendly("terser", file, error);
  }
}

/**
 * clean-css — minify a CSS string with a version banner.
 *
 * @param {string} file   Source path (for error messages).
 * @param {string} source CSS content.
 * @param {string} banner Banner comment prepended to the output.
 * @return {Promise<string>} Minified CSS.
 */
export async function cleanCssMinify(file, source, banner = "") {
  try {
    const CleanCSS = (await import("clean-css")).default;
    const output = new CleanCSS({ level: 1 }).minify(source);
    if (output.errors.length) {
      throw new Error(output.errors.join("\n"));
    }
    const css = output.styles.trim();
    return banner ? `${banner}\n${css}` : css;
  } catch (error) {
    friendly("clean-css", file, error);
  }
}

/**
 * Concatenate files with a banner header.
 *
 * @param {string[]} files      Source paths (in order).
 * @param {string}   banner     Banner comment prepended to the output.
 * @return {Promise<string>} Concatenated content.
 */
export async function concatFiles(files, banner = "") {
  const parts = [];
  for (const file of files) {
    parts.push((await Bun.file(file).text()).trimEnd());
  }
  const content = `${parts.join("\n")}\n`;
  return banner ? `${banner}\n${content}` : content;
}

export async function cleanGlob(pattern) {
  const glob = new Bun.Glob(pattern);
  for await (const file of glob.scan({ cwd: root, onlyFiles: true })) {
    rmSync(join(root, file));
  }
}
