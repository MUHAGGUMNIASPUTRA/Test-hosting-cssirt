# resources/js/CLAUDE.md — Vue 3 + Inertia Frontend Context

Dibaca oleh Claude saat bekerja di direktori `resources/js/`. Lihat root `CLAUDE.md` untuk gambaran lengkap.

---

## Auto-Import — JANGAN tulis import manual

Semua komponen di `Components/` dan `Layouts/` auto-import via Vite.
Semua PrimeVue components auto-import (Button, DataTable, Dialog, Tag, Select, dll).
Tabler Icons: `<IconNamaIcon>` auto-import.
Lucide: `<i-lucide-nama-icon>` auto-import.

**Yang TIDAK auto-import (harus import eksplisit):**

- `import { formatDate } from '@/utils/date'` — utils functions
- `import { useAdminTable } from '@/Composables/useAdminTable'` — composables
- `import { router } from '@inertiajs/vue3'` — Inertia router
- `import { useForm } from '@inertiajs/vue3'` — Inertia form helper

---

## Pola Wajib Admin Index Page

### 1. Selalu gunakan `useAdminTable`

```js
import { useAdminTable } from '@/Composables/useAdminTable'

const searchQuery = ref(props.filters?.search || '')
const selectedRole = ref(props.filters?.role || '')
const paginatedData = computed(() => props.users)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.users.index', {
  search: searchQuery,
  role: selectedRole,
})
```

**JANGAN** buat `lazyParams`, `buildUrl`, atau `navigate` manual di page. `useAdminTable` sudah handle ini semua.

**Pengecualian:** `Documents/Index.vue` — multi-select `areas[]` menggunakan `params.append`, tidak kompatibel. Biarkan custom, tapi tetap gunakan `serverSideConfig` dari composable.

### 2. Gunakan `AdminPageHeader` untuk header

```vue
<AdminPageHeader
  title="Daftar Pengguna"
  description="Kelola akun pengguna sistem."
>
  <template #action>
    <Button @click="openCreateDialog">Tambah Pengguna</Button>
  </template>
</AdminPageHeader>
```

### 3. Gunakan `AdminFilterBar` untuk filter

```vue
<AdminFilterBar :has-active-filters="hasActiveFilters" @clear="clearFilters">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- inputs search + select dropdown di sini -->
  </div>
</AdminFilterBar>
```

### 4. Gunakan `StatCard` untuk semua stats — JANGAN buat div manual

```vue
<StatCard color="blue" label="Total Pengguna" :value="users.total || 0">
  <template #default="{ iconClass, iconSize }">
    <IconUsers :class="iconClass" :size="iconSize" />
  </template>
</StatCard>
```

### 5. Pola Delete — gunakan `DeleteConfirmDialog` (canonical)

```vue
<script setup>
const showDeleteDialog = ref(false)
const itemToDelete = ref(null)

const confirmDelete = (item) => {
  itemToDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  if (!itemToDelete.value) return
  router.delete(route('admin.xxx.destroy', itemToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      itemToDelete.value = null
    },
  })
}
</script>

<template>
  <DeleteConfirmDialog
    v-model:visible="showDeleteDialog"
    entity-label="Nama Entity"
    @confirm="handleDelete"
  >
    <template #item-info>
      <span>{{ itemToDelete?.name }}</span>
    </template>
  </DeleteConfirmDialog>
</template>
```

**JANGAN** gunakan `useConfirm()` + `<ConfirmDialog />` di halaman yang punya dedicated CRUD page.

---

## Utils (`resources/js/utils/`) — Import Eksplisit

Fungsi pure (non-reaktif) untuk operasi berulang. **Jangan taruh di Composables.**

| File              | Fungsi                                                             |
| ----------------- | ------------------------------------------------------------------ |
| `utils/date.js`   | `formatDate(date)`, `formatDatetime(date)`, `formatRelative(date)` |
| `utils/status.js` | `getSeverity(type, value)`, `getStatusLabel(type, value)`          |
| `utils/string.js` | `truncate(str, len)`, `slugify(str)`                               |
| `utils/file.js`   | `getFileExtension(path)`, `formatFileSize(bytes)`                  |

> **Catatan**: `isExternalUrl()` sudah tidak dipakai untuk deteksi tipe attachment — gunakan `attachment?.type === 'link'` dari objek `Attachment`.

**Contoh penggunaan di template:**

```js
import { formatDate } from '@/utils/date'
```

---

## Sistem Attachment — Shape Objek dari API

Semua attachment (incident, log, document, post) menggunakan shape objek yang sama dari `AttachmentResource`:

```js
// type = 'file'
{
  type: 'file',
  filename: 'laporan.pdf',
  extension: 'PDF',          // uppercase
  file_size: '1.23 MB',      // formatted string
  url: '/storage/incidents/logs/laporan.pdf'  // null jika disk 'local'
}

// type = 'link'
{
  type: 'link',
  filename: null,
  extension: null,
  file_size: null,
  url: 'https://drive.google.com/...'
}
```

**Pola konsumsi di komponen:**

```vue
<!-- Cek ada attachment -->
<div v-if="item.attachment"></div>
```

**Pre-populate form edit:**

```js
// Deteksi mode dari objek attachment (bukan string)
const detectMode = () => props.item?.attachment?.type ?? 'file'

// Pre-populate link value
const form = useForm({
  attachment_link: detectMode() === 'link' ? props.item?.attachment?.url : '',
})
```

**Catatan khusus incident dari form publik**: Attachment disimpan di disk `local` (private). URL-nya adalah signed route dengan TTL 15 menit — tetap bisa dipakai di `<a :href>` seperti biasa.

