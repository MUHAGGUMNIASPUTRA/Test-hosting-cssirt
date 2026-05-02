# Panduan Pengembangan

Dokumen ini berisi panduan instalasi, pengembangan, troubleshooting, dan kontribusi untuk proyek CSIRT Kabupaten Bojonegoro.

## Prasyarat

- PHP 8.2+
- Composer
- Node.js 20+ / npm 10+
- MySQL 8 atau MariaDB 10.5+

## Instalasi

1. Clone repositori dan masuk ke direktori proyek:

   ```bash
   git clone <url-repo>
   cd csirt.bojonegorokab.go.id
   ```

2. Install dependensi PHP dan JavaScript:

   ```bash
   composer install
   npm install
   ```

3. Salin file environment dan generate app key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Konfigurasi koneksi database di `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=csirt_bojonegorokab
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Jalankan migrasi dan seeder:

   ```bash
   php artisan migrate --seed
   ```

6. Buat symlink storage:

   ```bash
   php artisan storage:link
   ```

## Development

Ada dua perintah dev tergantung environment:

### Menggunakan Laravel Herd (Windows)

Herd sudah menangani web server secara otomatis via domain `.test`, sehingga hanya perlu menjalankan queue dan Vite:

```bash
composer dev:herd
```

Akses aplikasi di `http://csirt.bojonegorokab.go.id.test`.

### Tanpa Herd (Linux / macOS / Docker)

Menjalankan server, queue, log viewer (Pail), dan Vite sekaligus:

```bash
composer dev
```

> **Catatan:** `composer dev` membutuhkan ekstensi `pcntl` (hanya tersedia di Linux/macOS) untuk Laravel Pail. Tidak bisa digunakan di Windows.

### Menjalankan masing-masing secara terpisah

```bash
# Backend (tidak diperlukan jika pakai Herd)
php artisan serve

# Queue worker
php artisan queue:listen --tries=1

# Log viewer (Linux/macOS only)
php artisan pail --timeout=0

# Frontend (Vite)
npm run dev
```

### Build Production

```bash
npm install
npm run build
npm prune --omit=dev

php artisan optimize
```

### SSR (Server-Side Rendering)

```bash
npm run build           # build client + SSR
node bootstrap/ssr/ssr.js   # jalankan SSR server
```

Atau menggunakan PM2 (lihat `ecosystem.config.js`):

```bash
pm2 start ecosystem.config.js
```

## Testing

### PHP Tests

Membutuhkan database PostgreSQL yang dikonfigurasi untuk testing. Pastikan sudah ada `.env.testing` atau variabel `DB_*` sudah mengarah ke database test.

```bash
# Semua test
php artisan test

# Hanya unit test
php artisan test --filter Unit
```

### JS Tests

```bash
npm run test:js        # run once
npm run test:js:watch  # watch mode
```

### Jalankan Semua Test Sekaligus

```bash
composer test
```

> Perintah ini menjalankan PHP test (`php artisan test`) diikuti JS test (`npm run test:js`).

## Troubleshooting

### Halaman blank setelah build

Pastikan SSR build sudah dijalankan:

```bash
npm run build
```

Lalu restart SSR server jika berjalan.

### Error `Class not found` setelah menambah model/controller

Regenerate autoload Composer:

```bash
composer dump-autoload
```

### Aset tidak termuat sekarang

Hapus cache dan rebuild:

```bash
php artisan optimize:clear
npm run build
```

### Queue job tidak berjalan

Pastikan queue worker aktif:

```bash
php artisan queue:listen --tries=1
```

Cek status job yang gagal:

```bash
php artisan queue:failed
```

### Storage tidak bisa diakses

Pastikan symlink sudah dibuat:

```bash
php artisan storage:link
```

## Kode Styling

Proyek menggunakan **Laravel Pint** untuk PHP dan **Prettier** untuk JavaScript/Vue:

```bash
# Format PHP
./vendor/bin/pint

# Format JS/Vue
npm run format
```

## VS Code Setup

Project sudah menyertakan konfigurasi `.vscode/` untuk format otomatis saat file disimpan.

### Ekstensi yang Direkomendasikan

Install dua ekstensi berikut (VS Code akan menyarankan otomatis saat membuka project):

| Ekstensi    | ID                       | Fungsi                                             |
| ----------- | ------------------------ | -------------------------------------------------- |
| Run on Save | `emeraldwalk.runonsave`  | Menjalankan Pint otomatis saat file PHP disimpan   |
| Prettier    | `esbenp.prettier-vscode` | Memformat JS / Vue / TS / CSS / JSON saat disimpan |

### Cara Install

```bash
# Via VS Code CLI
code --install-extension emeraldwalk.runonsave
code --install-extension esbenp.prettier-vscode
```

Atau buka Command Palette (`Ctrl+Shift+P`) → **Extensions: Show Recommended Extensions** → install semua.

### Perilaku Format on Save

Setelah ekstensi terinstall, format berjalan otomatis saat `Ctrl+S`:

- **File `.php`** → diformat oleh Laravel Pint (`./vendor/bin/pint`)
- **File `.js`, `.ts`, `.vue`, `.css`, `.json`** → diformat oleh Prettier (menggunakan `.prettierrc.json`)

---

## Kontribusi

1. Buat branch baru dari `main`:

   ```bash
   git checkout -b feature/nama-fitur
   ```

2. Lakukan perubahan dan pastikan tidak ada error.

3. Format kode sebelum commit:

   ```bash
   ./vendor/bin/pint
   npm run format
   ```

4. Buat pull request ke branch `main` dengan deskripsi yang jelas mengenai perubahan yang dilakukan.

### Konvensi Commit

Gunakan format [Conventional Commits](https://www.conventionalcommits.org):

```
feat: tambah fitur laporan insiden
fix: perbaiki validasi form kontak
refactor: pisah komponen tabel insiden
docs: update panduan instalasi
```
