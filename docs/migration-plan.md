# Kabar Sembada — Plan Migrasi Data

Status: **rencana, belum dieksekusi.** Fase 4 di `docs/plan.md` mengacu ke
dokumen ini.

Tujuan: memindahkan artikel kabarsembada.com lama (Elementor, WordPress
standar) ke situs baru (tema + plugin `kabarsembada`) di **lokal (Docker)**.
Deploy produksi adalah task terpisah (dipegang senior).

Sumber: `C:\KAIRAV\backup_kabarsembada\`

- `kab44267_wp914.sql.gz` — dump MariaDB 11.4, prefix `wp_`, 135 MB setelah
  diekstrak.
- `wp-content/uploads/` — 84.042 file, 8,3 GB.

Semua script dan file kerja migrasi tinggal di
`C:\KAIRAV\backup_kabarsembada\migration\` (di luar repo). Tidak ada dump,
gambar, atau script migrasi yang masuk git.

---

## 1. Temuan analisis

### Keamanan — situs lama kemungkinan dibobol

- 7 administrator, beberapa dibuat dalam pola tidak wajar: "Malcolm",
  "backup_manager", 2 akun role `internal_cache_sync` (24 Agu 2026, berselang
  menit), "maria" (12 Okt), "Redaksi" kedua / user 18 (23 Okt), "owodefev"
  (20 Nov). Plugin `wp-file-manager` aktif.
- File tersembunyi `.6fc25543a5cf8878.cache` (51 KB) di root uploads.
- Konten artikel bersih dari `<script>`, `eval`, base64.

Konsekuensi: **tidak ada user, opsi, plugin, tema, atau file non-gambar yang
dibawa.** Hanya baris artikel/attachment/term yang lolos filter dan file gambar
yang lolos whitelist. Pemilik situs lama sebaiknya segera mengganti semua
password, kunci hosting, dan salt.

### Data

| Data | Lama | Dibawa |
|---|---|---|
| Artikel terbit | 4.003 (Jul 2024 – Nov 2026) | 3.998 (lihat §2) |
| Draft | 2 | 2, tetap draft |
| Format konten | 3.814 blok `core/*` murni, 189 klasik (2024), 3 Elementor (juga punya blok) | semua jadi blok |
| Halaman | 9 | 5 halaman teks redaksi |
| Attachment | 4.661 (11 file hilang di disk) | gambar 2024–2026 yang filenya ada |
| Featured image | 3.996 artikel | ya, ID sama |
| Kategori | 19 datar | 16 + Uncategorized (default WP) |
| Tag | 13.932 (472 tak terpakai) | yang terpakai, slug sama |
| Penulis | 13 akun (19 user total) | 0 — semua ke admin situs baru |
| Komentar | 138, tak satu pun disetujui | tidak |
| Views | `wp_post_views` type 4, ±1,5 juta | ya → `kabarsembada_views` |
| SEO Yoast | kategori utama 3.971, metadesc 2, sisanya bawaan | dikonversi ke meta plugin |
| `_wp_old_slug` | 3 | ya (redirect slug lama bawaan WP) |
| Menu, home Elementor, template, popup, revisi | ada | tidak |

---

## 2. Keputusan

| # | Topik | Keputusan |
|---|---|---|
| M1 | Lingkungan | Lokal (Docker) dulu. Produksi task terpisah. |
| M2 | Metode | **SQL mentah.** Dump dimuat ke DB `legacy` di container `db` yang sama, lalu `INSERT … SELECT` lintas DB ke `wordpress`. Satu-satunya langkah non-SQL: regenerate ukuran gambar (M9). |
| M3 | Lokasi | Script & file kerja di `C:\KAIRAV\backup_kabarsembada\migration\`. Repo hanya berisi dokumen ini dan perubahan plugin. |
| M4 | ID | ID post, attachment, term, dan term_taxonomy **sama dengan lama**. Tiap post juga diberi meta `_ks_legacy_id`. |
| M5 | Idempoten | Setiap run mengosongkan tabel konten target (`wp_posts`, `wp_postmeta`, `wp_terms`, `wp_term_taxonomy`, `wp_term_relationships`, `wp_termmeta`, `wp_comments`, `wp_commentmeta`) lalu mengisi ulang. User dan opsi situs baru tidak disentuh. Jalankan **sebelum** ada kustomisasi Site Editor di DB. Ada mode `--dry-run` (hanya laporan hitungan) dan uji kecil (`LIMIT`). |
| M6 | Artikel | Semua `post` terbit + 2 draft. **Dibuang:** #42 (demo Inggris), 4 artikel milik user 18 ("Redaksi" kedua, akun era pembobolan), auto-draft, revisi. |
| M7 | Halaman | Tentang, Redaksi, Pedoman Media Siber, Panduan Komunitas, Privacy Policy. Isinya dicek manual setelah import (bisa jadi sisa Elementor). |
| M8 | Konten | Blok dibiarkan. 189 klasik dikonversi ke `core/paragraph` dengan SQL (§4 langkah 6). URL `http(s)://kabarsembada.com/wp-content/uploads/…` → `/wp-content/uploads/…`; link internal `https://kabarsembada.com/<slug>/` → `/<slug>/`. `src` inline yang menunjuk varian ukuran lama diarahkan ke file utama. |
| M9 | Gambar | **Satu file per attachment** — file yang tercatat di `_wp_attached_file` (versi `-scaled` bila ada). Ukuran dibuat ulang di lokal, sub-size jadi AVIF (fallback WebP) lewat `includes/media.php`. Regenerate memakai container sekali pakai `wordpress:cli` (`compose.yml` tidak diubah). |
| M10 | Attachment | Hanya mime gambar (jpeg/png/webp/gif), tidak dari folder `2021/` (demo). 11 attachment yang filenya hilang tidak dibawa, dan `_thumbnail_id` yang menunjuk ke sana dihapus (artikel tampil tanpa gambar, dicatat di laporan). |
| M11 | Kategori | Slug sama. `politik-2` ("Politik", 14) **digabung** ke `politik` ("Politik Hukum"), redirect `/category/politik-2/` → `/category/politik/` di plugin. "Video" (0) dibuang. "Uncategorized" tetap ada karena WordPress wajib punya kategori bawaan (0 artikel, tidak tampil di menu). "Headline" tetap kategori; sticky (Pilihan Redaksi) dipilih manual oleh redaksi. |
| M12 | Tag | Tag yang dipakai artikel yang dibawa, slug & ID sama. Tag tak terpakai dibuang. Rapikan duplikat tag = task editorial nanti. `count` dihitung ulang. |
| M13 | Penulis | Semua artikel, halaman, dan attachment → `post_author` = admin situs baru (user ID 1). Display name admin = **"Redaksi Kabar Sembada"**. Tidak ada user lama yang dibawa. |
| M14 | Komentar | Tidak dibawa. |
| M15 | Views | `legacy.wp_post_views` (type 4 = total) → meta `kabarsembada_views`. |
| M16 | SEO | Tanpa Yoast. Otomatis dikonversi saat migrasi: `_yoast_wpseo_primary_category` → `kabarsembada_primary_category` (ID `politik-2` diganti `politik`); `_yoast_wpseo_metadesc` → `kabarsembada_seo_description`. Plugin 0.2.0 membaca kunci sendiri + panel SEO di editor (§3). Focus keyword, skor, dan meta Yoast lain tidak dibawa. |
| M17 | Meta lain | Dibawa: `_thumbnail_id`, `_wp_attached_file`, `_wp_attachment_image_alt`, `_wp_old_slug`, `footnotes`. `_wp_attachment_metadata` **tidak** dibawa (dibuat ulang oleh regenerate). Semua meta Elementor/Stackable/UAG/Jetpack/`_edit_*`/`_rpt_*` dibuang. |
| M18 | Verifikasi | Laporan otomatis (§5). |

---

## 3. Prasyarat di repo (sebelum migrasi)

Dikerjakan dan di-commit seperti biasa (scope `plugin`).

- **P1 — SEO bawaan plugin 0.2.0**
  - `register_post_meta` untuk `kabarsembada_primary_category` (integer) dan
    `kabarsembada_seo_description` (string), `show_in_rest`.
  - Panel "SEO" di sidebar dokumen: deskripsi (textarea, hitung karakter) dan
    kategori utama (pilihan dari kategori artikel itu).
  - `kabarsembada_primary_category()` dan deskripsi SEO membaca kunci sendiri.
    Kode fallback Yoast (`_yoast_wpseo_*`, termasuk judul) dihapus.
- **P2 — Redirect kategori**: `includes/redirects.php`, satu aturan
  `/category/politik-2/…` → `/category/politik/…` (301).
- **P3 — Lingkungan lokal jalan**: Docker aktif, WordPress terpasang, tema &
  plugin Kabar Sembada aktif, permalink `/%postname%/`.

---

## 4. Langkah migrasi (lokal)

Folder kerja `C:\KAIRAV\backup_kabarsembada\migration\`:

```
migration/
  README.md            urutan perintah
  00-legacy-load.sh    muat dump ke DB legacy
  10-reset-target.sql  kosongkan tabel konten target
  20-terms.sql         kategori, tag, gabung politik-2
  30-attachments.sql   attachment gambar
  40-posts.sql         artikel + halaman, author, URL, konversi klasik
  50-meta.sql          thumbnail, views, SEO, old slug, legacy id
  60-cleanup.sql       hitung ulang count, auto_increment, thumbnail yatim
  70-files.py          salin gambar (whitelist dari DB)
  80-regenerate.sh     wp media regenerate via wordpress:cli
  90-report.sql        laporan verifikasi
```

1. **Muat legacy** — `CREATE DATABASE legacy`, lalu impor dump ke situ
   (`mariadb` client di container `db`, dump di-mount read-only dari folder
   backup). Tidak menyentuh DB `wordpress`.
2. **Reset target** — `TRUNCATE` tabel konten target (M5). Admin & opsi aman.
3. **Terms** — salin `wp_terms`, `wp_term_taxonomy` untuk `category` dan
   `post_tag` yang dipakai artikel yang dibawa (ID tetap; 202 tag punya
   `term_taxonomy_id ≠ term_id`, dipertahankan apa adanya). Relasi
   `politik-2` dipindah ke `politik`, term `politik-2` dibuang.
4. **Attachment** — baris `attachment` sesuai M10, `post_author` = 1,
   `post_parent` diarahkan ke 0 bila induknya tidak dibawa.
5. **Posts & pages** — sesuai M6/M7, `post_author` = 1, `guid` tetap.
6. **Konversi klasik → blok** (189 artikel tanpa `<!-- wp:`):
   - buang `&nbsp;` di awal, `\r\n` → `\n`;
   - pecah per baris kosong, bungkus tiap bagian dengan
     `<!-- wp:paragraph --><p>…</p><!-- /wp:paragraph -->`;
   - baris tunggal → `<br>`; paragraf kosong dibuang;
   - 23 artikel yang berisi `<img>`/`[caption]` ditandai di laporan untuk
     dicek manual (gambar tetap tampil, tapi di dalam paragraf).
7. **URL** — `REPLACE` domain → relatif (M8). Varian ukuran inline
   (`-1024x683.jpg`) diarahkan ke file utama attachment lewat tabel peta
   `nama-dasar → _wp_attached_file` (termasuk kasus `-scaled`), diulang sampai
   tidak ada perubahan.
8. **Meta** — sesuai M15–M17, plus `_ks_legacy_id`.
9. **Cleanup** — hitung ulang `count` term (hanya relasi ke post yang
   dibawa), buang term yang jadi 0 (kecuali Uncategorized), set
   `AUTO_INCREMENT` di atas ID tertinggi, hapus `_thumbnail_id` yang menunjuk
   attachment yang tidak ada. Set display name admin (M13).
10. **Salin gambar** — `70-files.py` membaca daftar `_wp_attached_file` dari
    DB target, menyalin hanya file itu dari backup ke
    `wp-content/uploads/YYYY/MM/` repo (sudah di-gitignore). Ekstensi
    di-whitelist; tidak ada file lain yang ikut.
11. **Regenerate** — `docker run --rm wordpress:cli wp media regenerate --yes`
    di jaringan & volume yang sama. Cek dulu dukungan AVIF di image (bila tidak
    ada, plugin otomatis jatuh ke WebP). Perkiraan: lama (±4.600 gambar,
    AVIF berat) — jalan di background.
12. **Laporan** — `90-report.sql` + cek HTTP (§5).

Setiap langkah SQL dibungkus transaksi. Urutan 2–9 bisa diulang kapan saja
(M5); langkah 10–11 hanya perlu diulang bila daftar attachment berubah.

---

## 5. Verifikasi

- Hitungan lama vs baru: artikel terbit (3.998), draft (2), halaman (5),
  attachment, kategori, tag, relasi.
- Artikel tanpa featured image (harusnya ±7 + yang filenya hilang), dengan
  daftar ID.
- Total views (±1,5 juta) dan 10 artikel teratas cocok dengan lama.
- Kategori utama terisi di ±3.971 artikel.
- Sisa string `kabarsembada.com/wp-content` di konten = 0; sisa `<!-- wp:`
  hilang = 0 (semua artikel berblok).
- Daftar domain eksternal di link konten (untuk review manual spam/backlink
  titipan).
- Cek HTTP 200 untuk 50 URL lama acak (`/<slug>/`), 3 `_wp_old_slug`,
  `/category/politik-2/` → 301, beberapa `/tag/<slug>/`.
- Cek visual: 5 artikel blok, 5 artikel hasil konversi klasik, 23 artikel
  bertanda, 5 halaman.

---

## 6. Tasks

- [ ] **T4.1** P1 — SEO bawaan plugin 0.2.0 (meta, panel, hapus fallback Yoast).
- [ ] **T4.2** P2 — redirect `politik-2`.
- [ ] **T4.3** P3 — Docker + WordPress lokal aktif dengan tema & plugin.
- [ ] **T4.4** Script `00`–`60` + uji `--dry-run` dan `LIMIT 20`.
- [ ] **T4.5** Run penuh langkah 1–9, review laporan hitungan.
- [ ] **T4.6** Salin gambar (`70`) + regenerate (`80`).
- [ ] **T4.7** Laporan verifikasi (`90`) + cek HTTP + cek visual.
- [ ] **T4.8** Review manual: 23 artikel bertanda, 5 halaman, daftar domain
      eksternal.
- [ ] **T4.9** Serah ke senior untuk deploy produksi (DB + uploads hasil
      lokal), di luar plan ini.

## 7. Risiko & catatan

- **Keamanan**: lihat §1. Jangan pernah menyalin folder/file selain whitelist
  gambar.
- **Site Editor**: reset target (M5) menghapus kustomisasi Site Editor yang
  tersimpan di DB. Migrasi final dijalankan sebelum redaksi mengubah template
  lewat editor.
- **Byline**: semua artikel tampil atas nama "Redaksi Kabar Sembada"; nama
  penulis asli tidak dibawa.
- **Regenerate** butuh waktu dan CPU; AVIF tergantung dukungan library di
  image Docker.
- **Tag** tetap 13 ribuan (banyak dipakai sekali); merapikannya bukan bagian
  migrasi.
