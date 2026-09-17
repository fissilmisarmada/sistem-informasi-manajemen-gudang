# AGENTS.md

## Stack & Entrypoints
- Laravel 12 + Vite 7 + Tailwind 4 + Blade. `vite.config.js` entrypoints are `resources/css/app.css`, `resources/css/dashboard.css`, `resources/js/app.js` — all three must be in the `@vite` array in layouts.
- Roles via `CheckRole` middleware (`role:admin`, `role:staff`, `role:pimpinan`). Dashboards are role-specific: `dashboard/admin|staff|pimpinan`.
- Views are under `resources/views/{dashboard,barang,kategori,rak,denah-area,mutasi-barang,stock-opname-barang,pencarian,laporan,users,auth}`. Most pages embed `<style>` blocks — changes there won't show in `resources/css/*.css` alone.

## Design System — Read DESIGN.md First
- `DESIGN.md` is the source of truth for the "Andon Lantai Gudang" visual world. Do not invent colors/tokens outside it.
- Core tokens live in `resources/css/app.css: :root --andon-*` (bg `#F6F1E8`, ink `#0F172A`, amber `#F7D60A`, line `#E2E8F0/CBD5E1`). Dashboard-specific styles in `resources/css/dashboard.css`. Shared tokens source is `.impeccable/design.json`.
- Hard rules from DESIGN.md: amber only as marker (≤3px line / 6-8px dot / scan-line), no thick `border-left` as accent, shadows must have offset+blur, one Display per viewport.

## Commands
- First-time setup: `composer setup` (install + `.env` + key + migrate + build).
- Dev (all services): `composer dev` (serve + queue + pail + vite via `concurrently`).
- Dev (frontend only): `npm run dev` / `npm run build` (Vite).
- Tests: `composer test` (= `php artisan test` with `config:clear`) or `php artisan test`. Uses in-memory `sqlite` — no DB setup needed. Single file: `php artisan test --filter=ExampleTest`.
- Format: `vendor/bin/pint` (Laravel Pint). No `phpunit.xml`-level coverage config — `tests/{Unit,Feature}` only.

## Conventions & Gotchas
- Migrations are the contract — never delete/rename one that has shipped. Legacy `buku` → `barang` rename exists; keep it.
- Language: UI is Indonesian with English technical terms (SKU, barcode, rak, denah). Match existing voice.
- Mobile is first-class: bottom-nav fixed (≤640px, `safe-area-inset-bottom`), inputs 48px / buttons 44px, tables flip to card-stack via `[data-label]`. Test at 380/560/640/980px breakpoints (see `.impeccable/design.json: breakpoints`).
- `public/build` and `storage/framework/views` are ignored in Vite watch — `npm run build` regenerates `public/build`; don't edit it directly.
- Pin `opencode` skill shortcuts: `\.opencode\skills\impeccable\scripts\impeccable.cmd <verb>` (Windows) — e.g. `impeccable context`, `impeccable detect --json`.

## References
- Product truth: `PRODUCT.md` (users, principles, constraints).
- Visual truth: `DESIGN.md` + `.impeccable/design.json`.
