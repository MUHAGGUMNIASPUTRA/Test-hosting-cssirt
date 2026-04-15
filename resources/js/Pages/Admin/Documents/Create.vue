<script setup>
import { useResponsive } from '@/Composables/useResponsive'
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  document: {
    type: Object,
    default: null,
  },
  documentAreas: {
    type: Array,
    default: () => [],
  },
  stageOptions: {
    type: Array,
    default: () => [],
  },
})

const { isMobile } = useResponsive()
const isEditMode = computed(() => !!props.document)
const isStageFinal = computed(() => form.stage === 'Final')

// Deteksi mode file existing untuk File Dokumen Sah
const detectOfficialFileMode = () => {
  return props.document?.officialAttachment?.type ?? 'file'
}

const officialFileMode = ref(detectOfficialFileMode())

const form = useForm({
  title: props.document?.title || '',
  description: props.document?.description || '',
  version: props.document?.version || '',
  published_at: props.document?.published_at
    ? new Date(props.document.published_at)
    : null,
  is_public: props.document?.is_public ?? false,
  document_area_id: props.document?.document_area_id ?? null,
  // File Dokumen (Word — link saja, hanya admin)
  doc_file_link: props.document?.draft_file_path || '',
  // File Dokumen Sah (PDF — upload atau link, wajib)
  official_file_type: detectOfficialFileMode(),
  official_file: null,
  official_file_link:
    (detectOfficialFileMode() === 'link'
      ? props.document?.officialAttachment?.url
      : '') || '',
  // Kolom baru
  reference_number: props.document?.reference_number || '',
  stage: props.document?.stage || null,
})

// Uploader ref untuk File Dokumen Sah
const officialUploader = ref(null)
const officialFileName = ref(null)

const setOfficialFileMode = (mode) => {
  officialFileMode.value = mode
  form.official_file_type = mode
  form.official_file = null
  form.official_file_link = ''
  officialFileName.value = null
  if (officialUploader.value) officialUploader.value.clear()
}

const handleOfficialFileSelect = (event) => {
  const file = event.files[0]
  form.official_file = file
  officialFileName.value = file?.name || null
}

const clearOfficialFile = () => {
  if (officialUploader.value) officialUploader.value.clear()
  form.official_file = null
  officialFileName.value = null
}

