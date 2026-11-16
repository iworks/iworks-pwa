# AGENTS.md — PWA

> Working rules only. Technical knowledge lives in topic docs — read on demand, never preload.

## What the project is

The "PWA — easy way to Progressive Web App" WordPress plugin — turns any
WordPress site into a **Progressive Web App**: a web app manifest
(`/manifest.json`), an offline-capable service worker (served from a
PHP-generated template), Apple (Safari/iOS) touch icons and splash
screens, Microsoft (IE11/Edge) tile metas + `ieconfig.xml`, an offline
fallback page, and an Add-to-Home-Screen prompt. Structure: main file
`iworks-pwa.php` + base class (`includes/iworks/class-iworks-pwa.php`) +
feature classes (`includes/iworks/pwa/` — manifest, frontend, apple,
microsoft, administrator, github updater, integrations) + options config
(`etc/options.php`, iworks_options format) + service-worker/offline
templates (`assets/templates/`, `assets/pwa/`) + two vendored iWorks
modules (`includes/iworks/options`, `includes/iworks/rate` — git
submodules). Built with a bun bundler (`bundler/` → `release/` ZIPs,
wporg and github flavors): sources contain build-time placeholders
(`PLUGIN_VERSION`, `PLUGIN_TILL_YEAR`, …) replaced at build time; the
version lives only in `package.json`.

Git: the default branch is `master` (not `main`).

## Docs map — read only what the task needs

| Task | Read first |
| --- | --- |
| Bun bundler, build, ZIPs, placeholders, translations (.pot/.po/.mo), Plugin Check | `.opencode/docs/build.md` |
| Loading flow, endpoints (manifest/service worker/offline/ieconfig), head metas, options, integrations, conventions | `.opencode/docs/project.md` |
| Feature class internals | the class file's header comment + method docblocks |

- When a change alters documented behavior, update the corresponding doc in
  the same task.

## Evergreen rules (every task)

- All documentation and README in English.
- `readme.txt` changelog is edited only when the user explicitly asks.
- Never commit, push, amend or create PRs without an explicit request.
- Naming: kebab-case files (`class-iworks-pwa-{name}.php`); PHP classes
  snake_case lowercase with `iworks_pwa_` prefix (`iworks_pwa_manifest`,
  `iworks_pwa_frontend`).
- Version lives only in `package.json` — placeholders (`PLUGIN_VERSION`,
  `PLUGIN_TILL_YEAR`, `BUILDTIME`) are replaced by the bundler during
  build; never hardcode real values in sources.
- The `includes/iworks/options` and `includes/iworks/rate` submodules are
  shared iWorks modules — never edit them in this repo; upstream changes
  go to their own repositories.
- Textdomain is `iworks-pwa`; option names get the `iworks_pwa_` prefix.
- Every PHP file: `defined( 'ABSPATH' ) || exit;` (or `WPINC` guard in
  includes).
- Do not fix known typos/pitfalls without approval (see below).

## Known pitfalls (do not "fix" without approval)

- Source placeholders (`PLUGIN_VERSION`, `PLUGIN_TILL_YEAR`, …) are
  intentional — never replace them with real values in the repo.
- The build's prettier step rewrites JS sources in place (formatting-only
  diffs after a build are expected); the i18n step updates tracked
  `languages/*.po` files (timestamp diffs are normal).
- Hard-coded option groups in `etc/options.php` — `service-worker-handler`
  = `iworks-pwa-service-worker-js`, `cache-name` =
  `iworks-pwa-offline-cache`, `offline-page` = `iworks-pwa-offline` —
  are requested-URI contracts; changing them breaks already-installed
  service workers.
- Endpoints work by `parse_request` URI matching, **no rewrite rules** —
  they require pretty permalinks; the admin class warns when they are
  unreachable. Do not "fix" by adding rewrite rules without approval.
- `wp_localize_script_iworks_pwa_manifest` and other public filter names
  are a public API — renaming is a breaking change.
- Plugin Check (PCP) passes with 0 errors (`bun run build:wporg` enforces
  this). The iWorks submodules (`includes/iworks/options`,
  `includes/iworks/rate`) are excluded via `excludeDirectories` in
  `bundler/index.js` — their findings are upstream's, not ours.

## Commands

`bun run dev` (watcher: rebuilds assets on JS/SCSS change) ·
`bun run js` / `bun run css` (asset pipelines only) ·
`bun run build` (both flavors: github first, then wporg with Plugin
Check) ·
`bun run build:wporg` (wp.org ZIP + Plugin Check) ·
`bun run build:github` (GitHub ZIP + .mo) ·
`bun run make-pot` (translations) ·
`bun run pcp` (Plugin Check only; builds the wporg ZIP first if missing) ·
`bun run test` (PHPUnit; needs a WordPress test environment — see
`README.md`) ·
`bun run update-libs` (pulls latest changes for the `includes/iworks/*`
submodules).
Requires PHP ≥ 8.0, WordPress ≥ 6.0, Bun ≥ 1.3.
