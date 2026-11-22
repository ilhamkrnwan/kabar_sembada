# Kabar Sembada — Aturan Kerja

Portal berita regional Sleman. Rencana dan keputusan: `docs/plan.md`.

## Repository

- Satu repositori (monorepo) di root, remote `ilhamkrnwan/kabar_sembada`.
  Tema dan plugin hidup berdampingan karena saling bergantung (pattern tema
  memakai blok `kabarsembada/*`).
- Branch utama `main`. Tidak ada tag atau release.
- Yang dilacak git hanya kode proyek:
  - `wp-content/themes/kabarsembada/`
  - `wp-content/plugins/kabarsembada/`
  - `docs/`
  - `compose.yml`, `.gitignore`, `.gitattributes`, `AGENTS.md`, `README.md`,
    `PRODUCT.md`, `DESIGN.md`
- `wp-content/themes/sekeco/` dan `wp-content/plugins/sekeco/` hanya referensi
  selama pembangunan. Jangan diaktifkan, jangan diubah. Dihapus setelah Kabar
  Sembada stabil (task T3.3).
- WordPress core (`wp-admin/`, `wp-includes/`, `wp-*.php`, `license.txt`,
  `readme.html`) berasal dari image Docker — jangan di-commit.
- Jangan pernah commit: `wp-content/uploads/`, `wp-content/upgrade/`,
  `wp-content/debug.log`, `.db/`, `node_modules/`, plugin pihak ketiga, dump
  database situs lama.

## Aturan Gutenberg

- Semua tampilan dari blok native. Tanpa page builder.
- Tema = tema blok murni (`theme.json` v3). Plugin memegang blok kustom, meta,
  binding, SEO, views, motion.
- Blok kustom dinamis: `block.json` `apiVersion: 3`,
  `render: file:./render.php`, registrasi dari `build/blocks/<name>`.
- Utamakan blok core + block style. Blok kustom hanya bila core tidak bisa.
- Markup tersimpan berubah → tambah `deprecated`, jangan hapus atribut.
- Menu dikelola editor, tanpa link hardcode.
- Konten jujur: state kosong menyembunyikan diri, tanpa UI palsu.
- String UI Bahasa Indonesia, dibungkus `__()` dengan text domain
  `kabarsembada`.

## Format commit

`type(scope): ringkasan imperatif`

Scope:

- `theme` — tema Kabar Sembada
- `plugin` — plugin Kabar Sembada
- `repo` — Docker, gitignore, aturan, tooling
- `docs` — dokumentasi

Type: `feat`, `fix`, `refactor`, `style`, `docs`, `chore`, `perf`, `test`.

Contoh:

```
feat(plugin): add a views counter for posts
fix(theme): keep the ticker readable on mobile
chore(repo): ignore local database volume
```

Aturan:

- Satu commit = satu concern. Jangan mencampur perubahan tema dan plugin dalam
  satu commit.
- Pesan dalam bahasa Inggris, ringkas, tanpa emoji.
- Body opsional, jelaskan **kenapa**, bukan mengulang **apa**.
- Jangan membuat tag/release.

## Sebelum commit

- Jika menyentuh `wp-content/plugins/kabarsembada/src/`:
  `bun run lint:js && bun run lint:css && bun run build`, lalu ikutkan `build/`.
- Jika menyentuh PHP: `php -l` pada file yang berubah.
- Pastikan tidak ada `wp-content/debug.log` baru dan tidak ada data uji tertinggal.
- Jalankan `git status`, lalu review file yang di-stage sebelum commit.

## Yang dilarang

- `git add -A` atau `git add .` dari root tanpa memeriksa daftar file.
- `git commit -am` untuk perubahan campuran.
- Force push ke `main`.
- Menulis kredensial atau secret ke file yang dilacak. Kredensial lokal di
  `compose.yml` hanya untuk development.

## Versi

- Tema: `Version:` di `wp-content/themes/kabarsembada/style.css`.
- Plugin: header `Version:` dan `KABARSEMBADA_VERSION` di
  `wp-content/plugins/kabarsembada/kabarsembada.php`.
- Naikkan versi setiap perubahan perilaku. Tanpa tag/release.
