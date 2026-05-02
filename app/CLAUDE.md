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
| `DocumentStage`     | Form Requests, DocumentController (create/edit)    |

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

## Controllers Admin

| Controller                   | Route prefix             | Catatan                                                                    |
| ---------------------------- | ------------------------ | -------------------------------------------------------------------------- |
| `DashboardController`        | `admin/`                 | Kirim stats + recentIncidents/Posts/Users                                  |
| `IncidentController`         | `admin/incidents`        | Full CRUD + management update + log tambah                                 |
| `PostController`             | `admin/posts`            | CRUD + AI excerpt via `ExcerptController`                                  |
| `TaxonomyController`         | `admin/taxonomy`         | Kelola Category + Tag dalam satu halaman                                   |
| `ServiceController`          | `admin/services`         | Full CRUD                                                                  |
| `FaqController`              | `admin/faqs`             | CRUD via dialog inline (no dedicated create/edit page)                     |
| `AnnouncementController`     | `admin/announcements`    | CRUD via dialog inline                                                     |
| `UserController`             | `admin/users`            | CRUD, hanya bisa diakses role `admin`                                      |
| `DocumentAreaController`     | `admin/document-areas`   | Full CRUD area/kategori dokumen                                            |
| `DocumentController` (Admin) | `admin/documents`        | Full CRUD + show + toggle-visibility; stage tracking + official attachment |
| `ImageUploadController`      | `admin/images/upload`    | Upload gambar untuk Tiptap editor                                          |
| `ExcerptController`          | `admin/generate-excerpt` | Generate excerpt artikel via AI                                            |

### Controllers Asset (`Admin/Assets/`)

| Controller                    | Route prefix                              | Catatan                                                                |
| ----------------------------- | ----------------------------------------- | ---------------------------------------------------------------------- |
| `OrganizationController`      | `admin/organizations`                     | CRUD organisasi; inline dialog (no create/edit page)                   |
| `DepartmentController`        | `admin/departments`                       | CRUD departemen; inline dialog                                         |
| `PositionController`          | `admin/positions`                         | CRUD jabatan; inline dialog                                            |
| `LocationController`          | `admin/locations`                         | CRUD lokasi; inline dialog                                             |
| `EmployeeController`          | `admin/employees`                         | Full CRUD pegawai (ada create/edit page, no show)                      |
| `VendorController`            | `admin/vendors`                           | Full CRUD vendor (ada create/edit page, no show)                       |
| `TechStackCategoryController` | `admin/tech-stack-categories`             | CRUD kategori tech stack; inline dialog                                |
| `TechStackController`         | `admin/tech-stacks`                       | Full CRUD tech stack (ada create/edit page, no show)                   |
| `VirtualAssetGuideController` | `admin/virtual-asset-guides`              | Full CRUD panduan aset virtual; link ke Documents via pivot table      |
| `WebApplicationController`    | `admin/web-applications`                  | Full CRUD aplikasi web (ada create/edit page, no show)                 |
| `MobileApplicationController` | `admin/mobile-applications`               | Full CRUD aplikasi mobile (ada create/edit page, no show)              |
| `LicenseController`           | `admin/licenses`                          | Full CRUD lisensi (ada create/edit/show page)                          |
| `AssetAuditLogController`     | `admin/assets/{type}/{id}/audit-logs`     | Tambah/edit/hapus audit log; embedded di halaman detail aset           |
| `AssetSecurityNoteController` | `admin/assets/{type}/{id}/security-notes` | Store/update/destroy catatan keamanan; embedded di halaman detail aset |

---

## Query Standards

- Selalu eager load relasi yang dipakai di view: `with(['relation'])` — hindari N+1
- Gunakan `select([...])` jika hanya butuh sebagian kolom dari tabel besar
- Filter wajib pakai indexed column (`id`, `slug`, `status`, `created_at`)
- Hindari `->get()` tanpa limit pada tabel yang bisa tumbuh — gunakan `->paginate()`

---

## Aturan Penting Lainnya

- `case_id` insiden digenerate otomatis — jangan set manual
- Query filter gunakan `when()` + `where('column', 'ilike', "%{$search}%")` untuk PostgreSQL case-insensitive
- Flash messages: `->with('success', '...')` atau `->with('error', '...')` — tersedia di `$page.props.flash` di Vue
- Test: `php artisan test --filter Unit` untuk unit test, `php artisan test` untuk semua
