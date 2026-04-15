# CLAUDE.md — CSIRT Bojonegoro

Referensi cepat proyek untuk AI. Baca ini sebelum menyentuh kode apapun.

---

## Stack

| Layer        | Teknologi                                                             |
| ------------ | --------------------------------------------------------------------- |
| Backend      | Laravel 11, PHP 8.2                                                   |
| Frontend SPA | Vue 3 (Composition API `<script setup>`) + Inertia.js 2.0             |
| Build        | Vite 6, SSR diaktifkan (`npm run build` → client + SSR bundle)        |
| Styling      | Tailwind CSS 3 + `@tailwindcss/forms` + `@tailwindcss/typography`     |
| UI Library   | PrimeVue 4 (Noir preset, auto-import)                                 |
| Icons        | Tabler Icons Vue (`Icon` prefix, auto-import) + Lucide (`i-lucide-*`) |
| Rich Text    | Tiptap 3                                                              |
| Auth         | Laravel Breeze + Sanctum                                              |
| Routing JS   | Ziggy (`route()` tersedia global di Vue)                              |
| HTTP Client  | Axios (via `bootstrap.js`)                                            |

**Dev command:**

- `composer dev:herd` — untuk Windows + Laravel Herd (queue + Vite; server di-handle Herd via domain `.test`)
- `composer dev` — untuk Linux/macOS/Docker (server + queue + Pail log viewer + Vite; butuh ekstensi `pcntl`)

---

## Auto-Import (jangan tulis import manual untuk ini)

Vite dikonfigurasi via `unplugin-vue-components` untuk auto-import:

- Semua komponen di `resources/js/Components/` dan `resources/js/Layouts/`
- Semua komponen PrimeVue (misal `<DataTable>`, `<Button>`, `<Dialog>`, `<Tag>`, `<Select>`)
- Tabler Icons: `<IconNamaIcon>` (misal `<IconTrash>`, `<IconEdit>`)
- Lucide Icons: `<i-lucide-nama-icon>` (misal `<i-lucide-user-pen>`)

---

## Struktur Direktori

```
app/
  Http/
    Controllers/
      Admin/          # Controller admin (lihat bawah)
        Assets/       # Controller asset SDM & virtual
      Auth/           # Laravel Breeze auth controllers
      *.php           # Controller publik
    Middleware/
    Requests/         # Form Request validation
    Traits/
  Models/             # 12 Eloquent models
  Mail/
  Services/

resources/js/
  app.js              # Entry point (inisialisasi Inertia + PrimeVue)
  ssr.js              # SSR entry point
  Components/         # Komponen reusable (AUTO-IMPORT)
  Layouts/            # Layout wrapper (AUTO-IMPORT)
  Pages/
    Admin/            # Halaman CRUD admin
    Auth/             # Halaman autentikasi
    Posts/            # Halaman publik artikel
    Services/         # Halaman publik layanan
    *.vue             # Halaman publik lainnya
  Composables/        # Shared logic (useAdminTable, useResponsive)
  utils/              # Pure non-reactive functions (date, status, string, file)

resources/views/
  app.blade.php       # Root Inertia shell (satu-satunya blade template)
  emails/             # Template email

routes/
  web.php             # Semua route web
  auth.php            # Route autentikasi Breeze
```

---

## Komponen, Composables & Utils Frontend

Detail lengkap (props, events, pola pakai, batas file) → **[`resources/js/CLAUDE.md`](resources/js/CLAUDE.md)**

---

## PHP Enums, Services & Form Requests

Detail lengkap (enum cases, method service, konvensi request, pola controller) → **[`app/CLAUDE.md`](app/CLAUDE.md)**

---

## Layout

| Layout            | Dipakai di                     |
| ----------------- | ------------------------------ |
| `AppLayout.vue`   | Semua halaman publik           |
| `AdminLayout.vue` | Semua halaman `/admin/*`       |
| `GuestLayout.vue` | Halaman auth (login, register) |
| `SEOLayout.vue`   | Halaman SEO (Pages/SEO\*.vue)  |

---

## Routing

### Publik (`routes/web.php`)

```
GET  /                      landing
GET  /services              services.index
GET  /posts                 posts.index
GET  /posts/{slug}          posts.show
GET  /posts/categories/{slug} categories.show
POST /posts/{post}/ratings  posts.ratings.store
GET  /faq                   faq.index
GET  /contact               contact.index
POST /contact               contact.store
GET  /incident              incident.create
POST /incident              incident.store
GET  /documents             documents.index
GET  /documents/{slug}/view     documents.view      (redirect jika link, inline PDF jika file)
GET  /documents/{slug}/download documents.download  (hanya berlaku untuk file, bukan link)
```

### Admin (`/admin`, middleware: `auth`, `verified`)

