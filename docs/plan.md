# Kabar Sembada — Plan & Tasks

Portal berita regional Sleman (kabarsembada.com) dibangun ulang sebagai tema blok
+ plugin ramping. Desain dari prototipe `C:\KAIRAV\portal_project\theme-1`
(statis HTML/CSS/JS). Aturan Gutenberg mengikuti Sekeco.

## Keputusan

| # | Topik | Keputusan |
|---|---|---|
| 1 | Repo | Repo baru `ilhamkrnwan/kabar_sembada`, histori baru, tanpa remote Sekeco. |
| 2 | Folder Sekeco | `themes/sekeco` + `plugins/sekeco` tetap sebagai referensi selama pembangunan, dihapus (commit terpisah) setelah Kabar Sembada stabil. Tidak diaktifkan. |
| 3 | Penamaan | Slug `kabarsembada` untuk folder tema/plugin, namespace blok `kabarsembada/*`, prefix PHP `kabarsembada_`, konstanta `KABARSEMBADA_VERSION`, text domain `kabarsembada`. Scope commit tetap `theme`/`plugin`/`repo`/`docs`. |
| 4 | Template | `front-page`, `single`, `archive` (kategori/tag/penulis), `search`, `page`, `404`, `index`. |
| 5 | Visual | Port setia theme-1 ke `theme.json`: Manrope 500–800 (woff2 lokal), brand `oklch(74.7% 0.1517 235.8)` + teks navy, radius 16/20, wrap 1200px, breakpoint 1080/900/620/400. Tanpa dark mode (dulu). |
| 6 | Motion | Modul motion Sekeco (GSAP + panel Motion di `core/group`/`core/cover`) dibawa. `.reveal` → GSAP. Ticker (keyframes) dan view transition hero→artikel tetap CSS. Wajib hormati `prefers-reduced-motion`. |
| 7 | Blok kustom | Hanya 5: `ticker`, `post-tabs`, `popular`, `share`, `search`. Sisanya core (Query Loop, Group + block style, Post Author Biography, Comments) + view script kecil bila perlu. |
| 8 | Plugin | Bawa & rampingkan dari Sekeco: loader modul, SEO (meta/OG/JSON-LD `NewsArticle`), REST search/suggest, AVIF/WebP, motion, tooling build. Buang: CPT portfolio/product/team, form builder, redirects, toc, theme-toggle, blok marketing. |
| 9 | Views | Meta `kabarsembada_views`, naik lewat REST ping dari single (aman dengan cache LiteSpeed). Paling Dibaca = all-time; Sedang Ramai = views tertinggi artikel 7 hari terakhir. |
| 10 | Kurasi | Pilihan Redaksi = post sticky + chip kategori. Fokus Berita = tag dipilih di atribut `post-tabs`. Ticker = post terbaru (opsional filter kategori). Tanpa meta tambahan. |
| 11 | Newsletter | Tidak ada sampai ada provider (aturan: tanpa UI palsu). |
| 12 | Komentar | Blok core Comments diberi style theme-1. Buka untuk post, tutup untuk page, moderasi default WP. |
| 13 | Navigasi | Header kategori = core Navigation dikelola editor. Tanpa link hardcode. |
| 14 | Bahasa | String UI front-end & editor langsung Bahasa Indonesia, dibungkus `__()`. Tanpa `.po` dulu. |
| 15 | Import | Lihat `docs/migration-plan.md`. Sumber `C:\KAIRAV\backup_kabarsembada\` (dump SQL + uploads). Permalink `/%postname%/` dipertahankan. |

## Aturan Gutenberg (warisan Sekeco)

- Semua tampilan dari blok native. Tanpa page builder.
- Tema = tema blok murni (`theme.json` v3). Plugin memegang blok kustom, meta,
  binding, SEO, motion.
- Blok kustom dinamis: `block.json` `apiVersion: 3`, `render: file:./render.php`,
  `save` → `null`, registrasi dari `build/blocks/<name>`.
- Blok anak hanya bisa disisipkan di dalam induknya (`parent`/`ancestor`).
- Markup tersimpan berubah → tambah `deprecated`, jangan hapus atribut.
- Konten jujur: state kosong menyembunyikan diri, tanpa angka/kutipan palsu.
- Pattern memakai blok yang bisa diedit di tempat. Tidak ada yang penting hanya
  hidup di markup mentah pattern.

## Tasks

Satu task ≈ satu commit (atau beberapa commit kecil satu concern). Centang saat
selesai.

### Fase 0 — Repo & dokumen

- [x] T0.1 Histori baru, push ke `ilhamkrnwan/kabar_sembada`.
- [x] T0.2 `docs/plan.md` (dokumen ini).
- [x] T0.3 Tulis ulang `AGENTS.md` untuk Kabar Sembada (path versi baru, scope
      commit sama, cek pre-commit untuk `plugins/kabarsembada/src/`).
- [x] T0.4 Tulis ulang `README.md` dan `PRODUCT.md` (produk, audiens Sleman,
      prinsip). `DESIGN.md` ditulis setelah tema jadi (T2.x), diturunkan dari
      hasil, bukan niat.

### Fase 1 — Plugin `kabarsembada`

- [x] T1.1 Kerangka: `kabarsembada.php` (header, `KABARSEMBADA_VERSION`
      0.1.0), loader modul, `package.json` (`@wordpress/scripts`, bun),
      `webpack.config.js`, lint config. Build kosong lulus.
- [x] T1.2 Modul `media`: AVIF/WebP (port dari Sekeco).
- [x] T1.3 Modul `views`: meta `kabarsembada_views` (`register_post_meta`,
      `show_in_rest`), endpoint REST ping (rate-limit sederhana per IP/post via
      transient), view script di single.
- [x] T1.4 Modul `seo`: meta, OG, Twitter, JSON-LD `NewsArticle` +
      `Organization`. Fallback ke `_yoast_wpseo_metadesc` / `_yoast_wpseo_title`.
      Halaman Settings → Kabar Sembada SEO.
- [x] T1.5 Modul `search`: REST `kabarsembada/v1/search` & `suggest` (port, hanya
      post).
- [x] T1.6 Modul `motion`: port GSAP runtime + panel Motion.
- [x] T1.7 Blok `kabarsembada/ticker` (post terbaru, filter kategori opsional,
      tombol jeda, `aria-live` off saat jalan).
- [x] T1.8 Blok `kabarsembada/post-tabs` (mode: Terbaru/Paling Dibaca, atau
      daftar tag Fokus; tab aksesibel panah, panel nonaktif `inert`).
- [x] T1.9 Blok `kabarsembada/popular` (daftar bernomor; rentang: all-time / 7
      hari).
- [x] T1.10 Blok `kabarsembada/share` (WA/FB/X/salin link, tanpa skrip pihak
      ketiga).
- [x] T1.11 Blok `kabarsembada/search` (overlay ala detik: trending, quick
      link, pencarian terakhir di localStorage, `/` untuk buka).
- [x] T1.12 Query var untuk Query Loop core: urut berdasarkan views & rentang 7
      hari ("Sedang Ramai"), related post per kategori (port `sekecoRelated`).

### Fase 2 — Tema `kabarsembada`

- [ ] T2.1 Kerangka: `style.css` (Version 0.1.0), `theme.json` v3 dengan token
      theme-1 (palet, Manrope lokal, radius, spacing, layout 1200), `functions.php`.
- [ ] T2.2 Parts: `topbar` (sosial, ticker, tanggal, email redaksi), `header`
      (logo, Navigation, search, menu mobile), `footer` (4 kolom).
- [ ] T2.3 Block style + CSS: kartu berita (besar/mini/list), chip, pill
      kategori, kotak aksi ("Yang perlu warga lakukan"), carousel scroll-snap.
- [ ] T2.4 `front-page`: hero + tabs, Sedang Ramai (carousel), Pilihan Redaksi
      (sticky + chip), Fokus Berita, Berita Terkini (feed + muat lebih banyak) +
      aside Tag Terpopuler & Terpopuler. Section jadi pattern
      `kabarsembada/home-*`.
- [ ] T2.5 `single`: progress bar, breadcrumbs, pill, judul, deck (excerpt),
      byline + share, foto utama, prose, tag, author box, komentar, sidebar
      (Fokus + Paling Dibaca), Berita Terkait, Berita Lainnya. View transition
      foto hero → artikel.
- [ ] T2.6 `archive`, `search`, `index`, `page`, `404`.
- [ ] T2.7 Style komentar (daftar, form, paginasi).
- [ ] T2.8 `DESIGN.md` dari hasil jadi.

### Fase 3 — QA & bersih-bersih

- [ ] T3.1 Seed dev lokal (kategori theme-1, ±30 post contoh) di DB saja,
      tidak di-commit.
- [ ] T3.2 Cek: `bun run lint:js && lint:css && build`, `php -l`, mobile
      375px, keyboard, `prefers-reduced-motion`, state kosong (tanpa sticky,
      tanpa views, tanpa komentar).
- [ ] T3.3 Hapus `themes/sekeco` + `plugins/sekeco` (commit `chore(repo)`).

### Fase 4 — Migrasi data (rencana terpisah)

Detail, keputusan, dan task T4.x ada di [`docs/migration-plan.md`](migration-plan.md).
Metode: SQL mentah dari dump lama ke DB lokal; script di luar repo.

## Belum diputuskan

- Provider newsletter (keputusan 11).
- Dark mode (keputusan 5).
- Tujuh kategori nav final — tunggu import.
