# Design — Kabar Sembada

Turunan dari tema yang sudah jadi (`wp-content/themes/kabarsembada`), bukan
niat. Sumber visual: prototipe `portal_project/theme-1` (layout berita
"Suprast").

## Token (`theme.json`)

| Token | Nilai | Pakai untuk |
|---|---|---|
| `brand` | `oklch(74.7% 0.1517 235.8)` | pil kategori, tombol, aksen |
| `on-brand` | `oklch(22% 0.05 245)` | teks di atas brand (brand terang, teks gelap) |
| `brand-ink` | `oklch(45% 0.13 240)` | tautan, kategori di kartu, angka peringkat |
| `brand-tint` / `brand-tint-2` | 95.5% / 91% | hover, kutipan, chip aktif |
| `ink` / `ink-2` / `ink-3` | 20% / 42% / 52% | judul / teks / meta |
| `line`, `surface`, `bg` | 91% / 97% / 99.5% | garis, kotak samping, latar |
| Font | Manrope variable 200–800 (woff2 lokal) | semua teks; judul 800, isi 500 |
| Radius | `--wp--custom--radius` 16px, `radius-lg` 20px | kartu, foto; pil 99px |
| Easing | `cubic-bezier(.16, 1, .3, 1)` | semua transisi |
| Layout | konten 720px, lebar 1200px | kolom baca, halaman |

## Komponen

- **Kartu** = Query Loop core + gaya blok `core/post-template`:
  `ks-hero` (foto penuh + judul di atas gradasi), `ks-rail` (carousel snap,
  panah dari `theme.js`), `ks-featured` (1 besar + daftar, chip kategori dari
  `theme.js`), `ks-feed` (foto kiri 200px), `ks-grid` (3 kolom). Seluruh kartu
  bisa diklik (link judul direntangkan), kategori di atasnya tetap bisa diklik.
- **Variasi kartu** (pola "Kartu: …", tiap pola = seksi + Query Loop):
  `ks-grid-4` (4 kolom ringkas), `ks-bento` (1 besar + 4), `ks-bento-alt`
  (asimetris, judul di atas foto), `ks-overlay` (potret 3 kolom),
  `ks-spotlight` (1 lebar + 3 judul), `ks-headlines` (bernomor tanpa foto),
  `ks-timeline` (jam terbit pada garis waktu). Gaya bisa ditukar dari panel
  Gaya Query Loop.
- **Kategori di kartu** (`post-terms` dengan class `ks-card-term`) hanya
  menampilkan kategori utama.
- **Gaya lain**: `post-terms` `ks-pill` / `ks-tags`, `tag-cloud` `ks-pills`,
  `group` `ks-box` (kotak samping) dan `ks-action` (kotak "Yang perlu warga
  lakukan").
- **Blok plugin**: ticker, tab berita (Terbaru/Paling Dibaca; Fokus per tag —
  tanpa pilihan editor memakai tag artikel terlaris 7 hari), Terpopuler, Bagikan,
  Pencarian (overlay, `/` untuk membuka), Form Langganan.

## Halaman

- **Beranda**: hero + tab, Sedang Ramai (views 7 hari), Pilihan Redaksi
  (sticky), Fokus Berita, Berita Terkini + Terpopuler & Tag.
- **Artikel**: breadcrumbs, pil kategori, judul, deck (hanya ringkasan tulisan
  tangan), byline + bagikan, foto utama 16:9, kolom baca 68ch dengan drop cap,
  tag, komentar, samping Paling Dibaca, Berita Terkait, Berita Lainnya.
- **Arsip / pencarian / 404 / halaman** memakai daftar `ks-feed` + samping.

## Gerak

- CSS: ticker, transisi tab, view transition foto hero → artikel
  (`lead-photo`), progres baca (scroll timeline), foto "settle".
- GSAP (plugin): panel Animasi di Group/Cover; dipakai di Sedang Ramai,
  Pilihan Redaksi, Berita Terkait.
- Semua mematuhi `prefers-reduced-motion`.

## Aturan

- Seksi yang daftarnya kosong menyembunyikan diri, termasuk judulnya.
- Tanpa dark mode (belum).
- Tanpa newsletter sampai ada provider.
