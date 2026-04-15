<script setup>
import {
  IconFile,
  IconFileTypeDoc,
  IconFileTypeDocx,
  IconFileTypeJpg,
  IconFileTypePdf,
  IconFileTypePng,
  IconFileTypeZip,
} from '@tabler/icons-vue'
import { computed, ref } from 'vue'

const props = defineProps({
  form: Object,
  incidentTypes: Array,
})

defineEmits(['next', 'back'])

const priorityOptions = [
  {
    label: 'Rendah',
    value: 'Rendah',
    color: 'success',
    description: 'Tidak mengganggu operasional',
  },
  {
    label: 'Sedang',
    value: 'Sedang',
    color: 'info',
    description: 'Sedikit mengganggu operasional',
  },
  {
    label: 'Tinggi',
    value: 'Tinggi',
    color: 'warn',
    description: 'Sangat mengganggu operasional',
  },
  {
    label: 'Kritikal',
    value: 'Kritikal',
    color: 'danger',
    description: 'Menghentikan operasional',
  },
]

const maxDate = new Date()

const selectedType = computed(() => {
  if (!props.form.incident_type_id) return null
  return (
    props.incidentTypes.find((t) => t.id === props.form.incident_type_id) ||
    null
  )
})

// --- Attachment ---
const attachmentMode = ref('file')
const uploader = ref(null)
const attachmentPreview = ref(null)

