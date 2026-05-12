# Kriptografi & Keamanan Data Pegawai

Dokumentasi lengkap tentang sistem enkripsi, masking, dan dekripsi data sensitif pegawai (NIP, NIK, telepon, email).

---

## 1. Enkripsi — AES-256-CBC via Laravel Crypt

Data sensitif pegawai disimpan terenkripsi di database menggunakan Laravel's built-in `Crypt` class dengan algoritma **AES-256-CBC**.

### APP_KEY — Kunci Enkripsi

Kunci enkripsi disimpan di file `.env`:

```env
APP_KEY=base64:xYz123abcdef...
```

Format: Base64-encoded, 32 byte (256-bit) acak. Dihasilkan saat setup via `php artisan key:generate`.

**Tanggung jawab:**
- APP_KEY harus dijaga ketat dan tidak boleh bocor ke Git, logs, atau public channels
- APP_KEY SAMA untuk semua server (multi-server deployment)
- APP_KEY TIDAK BOLEH DIUBAH setelah ada data terenkripsi (kecuali dengan procedure khusus key rotation)

### Alur Enkripsi saat Simpan

```
Input user: "196804121990031005" (NIP plaintext)
                ↓
Employee model:  $casts = ['nip' => 'encrypted']
                ↓
Laravel otomatis panggil Crypt::encryptString("196804121990031005")
                ↓
Disimpan di DB: "eyJpdiI6Ii4uLiIsInZhbHVlIjoiLi4uIn0=" (ciphertext Base64)
```

**Struktur ciphertext JSON:**
```json
{
  "iv": "random_16_bytes_base64",
  "value": "encrypted_data_base64",
  "mac": "hmac_verification_code"
}
```

### IV (Initialization Vector) — Acak Per Enkripsi

Setiap kali sebuah nilai dienkripsi, **IV baru digenerate secara acak**. Ini berarti:

✓ Enkripsi "196804121990031005" dua kali menghasilkan ciphertext BERBEDA (same plaintext, different IV)
✗ Tidak bisa melakukan query exact-match pada field terenkripsi (e.g., `where email = 'user@domain.com'`)
✗ firstOrCreate by email tidak bisa bekerja pada field terenkripsi (EmployeeSeeder pakai `name` sebagai unique key)

---

## 2. Masking — One-Way Display Transform

Masking bukan enkripsi. Ini transformasi tampilan saja untuk UI admin — menyembunyikan sebagian data ke frontend tanpa perlu dekripsi penuh.

### Alur saat Model di-Serialize

```
DB: "eyJpdiI6Ii4uLiIsInZhbHVlIjoiLi4uIn0="  (ciphertext)
                ↓
Crypt::decryptString()  (hanya di PHP memory, tidak di frontend)
                ↓
"196804121990031005"  (plaintext temporary)
                ↓
maskNipNik()
                ↓
"19680*********005"  (masked plaintext)
                ↓
Dikirim ke frontend sebagai `nip_masked`
```

### Format Masking per Field

| Field  | Contoh Asli | Hasil Masked | Pola |
|--------|-------------|--------------|------|
| NIP    | 196804121990031005 | 19680*********005 | 5 awal + *** + 3 akhir |
| NIK    | 3522041204680003 | 35220*********003 | 5 awal + *** + 3 akhir |
| Phone  | 08123456789 | 0812****6789 | 4 awal + **** + 4 akhir |
| Email  | kadis@bojonegorokab.go.id | k****@bojonegorokab.go.id | 1 awal + **** + domain intact |

### Field Visibility di JSON Response

Sensitive fields ditangani via Eloquent model:

```php
// app/Models/Employee.php
protected $hidden = ['nip', 'nik', 'phone', 'email'];  // tidak pernah di-serialize
protected $appends = ['nip_masked', 'nik_masked', 'phone_masked', 'email_masked'];  // accessor
```

**Hasil JSON frontend:**
```json
{
  "id": "uuid...",
  "name": "Drs. Agus Supriyanto",
  "nip_masked": "19680*********005",
  "nik_masked": "35220*********003",
  "phone_masked": "0812****6789",
  "email_masked": "k****@bojonegorokab.go.id",
  "position": { ... },
  "organization": { ... }
}
```

Plaintext fields (`nip`, `nik`, `phone`, `email`) **tidak akan pernah muncul** di response.

