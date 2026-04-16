# app/CLAUDE.md — Laravel Backend Context

Dibaca oleh Claude saat bekerja di direktori `app/`. Lihat root `CLAUDE.md` untuk gambaran lengkap proyek.

---

## Pola Validasi — WAJIB Form Request

Semua validasi admin menggunakan Form Request di `app/Http/Requests/Admin/`. **JANGAN** tulis `$request->validate()` inline di controller.

**Konvensi nama:**

- Store berbeda dari update → `StoreXxxRequest` + `UpdateXxxRequest`
- Store sama dengan update → satu class `SaveXxxRequest`
- Update extend store bila rules identik: `class UpdateXxxRequest extends StoreXxxRequest {}`

**Setiap Form Request wajib punya:**

```php
public function authorize(): bool { return true; } // middleware sudah handle auth
public function rules(): array { ... }             // gunakan Rule::enum() untuk enum fields
public function messages(): array { ... }          // hanya jika ada pesan kustom
```

**Struktur folder Requests:**

```
app/Http/Requests/Admin/
  Announcement/SaveAnnouncementRequest.php
  DocumentArea/SaveDocumentAreaRequest.php
  Document/StoreDocumentRequest.php
  Document/UpdateDocumentRequest.php
  Faq/SaveFaqRequest.php
  Incident/StoreIncidentRequest.php
  Incident/UpdateIncidentRequest.php
  Incident/UpdateManagementRequest.php
  Incident/AddLogRequest.php
  IncidentType/SaveIncidentTypeRequest.php
  Post/StorePostRequest.php
  Post/UpdatePostRequest.php
  Service/SaveServiceRequest.php
  User/StoreUserRequest.php
  User/UpdateUserRequest.php
```

---

## Enums — Gunakan selalu, bukan string literal

Lokasi: `app/Enums/`. Semua adalah backed string enum.

| Enum                | Dipakai di                                         |
| ------------------- | -------------------------------------------------- |
| `IncidentStatus`    | Form Requests, IncidentService, IncidentController |
| `IncidentPriority`  | Form Requests, IncidentService                     |
| `UserRole`          | Form Requests, UserController                      |
| `PostStatus`        | Form Requests, PostService                         |
| `AnnouncementLevel` | Form Requests, AnnouncementController              |
| `AttachmentType`    | AttachmentService, Attachment model                |

**Validasi dengan Enum:**

```php
use Illuminate\Validation\Rule;
use App\Enums\IncidentStatus;

'status' => ['required', Rule::enum(IncidentStatus::class)],
```

**Di service/controller:**

```php
IncidentStatus::from($value)          // throw jika invalid
IncidentStatus::tryFrom($value)       // null jika invalid
IncidentStatus::values()              // ['Baru', 'Diverifikasi', ...]
```

---

## Service Layer

Lokasi: `app/Services/`. Inject via constructor di controller.

| Service             | Tanggung Jawab                                                 | Return Types                                      |
| ------------------- | -------------------------------------------------------------- | ------------------------------------------------- |
| `AttachmentService` | `storeFile()`, `storeLink()`, `resolve()`, `delete()`          | `Attachment`, `Attachment`, `?Attachment`, `void` |
| `IncidentService`   | `create()`, `update()`, `logChanges()`, `getGlobalStats()`     | `Incident`, `void`, `void`, `array{...}`          |
| `DocumentService`   | `create()`, `update()`, `getDocumentStatus()`                  | `Document`, `void`, `string`                      |
| `PostService`       | `create()`, `update()`, `deleteWithAssets()`, `syncTaxonomy()` | `Post`, `void`, `void`, `void`                    |
| `FaqCacheService`   | Cache publik FAQ (static methods)                              | `Collection`, `array`, `void`                     |
| `SeoService`        | SSR SEO rendering                                              | `string`, `bool`, `?string`                       |

**`AttachmentService` — Pola Penggunaan:**

