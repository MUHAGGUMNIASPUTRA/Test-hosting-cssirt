<script setup>
import { useResponsive } from '@/Composables/useResponsive';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  document: {
    type: Object,
    default: null,
  },
  documentAreas: {
    type: Array,
    default: () => [],
  },
});

const { isMobile } = useResponsive();
const isEditMode = computed(() => !!props.document);

// Deteksi mode file existing untuk File Dokumen Sah
const detectOfficialFileMode = () => {
  if (!props.document?.official_file_path) return 'file';
  const val = props.document.official_file_path;
  return val.startsWith('http://') || val.startsWith('https://') ? 'link' : 'file';
};

const officialFileMode = ref(detectOfficialFileMode());

const form = useForm({
  title: props.document?.title || '',
  description: props.document?.description || '',
  version: props.document?.version || '',
  published_at: props.document?.published_at ? new Date(props.document.published_at) : null,
  is_public: props.document?.is_public ?? false,
  document_area_id: props.document?.document_area_id ?? null,
  // File Dokumen (Word — link saja, hanya admin)
  doc_file_link: props.document?.file_path || '',
  // File Dokumen Sah (PDF — upload atau link, wajib)
  official_file_type: detectOfficialFileMode(),
  official_file: null,
  official_file_link: (detectOfficialFileMode() === 'link' ? props.document?.official_file_path : '') || '',
});

// Uploader ref untuk File Dokumen Sah
const officialUploader = ref(null);
const officialFileName = ref(null);

const setOfficialFileMode = (mode) => {
  officialFileMode.value = mode;
  form.official_file_type = mode;
  form.official_file = null;
  form.official_file_link = '';
  officialFileName.value = null;
  if (officialUploader.value) officialUploader.value.clear();
};

const handleOfficialFileSelect = (event) => {
  const file = event.files[0];
  form.official_file = file;
  officialFileName.value = file?.name || null;
};

const clearOfficialFile = () => {
  if (officialUploader.value) officialUploader.value.clear();
  form.official_file = null;
  officialFileName.value = null;
};