---

## 3. Dekripsi / Reveal — Hanya Admin dengan Verifikasi Password

Jika admin perlu melihat data asli (plaintext), ada endpoint `/reveal` khusus yang memerlukan verifikasi ulang.

### Full Flow Reveal

```
Frontend: EmployeeRevealDialog
          ↓
          input password admin
          ↓
          POST /admin/employees/{id}/reveal  { password: "..." }
          ↓

Backend: EmployeeController::reveal()
         ↓
         [1] Check: auth()->user()->role === 'admin'  (abort 403 jika bukan)
         ↓
         [2] Check: Hash::check($password, auth()->user()->password)
             ✗ salah → throw AuthorizationException → response 422
             ✓ benar → proceed
         ↓
         [3] EmployeeService::reveal() mengakses:
             $employee->nip       (auto-decrypt via 'encrypted' cast)
             $employee->nik
             $employee->phone
             $employee->email
         ↓
         [4] Return: { nip, nik, phone, email }  (plaintext hanya di response ini)
         ↓

Frontend: EmployeeRevealDialog
          ↓
          Display di panel amber "Data Asli (sesi ini saja)"
          ↓
          Data TIDAK disimpan ke form
          ↓
          Data hilang saat dialog ditutup / halaman direfresh
```

### Lapisan Keamanan Ganda

1. **Layer 1: Role Check** (`role === 'admin'`)
   - Hanya user admin yang bisa POST ke endpoint reveal
   - User biasa atau staff → abort 403

2. **Layer 2: Password Verification** (`Hash::check()`)
   - Hash dari plaintext password yang diinput
   - Cocokkan dengan `users.password` (Bcrypt hash)
   - Ini verifikasi ulang selain sesi login yang sudah aktif
   - Melindungi jika terminal admin dibiarkan tidak terkunci

3. **Layer 3: Response Only** (data plaintext hanya di HTTP response)
   - Data asli tidak disimpan di frontend state
   - Tidak dikirim ulang ke server (kecuali via new reveal)
   - Hilang saat reload/close dialog

---

## 4. Ringkasan 3 Lapisan Keamanan

| Lapisan | Lokasi | Apa | Siapa Bisa Akses |
|---------|--------|-----|------------------|
| **Layer 1: Database** | DB table `employees` | Ciphertext AES-256-CBC (cipherbisa dibaca hanya dengan APP_KEY) | Hanya proses dengan APP_KEY |
| **Layer 2: PHP Model** | `app/Models/Employee.php` | Decrypt → Mask → Kirim JSON (plaintext hanya di memory PHP) | Frontend via index/edit (menerima masked) |
| **Layer 3: Reveal Endpoint** | `POST /admin/employees/{id}/reveal` | Role check + password verify (double gate) | Admin yang tahu password-nya sendiri |

```
┌─────────────────────────────────────────────────────────┐
│ DATABASE                                                │
│ nip: "eyJpdiI6Ii4uLiIsInZhbHVlIjoiLi4uIn0="            │
│ (ciphertext — bisa dibaca hanya dengan APP_KEY)         │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ↓ (decrypt via crypt cast)
           
┌──────────────────────────────────────────────────────────┐
│ PHP MODEL: Accessor                                      │
│ nip_masked: "19680*********005"                          │
│ (plaintext temporary — di memory saja)                   │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ↓ (serialize → JSON)
           
┌──────────────────────────────────────────────────────────┐
│ FRONTEND (Index/Edit)                                    │
│ { nip_masked: "19680*********005" }                      │
│ (plaintext field 'nip' tidak ada — $hidden)              │
└──────────────────────────────────────────────────────────┘

                  [Admin klik eye icon]
                         ↓
                   
┌──────────────────────────────────────────────────────────┐
│ REVEAL ENDPOINT                                          │
│ 1. role === 'admin' ✓                                    │
│ 2. Hash::check(password) ✓                               │
│ 3. Return plaintext:                                     │
│    { nip: "196804121990031005",                          │
│      nik: "3522041204680003",                            │
│      phone: "08123456789",                               │
│      email: "kadis@bojonegorokab.go.id" }                │
└──────────────────────────────────────────────────────────┘
                       ↓
           (display di UI, tidak disimpan)
```

---

## 5. ⚠️ Peringatan Kritis: `php artisan key:generate`