```php
// Resolve dari form (buat baru / pertahankan existing / hapus)
$attachment = $this->attachmentService->resolve(
    file: $request->file('attachment'),      // UploadedFile|null
    type: $validated['attachment_type'],     // 'file'|'link'|null
    linkValue: $validated['attachment_link'] ?? null,
    existing: $model->attachment,            // Attachment|null (lama)
    disk: 'public',                          // 'local' untuk incident publik
    directory: 'incidents/logs',
);
$model->update(['attachment_id' => $attachment?->id]);

// Hapus attachment beserta file dari storage
$this->attachmentService->delete($model->attachment);
```

**Disk per Konteks:**

| Konteks                | Disk     | Directory             |
| ---------------------- | -------- | --------------------- |
| Incident (form publik) | `local`  | `incidents/`          |
| Incident (admin)       | `public` | `attachments/`        |
| IncidentLog            | `public` | `incidents/logs/`     |
| Document official      | `public` | `documents/official/` |
| Post image             | `public` | `posts/`              |

**Aturan penting untuk service:**

- Jangan gunakan `Auth::id()` atau `auth()` di service — terima `int $actorId` sebagai parameter
- Jangan akses `request()` helper di service — terima data sebagai parameter
- **Jangan** hapus file storage manual — gunakan `AttachmentService::delete()` yang handle file + record DB
- Disk `local` (private) khusus untuk attachment incident dari form publik — download via signed route

---

## Controller Pattern — Thin Controller

Controller hanya boleh:

1. Terima request (Form Request sudah handle validasi)
2. Panggil service
3. Return Inertia response atau redirect

**Return types wajib di setiap method:**

```php
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

public function index(Request $request): Response { ... }
public function store(StoreXxxRequest $request): RedirectResponse { ... }
public function update(UpdateXxxRequest $request, Model $model): RedirectResponse { ... }
public function destroy(Model $model): RedirectResponse { ... }
```

**Contoh thin controller:**

```php
public function store(StoreIncidentRequest $request): RedirectResponse
{
    $this->incidentService->create(
        $request->validated(),
        $request->file('attachment'),
        Auth::id()
    );
    return redirect()->route('admin.incidents.index')
        ->with('success', 'Insiden berhasil dilaporkan.');
}
```

---

## Batas Panjang File — Wajib Dipatuhi

File PHP yang melebihi batas baris berikut adalah sinyal bahwa file tersebut **perlu di-refactor**:

| Tipe File                            | Batas         | Aksi jika melebihi                             |
| ------------------------------------ | ------------- | ---------------------------------------------- |
| Controller (`app/Http/Controllers/`) | **150 baris** | Ekstrak logika ke Service                      |
| Service (`app/Services/`)            | **300 baris** | Pecah menjadi beberapa Service atau trait      |
| Model (`app/Models/`)                | **200 baris** | Pindahkan logika ke Service atau Model concern |
| Form Request (`app/Http/Requests/`)  | **80 baris**  | Pecah rules() ke rule objects terpisah         |
| Lainnya (Enum, Mail, dll.)           | **100 baris** | Evaluasi per kasus                             |

**Cara cek baris di terminal:**

```bash
wc -l app/Http/Controllers/Admin/IncidentController.php
```

> File yang mendekati batas bukan berarti langsung refactor — nilai lebih panjang lebih jelas daripada nilai lebih pendek tapi abstraksi berlebihan. Gunakan jumlah baris sebagai **sinyal awal**, bukan aturan absolut.

---

## Auto-Format — Laravel Pint

Setiap file PHP yang ditulis/diedit Claude **otomatis diformat** oleh Laravel Pint via hook PostToolUse.

Jalankan manual jika perlu:

```bash
./vendor/bin/pint app/          # format semua file di app/
./vendor/bin/pint app/Http/Controllers/Admin/PostController.php  # satu file
```

---

## Aturan Penting Lainnya

- `case_id` insiden digenerate otomatis — jangan set manual
- Query filter gunakan `when()` + `where('column', 'ilike', "%{$search}%")` untuk PostgreSQL case-insensitive
- Flash messages: `->with('success', '...')` atau `->with('error', '...')` — tersedia di `$page.props.flash` di Vue
- Test: `php artisan test --filter Unit` untuk unit test, `php artisan test` untuk semua
