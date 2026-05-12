# Testing

Panduan menjalankan automated tests untuk PHP dan JavaScript.

## PHP Tests

Membutuhkan database yang dikonfigurasi untuk testing (gunakan SQLite untuk development cepat atau PostgreSQL per dokumentasi lama). Pastikan `.env.testing` sudah ada atau variabel `DB_*` mengarah ke database test.

```bash
# Semua test
php artisan test

# Hanya unit test
php artisan test --filter Unit

# Test dengan verbose output
php artisan test --verbose
```

## JavaScript Tests

```bash
# Run sekali
npm run test:js

# Watch mode (otomatis rerun saat file berubah)
npm run test:js:watch
```

## Jalankan Semua Test Sekaligus

```bash
composer test
```

Perintah ini menjalankan PHP test (`php artisan test`) diikuti JS test (`npm run test:js`).

---

## Tips

- Pastikan database test kosong sebelum menjalankan tests untuk hasil yang konsisten.
- Gunakan `php artisan test --filter NamaTest` untuk menjalankan test tertentu saja.
- Lihat output test untuk failure messages yang detail.