### Jangan Jalankan di Production yang Sudah Ada Data

```bash
# ❌ JANGAN LAKUKAN INI DI SERVER PRODUCTION
php artisan key:generate
```

**Apa yang terjadi jika APP_KEY diganti:**

1. APP_KEY lama: `base64:abcd1234...`
2. Jalankan `php artisan key:generate` → APP_KEY baru: `base64:wxyz9876...`
3. Semua data terenkripsi dengan APP_KEY lama tidak bisa di-decrypt
4. Database query: `Employee::first()->nip` → **Decryption fails** → Exception atau garbage data
5. **Data NIP, NIK, phone, email tidak bisa dibaca lagi**
6. **Harus di-input ulang secara manual oleh admin**

### Yang Aman

- ✓ Jalankan `key:generate` hanya saat **instalasi baru** (sebelum migrate)
- ✓ Gunakan `.env` yang sama di semua server (multi-server) → copy dari server pertama
- ✓ Jika perlu rotate key: ada procedure khusus (tidak dalam scope doc ini)

### Jika Terjadi Kecelakaan

Jika APP_KEY sudah diganti dan data terenkripsi:

1. **Restore `.env` dengan APP_KEY lama** (dari backup atau `git diff`)
2. Jalankan `php artisan config:cache` untuk clear cache
3. Coba akses data lagi

Jika APP_KEY lama tidak ada:
- Data NIP/NIK/phone/email hilang permanent (hanya `name`, `position`, `organization` tersimpan)
- Admin harus input ulang via form employee edit

---

## 6. Implementasi Detail

### Model: app/Models/Employee.php

```php
protected $casts = [
    'nip' => 'encrypted',
    'nik' => 'encrypted',
    'phone' => 'encrypted',
    'email' => 'encrypted',
];

protected $hidden = ['nip', 'nik', 'phone', 'email'];
protected $appends = ['nip_masked', 'nik_masked', 'phone_masked', 'email_masked'];

public function getNipMaskedAttribute(): ?string
{
    return $this->maskNipNik($this->nip);
}
// ... etc
```

### Service: app/Services/Assets/EmployeeService.php

```php
public function reveal(Employee $employee, string $password): array
{
    if (! Hash::check($password, auth()->user()->password)) {
        throw new AuthorizationException('Password salah. Akses ditolak.');
    }

    return [
        'nip' => $employee->nip,       // auto-decrypt via cast
        'nik' => $employee->nik,
        'phone' => $employee->phone,
        'email' => $employee->email,
    ];
}
```

### Controller: app/Http/Controllers/Admin/Assets/EmployeeController.php

```php
public function reveal(Request $request, Employee $employee): JsonResponse
{
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Hanya admin yang dapat mengakses data sensitif.');
    }

    $request->validate(['password' => ['required', 'string']]);

    try {
        $data = $this->service->reveal($employee, $request->input('password'));
        return response()->json($data);
    } catch (AuthorizationException $e) {
        return response()->json(['message' => $e->getMessage()], 422);
    }
}
```

### Migration: database/migrations/2026_05_12_000001_encrypt_employee_sensitive_fields.php

```php
// up(): encrypt existing data
DB::table('employees')->get()->each(function ($employee) {
    DB::table('employees')->where('id', $employee->id)->update([
        'nip' => $employee->nip ? Crypt::encryptString($employee->nip) : null,
        'nik' => $employee->nik ? Crypt::encryptString($employee->nik) : null,
        'phone' => $employee->phone ? Crypt::encryptString($employee->phone) : null,
        'email' => $employee->email ? Crypt::encryptString($employee->email) : null,
    ]);
});

// down(): decrypt back
DB::table('employees')->get()->each(function ($employee) {
    DB::table('employees')->where('id', $employee->id)->update([
        'nip' => $employee->nip ? Crypt::decryptString($employee->nip) : null,
        // ... etc
    ]);
});
```

---

## Referensi

- [Laravel Encryption Docs](https://laravel.com/docs/encryption)
- [OWASP Data Protection](https://owasp.org/www-project-top-ten/)
- Migration: `database/migrations/2026_05_12_000001_encrypt_employee_sensitive_fields.php`
- Deployment: [docs/PRODUCTION.md](PRODUCTION.md#migration-case-enkripsi-data-sensitif-pegawai)