```
GET    /admin               admin.dashboard
resource incidents          admin.incidents.*   (+management.update, +logs.store)
resource posts              admin.posts.*       (kecuali show)
GET    /admin/taxonomy      admin.taxonomy.index
resource services           admin.services.*
resource faqs               admin.faqs.*        (kecuali show, create, edit)
resource announcements      admin.announcements.* (kecuali show, create, edit)
resource users              admin.users.*       (kecuali show, create, edit) [middleware: admin]
resource document-areas     admin.document-areas.*
resource documents          admin.documents.*   (+toggle-visibility)
POST   /admin/images/upload admin.images.upload
POST   /admin/generate-excerpt admin.generate-excerpt

// Assets — SDM
resource organizations          admin.organizations.*         (kecuali show, create, edit)
resource departments            admin.departments.*           (kecuali show, create, edit)
resource positions              admin.positions.*             (kecuali show, create, edit)
resource locations              admin.locations.*             (kecuali show, create, edit)
resource employees              admin.employees.*             (kecuali show)
resource vendors                admin.vendors.*               (kecuali show)

// Assets — Virtual
resource tech-stack-categories  admin.tech-stack-categories.* (kecuali show, create, edit)
resource tech-stacks            admin.tech-stacks.*           (kecuali show)
resource virtual-asset-guides   admin.virtual-asset-guides.*  (kecuali show)
resource web-applications       admin.web-applications.*      (kecuali show)
resource mobile-applications    admin.mobile-applications.*   (kecuali show)
resource licenses               admin.licenses.*              (kecuali show)

// Assets — Audit Logs (embedded)
POST   /admin/assets/{assetType}/{assetId}/audit-logs  assets.audit-logs.store
PUT    /admin/assets/audit-logs/{auditLog}             assets.audit-logs.update
DELETE /admin/assets/audit-logs/{auditLog}             assets.audit-logs.destroy
```

---

## Models & Relasi

```
User          role: admin|staff|user
Post          belongsToMany Category, Tag | hasMany Rating | belongsTo Attachment (image_id)
Category      belongsToMany Post
Tag           belongsToMany Post
Incident      belongsTo IncidentType | belongsTo User (assigned_to) | hasMany IncidentLog
              | belongsTo Attachment (attachment_id)
IncidentType  hasMany Incident
IncidentLog   belongsTo Incident, User | belongsTo Attachment (attachment_id)
Attachment    type: file|link — dipakai oleh Incident, IncidentLog, Document (official), Post (image)
Announcement  (standalone)
Service       (standalone)
Faq           (standalone)
Rating        belongsTo Post, User
Document      belongsTo DocumentArea | belongsTo Attachment (official_attachment_id)
              | draft_file_path: string URL plain (Word doc, admin-only)
DocumentArea  hasMany Document
```

---

## Skema Database (ringkas)

```sql
users           id, name, email, password, role(admin|staff|user)
attachments     id, type(file|link), disk(local|public), path, url, filename,
                file_size(bytes), mime_type
posts           id, title, slug, image_id(FK attachments), excerpt, body,
                status(Draft|Published), views_count, published_at, published_by,
                rating, ratings_count
categories      id, name, slug
tags            id, name, slug
category_post   post_id, category_id
post_tag        post_id, tag_id
ratings         id, post_id, user_id, ip_address, rating(1-5)
incidents       id, case_id, reporter_name, reporter_email, reporter_phone,
                incident_type_id, description, attachment_id(FK attachments), incident_at,
                status(Baru|Diverifikasi|Dalam Penyelidikan|Selesai|Ditutup),
                priority(Rendah|Sedang|Tinggi|Kritikal),
                assigned_to(FK users), reported_at, resolved_at
incident_types  id, name, slug, description
incident_logs   id, incident_id, user_id, log_message, is_public,
                attachment_id(FK attachments)
announcements   id, title, content, level(info|warning|critical),
                start_date, end_date, is_active
services        id, name, slug, icon, short_description, full_description, is_active
faqs            id, question, answer, category, is_published
document_areas  id, name, slug, description
documents       id, title, slug, description,
                draft_file_path (URL string — link Word doc, hanya admin),
                official_attachment_id(FK attachments),
                version, published_at, is_public, document_area_id(FK document_areas)
```

> **Sistem Attachment Terpadu**: Semua attachment (file upload maupun link eksternal) disimpan di tabel `attachments`. Parent table cukup menyimpan `attachment_id` nullable. Tidak ada lagi pendeteksian tipe dari prefix `http` pada string. Satu-satunya pengecualian adalah `documents.draft_file_path` yang tetap berupa URL string biasa karena selalu link dan tidak perlu join.
>
> Disk `local` (private) dipakai khusus untuk attachment incident dari form publik — file tidak bisa diakses langsung, download via signed route 15 menit (`incident.attachment.download`). Semua attachment lain menggunakan disk `public` dan diakses via `/storage/`.

---

## Controllers Admin

