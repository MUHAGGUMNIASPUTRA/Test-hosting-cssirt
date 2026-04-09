<script setup>
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  document: {
    type: Object,
    default: null,
  },
});

const { isMobile } = useResponsive();

const isEditMode = computed(() => !!props.document);

const detectExistingFileMode = () => {
  if (!props.document?.file_path) return 'file';
  const val = props.document.file_path;
  if (val.startsWith('http://') || val.startsWith('https://')) return 'link';
  return 'file';
};

const fileMode = ref(detectExistingFileMode());

const form = useForm({
  title: props.document?.title || '',
  description: props.document?.description || '',
  version: props.document?.version || '',
  published_at: props.document?.published_at ? new Date(props.document.published_at) : null,
  file_type: detectExistingFileMode(),
  file: null,
  file_links: (detectExistingFileMode() === 'link' ? props.document?.file_path : '') || '',
});

const uploader = ref(null);
const selectedFileName = ref(null);

const setFileMode = (mode) => {
  fileMode.value = mode;
  form.file_type = mode;
  form.file = null;
  form.file_links = '';
  selectedFileName.value = null;
  if (uploader.value) uploader.value.clear();
};

const handleFileSelect = (event) => {
  const file = event.files[0];
  form.file = file;
  selectedFileName.value = file?.name || null;
};

const clearFile = () => {
  if (uploader.value) uploader.value.clear();
  form.file = null;
  selectedFileName.value = null;
};

