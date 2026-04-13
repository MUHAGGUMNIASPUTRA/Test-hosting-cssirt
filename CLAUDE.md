# CLAUDE.md — CSIRT Bojonegoro

Referensi cepat proyek untuk AI. Baca ini sebelum menyentuh kode apapun.

---

## Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 11, PHP 8.2 |
| Frontend SPA | Vue 3 (Composition API `<script setup>`) + Inertia.js 2.0 |
| Build | Vite 6, SSR diaktifkan (`npm run build` → client + SSR bundle) |
| Styling | Tailwind CSS 3 + `@tailwindcss/forms` + `@tailwindcss/typography` |
| UI Library | PrimeVue 4 (Noir preset, auto-import) |
| Icons | Tabler Icons Vue (`Icon` prefix, auto-import) + Lucide (`i-lucide-*`) |
| Rich Text | Tiptap 3 |
| Auth | Laravel Breeze + Sanctum |
| Routing JS | Ziggy (`route()` tersedia global di Vue) |
| HTTP Client | Axios (via `bootstrap.js`) |

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
      Admin/          # 10 controller admin (lihat bawah)
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

| Layout | Dipakai di |
|--------|-----------|
| `AppLayout.vue` | Semua halaman publik |
| `AdminLayout.vue` | Semua halaman `/admin/*` |
| `GuestLayout.vue` | Halaman auth (login, register) |
| `SEOLayout.vue` | Halaman SEO (Pages/SEO*.vue) |

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
```

---

## Models & Relasi

```
User          role: admin|staff|user
Post          belongsToMany Category, Tag | hasMany Rating
Category      belongsToMany Post
Tag           belongsToMany Post
Incident      belongsTo IncidentType | belongsTo User (assigned_to) | hasMany IncidentLog
IncidentType  hasMany Incident
IncidentLog   belongsTo Incident, User
Announcement  (standalone)
Service       (standalone)
Faq           (standalone)
Rating        belongsTo Post, User
Document      belongsTo DocumentArea | official_file_path bisa berupa storage path (PDF) atau URL eksternal
DocumentArea  hasMany Document
```

---

## Skema Database (ringkas)

```sql
users           id, name, email, password, role(admin|staff|user)
posts           id, title, slug, image, excerpt, body, status(Draft|Published),
                views_count, published_at, published_by, rating, ratings_count
categories      id, name, slug
tags            id, name, slug
category_post   post_id, category_id
post_tag        post_id, tag_id
ratings         id, post_id, user_id, ip_address, rating(1-5)
incidents       id, case_id, reporter_name, reporter_email, reporter_phone,
                incident_type_id, description, attachment, incident_at,
                status(Baru|Diverifikasi|Dalam Penyelidikan|Selesai|Ditutup),
                priority(Rendah|Sedang|Tinggi|Kritikal),
                assigned_to(FK users), reported_at, resolved_at
incident_types  id, name, slug, description
incident_logs   id, incident_id, user_id, log_message
announcements   id, title, content, level(info|warning|critical),
                start_date, end_date, is_active
services        id, name, slug, icon, short_description, full_description, is_active
faqs            id, question, answer, category, is_published
document_areas  id, name, slug, description
documents       id, title, slug, description,
                file_path (link Word doc — hanya admin),
                official_file_path (PDF upload path ATAU URL eksternal),
                version, published_at, is_public, document_area_id(FK document_areas)
```

> **Catatan `official_file_path`**: Jika nilai diawali `http://` atau `https://`, berarti dokumen berupa tautan eksternal (view=redirect, download=ditolak). Jika tidak, berarti path file di `storage/public/` (view=inline PDF, download=tersedia).

---

## Controllers Admin

| Controller | Route prefix | Catatan |
|-----------|-------------|---------|
| `DashboardController` | `admin/` | Kirim stats + recentIncidents/Posts/Users |
| `IncidentController` | `admin/incidents` | Full CRUD + management update + log tambah |
| `PostController` | `admin/posts` | CRUD + AI excerpt via `ExcerptController` |
| `TaxonomyController` | `admin/taxonomy` | Kelola Category + Tag dalam satu halaman |
| `ServiceController` | `admin/services` | Full CRUD |
| `FaqController` | `admin/faqs` | CRUD via dialog inline (no dedicated create/edit page) |
| `AnnouncementController` | `admin/announcements` | CRUD via dialog inline |
| `UserController` | `admin/users` | CRUD, hanya bisa diakses role `admin` |
| `DocumentAreaController` | `admin/document-areas` | Full CRUD area/kategori dokumen |
| `DocumentController` (Admin) | `admin/documents` | Full CRUD + toggle-visibility; `official_file_path` bisa file upload atau link |
| `ImageUploadController` | `admin/images/upload` | Upload gambar untuk Tiptap editor |
| `ExcerptController` | `admin/generate-excerpt` | Generate excerpt artikel via AI |

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

| Tipe File | Batas |
|-----------|-------|
| PHP Controller | 150 baris |
| PHP Service | 300 baris |
| PHP Model | 200 baris |
| Vue Page (Index/Create) | 250 baris |
| Vue Component | 150 baris |
| JS Composable / Utils | 80–150 baris |

File yang melebihi batas → ekstrak ke service/komponen/composable. Detail per jenis file ada di `app/CLAUDE.md` dan `resources/js/CLAUDE.md`.

### Auto-Format via Hook

Setiap file yang ditulis/diedit Claude otomatis diformat setelah tool call selesai:
- **PHP** → Laravel Pint (`./vendor/bin/pint <file>`)
- **JS / Vue / TS / CSS / JSON** → Prettier (`npx prettier --write <file>`, menggunakan `.prettierrc.json`)

---

## Catatan Penting

- **Jangan** buat import manual untuk komponen, PrimeVue, atau Tabler Icons — semua auto-import.
- **Gambar** artikel disimpan di `storage/app/public/` dan diakses via `/storage/`.
- **SSR pages** (SEO*.vue) adalah versi terpisah untuk server-side rendering SEO, jangan modifikasi bersama halaman reguler.
- **Middleware `admin`**: hanya user dengan `role === 'admin'` yang bisa akses `admin/users`.
- **`case_id`** insiden digenerate otomatis (format: `INC-YYYYMMDD-XXXX`).
- **Pagination** di halaman admin selalu server-side via `useAdminTable` composable.
- **Priority enum** di DB adalah `Kritis` (bukan `Kritikal`) di migration awal, tapi migration `2025_07_29` mengubahnya — pastikan konsisten dengan nilai terbaru `Kritikal`.