const submit = () => {
  if (isEditMode.value) {
    form.put(route('admin.documents.update', props.document.id), {
      forceFormData: true,
    });
  } else {
    form.post(route('admin.documents.store'), {
      forceFormData: true,
    });
  }
};
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen'">
    <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
              {{ isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen Baru' }}
            </h2>
            <p class="text-slate-600">
              {{ isEditMode ? 'Perbarui informasi dokumen' : 'Tambahkan dokumen panduan baru' }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <Link :href="route('admin.documents.index')"
              class="bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition">
              <IconArrowLeft size="16" />
              Kembali
            </Link>
            <button v-if="!isMobile" type="submit" :disabled="form.processing"
              class="bg-blue-600 hover:bg-blue-700 text-white inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50">
              <IconLoader3 v-if="form.processing" class="animate-spin" size="16" />
              <IconDeviceFloppy v-else size="16" />
              {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">

          <!-- Info Card -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                <IconFileDescription class="text-blue-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Informasi Dokumen</h3>
                <p class="text-xs sm:text-base text-slate-600">Judul dan deskripsi dokumen</p>
              </div>
            </div>

            <div class="space-y-4">
              <!-- Title -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  Judul Dokumen <span class="text-red-500">*</span>
                </label>
                <InputText v-model="form.title" class="w-full" :class="{ 'p-invalid': form.errors.title }"
                  placeholder="Masukkan judul dokumen..." required />
                <small v-if="form.errors.title" class="p-error block mt-1">{{ form.errors.title }}</small>
              </div>

              <!-- Description -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  Deskripsi <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <Textarea v-model="form.description" rows="3" class="w-full"
                  placeholder="Jelaskan isi atau tujuan dokumen ini..." />
                <small v-if="form.errors.description" class="p-error block mt-1">{{ form.errors.description }}</small>
              </div>
            </div>
          </div>

          <!-- File Dokumen (Word — link saja, admin only) -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 border border-amber-200 rounded-lg flex items-center justify-center">
                <IconFileWord class="text-amber-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">File Dokumen</h3>
                <p class="text-xs sm:text-base text-slate-600">Link dokumen Word — hanya terlihat oleh admin</p>
              </div>
              <div class="ml-auto">
                <Tag value="Admin Only" severity="warning" size="small" />
              </div>
            </div>

            <div class="space-y-2">
              <label class="block font-medium text-gray-700 text-sm">
                Link Dokumen Word <span class="text-slate-400 font-normal">(Opsional)</span>
              </label>
              <InputText v-model="form.doc_file_link" class="w-full" :class="{ 'p-invalid': form.errors.doc_file_link }"
                placeholder="https://docs.google.com/document/d/... atau URL lainnya" />
              <p class="text-xs text-slate-500">
                <IconInfoCircle size="12" class="inline mr-1" />
                Link ini hanya dapat diakses oleh admin, tidak ditampilkan ke publik.
              </p>
              <small v-if="form.errors.doc_file_link" class="p-error block">{{ form.errors.doc_file_link }}</small>
            </div>
          </div>

          <!-- File Dokumen Sah (PDF — upload atau link, wajib) -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                <IconFileCertificate class="text-green-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">File Dokumen Sah</h3>
                <p class="text-xs sm:text-base text-slate-600">File PDF resmi — wajib diisi</p>
              </div>
              <div class="ml-auto">
                <Tag value="Wajib" severity="danger" size="small" />
              </div>
            </div>

            <!-- Mode Toggle -->
            <div class="flex rounded-lg overflow-hidden border border-slate-300 mb-4 w-fit">
              <button type="button" @click="setOfficialFileMode('file')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors"
                :class="officialFileMode === 'file' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'">
                <IconUpload size="14" />
                Upload PDF
              </button>
              <button type="button" @click="setOfficialFileMode('link')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors border-l border-slate-300"
                :class="officialFileMode === 'link' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'">
                <IconLink size="14" />
                Kirim Link
              </button>
            </div>

            <!-- Existing file info (edit mode) -->
            <div v-if="isEditMode && document.official_file_path && !form.official_file && officialFileMode === 'file'"
              class="mb-3 p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-3">
              <IconFileCertificate size="16" class="text-slate-500 flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <p class="text-sm text-slate-600">File saat ini:</p>
                <p class="text-sm font-medium text-blue-600 truncate">{{ document.official_file_path }}</p>
              </div>
              <p class="text-xs text-slate-400 flex-shrink-0">Upload baru untuk mengganti</p>
            </div>

            <!-- File Upload -->
            <div v-if="officialFileMode === 'file'">
              <FileUpload ref="officialUploader" name="official_file" @select="handleOfficialFileSelect"
                :showUploadButton="false" :showCancelButton="false" :multiple="false" accept=".pdf"
                :maxFileSize="52428800">
                <template #content="{ files }">
                  <div v-if="files[0]" class="p-4 bg-slate-50">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <div
                          class="w-12 h-12 bg-white rounded-lg shadow-sm flex items-center justify-center flex-shrink-0">
                          <IconFileCertificate size="20" class="text-green-500" />
                        </div>
                        <div>
                          <p class="font-medium text-slate-900 text-sm">{{ files[0].name }}</p>
                          <p class="text-slate-500 text-xs">{{ (files[0].size / 1024 / 1024).toFixed(2) }} MB</p>
                        </div>
                      </div>
                      <button type="button" @click="clearOfficialFile"
                        class="w-7 h-7 bg-red-100 hover:bg-red-200 rounded-md flex items-center justify-center transition-colors">
                        <IconX size="14" class="text-red-600" />
                      </button>
                    </div>
                  </div>
                </template>
                <template #empty>
                  <div
                    class="p-8 text-center border-2 border-dashed border-slate-300 rounded-lg hover:border-green-400 transition-colors">
                    <IconCloudUpload size="36" class="text-slate-400 mx-auto mb-3" />
                    <p class="text-slate-600 font-medium mb-1">Seret file PDF atau klik untuk memilih</p>
                    <p class="text-slate-400 text-sm">PDF saja (Maks. 50MB)</p>
                  </div>
                </template>
              </FileUpload>
              <small v-if="form.errors.official_file" class="p-error block mt-1">{{ form.errors.official_file }}</small>
            </div>

            <!-- Link Input -->
            <div v-else class="space-y-2">
              <InputText v-model="form.official_file_link" class="w-full"
                :class="{ 'p-invalid': form.errors.official_file_link }"
                placeholder="https://example.com/dokumen-sah.pdf" />
              <p class="text-sm text-slate-500">
                <IconInfoCircle size="13" class="inline mr-1" />
                Pastikan link mengarah ke file PDF yang dapat diakses.
              </p>
              <small v-if="form.errors.official_file_link" class="p-error block">{{ form.errors.official_file_link
              }}</small>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">

          <!-- Publikasi -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
                <IconCalendar class="text-purple-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Info Publikasi</h3>
                <p class="text-xs sm:text-base text-slate-600">Versi dan tanggal terbit</p>
              </div>
            </div>

            <div class="space-y-4">
              <!-- Version -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  Versi <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <InputText v-model="form.version" class="w-full" placeholder="Contoh: 1.0, 2.1, v3..." />
                <small v-if="form.errors.version" class="p-error block mt-1">{{ form.errors.version }}</small>
              </div>

              <!-- Published At -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  Tanggal Terbit <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <DatePicker v-model="form.published_at" dateFormat="dd/mm/yy" placeholder="Pilih tanggal" class="w-full"
                  showButtonBar />
                <small v-if="form.errors.published_at" class="p-error block mt-1">{{ form.errors.published_at }}</small>
              </div>
            </div>
          </div>

          <!-- Area Dokumen -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-50 border border-indigo-200 rounded-lg flex items-center justify-center">
                <IconFolders class="text-indigo-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Area Dokumen</h3>
                <p class="text-xs sm:text-base text-slate-600">Kategori pengelompokan</p>
              </div>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-2">
                Pilih Area <span class="text-slate-400 font-normal">(Opsional)</span>
              </label>
              <Select v-model="form.document_area_id" :options="documentAreas" optionLabel="name" optionValue="id"
                placeholder="— Tidak Ada Area —" class="w-full" showClear />
              <small v-if="form.errors.document_area_id" class="p-error block mt-1">{{ form.errors.document_area_id
              }}</small>
            </div>
          </div>

          <!-- Visibilitas -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-teal-50 border border-teal-200 rounded-lg flex items-center justify-center">
                <IconEye class="text-teal-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Visibilitas</h3>
                <p class="text-xs sm:text-base text-slate-600">Tampilkan ke publik</p>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-sm text-slate-700">Publik</p>
                <p class="text-xs text-slate-500 mt-0.5">
                  {{ form.is_public ? 'Ditampilkan di halaman publik' : 'Hanya terlihat admin' }}
                </p>
              </div>
              <ToggleSwitch v-model="form.is_public" />
            </div>
            <small v-if="form.errors.is_public" class="p-error block mt-1">{{ form.errors.is_public }}</small>
          </div>

          <!-- Mobile Submit -->
          <div class="block sm:hidden">
            <button type="submit" :disabled="form.processing"
              class="bg-blue-600 hover:bg-blue-700 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50">
              <IconLoader3 v-if="form.processing" class="animate-spin" size="16" />
              <IconDeviceFloppy v-else size="16" />
              {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update' : 'Simpan' }}
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