const submit = () => {
  if (isEditMode.value) {
    form.post(route('admin.documents.update', props.document.id), {
      forceFormData: true,
    });
  } else {
    form.post(route('admin.documents.store'));
  }
};
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Panduan' : 'Tambah Panduan'">
    <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
              {{ isEditMode ? 'Edit Panduan' : 'Tambah Panduan Baru' }}
            </h2>
            <p class="text-slate-600">
              {{ isEditMode ? 'Perbarui informasi panduan atau dokumen' : 'Upload atau tambahkan link dokumen panduan' }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <Link
              :href="route('admin.documents.index')"
              class="bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
            >
              <IconArrowLeft size="16" />
              Kembali
            </Link>
            <button
              v-if="!isMobile"
              type="submit"
              :disabled="form.processing"
              class="bg-blue-600 hover:bg-blue-700 text-white inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
            >
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
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                <IconFileDescription class="text-blue-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Informasi Panduan</h3>
                <p class="text-xs sm:text-base text-slate-600">Detail dokumen panduan atau referensi</p>
              </div>
            </div>

            <div class="space-y-4">
              <!-- Title -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  Judul Panduan <span class="text-red-500">*</span>
                </label>
                <InputText
                  v-model="form.title"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.title }"
                  placeholder="Masukkan judul panduan..."
                  required
                />
                <small v-if="form.errors.title" class="p-error block mt-1">{{ form.errors.title }}</small>
              </div>

              <!-- Description -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  Deskripsi <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <Textarea
                  v-model="form.description"
                  rows="4"
                  class="w-full"
                  placeholder="Jelaskan isi atau tujuan dokumen ini..."
                />
                <small v-if="form.errors.description" class="p-error block mt-1">{{ form.errors.description }}</small>
              </div>
            </div>
          </div>

          <!-- File/Link Card -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                <IconPaperclip class="text-green-600" :size="isMobile ? 18 : undefined" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">File Dokumen</h3>
                <p class="text-xs sm:text-base text-slate-600">Upload file atau sertakan link dokumen</p>
              </div>
            </div>

            <!-- Mode Toggle -->
            <div class="flex rounded-lg overflow-hidden border border-slate-300 mb-4 w-fit">
              <button
                type="button"
                @click="setFileMode('file')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors"
                :class="fileMode === 'file' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
              >
                <IconUpload size="14" />
                Upload Dokumen
              </button>
              <button
                type="button"
                @click="setFileMode('link')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors border-l border-slate-300"
                :class="fileMode === 'link' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
              >
                <IconLink size="14" />
                Kirim Link
              </button>
            </div>

            <!-- Existing file info (edit mode) -->
            <div v-if="isEditMode && document.file_path && !form.file && fileMode === 'file'" class="mb-3 p-3 bg-slate-50 rounded-lg border border-slate-200 flex items-center gap-3">
              <IconPaperclip size="16" class="text-slate-500 flex-shrink-0" />
              <div class="flex-1 min-w-0">
                <p class="text-sm text-slate-600">File saat ini:</p>
                <p class="text-sm font-medium text-blue-600 truncate">{{ document.file_path }}</p>
              </div>
              <p class="text-xs text-slate-400 flex-shrink-0">Upload baru untuk mengganti</p>
            </div>

            <!-- File Upload -->
            <div v-if="fileMode === 'file'">
              <FileUpload
                ref="uploader"
                name="file"
                @select="handleFileSelect"
                :showUploadButton="false"
                :showCancelButton="false"
                :multiple="false"
                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.txt"
                :maxFileSize="20971520"
              >
                <template #content="{ files }">
                  <div v-if="files[0]" class="p-4 bg-slate-50">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white rounded-lg shadow-sm flex items-center justify-center flex-shrink-0">
                          <IconFile size="20" class="text-slate-400" />
                        </div>
                        <div>
                          <p class="font-medium text-slate-900 text-sm">{{ files[0].name }}</p>
                          <p class="text-slate-500 text-xs">{{ (files[0].size / 1024 / 1024).toFixed(2) }} MB</p>
                        </div>
                      </div>
                      <button type="button" @click="clearFile" class="w-7 h-7 bg-red-100 hover:bg-red-200 rounded-md flex items-center justify-center transition-colors">
                        <IconX size="14" class="text-red-600" />
                      </button>
                    </div>
                  </div>
                </template>
                <template #empty>
                  <div class="p-8 text-center border-2 border-dashed border-slate-300 rounded-lg hover:border-blue-400 transition-colors">
                    <IconCloudUpload size="36" class="text-slate-400 mx-auto mb-3" />
                    <p class="text-slate-600 font-medium mb-1">Seret file atau klik untuk memilih</p>
                    <p class="text-slate-400 text-sm">PDF, DOC, DOCX, XLS, PPT, ZIP, TXT (Maks. 20MB)</p>
                  </div>
                </template>
              </FileUpload>
              <small v-if="form.errors.file" class="p-error block mt-1">{{ form.errors.file }}</small>
            </div>

            <!-- Link Input -->
            <div v-else class="space-y-2">
              <Textarea
                v-model="form.file_links"
                rows="3"
                class="w-full"
                :class="{ 'p-invalid': form.errors.file_links }"
                placeholder="Masukkan URL dokumen, pisahkan dengan koma jika lebih dari satu.&#10;Contoh: https://drive.google.com/file/xxx, https://example.com/panduan.pdf"
              />
              <p class="text-sm text-slate-500">
                <IconInfoCircle size="13" class="inline mr-1" />
                Untuk beberapa link, pisahkan dengan koma (,)
              </p>
              <small v-if="form.errors.file_links" class="p-error block">{{ form.errors.file_links }}</small>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
          <!-- Publication Info -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-5">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
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
                <InputText
                  v-model="form.version"
                  class="w-full"
                  placeholder="Contoh: 1.0, 2.1, v3..."
                />
                <small v-if="form.errors.version" class="p-error block mt-1">{{ form.errors.version }}</small>
              </div>

              <!-- Published At -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  Tanggal Terbit <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <DatePicker
                  v-model="form.published_at"
                  dateFormat="dd/mm/yy"
                  placeholder="Pilih tanggal"
                  class="w-full"
                  showButtonBar
                />
                <small v-if="form.errors.published_at" class="p-error block mt-1">{{ form.errors.published_at }}</small>
              </div>
            </div>
          </div>

          <!-- Mobile Submit Button -->
          <div class="block sm:hidden">
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-blue-600 hover:bg-blue-700 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
            >
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
