# AGENTS.md — absensinuris

Laravel 13 attendance app (Sekolah): students, teachers, classes, schedules, daily/subject attendance, leave letters. Blade + Breeze auth, Tailwind v3 + Alpine, Vite. PHP `^8.3`.

## Commands

- `composer dev` — serve + queue:listen + vite concurrently (via `php artisan dev`). Use instead of separate `serve`/`vite` terminals.
- `composer test` — runs `config:clear` then `php artisan test`. Use for all test runs.
- `npm run build` / `npm run dev` — Vite only (entries: `resources/css/app.css`, `resources/js/app.js`).
- `./vendor/bin/pint` — formatter (no composer/npm script for it; run before committing PHP changes).
- Focused test: `php artisan test --filter=NameOfTest` (e.g. `--filter=ProfileTest`).
- `php artisan db:seed --class=DummyDataSeeder` — tops up every master table to 100 rows (idempotent). Not in `DatabaseSeeder`; run manually. `pengaturan_absensi` maxes at 6 (one per weekday).
- `php artisan db:seed --class=RealisticSeeder` — small realistic Indonesian school dataset (3 jurusan, 9 kelas, 9 guru, 28 siswa, 14 mapel, 180 jadwal, role accounts with `password123`). Run on an EMPTY database; the two seeders are alternatives, never chained.
- `php artisan absensi:auto-alpa [--tanggal=YYYY-MM-DD] [--force]` — marks missing records alpa (approved letters become izin/sakit). Scheduled nightly 23:30 Asia/Jakarta via `routes/console.php`; app timezone stays UTC so the schedule pins `->timezone('Asia/Jakarta')`. Same logic callable from admin via `App\Services\AutoAlpa::jalankan()`.
- Manual corrections go through `RekapController@koreksi` (`admin.rekap.koreksi` + `piket.koreksi`): creates-or-updates the daily record, requires a reason, stamps `absensi_harian.dikoreksi_oleh` (relation `AbsensiHarian::korektor()`).
- OSIS (`role=osis`) operates the gate scan page alongside piket/admin, but scan-only: manual entry and koreksi are hidden in the view AND rejected server-side (403). OSIS sidebar has just Dashboard + Scan Gerbang. The OSIS Dashboard is `osis.dashboard` (`OsisController@index`, `osis.*` routes) — stats come from the shared `App\Services\StatistikAbsensiHarian` service (also used by the gate page); the live feed reuses the `piket.absensi.feed` JSON endpoint.

## Database

- Local dev uses **MySQL** (`DB_DATABASE=absensinuris` on `127.0.0.1:3306`, Laragon). `.env.example` says sqlite but the real `.env` is MySQL — don't "fix" this.
- Tests force sqlite `:memory:` via `phpunit.xml` — no DB setup needed for tests.
- `struktur.sql` is a MySQL dump fallback; migrations in `database/migrations/` are source of truth.
- `database/database.sqlite` exists but is unused while `.env` points to MySQL.

## Structure

