/**
 * Console helpers — colored logging and execution timing.
 */

const COLORS = {
  info: "\x1b[36m",
  success: "\x1b[32m",
  warning: "\x1b[33m",
  error: "\x1b[31m",
  reset: "\x1b[0m",
};

/**
 * Print a log line.
 *
 * @param {string} text Message.
 * @param {string} type info/i, success/s, warning/w, error/e.
 */
export function log(text, type = "info") {
  const key = Object.keys(COLORS).find((k) => k.startsWith(type)) || "info";
  console.log(`${COLORS[key]}${text}${COLORS.reset}`);
}

/**
 * Time an async step and log its duration.
 *
 * @param {string}   id      Step label.
 * @param {Function} run     Async callback.
 * @param {boolean}  silent  Skip the timing log.
 * @return {*} Callback result.
 */
export async function executionTime(id, run, silent = false) {
  const start = Date.now();
  const result = await run();
  if (!silent) {
    log(`${id} finished in ${((Date.now() - start) / 1000).toFixed(2)}s`, "i");
  }
  return result;
}