const submit = () => {
  if (isEditMode.value) {
    form
      .transform((data) => ({ ...data, _method: 'PUT' }))
      .post(route('admin.documents.update', props.document.id), {
        forceFormData: true,
      })
  } else {
    form.post(route('admin.documents.store'), {
      forceFormData: true,
    })
  }
}
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen'">
    <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
              {{ isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen Baru' }}
            </h2>
            <p class="text-slate-600">
              {{
                isEditMode
                  ? 'Perbarui informasi dokumen'
                  : 'Tambahkan dokumen panduan baru'
              }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <Link
              :href="route('admin.documents.index')"
              class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-100 px-4 py-2 text-slate-600 transition hover:bg-slate-200"
            >
              <IconArrowLeft size="16" />
              Kembali
            </Link>
            <button
              v-if="!isMobile"
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
            >
              <IconLoader3
                v-if="form.processing"
                class="animate-spin"
                size="16"
              />
              <IconDeviceFloppy v-else size="16" />
              {{
                form.processing
                  ? 'Menyimpan...'
                  : isEditMode
                    ? 'Update'
                    : 'Simpan'
              }}
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="space-y-4 sm:space-y-6 lg:col-span-2">
          <!-- Info Card -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <div class="mb-5 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 sm:h-12 sm:w-12"
              >
                <IconFileDescription
                  class="text-blue-600"
                  :size="isMobile ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Informasi Dokumen</h3>
                <p class="text-xs text-slate-600 sm:text-base">
                  Judul dan deskripsi dokumen
                </p>
              </div>
            </div>

            <div class="space-y-4">
              <!-- Title -->
              <div>
                <label class="mb-2 block font-medium text-gray-700">
                  Judul Dokumen <span class="text-red-500">*</span>
                </label>
                <InputText
                  v-model="form.title"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.title }"
                  placeholder="Masukkan judul dokumen..."
                  required
                />
                <small v-if="form.errors.title" class="p-error mt-1 block">{{
                  form.errors.title
                }}</small>
              </div>

              <!-- Description -->
              <div>
                <label class="mb-2 block font-medium text-gray-700">
                  Deskripsi
                  <span class="font-normal text-slate-400">(Opsional)</span>
                </label>
                <Textarea
                  v-model="form.description"
                  rows="3"
                  class="w-full"
                  placeholder="Jelaskan isi atau tujuan dokumen ini..."
                />
                <small
                  v-if="form.errors.description"
                  class="p-error mt-1 block"
                  >{{ form.errors.description }}</small
                >
              </div>
            </div>
          </div>

          <!-- File Dokumen (Word — link saja, admin only) -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <div class="mb-5 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 sm:h-12 sm:w-12"
              >
                <IconFileWord
                  class="text-amber-600"
                  :size="isMobile ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">File Dokumen</h3>
                <p class="text-xs text-slate-600 sm:text-base">
                  Link dokumen Word — hanya terlihat oleh admin
                </p>
              </div>
              <div class="ml-auto">
                <Tag value="Admin Only" severity="warning" size="small" />
              </div>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Link Dokumen Word
                <span class="font-normal text-slate-400">(Opsional)</span>
              </label>
              <div class="flex gap-2">
                <InputText
                  v-model="form.doc_file_link"
                  class="w-full flex-1"
                  :class="{ 'p-invalid': form.errors.doc_file_link }"
                  placeholder="https://docs.google.com/document/d/... atau URL lainnya"
                />
                <a
                  v-if="form.doc_file_link"
                  :href="form.doc_file_link"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex flex-shrink-0 items-center gap-1 rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
                >
                  <IconExternalLink size="15" />
                </a>
              </div>
              <p class="text-xs text-slate-500">
                <IconInfoCircle size="12" class="mr-1 inline" />
                Link ini hanya dapat diakses oleh admin, tidak ditampilkan ke
                publik.
              </p>
              <small v-if="form.errors.doc_file_link" class="p-error block">{{
                form.errors.doc_file_link
              }}</small>
            </div>
          </div>

          <!-- File Dokumen Sah (PDF — upload atau link, wajib) -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <div class="mb-5 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-green-200 bg-green-50 sm:h-12 sm:w-12"
              >
                <IconFileCertificate
                  class="text-green-600"
                  :size="isMobile ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">File Dokumen Sah</h3>
                <p class="text-xs text-slate-600 sm:text-base">
                  File PDF resmi
                </p>
              </div>
            </div>

            <!-- Mode Toggle -->
            <div
              class="mb-4 flex w-fit overflow-hidden rounded-lg border border-slate-300"
            >
              <button
                type="button"
                @click="setOfficialFileMode('file')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors"
                :class="
                  officialFileMode === 'file'
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-slate-600 hover:bg-slate-50'
                "
              >
                <IconUpload size="14" />
                Upload PDF
              </button>
              <button
                type="button"
                @click="setOfficialFileMode('link')"
                class="flex items-center gap-2 border-l border-slate-300 px-4 py-2 text-sm font-medium transition-colors"
                :class="
                  officialFileMode === 'link'
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-slate-600 hover:bg-slate-50'
                "
              >
                <IconLink size="14" />
                Kirim Link
              </button>
            </div>

            <!-- Existing file info (edit mode) -->
            <div
              v-if="
                isEditMode &&
                document.officialAttachment &&
                !form.official_file &&
                officialFileMode === 'file'
              "
              class="mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3"
            >
              <IconFileCertificate
                size="16"
                class="flex-shrink-0 text-slate-500"
              />
              <div class="min-w-0 flex-1">
                <p class="text-sm text-slate-600">File saat ini:</p>
                <p class="truncate text-sm font-medium text-blue-600">
                  {{ document.officialAttachment.filename }}
                </p>
              </div>
              <p class="flex-shrink-0 text-xs text-slate-400">
                Upload baru untuk mengganti
              </p>
            </div>

            <!-- File Upload -->
            <div v-if="officialFileMode === 'file'">
              <FileUpload
                ref="officialUploader"
                name="official_file"
                @select="handleOfficialFileSelect"
                :showUploadButton="false"
                :showCancelButton="false"
                :multiple="false"
                accept=".pdf"
                :maxFileSize="52428800"
                :class="{
                  'rounded-lg border border-red-400': form.errors.official_file,
                }"
              >
                <template #content="{ files }">
                  <div v-if="files[0]" class="bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <div
                          class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-white shadow-sm"
                        >
                          <IconFileCertificate
                            size="20"
                            class="text-green-500"
                          />
                        </div>
                        <div>
                          <p class="text-sm font-medium text-slate-900">
                            {{ files[0].name }}
                          </p>
                          <p class="text-xs text-slate-500">
                            {{ (files[0].size / 1024 / 1024).toFixed(2) }} MB
                          </p>
                        </div>
                      </div>
                      <button
                        type="button"
                        @click="clearOfficialFile"
                        class="flex h-7 w-7 items-center justify-center rounded-md bg-red-100 transition-colors hover:bg-red-200"
                      >
                        <IconX size="14" class="text-red-600" />
                      </button>
                    </div>
                  </div>
                </template>
                <template #empty>
                  <div
                    class="rounded-lg border-2 border-dashed border-slate-300 p-8 text-center transition-colors hover:border-green-400"
                  >
                    <IconCloudUpload
                      size="36"
                      class="mx-auto mb-3 text-slate-400"
                    />
                    <p class="mb-1 font-medium text-slate-600">
                      Seret file PDF atau klik untuk memilih
                    </p>
                    <p class="text-sm text-slate-400">PDF saja (Maks. 50MB)</p>
                  </div>
                </template>
              </FileUpload>
              <small
                v-if="
                  isStageFinal &&
                  !form.official_file &&
                  !(isEditMode && document.officialAttachment?.type === 'file')
                "
                class="mt-1 block text-amber-600"
              >
                File PDF Dokumen Sah wajib diupload untuk stage Final.
              </small>
              <small
                v-if="form.errors.official_file"
                class="p-error mt-1 block"
                >{{ form.errors.official_file }}</small
              >
            </div>

            <!-- Link Input -->
            <div v-else class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Link PDF
                <span v-if="isStageFinal" class="text-red-500">*</span>
                <span v-else class="font-normal text-slate-400"
                  >(Opsional)</span
                >
              </label>
              <InputText
                v-model="form.official_file_link"
                class="w-full"
                :class="{ 'p-invalid': form.errors.official_file_link }"
                placeholder="https://example.com/dokumen-sah.pdf"
                :required="isStageFinal"
              />
              <p class="text-sm text-slate-500">
                <IconInfoCircle size="13" class="mr-1 inline" />
                Pastikan link mengarah ke file PDF yang dapat diakses.
              </p>
              <small
                v-if="isStageFinal && !form.official_file_link"
                class="block text-amber-600"
              >
                File Dokumen Sah wajib diisi untuk stage Final.
              </small>
              <small
                v-if="form.errors.official_file_link"
                class="p-error block"
                >{{ form.errors.official_file_link }}</small
              >
            </div>

            <!-- Nomor Referensi -->
            <div class="mt-4 space-y-2 border-t border-slate-100 pt-4">
              <label class="block text-sm font-medium text-gray-700">
                Nomor Referensi
                <span v-if="isStageFinal" class="text-red-500">*</span>
                <span v-else class="font-normal text-slate-400"
                  >(Opsional)</span
                >
              </label>
              <InputText
                v-model="form.reference_number"
                class="w-full"
                :class="{ 'p-invalid': form.errors.reference_number }"
                placeholder="Contoh: No. 001/CSIRT/BPN/2024"
                :required="isStageFinal"
              />
              <small
                v-if="isStageFinal && !form.reference_number"
                class="block text-amber-600"
              >
                Nomor Referensi wajib diisi untuk stage Final.
              </small>
              <small
                v-if="form.errors.reference_number"
                class="p-error block"
                >{{ form.errors.reference_number }}</small
              >
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
          <!-- Publikasi -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <div class="mb-5 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-purple-200 bg-purple-50 sm:h-12 sm:w-12"
              >
                <IconCalendar
                  class="text-purple-600"
                  :size="isMobile ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Info Publikasi</h3>
                <p class="text-xs text-slate-600 sm:text-base">
                  Versi dan tanggal terbit
                </p>
              </div>
            </div>

            <div class="space-y-4">
              <!-- Version -->
              <div>
                <label class="mb-2 block font-medium text-gray-700">
                  Versi
                  <span class="font-normal text-slate-400">(Opsional)</span>
                </label>
                <InputText
                  v-model="form.version"
                  class="w-full"
                  placeholder="Contoh: 1.0, 2.1, v3..."
                />
                <small v-if="form.errors.version" class="p-error mt-1 block">{{
                  form.errors.version
                }}</small>
              </div>

              <!-- Published At -->
              <div>
                <label class="mb-2 block font-medium text-gray-700">
                  Tanggal Terbit
                  <span class="font-normal text-slate-400">(Opsional)</span>
                </label>
                <DatePicker
                  v-model="form.published_at"
                  dateFormat="dd/mm/yy"
                  placeholder="Pilih tanggal"
                  class="w-full"
                  showButtonBar
                />
                <small
                  v-if="form.errors.published_at"
                  class="p-error mt-1 block"
                  >{{ form.errors.published_at }}</small
                >
              </div>
            </div>
          </div>

          <!-- Area Dokumen -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <div class="mb-5 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 sm:h-12 sm:w-12"
              >
                <IconFolders
                  class="text-indigo-600"
                  :size="isMobile ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Area Dokumen</h3>
                <p class="text-xs text-slate-600 sm:text-base">
                  Kategori pengelompokan
                </p>
              </div>
            </div>

            <div>
              <label class="mb-2 block font-medium text-gray-700">
                Pilih Area
                <span class="font-normal text-slate-400">(Opsional)</span>
              </label>
              <Select
                v-model="form.document_area_id"
                :options="documentAreas"
                optionLabel="name"
                optionValue="id"
                placeholder="— Tidak Ada Area —"
                class="w-full"
                showClear
              />
              <small
                v-if="form.errors.document_area_id"
                class="p-error mt-1 block"
                >{{ form.errors.document_area_id }}</small
              >
            </div>
          </div>

          <!-- Stage -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <div class="mb-5 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-200 bg-orange-50 sm:h-12 sm:w-12"
              >
                <IconCircleCheck
                  class="text-orange-600"
                  :size="isMobile ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Stage</h3>
                <p class="text-xs text-slate-600 sm:text-base">
                  Tahap pengerjaan dokumen
                </p>
              </div>
            </div>

            <div>
              <label class="mb-2 block font-medium text-gray-700">
                Pilih Stage
                <span class="font-normal text-slate-400">(Opsional)</span>
              </label>
              <Select
                v-model="form.stage"
                :options="stageOptions"
                placeholder="— Belum Ditentukan —"
                class="w-full"
                showClear
              />
              <small v-if="form.errors.stage" class="p-error mt-1 block">{{
                form.errors.stage
              }}</small>
            </div>
          </div>

          <!-- Visibilitas -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <div class="mb-5 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-teal-200 bg-teal-50 sm:h-12 sm:w-12"
              >
                <IconEye
                  class="text-teal-600"
                  :size="isMobile ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Visibilitas</h3>
                <p class="text-xs text-slate-600 sm:text-base">
                  Tampilkan ke publik
                </p>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-700">Publik</p>
                <p class="mt-0.5 text-xs text-slate-500">
                  {{
                    form.is_public
                      ? 'Ditampilkan di halaman publik'
                      : 'Hanya terlihat admin'
                  }}
                </p>
              </div>
              <ToggleSwitch v-model="form.is_public" />
            </div>
            <small v-if="form.errors.is_public" class="p-error mt-1 block">{{
              form.errors.is_public
            }}</small>
          </div>

          <!-- Mobile Submit -->
          <div class="block sm:hidden">
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
            >
              <IconLoader3
                v-if="form.processing"
                class="animate-spin"
                size="16"
              />
              <IconDeviceFloppy v-else size="16" />
              {{
                form.processing
                  ? 'Menyimpan...'
                  : isEditMode
                    ? 'Update'
                    : 'Simpan'
              }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>

<style>
.p-fileupload-header {
  display: none !important;
}

.p-fileupload-content {
  padding: 0 !important;
}
</style>
