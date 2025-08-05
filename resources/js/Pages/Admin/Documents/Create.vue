<script setup>
// filepath: resources/js/Pages/Admin/Documents/Create.vue

import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'
import {
  IconFileText, IconUpload, IconCalendar, IconArrowLeft,
  IconLoader3, IconDeviceFloppy, IconEye, IconX
} from '@tabler/icons-vue'

const props = defineProps({
  document: {
    type: Object,
    default: null
  }
})

const { isMobile, isDesktop } = useResponsive()

// Check if we're in edit mode
const isEditMode = computed(() => !!props.document)

// Initialize form
const form = useForm({
  _method: isEditMode.value ? 'PUT' : 'POST',
  title: props.document?.title || '',
  description: props.document?.description || '',
  version: props.document?.version || '',
  file: null,
  published_at: props.document?.published_at || null,
})

const publishNow = ref(!!props.document?.published_at)
const fileUploader = ref(null)

const onFileSelect = (event) => {
  const file = event.files[0]
  form.file = file
}

const clearFile = () => {
  form.file = null
  if (fileUploader.value) {
    fileUploader.value.clear()
  }
}

const triggerFileInput = () => {
  const input = fileUploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
}

const togglePublishNow = () => {
  form.published_at = publishNow.value ? new Date().toISOString() : null
}

const submit = () => {
  if (isEditMode.value) {
    form.post(route('admin.documents.update', props.document.id), {
      forceFormData: true,
      _method: 'PUT'
    })
  } else {
    form.post(route('admin.documents.store'), {
      forceFormData: true
    })
  }
}
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen Baru'">
    <div class="min-h-screen bg-gray-50">
      <form @submit.prevent="submit" class="space-y-4 lg:space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
              <h2 class="text-xl lg:text-2xl font-bold text-slate-900">{{ isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen Baru' }}</h2>
              <p class="text-slate-600">{{ isEditMode ? 'Perbarui informasi dokumen' : 'Tambahkan dokumen panduan keamanan siber' }}</p>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('admin.documents.index')"
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 w-full lg:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
              >
                <IconArrowLeft size="16"/>
                Kembali
              </Link>
              <button
                v-if="!isMobile"
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full lg:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update Dokumen' : 'Simpan Dokumen' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <Message v-if="$page.props.flash?.success" severity="success" class="shadow-sm">
          {{ $page.props.flash?.success }}
        </Message>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
          <!-- Main Content (Left Column) -->
          <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Document Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                  <IconFileText class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Informasi Dokumen</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Data dasar dokumen yang akan ditampilkan</p>
                </div>
              </div>

              <div class="space-y-4 lg:space-y-6">
                <!-- Title -->
                <div>
                  <label for="title" class="block font-medium text-gray-700 mb-2">
                    Judul Dokumen <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="title"
                    v-model="form.title"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.title }"
                    placeholder="Masukkan judul dokumen..."
                    required />
                  <small v-if="form.errors.title" class="p-error block mt-1">{{ form.errors.title }}</small>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block font-medium text-gray-700 mb-2">
                    Deskripsi <span class="text-slate-500 text-xs">(Opsional)</span>
                  </label>
                  <Textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.description }"
                    placeholder="Jelaskan isi dokumen ini..."
                  />
                  <small v-if="form.errors.description" class="p-error block mt-1">{{ form.errors.description }}</small>
                </div>

                <!-- Version -->
                <div>
                  <label for="version" class="block font-medium text-gray-700 mb-2">
                    Versi <span class="text-slate-500 text-xs">(Opsional)</span>
                  </label>
                  <InputText
                    id="version"
                    v-model="form.version"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.version }"
                    placeholder="contoh: 1.0, 2.1, dll"
                  />
                  <small v-if="form.errors.version" class="p-error block mt-1">{{ form.errors.version }}</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar (Right Column) -->
          <div class="space-y-4 lg:space-y-6">
            <!-- File Upload -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                  <IconFileUpload class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">File Dokumen</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Upload file dokumen dalam format PDF</p>
                </div>
              </div>

              <!-- Current File Info (Edit Mode) -->
              <div v-if="isEditMode && props.document?.file_path" class="mb-4">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 border border-red-200 rounded-lg flex items-center justify-center">
                      <IconFileTypePdf class="text-red-600" size="18" />
                    </div>
                    <div class="flex-1">
                      <p class="font-medium text-slate-900">File Saat Ini</p>
                      <p class="text-sm text-slate-500">{{ props.document.file_size }}</p>
                      <p v-if="!props.document.file_exists" class="text-sm text-red-600 font-medium">⚠️ File tidak ditemukan</p>
                    </div>
                    <div class="flex gap-2">
                      <a
                        v-if="props.document.file_exists"
                        :href="`/storage/${props.document.file_path}`"
                        target="_blank"
                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        title="Lihat File"
                      >
                        <IconEye size="16" />
                      </a>
                    </div>
                  </div>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                  {{ isEditMode ? 'Upload file baru untuk mengganti file lama' : '' }}
                </p>
              </div>

              <!-- File Upload -->
              <div>
                <label class="block font-medium text-gray-700 mb-2">
                  File Dokumen
                  <span v-if="!isEditMode" class="text-red-500">*</span>
                  <span v-if="isEditMode" class="text-slate-500 text-xs">(Opsional - kosongkan jika tidak ingin mengganti)</span>
                </label>

                <FileUpload
                  ref="fileUploader"
                  name="file"
                  @select="onFileSelect"
                  :auto="true"
                  :customUpload="true"
                  :showUploadButton="false"
                  :showCancelButton="false"
                  :multiple="false"
                  accept=".pdf"
                  :maxFileSize="8388608"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.file }"
                >
                  <template #content="{ files }">
                    <div v-if="files[0]" class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                      <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start">
                          <div class="mt-1"><IconFileTypePdf class="text-red-600 mr-3" size="18"/></div>
                          <div>
                            <p class="font-medium text-slate-900 break-all">{{ files[0].name }}</p>
                            <p class="text-sm text-slate-500">{{ (files[0].size / 1024 / 1024).toFixed(2) }} MB</p>
                          </div>
                        </div>
                        <button
                          type="button"
                          @click="clearFile"
                          class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        >
                          <IconX size="16" />
                        </button>
                      </div>
                    </div>
                  </template>

                  <template #empty>
                    <div
                      class="flex flex-col items-center justify-center py-6 px-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-600 transition-colors cursor-pointer"
                      @click="triggerFileInput"
                    >
                      <IconFileSearch class="text-gray-400 mb-2" :size="!isDesktop ? 18 : undefined"/>
                      <p class="text-sm text-gray-600 text-center">
                        {{ isEditMode ? 'Pilih file baru untuk mengganti' : 'Drag & drop atau klik untuk memilih file' }}
                      </p>
                      <p class="text-xs text-gray-400 mt-1">PDF (Maks. 8MB)</p>
                    </div>
                  </template>
                </FileUpload>

                <small v-if="form.errors.file" class="p-error block mt-1">{{ form.errors.file }}</small>
              </div>
            </div>

            <!-- Button Submit (Mobile Only) -->
            <div v-if="isMobile">
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update Dokumen' : 'Simpan Dokumen' }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
