<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Create.vue

import { router, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import axios from 'axios'
import { useResponsive } from '@/Composables/useResponsive'

import {
  IconFileText,
  IconFileTypePdf,
  IconFileTypeDoc,
  IconFileTypeZip,
  IconPhoto,
} from '@tabler/icons-vue'

const props = defineProps({
  incident: {
    type: Object,
    default: null,
  },
  incidentTypes: Array,
  staffUsers: Array,
})

const { isMobile, isDesktop } = useResponsive()

// File upload ref
const fileUploader = ref(null)

const isEditing = computed(() => !!props.incident)
const pageTitle = computed(() =>
  isEditing.value
    ? `Edit Insiden: ${props.incident.case_id}`
    : 'Lapor Insiden Baru',
)
const headerTitle = computed(() =>
  isEditing.value ? 'Edit Insiden' : 'Lapor Insiden Baru',
)
const submitButtonText = computed(() =>
  isEditing.value ? 'Update Laporan' : 'Simpan Laporan',
)

// Detect attachment type from existing attachment object
const detectExistingAttachmentMode = () => {
  return props.incident?.attachment?.type ?? 'file'
}

const attachmentMode = ref(detectExistingAttachmentMode())
const uploader = ref(null)
const attachmentPreview = ref(null)

const formatDateForInput = (date) => {
  const d = new Date(date)
  return d.toISOString().slice(0, 16)
}

const form = useForm({
  reporter_name: props.incident?.reporter_name || '',
  reporter_email: props.incident?.reporter_email || '',
  reporter_phone: props.incident?.reporter_phone || '',
  incident_type_id: props.incident?.incident_type_id || null,
  incident_at: props.incident
    ? new Date(props.incident.incident_at)
    : new Date(),
  description: props.incident?.description || '',
  status: props.incident?.status || 'Baru',
  priority: props.incident?.priority || 'Sedang',
  assigned_to: props.incident?.assigned_to || null,
  attachment_type: detectExistingAttachmentMode(),
  attachment: null,
  attachment_links:
    (detectExistingAttachmentMode() === 'link'
      ? props.incident?.attachment?.url
      : '') || '',
})

// Selected incident type for info panel
const selectedType = computed(() => {
  if (!form.incident_type_id) return null
  return (
    props.incidentTypes?.find((t) => t.id === form.incident_type_id) || null
  )
})

const setAttachmentMode = (mode) => {
  attachmentMode.value = mode
  form.attachment_type = mode
  form.attachment = null
  form.attachment_links = ''
  if (mode === 'file') {
    attachmentPreview.value = null
    if (uploader.value) uploader.value.clear()
  }
}

const handleFileSelect = (event) => {
  const file = event.files[0]
  form.attachment = file
  if (file && file.type.startsWith('image/')) {
    attachmentPreview.value = URL.createObjectURL(file)
  } else {
    attachmentPreview.value = null
  }
}

const clearAttachment = () => {
  if (uploader.value) uploader.value.clear()
  form.attachment = null
  attachmentPreview.value = null
}

const statusOptions = [
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' },
]

const priorityOptions = [
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' },
]

const incidentTypeOptions = computed(() => {
  return (
    props.incidentTypes?.map((type) => ({
      label: type.name,
      value: type.id,
    })) || []
  )
})

const staffUserOptions = computed(() => {
  return [
    { label: 'Tidak ditugaskan', value: null },
    ...(props.staffUsers?.map((user) => ({
      label: user.name,
      value: user.id,
    })) || []),
  ]
})

const submit = () => {
  const formData = { ...form.data() }
  if (formData.incident_at) {
    formData.incident_at = formatDateForInput(formData.incident_at)
  }
  formData.virtual_asset_ids = selectedAssets.value.map((a) => a.id)

  if (isEditing.value) {
    form
      .transform(() => ({ ...formData, _method: 'PUT' }))
      .post(route('admin.incidents.update', props.incident.id))
  } else {
    form.transform(() => formData).post(route('admin.incidents.store'))
  }
}

const getPrioritySeverity = (priority) => {
  const severities = {
    Rendah: 'success',
    Sedang: 'info',
    Tinggi: 'warn',
    Kritikal: 'danger',
  }
  return severities[priority] || 'info'
}

const getStatusSeverity = (status) => {
  const severities = {
    Baru: 'info',
    Diverifikasi: 'primary',
    'Dalam Penyelidikan': 'warn',
    Selesai: 'success',
    Ditutup: 'secondary',
  }
  return severities[status] || 'info'
}

const getPriorityButtonClasses = (priority, isSelected) => {
  const baseClasses =
    'p-2 font-medium border rounded-lg transition-all duration-200 text-center'
  if (isSelected) {
    const selectedClasses = {
      Rendah:
        'border-green-500 bg-green-50 text-green-700 ring-2 ring-green-500 ring-opacity-20',
      Sedang:
        'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500 ring-opacity-20',
      Tinggi:
        'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-500 ring-opacity-20',
      Kritikal:
        'border-red-500 bg-red-50 text-red-700 ring-2 ring-red-500 ring-opacity-20',
    }
    return `${baseClasses} ${selectedClasses[priority] || selectedClasses['Sedang']}`
  } else {
    const unselectedClasses = {
      Rendah:
        'border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300',
      Sedang:
        'border-sky-200 text-sky-600 hover:bg-sky-50 hover:border-sky-300',
      Tinggi:
        'border-orange-200 text-orange-600 hover:bg-orange-50 hover:border-orange-300',
      Kritikal:
        'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300',
    }
    return `${baseClasses} ${unselectedClasses[priority] || unselectedClasses['Sedang']}`
  }
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('id-ID')
}

// --- Virtual asset selector ---
const getInitialAssets = () => {
  if (!props.incident) return []
  const web = (props.incident.web_applications ?? []).map((a) => ({
    id: a.id,
    name: a.name,
    type: 'Web',
  }))
  const mobile = (props.incident.mobile_applications ?? []).map((a) => ({
    id: a.id,
    name: a.name,
    type: 'Mobile',
  }))
  return [...web, ...mobile]
}

const selectedAssets = ref(getInitialAssets())
const assetSearch = ref('')
const assetResults = ref([])
const assetLoading = ref(false)
let assetTimer = null

const searchVirtualAssets = async () => {
  if (!assetSearch.value || assetSearch.value.length < 2) {
    assetResults.value = []
    return
  }
  assetLoading.value = true
  try {
    const { data } = await axios.get('/api/virtual-assets', {
      params: { search: assetSearch.value },
    })
    assetResults.value = data.filter(
      (a) => !selectedAssets.value.some((s) => s.id === a.id),
    )
  } catch {
    assetResults.value = []
  } finally {
    assetLoading.value = false
  }
}

watch(assetSearch, () => {
  clearTimeout(assetTimer)
  assetTimer = setTimeout(searchVirtualAssets, 300)
})

const selectAsset = (asset) => {
  if (!selectedAssets.value.some((a) => a.id === asset.id)) {
    selectedAssets.value.push(asset)
  }
  assetSearch.value = ''
  assetResults.value = []
}

const removeSelectedAsset = (id) => {
  selectedAssets.value = selectedAssets.value.filter((a) => a.id !== id)
}
</script>

<template>
  <AdminLayout :title="pageTitle">
    <form @submit.prevent="submit">
      <div class="space-y-4 lg:space-y-6">
        <!-- Header Section -->
        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
          >
            <div>
              <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
                {{ headerTitle }}
              </h2>
              <p class="text-slate-600">
                {{
                  !isEditing
                    ? 'Buat laporan insiden keamanan siber baru untuk ditindaklanjuti'
                    : ''
                }}
              </p>
              <div v-if="isEditing" class="mt-2 flex items-center gap-3">
                <Tag
                  :value="incident.case_id"
                  severity="secondary"
                  size="small"
                  class="font-mono !text-slate-500"
                />
                <Tag
                  :value="incident.status"
                  :severity="getStatusSeverity(incident.status)"
                  size="small"
                />
                <Tag
                  :value="incident.priority"
                  :severity="getPrioritySeverity(incident.priority)"
                  size="small"
                />
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Button
                severity="secondary"
                @click="() => router.get(route('admin.incidents.index'))"
                class="w-full lg:w-auto"
              >
                <IconArrowLeft size="16" />
                Kembali
              </Button>
              <Button
                v-if="!isMobile"
                type="submit"
                severity="primary"
                :disabled="form.processing"
                class="w-full lg:w-auto"
              >
                <IconLoader3
                  v-if="form.processing"
                  class="animate-spin"
                  size="16"
                />
                <IconDeviceFloppy v-else size="16" />
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </Button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
          <!-- Main Content -->
          <div class="space-y-4 lg:col-span-2 lg:space-y-6">
            <!-- Reporter Information -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
                >
                  <IconUserExclamation
                    class="text-blue-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Informasi Pelapor
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Data kontak pelapor insiden
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
                <div>
                  <label
                    for="reporter_name"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Nama Pelapor <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="reporter_name"
                    v-model="form.reporter_name"
                    placeholder="Masukkan nama lengkap pelapor"
                    required
                    class="w-full"
                    :class="{ 'border-red-300': form.errors.reporter_name }"
                  />
                  <p
                    v-if="form.errors.reporter_name"
                    class="mt-1 text-sm text-red-600"
                  >
                    {{ form.errors.reporter_name }}
                  </p>
                </div>

                <div>
                  <label
                    for="reporter_email"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Email Pelapor <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="reporter_email"
                    v-model="form.reporter_email"
                    type="email"
                    placeholder="contoh@email.com"
                    required
                    class="w-full"
                    :class="{ 'border-red-300': form.errors.reporter_email }"
                  />
                  <p
                    v-if="form.errors.reporter_email"
                    class="mt-1 text-sm text-red-600"
                  >
                    {{ form.errors.reporter_email }}
                  </p>
                </div>

                <div class="md:col-span-2">
                  <label
                    for="reporter_phone"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Nomor Telepon
                  </label>
                  <InputText
                    id="reporter_phone"
                    v-model="form.reporter_phone"
                    type="tel"
                    placeholder="Contoh: 081234567890"
                    class="w-full"
                  />
                </div>
              </div>
            </div>

            <!-- Incident Details -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 lg:h-12 lg:w-12"
                >
                  <IconUrgent
                    class="text-red-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Detail Insiden
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Informasi lengkap tentang insiden yang terjadi
                  </p>
                </div>
              </div>

              <div class="space-y-4 lg:space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
                  <div>
                    <label
                      for="incident_type_id"
                      class="mb-2 block font-medium text-slate-700"
                    >
                      Jenis Insiden <span class="text-red-500">*</span>
                    </label>
                    <Select
                      v-model="form.incident_type_id"
                      :options="incidentTypeOptions"
                      optionLabel="label"
                      optionValue="value"
                      placeholder="Pilih kategori insiden"
                      class="w-full"
                      :class="{
                        'border-red-300': form.errors.incident_type_id,
                      }"
                    />
                    <p
                      v-if="form.errors.incident_type_id"
                      class="mt-1 text-sm text-red-600"
                    >
                      {{ form.errors.incident_type_id }}
                    </p>
                  </div>

                  <div>
                    <label class="mb-2 block font-medium text-slate-700">
                      Waktu Kejadian <span class="text-red-500">*</span>
                    </label>
                    <DatePicker
                      v-model="form.incident_at"
                      showTime
                      hourFormat="24"
                      dateFormat="dd/mm/yy"
                      placeholder="Pilih tanggal dan waktu"
                      class="w-full"
                      :class="{ 'border-red-300': form.errors.incident_at }"
                    />
                    <p
                      v-if="form.errors.incident_at"
                      class="mt-1 text-sm text-red-600"
                    >
                      {{ form.errors.incident_at }}
                    </p>
                  </div>
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
                      class="overflow-hidden rounded-lg border border-blue-200 bg-blue-50"
                    >
                      <div
                        class="flex items-center gap-2 border-b border-blue-200 bg-blue-100 px-4 py-2.5"
                      >
                        <IconInfoCircle
                          size="16"
                          class="flex-shrink-0 text-blue-600"
                        />
                        <div>
                          <span class="text-sm font-semibold text-blue-900">{{
                            selectedType.name
                          }}</span>
                          <span
                            v-if="selectedType.description"
                            class="ml-2 text-sm text-blue-700"
                            >— {{ selectedType.description }}</span
                          >
                        </div>
                      </div>
                      <div v-if="selectedType.guide" class="p-4">
                        <p
                          class="mb-2 text-xs font-semibold uppercase tracking-wider text-blue-600"
                        >
                          Panduan Pelaporan
                        </p>
                        <div
                          class="prose prose-sm max-w-none text-slate-700 [&>h3]:mb-1.5 [&>h3]:text-sm [&>h3]:font-semibold [&>h3]:text-blue-900 [&>li]:mb-0.5 [&>ol]:mb-1.5 [&>ol]:pl-4 [&>p]:mb-1.5 [&>ul]:mb-1.5 [&>ul]:pl-4"
                          v-html="selectedType.guide"
                        />
                      </div>
                    </div>
                  </div>
                </Transition>

                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Deskripsi Insiden <span class="text-red-500">*</span>
                  </label>
                  <Textarea
                    v-model="form.description"
                    rows="5"
                    placeholder="Jelaskan detail insiden yang terjadi, termasuk dampak dan langkah yang sudah diambil..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300': form.errors.description }"
                  />
                  <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-600"
                  >
                    {{ form.errors.description }}
                  </p>
                </div>

                <!-- Attachment -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Lampiran Bukti
                    <span class="font-normal text-slate-400">(Opsional)</span>
                  </label>

                  <!-- Mode Toggle -->
                  <div
                    class="mb-3 flex w-fit overflow-hidden rounded-lg border border-slate-300"
                  >
                    <button
                      type="button"
                      @click="setAttachmentMode('file')"
                      class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium transition-colors"
                      :class="
                        attachmentMode === 'file'
                          ? 'bg-blue-600 text-white'
                          : 'bg-white text-slate-600 hover:bg-slate-50'
                      "
                    >
                      <IconUpload size="14" />
                      Upload Dokumen
                    </button>
                    <button
                      type="button"
                      @click="setAttachmentMode('link')"
                      class="flex items-center gap-1.5 border-l border-slate-300 px-3 py-1.5 text-sm font-medium transition-colors"
                      :class="
                        attachmentMode === 'link'
                          ? 'bg-blue-600 text-white'
                          : 'bg-white text-slate-600 hover:bg-slate-50'
                      "
                    >
                      <IconLink size="14" />
                      Kirim Link
                    </button>
                  </div>

                  <!-- Existing file attachment (edit mode) -->
                  <div
                    v-if="
                      isEditing &&
                      incident.attachment &&
                      attachmentMode === 'file' &&
                      !form.attachment
                    "
                    class="mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3"
                  >
                    <IconPaperclip
                      size="16"
                      class="flex-shrink-0 text-slate-500"
                    />
                    <div class="min-w-0 flex-1">
                      <p class="text-sm text-slate-600">Lampiran saat ini:</p>
                      <p class="truncate text-sm font-medium text-blue-600">
                        {{ incident.attachment.filename }}
                      </p>
                    </div>
                    <p class="text-xs text-slate-400">
                      Upload baru untuk mengganti
                    </p>
                  </div>

                  <!-- File Upload -->
                  <div v-if="attachmentMode === 'file'">
                    <FileUpload
                      ref="uploader"
                      name="attachment"
                      @select="handleFileSelect"
                      :showUploadButton="false"
                      :showCancelButton="false"
                      :multiple="false"
                      accept=".jpg,.jpeg,.png,.pdf,.zip,.doc,.docx"
                      :maxFileSize="5000000"
                    >
                      <template #content="{ files }">
                        <div v-if="files[0]" class="bg-slate-50 p-4">
                          <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                              <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white shadow-sm"
                              >
                                <img
                                  v-if="attachmentPreview"
                                  :src="attachmentPreview"
                                  :alt="files[0].name"
                                  class="h-full w-full object-cover"
                                />
                                <IconFile
                                  v-else
                                  size="20"
                                  class="text-slate-400"
                                />
                              </div>
                              <div>
                                <p class="text-sm font-medium text-slate-900">
                                  {{ files[0].name }}
                                </p>
                                <p class="text-xs text-slate-500">
                                  {{ (files[0].size / 1024 / 1024).toFixed(2) }}
                                  MB
                                </p>
                              </div>
                            </div>
                            <button
                              type="button"
                              @click="clearAttachment"
                              class="flex h-7 w-7 items-center justify-center rounded-md bg-red-100 transition-colors hover:bg-red-200"
                            >
                              <IconX size="14" class="text-red-600" />
                            </button>
                          </div>
                        </div>
                      </template>
                      <template #empty>
                        <div
                          class="rounded-lg border-2 border-dashed border-slate-300 p-6 text-center"
                        >
                          <IconCloudUpload
                            size="32"
                            class="mx-auto mb-2 text-slate-400"
                          />
                          <p class="mb-1 text-sm font-medium text-slate-600">
                            Seret file atau klik untuk memilih
                          </p>
                          <p class="text-xs text-slate-400">
                            JPG, PNG, PDF, ZIP, DOC (Maks. 5MB)
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
                      class="w-full"
                      placeholder="Masukkan URL bukti, pisahkan dengan koma jika lebih dari satu.&#10;Contoh: https://drive.google.com/file/xxx, https://example.com/screenshot.png"
                    />
                    <p class="text-xs text-slate-500">
                      <IconInfoCircle size="12" class="mr-1 inline" />
                      Untuk beberapa link, pisahkan dengan koma (,)
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Virtual Assets -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 lg:h-12 lg:w-12"
                >
                  <IconCloud
                    class="text-indigo-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Aset Virtual Terdampak
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Aplikasi web atau mobile yang terdampak insiden ini
                    (opsional)
                  </p>
                </div>
              </div>

              <!-- Selected chips -->
              <div
                v-if="selectedAssets.length"
                class="mb-3 flex flex-wrap gap-2"
              >
                <div
                  v-for="asset in selectedAssets"
                  :key="asset.id"
                  class="flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-sm"
                >
                  <span class="text-xs font-semibold text-indigo-400">{{
                    asset.type
                  }}</span>
                  <span class="text-slate-700">{{ asset.name }}</span>
                  <button
                    type="button"
                    @click="removeSelectedAsset(asset.id)"
                    class="ml-1 text-slate-400 hover:text-red-500"
                  >
                    <IconX size="12" />
                  </button>
                </div>
              </div>

              <!-- Search -->
              <div class="relative">
                <IconField class="w-full">
                  <InputIcon>
                    <IconLoader3
                      v-if="assetLoading"
                      size="14"
                      class="animate-spin text-slate-400"
                    />
                    <i v-else class="pi pi-search" />
                  </InputIcon>
                  <InputText
                    v-model="assetSearch"
                    placeholder="Cari nama aplikasi web atau mobile..."
                    class="w-full"
                  />
                </IconField>
                <div
                  v-if="assetResults.length"
                  class="absolute z-10 mt-1 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg"
                >
                  <button
                    v-for="asset in assetResults"
                    :key="asset.id"
                    type="button"
                    @click="selectAsset(asset)"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-indigo-50"
                  >
                    <Tag
                      :value="asset.type"
                      severity="secondary"
                      size="small"
                      class="!text-xs"
                    />
                    {{ asset.name }}
                  </button>
                </div>
              </div>
              <p
                v-if="
                  assetSearch.length >= 2 &&
                  !assetResults.length &&
                  !assetLoading
                "
                class="mt-2 text-xs text-slate-400"
              >
                Tidak ada hasil untuk "{{ assetSearch }}"
              </p>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-4 lg:space-y-6">
            <!-- Status Management -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-green-200 bg-green-50 lg:h-12 lg:w-12"
                >
                  <IconClipboardList
                    class="text-green-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Manajemen
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Status dan penugasan
                  </p>
                </div>
              </div>

              <div class="space-y-4 lg:space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
                  <div>
                    <label
                      for="status"
                      class="mb-2 block font-medium text-slate-700"
                      >Status <span class="text-red-500">*</span></label
                    >
                    <Select
                      v-model="form.status"
                      :options="statusOptions"
                      optionLabel="label"
                      optionValue="value"
                      class="w-full"
                    />
                  </div>

                  <div>
                    <label
                      for="priority"
                      class="mb-2 block font-medium text-slate-700"
                      >Prioritas <span class="text-red-500">*</span></label
                    >
                    <Select
                      v-model="form.priority"
                      :options="priorityOptions"
                      optionLabel="label"
                      optionValue="value"
                      class="w-full"
                    />
                    <!-- <label class="block font-medium text-slate-700 mb-3">Prioritas</label>
                    <div class="grid grid-cols-2 gap-2">
                      <button
                        v-for="option in priorityOptions"
                        :key="option.value"
                        type="button"
                        @click="form.priority = option.value"
                        :class="getPriorityButtonClasses(option.value, form.priority === option.value)"
                      >
                        {{ option.label }}
                      </button>
                    </div> -->
                  </div>
                </div>

                <div>
                  <label class="mb-2 block font-medium text-slate-700"
                    >Tugaskan Kepada</label
                  >
                  <Select
                    v-model="form.assigned_to"
                    :options="staffUserOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Pilih staf"
                    class="w-full"
                  />
                </div>
              </div>
            </div>

            <!-- Preview Card -->
            <div
              class="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:p-6"
            >
              <h3 class="mb-4 text-xl/6 font-semibold text-slate-700">
                {{ isEditing ? 'Ringkasan Perubahan' : 'Preview Laporan' }}
              </h3>
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <span class="text-slate-500">ID Insiden:</span>
                  <span
                    class="rounded bg-slate-200 px-2 py-1 font-mono text-xs text-slate-700"
                  >
                    {{ isEditing ? incident.case_id : 'Auto Generated' }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Pelapor:</span>
                  <span class="ml-2 truncate text-slate-700">{{
                    form.reporter_name || 'Belum diisi'
                  }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Status:</span>
                  <Tag
                    :value="form.status"
                    :severity="getStatusSeverity(form.status)"
                    size="small"
                  />
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Prioritas:</span>
                  <Tag
                    :value="form.priority"
                    :severity="getPrioritySeverity(form.priority)"
                    size="small"
                  />
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-slate-500">Kategori:</span>
                  <span class="ml-2 truncate text-slate-700">{{
                    form.incident_type_id
                      ? incidentTypeOptions.find(
                          (type) => type.value === form.incident_type_id,
                        )?.label || 'Belum diisi'
                      : 'Belum diisi'
                  }}</span>
                </div>
                <div
                  v-if="form.incident_at"
                  class="flex items-start justify-between"
                >
                  <span class="text-slate-500">Waktu:</span>
                  <span class="text-right text-slate-700">{{
                    formatDateTime(form.incident_at)
                  }}</span>
                </div>
                <div
                  v-if="
                    form.attachment || (isEditing && incident?.attachment?.url)
                  "
                  class="flex items-center justify-between"
                >
                  <span class="text-slate-500">Lampiran:</span>
                  <span class="text-green-600">✓ Ada file</span>
                </div>
              </div>
            </div>

            <!-- Button Submit (Mobile Only) -->
            <div v-if="isMobile">
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-800 disabled:opacity-50"
              >
                <IconLoader3
                  v-if="form.processing"
                  class="animate-spin"
                  size="16"
                />
                <IconDeviceFloppy v-else size="16" />
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
