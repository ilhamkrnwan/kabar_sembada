# Kabar Sembada — WordPress

Portal berita regional Kabupaten Sleman. Monorepo: satu **block theme**
(`kabarsembada`) dan satu **plugin pendamping** (`kabarsembada`). Semua
tampilan dibangun dengan blok native Gutenberg, tanpa page builder.

Rencana, keputusan, dan task: [`docs/plan.md`](docs/plan.md). Aturan kerja:
[`AGENTS.md`](AGENTS.md).

| Bagian | Lokasi |
| --- | --- |
| Tema | `wp-content/themes/kabarsembada/` |
| Plugin | `wp-content/plugins/kabarsembada/` |
| Referensi (dihapus nanti) | `wp-content/themes/sekeco/`, `wp-content/plugins/sekeco/` |
| WordPress | image Docker `wordpress:latest` |

## Persyaratan

- Docker Desktop (atau Docker Engine + Compose v2)
- Bun 1.4+ untuk build aset plugin
- Port **80** dan **8080** bebas

## Instalasi lokal

```sh
git clone https://github.com/ilhamkrnwan/kabar_sembada.git
cd kabar_sembada
docker compose up -d
```

Buka `http://localhost`, selesaikan instalasi WordPress, lalu:

1. **Appearance → Themes** → aktifkan **Kabar Sembada**.
2. **Plugins** → aktifkan **Kabar Sembada**. Jangan aktifkan Sekeco.
3. **Settings → Permalinks** → *Post name* (`/%postname%/`, sama dengan situs
   lama) → **Save**.

Database ada di volume `.db/` dan tidak dilacak git. Adminer:
`http://localhost:8080` — server `db`, user `root`, password `password`
(kredensial development).

Container tidak menyertakan WP-CLI. Perintah PHP satu baris:

```sh
docker compose exec -T wordpress php -r 'require "/var/www/html/wp-load.php"; …'
```

## Development plugin

```sh
cd wp-content/plugins/kabarsembada
bun install
bun run start   # watch
bun run build   # produksi, hasil build/ ikut di-commit
bun run lint:js && bun run lint:css
```
