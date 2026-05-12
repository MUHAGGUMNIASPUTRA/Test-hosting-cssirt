# Setup & Instalasi

Panduan lengkap untuk menyiapkan lingkungan pengembangan awal atau production baru.

## Prasyarat

- **PHP 8.2+**
- **Composer**
- **Node.js 20+ / npm 10+**
- **MySQL 8 atau MariaDB 10.5+**

## Instalasi

### 1. Clone repositori

```bash
git clone <url-repo>
cd csirt.bojonegorokab.go.id
```

### 2. Install dependensi

```bash
composer install
npm install
```

### 3. Konfigurasi environment dan generate APP_KEY

Salin file `.env.example` ke `.env`:

```bash
cp .env.example .env
```

Generate aplikasi key:

```bash
php artisan key:generate
```

> **⚠️ PERINGATAN KRITIS**: Jangan pernah menjalankan `php artisan key:generate` di server production yang sudah memiliki data terenkripsi (seperti NIP/NIK pegawai). APP_KEY adalah satu-satunya kunci untuk mendekripsi data sensitif. Mengganti key akan membuat semua data tidak bisa dibaca. Lihat [docs/CRYPTOGRAPHY.md](CRYPTOGRAPHY.md) untuk detail.

### 4. Konfigurasi database

Edit file `.env` dan sesuaikan koneksi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csirt_bojonegorokab
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan migrasi dan seeder

```bash
php artisan migrate --seed
```

### 6. Buat symlink storage

```bash
php artisan storage:link
```

Aplikasi sekarang siap dijalankan. Lihat [docs/DEVELOPMENT.md](DEVELOPMENT.md) untuk dev commands atau [docs/PRODUCTION.md](PRODUCTION.md) untuk deployment.

---

## VS Code Setup

Project sudah menyertakan konfigurasi `.vscode/` untuk format otomatis saat file disimpan. Untuk mengaktifkan, install dua ekstensi berikut:

| Ekstensi    | ID                       | Fungsi                                             |
| ----------- | ------------------------ | -------------------------------------------------- |
| Run on Save | `emeraldwalk.runonsave`  | Menjalankan Pint otomatis saat file PHP disimpan   |
| Prettier    | `esbenp.prettier-vscode` | Memformat JS / Vue / TS / CSS / JSON saat disimpan |

### Cara Install

Via VS Code CLI:

```bash
code --install-extension emeraldwalk.runonsave
code --install-extension esbenp.prettier-vscode
```

Atau buka Command Palette (`Ctrl+Shift+P`) → **Extensions: Show Recommended Extensions** → install semua.

### Perilaku Format on Save

Setelah ekstensi terinstall, format berjalan otomatis saat `Ctrl+S`:

- **File `.php`** → diformat oleh Laravel Pint
- **File `.js`, `.ts`, `.vue`, `.css`, `.json`** → diformat oleh Prettier (`.prettierrc.json`)
