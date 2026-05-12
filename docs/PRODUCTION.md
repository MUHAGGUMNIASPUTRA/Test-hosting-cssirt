# Production & Deployment

Panduan build production, deployment, dan migration cases.

## Build Production

```bash
npm ci
npm run build
php artisan optimize
```

> Install dev dependencies hanya saat setup awal. Saat build production, gunakan `npm ci --omit=dev` untuk skip dev dependencies:
>
> ```bash
> npm ci --omit=dev
> npm run build
> php artisan optimize
> ```

Langkah-langkah:
1. **`npm ci` atau `npm ci --omit=dev`** — Install dependencies exact dari `package-lock.json` (dengan atau tanpa dev deps)
2. **`npm run build`** — Build client assets + SSR bundle
3. **`php artisan optimize`** — Cache routes, config, dan views untuk performa optimal

> **Note:** Gunakan `npm ci` (Clean Install) di production, bukan `npm install`. `npm ci` menggunakan `package-lock.json` yang fixed dan tidak menimbulkan konflik dependency.

---

## Server-Side Rendering (SSR)

### Menjalankan SSR Server

```bash
node bootstrap/ssr/ssr.js
```

### Menggunakan PM2

Lihat `ecosystem.config.js` untuk konfigurasi PM2. Deploy dan manage SSR server:

```bash
pm2 start ecosystem.config.js
pm2 restart all
pm2 stop all
pm2 logs
```

---

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

Pastikan queue worker aktif. Cek status job yang gagal:

```bash
php artisan queue:failed
```

### Storage tidak bisa diakses

Pastikan symlink sudah dibuat:

```bash
php artisan storage:link
```

---

## Migration Case: Enkripsi Data Sensitif Pegawai

Migration: `2026_05_12_000001_encrypt_employee_sensitive_fields`

Apa yang dilakukan: Mengubah kolom `nip`, `nik`, `phone`, `email` dari plaintext string menjadi text terenkripsi menggunakan AES-256-CBC (via Laravel Crypt).

### ⚠️ Prasyarat Wajib

1. **Backup database sebelum migrasi**
   ```bash
   mysqldump -u root -p csirt_bojonegorokab > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **APP_KEY di `.env` TIDAK BOLEH BERUBAH** sebelum atau sesudah migrasi
   - APP_KEY adalah satu-satunya kunci dekripsi
   - Jika berubah, semua data terenkripsi tidak bisa dibaca

3. **Pastikan APP_KEY sama persis di semua server** (jika multi-server deployment)
   - Copy `.env` APP_KEY value yang sama ke semua instance

### Langkah Migrasi Step by Step

1. **Backup database**
   ```bash
   mysqldump -u root -p csirt_bojonegorokab > backup_before_encryption.sql
   ```

2. **Aktifkan maintenance mode**
   ```bash
   php artisan down
   ```

3. **Deploy kode terbaru**
   ```bash
   git pull origin main
   composer install
   ```

4. **Jalankan migrasi**
   ```bash
   php artisan migrate
   ```
   Migration akan:
   - Ubah tipe kolom `nip`, `nik`, `phone`, `email` dari `string` ke `text`
   - Encrypt setiap row existing dengan `Crypt::encryptString()`
   - Setiap nilai disimpan sebagai ciphertext JSON (base64-encoded)

5. **Verifikasi data terenkripsi dengan benar**
   ```bash
   php artisan tinker
   >>> $emp = App\Models\Employee::first();
   >>> $emp->nip_masked  // Harus muncul masked (e.g., "19680*********005")
   >>> exit
   ```

6. **Nonaktifkan maintenance mode**
   ```bash
   php artisan up
   ```

### Rollback (jika diperlukan)

```bash
php artisan migrate:rollback --step=1
```

Migration `down()` akan:
- Decrypt setiap row kembali ke plaintext
- Ubah tipe kolom kembali ke `string`

> **WASPADA**: Rollback hanya bisa dilakukan jika APP_KEY masih intact. Jika APP_KEY sudah berubah, data tidak bisa di-decrypt.

### Post-Migration Checklist

- ✓ Pastikan `$casts = ['nip' => 'encrypted', ...]` sudah ada di `app/Models/Employee.php`
- ✓ Pastikan `$hidden` dan `$appends` benar (masked fields dikirim ke frontend, plaintext tidak)
- ✓ Test reveal endpoint via UI admin (`/admin/employees` → eye icon → input password admin)
- ✓ Cek database: semua `nip`, `nik`, `phone`, `email` harus ciphertext (dimulai dengan `eyJ...`)
- ✓ Monitor logs untuk decryption errors: `php artisan tail`

### Data yang Dienkripsi

Hanya pegawai existing di tabel `employees` yang terenkripsi secara otomatis. Employee baru yang dibuat setelah migrasi akan otomatis terenkripsi via model cast (tidak perlu aksi manual).

---

## Keamanan APP_KEY

Lihat [docs/CRYPTOGRAPHY.md](CRYPTOGRAPHY.md) untuk detail lengkap tentang encryption system, masking, dan reveal mechanism.
