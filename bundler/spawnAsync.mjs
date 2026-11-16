/**
 * spawnAsync — child_process.spawn with shell, captured output and friendly errors.
 */

import { spawn } from "node:child_process";
import { log } from "./consoleHelpers.mjs";

/**
 * Run a command.
 *
 * @param {string}  command    Command to run.
 * @param {string[]} args      Arguments.
 * @param {string}  label      Human-readable process name.
 * @param {boolean} exitOnError Exit the process when the command fails.
 * @return {Promise<{code: number, stdout: string, stderr: string}>}
 */
export function spawnAsync(command, args, label, exitOnError = true) {
  return new Promise((resolve) => {
    const child = spawn(command, args, {
      shell: true,
      stdio: ["ignore", "pipe", "pipe"],
    });
    let stdout = "";
    let stderr = "";
    child.stdout.on("data", (chunk) => {
      stdout += chunk;
    });
    child.stderr.on("data", (chunk) => {
      stderr += chunk;
    });
    child.on("error", (error) => {
      log(`${label} failed to start: ${error.message}`, "e");
      if (exitOnError) {
        process.exit(1);
      }
      resolve({ code: 1, stdout, stderr });
    });
    child.on("close", (code) => {
      if (code !== 0) {
        log(`${label} failed with code ${code}`, "e");
        if (exitOnError) {
          process.exit(1);
        }
      }
      resolve({ code: code ?? 1, stdout, stderr });
    });
  });
}