- `routes/web.php` + `routes/auth.php` (Breeze) — no API routes file.
- `app/Http/Controllers/Admin/` (Jurusan, Kelas, TahunAjaran, PengaturanAbsensi, Pengguna, Siswa+Guru+Mapel+Jadwal), `DashboardController` (counts for active year + today), `app/Http/Controllers/BK/LaporanController` (rekap + perhatian + detail siswa, route `bk.*` dengan `role:bk`), `app/Http/Controllers/Piket/AbsensiController` (scan QR/RFID/fingerprint + manual, route `piket.*` dengan `role:piket,admin`), `app/Http/Controllers/Guru/AbsensiMapelController` (jadwal hari ini + form per siswa, route `guru.*` dengan `role:guru,admin`), `ProfileController` from Breeze. Scan kamera pakai `html5-qrcode` dari npm (dibundel vite, bukan CDN — aman untuk LAN sekolah offline); endpoint scan dukung JSON untuk auto-submit tanpa reload; kamera butuh secure context (localhost OK, via IP/hostname wajib HTTPS).
- Views use two conventions (`<x-app-layout>` and `@extends('layouts.app')`) — both work: `layouts/app.blade.php` is a custom sidebar layout that renders `@yield('content')` or falls back to `$slot`. Do not "unify" them.
- Real nav is `components/sidebars/{admin,guru,orang-tua}.blade.php` (picked by role in the layout). Sidebar theme is clean SaaS white (`bg-white` + `border-r`): grouped labels, shared `x-sidebar.link` (active = `bg-blue-50` + left accent pill + optional count badge). Main pages favor floating cards / bento grids over plain tables; page modals and alerts use Alpine `x-transition` (+ auto-dismiss alerts).
- Shared UI tokens live in `resources/css/app.css` (`btn-primary`, `card`, `input`, `badge`, `page-title`): prefer them over new inline styles. Parent (`orangtua.*`) pages are mobile-first (max-w-2xl, stacked cards).
- Layout extras are vanilla JS (no Alpine): mobile drawer (`#btn-drawer`), `⌘K` command palette built at runtime from sidebar links, `print:hidden` on chrome + Cetak buttons on rekap pages. `layouts/navigation.blade.php` is dead Breeze leftover — do not add links there.
- `role` middleware alias (`role:admin`) is registered in `bootstrap/app.php`, implemented in `app/Http/Middleware/CheckRole.php` — not in a Kernel. Supports comma-separated roles (`role:admin,guru`).
- BK is not a `users.role` — it is a `users.penugasan` value on `role=guru` accounts. `role:bk` matches penugasan, so guru+penugasan bk passes both `role:guru` and `role:bk`; plain guru passes only `role:guru`. Helper: `User::bertugasSebagai('bk')`.
- Wali kelas is assignment-based too: `role:wali_kelas` passes for `role=wali_kelas` OR a guru appointed via `kelas.wali_kelas_id`. Helper: `User::isWaliKelas()`. Approval pages live under `walikelas.*` routes (`WaliKelas\SuratIzinController`): approving auto-fills empty dates + upgrades alpa, never overwrites recorded presence.
- Domain models in `app/Models/`: `AbsensiHarian`, `AbsensiMapel`, `JadwalPelajaran`, `AnggotaKelas`, `SuratIzin`, `Guru`, `Siswa`, plus `OrangTuaSiswa` (parent login link) and `NotifikasiKehadiran` (WA/send log).
- Parent accounts (`role=orang_tua`) link to children via `orang_tua_siswa` pivot, managed inline on the Pengguna page (`tautkan-anak` / `lepas-anak`, duplicate pairs rejected). `User::anak()` powers the parent dashboard overview.
- Parent portal lives under `orangtua.*` routes (`OrangTua\NotifikasiController/RiwayatController/SuratIzinController`): in-app notifications are written via `App\Services\NotifikasiOrtu::catat()` from scan, auto-alpa, and approval flows; uploads go to `public/bukti-izin` (no storage symlink on this setup).
- `admin.siswa.show` is the student detail page (biodata + server-rendered QR SVG + stats + history). Student photos live in `public/foto-siswa` (nullable `siswa.foto`, old files deleted on update/destroy).
- `admin.siswa.kartu` is the printable student ID card (front biodata + back QR, print CSS included). Guru photos work the same way (`guru.foto`).
- Lists are paginated everywhere (12 grid cards, 15–20 table rows) with `withQueryString`; summaries are computed from the full filter, not the visible page. Master grids use server-side search (the Alpine instant filter was dropped as incompatible with pagination).
- Pagination chrome is overridden app-wide at `views/vendor/pagination/tailwind.blade.php` (blue/white pills) — edit there, not per page.
- QR generation uses `simplesoftwareio/simple-qrcode` (pure PHP, no extension needed). Composer hangs on this machine — its PSR-4 maps were patched manually into `vendor/composer/autoload_{psr4,static}.php`; rerun `composer dump-autoload` when composer works again to regenerate properly.
- Gerbang page polls `piket.absensi.feed` (JSON, 10s) for the live log; jadwal uses day tabs. Dead `#` sidebar links were removed rather than kept.

## Gotchas

- `2026_10_12_041135_sampah.php` migration creates a junk `sampah` table and has an empty `down()` — remove it if touching migrations; don't copy it as a template.
- `CheckRole` usage is `role:admin` (singular `role`, comma-separated values) — don't use Spatie-style `role:` plural or `can:` syntax.
- Session/cache/queue drivers are `database` in `.env` (not file/array outside testing) — run `php artisan migrate` on fresh setups so sessions and queues work.
