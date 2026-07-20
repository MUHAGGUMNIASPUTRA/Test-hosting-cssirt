# Pengembangan Lokal

Panduan menjalankan aplikasi di development dan kontribusi kode.

## Dev Commands

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

---

## Kode Styling

Proyek menggunakan **Laravel Pint** untuk PHP dan **Prettier** untuk JavaScript/Vue. Format otomatis saat save via VS Code (lihat [SETUP.md](SETUP.md)).

### Format Manual

```bash
# Format PHP
./vendor/bin/pint

# Format JS/Vue
npm run format
```

---

## Analisis Statis (Larastan)

Proyek menggunakan **Larastan** (PHPStan + aturan Laravel) untuk mendeteksi bug dan type error di kode PHP. Berbeda dengan Pint yang hanya memformat, Larastan melakukan analisis statis untuk keamanan dan kualitas kode.

### Cara Menjalankan

```bash
# Analisis seluruh app/
composer stan

# Analisis file tertentu
./vendor/bin/phpstan analyse app/Http/Controllers/SomeController.php

# Generate baseline (untuk menyimpan error existing, agar hanya error baru yang ditolak)
composer stan:baseline
```

### Tingkat Analisis

Proyek diatur di level 5 (moderat) di [`phpstan.neon`](../phpstan.neon). Level dapat dinaikkan bertahap ke 6–9 seiring peningkatan type coverage.

---

## Kontribusi

### Git Workflow

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

4. Jalankan analisis statis:

   ```bash
   composer stan
   ```

   Perbaiki warning atau error yang muncul.

5. Buat pull request ke branch `main` dengan deskripsi yang jelas.

### Konvensi Commit

Gunakan format [Conventional Commits](https://www.conventionalcommits.org):

```
feat: tambah fitur laporan insiden
fix: perbaiki validasi form kontak
refactor: pisah komponen tabel insiden
docs: update panduan instalasi
```