| Controller                   | Route prefix             | Catatan                                                                        |
| ---------------------------- | ------------------------ | ------------------------------------------------------------------------------ |
| `DashboardController`        | `admin/`                 | Kirim stats + recentIncidents/Posts/Users                                      |
| `IncidentController`         | `admin/incidents`        | Full CRUD + management update + log tambah                                     |
| `PostController`             | `admin/posts`            | CRUD + AI excerpt via `ExcerptController`                                      |
| `TaxonomyController`         | `admin/taxonomy`         | Kelola Category + Tag dalam satu halaman                                       |
| `ServiceController`          | `admin/services`         | Full CRUD                                                                      |
| `FaqController`              | `admin/faqs`             | CRUD via dialog inline (no dedicated create/edit page)                         |
| `AnnouncementController`     | `admin/announcements`    | CRUD via dialog inline                                                         |
| `UserController`             | `admin/users`            | CRUD, hanya bisa diakses role `admin`                                          |
| `DocumentAreaController`     | `admin/document-areas`   | Full CRUD area/kategori dokumen                                                |
| `DocumentController` (Admin) | `admin/documents`        | Full CRUD + toggle-visibility; official attachment via `AttachmentService`     |
| `ImageUploadController`      | `admin/images/upload`    | Upload gambar untuk Tiptap editor                                              |
| `ExcerptController`          | `admin/generate-excerpt` | Generate excerpt artikel via AI                                                |

### Controllers Asset (`Admin/Assets/`)

| Controller                   | Route prefix                       | Catatan                                                   |
| ---------------------------- | ---------------------------------- | --------------------------------------------------------- |
| `OrganizationController`     | `admin/organizations`              | CRUD organisasi; inline dialog (no create/edit page)      |
| `DepartmentController`       | `admin/departments`                | CRUD departemen; inline dialog                            |
| `PositionController`         | `admin/positions`                  | CRUD jabatan; inline dialog                               |
| `LocationController`         | `admin/locations`                  | CRUD lokasi; inline dialog                                |
| `EmployeeController`         | `admin/employees`                  | Full CRUD pegawai (ada create/edit page, no show)         |
| `VendorController`           | `admin/vendors`                    | Full CRUD vendor (ada create/edit page, no show)          |
| `TechStackCategoryController`| `admin/tech-stack-categories`      | CRUD kategori tech stack; inline dialog                   |
| `TechStackController`        | `admin/tech-stacks`                | Full CRUD tech stack (ada create/edit page, no show)      |
| `VirtualAssetGuideController`| `admin/virtual-asset-guides`       | Full CRUD panduan aset virtual (ada create/edit page)     |
| `WebApplicationController`   | `admin/web-applications`           | Full CRUD aplikasi web (ada create/edit page, no show)    |
| `MobileApplicationController`| `admin/mobile-applications`        | Full CRUD aplikasi mobile (ada create/edit page, no show) |
| `LicenseController`          | `admin/licenses`                   | Full CRUD lisensi (ada create/edit page, no show)         |
| `AssetAuditLogController`    | `admin/assets/{type}/{id}/audit-logs` | Tambah/edit/hapus audit log; embedded di halaman detail aset |

---

## Pola Inertia

Controller mengembalikan `Inertia::render('PageName', [...data])`.

- Nama page = path dari `resources/js/Pages/` tanpa ekstensi
- Data otomatis jadi `props` di `defineProps()` Vue
- Flash messages: `session()->flash('success', '...')` → tersedia di `$page.props.flash`

**Navigasi:** Gunakan `<Link :href="route('name')">` atau `router.visit()` / `router.delete()` dari `@inertiajs/vue3`.

---

## Kualitas Kode — Batas Panjang File & Auto-Format

### Batas Panjang File (sinyal refactor, bukan aturan absolut)

| Tipe File               | Batas        |
| ----------------------- | ------------ |
| PHP Controller          | 150 baris    |
| PHP Service             | 300 baris    |
| PHP Model               | 200 baris    |
| Vue Page (Index/Create) | 250 baris    |
| Vue Component           | 150 baris    |
| JS Composable / Utils   | 80–150 baris |

File yang melebihi batas → ekstrak ke service/komponen/composable. Detail per jenis file ada di `app/CLAUDE.md` dan `resources/js/CLAUDE.md`.

### Auto-Format via Hook

Setiap file yang ditulis/diedit Claude otomatis diformat setelah tool call selesai:

- **PHP** → Laravel Pint (`./vendor/bin/pint <file>`)
- **JS / Vue / TS / CSS / JSON** → Prettier (`npx prettier --write <file>`, menggunakan `.prettierrc.json`)

---

## Catatan Penting

- **Jangan** buat import manual untuk komponen, PrimeVue, atau Tabler Icons — semua auto-import.
- **Gambar** artikel disimpan di `storage/app/public/` dan diakses via `/storage/`.
- **SSR pages** (SEO\*.vue) adalah versi terpisah untuk server-side rendering SEO, jangan modifikasi bersama halaman reguler.
- **Middleware `admin`**: hanya user dengan `role === 'admin'` yang bisa akses `admin/users`.
- **`case_id`** insiden digenerate otomatis (format: `INC-YYYYMMDD-XXXX`).
- **Pagination** di halaman admin selalu server-side via `useAdminTable` composable.
- **Priority enum** di DB adalah `Kritis` (bukan `Kritikal`) di migration awal, tapi migration `2025_07_29` mengubahnya — pastikan konsisten dengan nilai terbaru `Kritikal`.