const getFileIcon = (filename) => {
  if (!filename) return [IconFile, 'bg-slate-100', 'text-slate-600']
  const ext = filename.split('.').pop().toLowerCase()
  const iconMap = {
    pdf: [IconFileTypePdf, 'bg-red-100', 'text-red-600'],
    doc: [IconFileTypeDoc, 'bg-blue-100', 'text-blue-600'],
    docx: [IconFileTypeDocx, 'bg-blue-100', 'text-blue-600'],
    zip: [IconFileTypeZip, 'bg-yellow-100', 'text-yellow-600'],
    jpg: [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    jpeg: [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    png: [IconFileTypePng, 'bg-green-100', 'text-green-600'],
  }
  return iconMap[ext] || [IconFile, 'bg-slate-100', 'text-slate-600']
}

const setAttachmentMode = (mode) => {
  attachmentMode.value = mode
  props.form.attachment_type = mode
  props.form.attachment = null
  props.form.attachment_links = ''
  if (mode === 'file') {
    attachmentPreview.value = null
    if (uploader.value) uploader.value.clear()
  }
}

const triggerFileInput = () => {
  const input = uploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
}

const handleFileSelect = (event) => {
  const file = event.files[0]
  props.form.attachment = file
  attachmentPreview.value = file?.type.startsWith('image/')
    ? URL.createObjectURL(file)
    : null
}

const clearAttachment = () => {
  if (uploader.value) uploader.value.clear()
  props.form.attachment = null
  attachmentPreview.value = null
}
</script>

<template>
  <div class="p-0 py-6 sm:p-8">
    <div class="mx-auto max-w-4xl">
      <div class="mb-6 text-center sm:mb-8">
        <h2 class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl">
          Buat Tiket Baru
        </h2>
        <p class="text-slate-600">
          Isi formulir di bawah dengan lengkap dan akurat
        </p>
      </div>

      <hr class="mx-6 !mt-0 sm:hidden" />

      <form @submit.prevent="$emit('next')" class="space-y-6 sm:space-y-8">
        <!-- Reporter Information -->
        <div class="rounded-xl border-slate-200 p-6 sm:border sm:bg-slate-50">
          <h3
            class="mb-4 flex items-center text-lg font-semibold text-slate-900"
          >
            <IconUserExclamation size="18" class="mr-2 text-blue-600" />
            Informasi Pelapor
          </h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-2 block font-medium text-slate-700">
                Nama Lengkap <span class="text-red-500">*</span>
              </label>
              <InputText
                v-model="form.reporter_name"
                placeholder="Masukkan nama lengkap"
                class="w-full"
                :class="{ 'p-invalid': form.errors.reporter_name }"
                required
              />
              <small v-if="form.errors.reporter_name" class="p-error">
                {{ form.errors.reporter_name }}
              </small>
            </div>
            <div>
              <label class="mb-2 block font-medium text-slate-700">
                Email <span class="text-red-500">*</span>
              </label>
              <InputText
                v-model="form.reporter_email"
                type="email"
                placeholder="nama@email.com"
                class="w-full"
                :class="{ 'p-invalid': form.errors.reporter_email }"
                required
              />
              <small v-if="form.errors.reporter_email" class="p-error">
                {{ form.errors.reporter_email }}
              </small>
            </div>
            <div class="md:col-span-2">
              <label class="mb-2 block font-medium text-slate-700">
                Nomor Telepon <span class="text-slate-500">(Opsional)</span>
              </label>
              <InputText
                v-model="form.reporter_phone"
                placeholder="08123456789"
                class="w-full"
                :class="{ 'p-invalid': form.errors.reporter_phone }"
              />
              <small v-if="form.errors.reporter_phone" class="p-error">
                {{ form.errors.reporter_phone }}
              </small>
            </div>
          </div>
        </div>

        <hr class="mx-6 !mt-0 sm:hidden" />

        <!-- Incident Details -->
        <div
          class="rounded-xl border-slate-200 px-6 py-0 sm:border sm:bg-slate-50 sm:p-6"
        >
          <h3
            class="mb-4 flex items-center text-lg font-semibold text-slate-900"
          >
            <IconUrgent size="18" class="mr-2 text-red-600" />
            Detail Tiket
          </h3>
          <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="mb-2 block font-medium text-slate-700">
                  Kategori <span class="text-red-500">*</span>
                </label>
                <Select
                  v-model="form.incident_type_id"
                  :options="incidentTypes"
                  optionLabel="name"
                  optionValue="id"
                  placeholder="Pilih kategori"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.incident_type_id }"
                  required
                />
                <small v-if="form.errors.incident_type_id" class="p-error">
                  {{ form.errors.incident_type_id }}
                </small>
              </div>
              <div>
                <label class="mb-2 block font-medium text-slate-700">
                  Waktu Kejadian <span class="text-red-500">*</span>
                </label>
                <DatePicker
                  v-model="form.incident_at"
                  showTime
                  showIcon
                  hourFormat="24"
                  iconDisplay="input"
                  :maxDate="maxDate"
                  placeholder="Pilih tanggal & waktu"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.incident_at }"
                  required
                />
                <small v-if="form.errors.incident_at" class="p-error">
                  {{ form.errors.incident_at }}
                </small>
              </div>
            </div>

            <div>
              <label class="mb-2 block font-medium text-slate-700">
                Prioritas Tiket <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div
                  v-for="priority in priorityOptions"
                  :key="priority.value"
                  @click="form.priority = priority.value"
                  class="cursor-pointer"
                >
                  <div
                    class="rounded-lg border p-4 text-center transition-all duration-200 hover:shadow-md"
                    :class="
                      form.priority === priority.value
                        ? 'border-blue-500 bg-blue-50'
                        : 'border-slate-200 hover:border-slate-300'
                    "
                  >
                    <Tag
                      :value="priority.label"
                      :severity="priority.color"
                      size="small"
                    />
                  </div>
                </div>
              </div>
              <small v-if="form.errors.priority" class="p-error">
                {{ form.errors.priority }}
              </small>
            </div>

            <div>
              <label class="mb-2 block font-medium text-slate-700">
                Deskripsi <span class="text-red-500">*</span>
              </label>
              <Textarea
                v-model="form.description"
                rows="5"
                placeholder="Jelaskan tiket Anda secara detail..."
                class="w-full"
                :class="{ 'p-invalid': form.errors.description }"
                required
              />
              <small v-if="form.errors.description" class="p-error">
                {{ form.errors.description }}
              </small>
            </div>

            <!-- Selected Type Info Panel -->
            <Transition
              enter-active-class="transition-all duration-300 ease-out"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition-all duration-200 ease-in"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 -translate-y-2"
            >
              <div
                v-if="
                  selectedType &&
                  (selectedType.description || selectedType.guide)
                "
              >
                <div
                  class="overflow-hidden rounded-xl border border-indigo-200 bg-indigo-50"
                >
                  <div
                    class="flex items-center gap-3 border-b border-indigo-200 bg-indigo-100 px-5 py-3"
                  >
                    <div
                      class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-600"
                    >
                      <i class="pi pi-info-circle !text-sm text-white"></i>
                    </div>
                    <div>
                      <p class="font-semibold text-indigo-900">
                        {{ selectedType.name }}
                      </p>
                      <p
                        v-if="selectedType.description"
                        class="text-sm text-indigo-700"
                      >
                        {{ selectedType.description }}
                      </p>
                    </div>
                  </div>
                  <div v-if="selectedType.guide" class="p-5">
                    <p
                      class="mb-3 text-xs font-semibold uppercase tracking-wider text-indigo-600"
                    >
                      Panduan Pelaporan
                    </p>
                    <div
                      class="prose prose-sm max-w-none text-slate-700 [&>h3]:mb-2 [&>h3]:text-base [&>h3]:font-semibold [&>h3]:text-indigo-900 [&>li]:mb-1 [&>ol]:mb-2 [&>ol]:pl-5 [&>p]:mb-2 [&>ul]:mb-2 [&>ul]:pl-5"
                      v-html="selectedType.guide"
                    />
                  </div>
                </div>
              </div>
            </Transition>

            <!-- Attachment -->
            <div>
              <label class="mb-2 block font-medium text-slate-700">
                Lampiran Bukti <span class="text-slate-500">(Opsional)</span>
              </label>

              <!-- Mode Toggle -->
              <div
                class="mb-4 flex w-fit overflow-hidden rounded-xl border border-slate-300"
              >
                <button
                  type="button"
                  @click="setAttachmentMode('file')"
                  class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors"
                  :class="
                    attachmentMode === 'file'
                      ? 'bg-indigo-600 text-white'
                      : 'bg-white text-slate-600 hover:bg-slate-50'
                  "
                >
                  <i class="pi pi-upload text-xs"></i>
                  Upload Dokumen
                </button>
                <button
                  type="button"
                  @click="setAttachmentMode('link')"
                  class="flex items-center gap-2 border-l border-slate-300 px-4 py-2 text-sm font-medium transition-colors"
                  :class="
                    attachmentMode === 'link'
                      ? 'bg-indigo-600 text-white'
                      : 'bg-white text-slate-600 hover:bg-slate-50'
                  "
                >
                  <i class="pi pi-link text-xs"></i>
                  Kirim Link
                </button>
              </div>

              <!-- File Upload -->
              <div v-if="attachmentMode === 'file'">
                <FileUpload
                  ref="uploader"
                  name="attachment"
                  @select="handleFileSelect"
                  :auto="true"
                  :customUpload="true"
                  :showUploadButton="false"
                  :showCancelButton="false"
                  :multiple="false"
                  accept=".jpg,.jpeg,.png,.pdf,.zip,.doc,.docx"
                  :maxFileSize="2097152"
                >
                  <template #content="{ files }">
                    <div
                      v-if="files[0]"
                      class="rounded-lg border border-slate-200 bg-slate-50 p-4"
                    >
                      <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start">
                          <div class="mt-1">
                            <component
                              :is="getFileIcon(files[0].name)[0]"
                              :class="getFileIcon(files[0].name)[2]"
                              class="mr-3"
                              size="18"
                            />
                          </div>
                          <div>
                            <p class="break-all font-medium text-slate-900">
                              {{ files[0].name }}
                            </p>
                            <p class="text-sm text-slate-500">
                              {{ (files[0].size / 1024 / 1024).toFixed(2) }} MB
                            </p>
                          </div>
                        </div>
                        <button
                          type="button"
                          @click="clearAttachment"
                          class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                        >
                          <IconX size="16" />
                        </button>
                      </div>
                    </div>
                  </template>
                  <template #empty>
                    <div
                      class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 px-4 py-6 transition-colors hover:border-blue-400"
                      @click="triggerFileInput"
                    >
                      <IconFileSearch class="mb-2 text-gray-400" />
                      <p class="font-medium text-slate-600">
                        Klik atau drag file ke sini
                      </p>
                      <p class="text-sm text-slate-400">
                        JPG, PNG, PDF, ZIP, DOC (Maks. 2MB)
                      </p>
                    </div>
                  </template>
                </FileUpload>
              </div>

              <!-- Link Input -->
              <div v-else class="space-y-2">
                <Textarea
                  v-model="form.attachment_links"
                  rows="3"
                  class="w-full rounded-xl"
                  placeholder="Masukkan URL bukti, pisahkan dengan koma jika lebih dari satu.&#10;Contoh: https://drive.google.com/file/xxx, https://example.com/screenshot.png"
                />
                <p class="text-sm text-slate-500">
                  <i class="pi pi-info-circle mr-1"></i>
                  Untuk beberapa link, pisahkan dengan koma (,)
                </p>
              </div>

              <small
                v-if="form.errors.attachment || form.errors.attachment_links"
                class="p-error"
              >
                {{ form.errors.attachment || form.errors.attachment_links }}
              </small>
            </div>
          </div>
        </div>

        <hr class="mx-6 sm:hidden" />

        <!-- Navigation -->
        <div class="flex justify-between px-6 sm:px-0">
          <button
            type="button"
            @click="$emit('back')"
            class="py-2 text-slate-600 transition-colors hover:text-slate-800"
          >
            ← Kembali
          </button>
          <button
            type="submit"
            class="rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
          >
            Selanjutnya →
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