---

## Komponen Admin yang Tersedia

| Komponen              | Props                                           | Slot/Event                             |
| --------------------- | ----------------------------------------------- | -------------------------------------- |
| `AdminPageHeader`     | `title` (req), `description`                    | `#action`                              |
| `AdminFilterBar`      | `hasActiveFilters`, `title`                     | `#default`, `@clear`                   |
| `AdminDataTable`      | `value`, `serverConfig`                         | `default` (columns), `#empty`, `@page` |
| `StatCard`            | `label`, `value`, `color`, `layout`, `subtext`  | `#default { iconClass, iconSize }`     |
| `StatusBadge`         | `type`, `value`                                 | —                                      |
| `DeleteConfirmDialog` | `v-model:visible`, `entityLabel`, `deleteLabel` | `#item-info`, `@confirm`               |
| `RichTextEditor`      | `v-model`                                       | —                                      |
| `PostImage`           | `src`, `alt`                                    | —                                      |
| `DocumentPickerSection` | `v-model`, `label` (opt)                       | —                                      |
| `DocumentMainFormSection` | `form`                                        | —                                      |
| `DocumentFilesFormSection` | `form`                                        | —                                      |
| `DocumentClassificationFormSection` | `form`                                        | —                                      |

---

## Composables

| File                                  | Return                                                                           |
| ------------------------------------- | -------------------------------------------------------------------------------- |
| `useAdminTable(data, route, filters)` | `serverSideConfig`, `applyFilters`, `onPage`, `clearFilters`, `hasActiveFilters` |
| `useResponsive()`                     | `isMobile`, `dtConfig()`                                                         |
| `useParticles()`                      | `initParticles`                                                                  |

---

## Batas Panjang File — Wajib Dipatuhi

File Vue/JS yang melebihi batas baris berikut adalah sinyal bahwa file tersebut **perlu di-refactor**:

| Tipe File                             | Batas         | Aksi jika melebihi                         |
| ------------------------------------- | ------------- | ------------------------------------------ |
| Vue Page (`Pages/Admin/*/Index.vue`)  | **250 baris** | Ekstrak section ke komponen terpisah       |
| Vue Page (`Pages/Admin/*/Create.vue`) | **250 baris** | Pecah form menjadi komponen form terpisah  |
| Vue Component (`Components/`)         | **150 baris** | Pecah menjadi sub-komponen atau composable |
| Composable (`Composables/`)           | **150 baris** | Pecah ke composable yang lebih kecil       |
| Utils (`utils/`)                      | **80 baris**  | Pecah ke file utils yang lebih spesifik    |

> Hitung baris template + script + style secara keseluruhan di SFC. File yang mendekati batas bukan berarti harus langsung refactor — gunakan sebagai **sinyal awal**.

---

## Auto-Format — Prettier

Setiap file JS/Vue/TS/CSS/JSON yang ditulis/diedit Claude **otomatis diformat** oleh Prettier via hook PostToolUse.

Konfigurasi aktif: [`.prettierrc.json`](../../.prettierrc.json) (singleQuote, semi: false, tabWidth: 2, tailwindcss plugin).
File yang diabaikan: [`.prettierignore`](../../.prettierignore) (vendor, node_modules, public, storage, bootstrap).

Jalankan manual jika perlu:

```bash
npm run format                          # format semua file
npx prettier --write resources/js/      # format folder JS saja
npx prettier --write resources/js/Pages/Admin/Posts/Index.vue  # satu file
```

---

## Layout

| Layout            | Dipakai di                     |
| ----------------- | ------------------------------ |
| `AppLayout.vue`   | Semua halaman publik           |
| `AdminLayout.vue` | Semua halaman `/admin/*`       |
| `GuestLayout.vue` | Halaman auth (login, register) |
| `SEOLayout.vue`   | Halaman SEO (Pages/SEO\*.vue)  |

---

## Pola Inertia

Controller mengembalikan `Inertia::render('PageName', [...data])`.

- Nama page = path dari `resources/js/Pages/` tanpa ekstensi
- Data otomatis jadi `props` di `defineProps()` Vue
- Flash messages: `session()->flash('success', '...')` → tersedia di `$page.props.flash`

**Navigasi:** Gunakan `<Link :href="route('name')">` atau `router.visit()` / `router.delete()` dari `@inertiajs/vue3`.

---

## Header Doc — Wajib di File Baru/Diubah

Setiap file Vue/JS yang dibuat atau diubah logic utamanya wajib punya header singkat:

```vue
<!-- Tujuan: [1 kalimat] -->
<!-- Caller: [page/component induk] -->
<!-- Side Effects: [Inertia visit / axios call / emit — atau "none"] -->
```

---

## Aturan Penting

- **Jangan** tulis import manual untuk komponen, PrimeVue, atau Tabler Icons
- **Jangan** taruh logika format/transformasi inline di template — gunakan `utils/`
- **Jangan** roll ulang filter/pagination manual di Index page — gunakan `useAdminTable`
- Fungsi reaktif (pakai `ref`, `computed`, `watch`) → `Composables/`
- Fungsi non-reaktif, pure → `utils/`
- Test utils: `npm run test:js` (Vitest)
- Build check: `npm run build`
- **Documents Show page**: gunakan `Pages/Admin/Documents/Show.vue` untuk detail dokumen dengan nested relasi `officialAttachment` dan `documentArea`
- **VirtualAssetGuides document picker**: gunakan `DocumentPickerSection` component untuk select multiple documents dengan drag-reorder. Endpoint API: `/api/admin/documents` dan `/api/admin/virtual-asset-guides`
