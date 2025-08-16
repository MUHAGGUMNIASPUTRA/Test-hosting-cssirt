<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Create.vue

import { router, useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import { useResponsive } from '@/Composables/useResponsive';

import {
  IconFileText, IconFileTypePdf, IconFileTypeDoc, IconFileTypeZip, IconPhoto,
} from '@tabler/icons-vue';

const props = defineProps({
  incident: {
    type: Object,
    default: null // null means creating, object means editing
  },
  incidentTypes: Array,
  staffUsers: Array,
})

const { isMobile, isDesktop } = useResponsive();

// File upload ref
const fileUploader = ref(null)

// Determine if editing or creating
const isEditing = computed(() => !!props.incident)
const pageTitle = computed(() =>
  isEditing.value ? `Edit Insiden: ${props.incident.case_id}` : 'Lapor Insiden Baru'
)
const headerTitle = computed(() =>
  isEditing.value ? 'Edit Insiden' : 'Lapor Insiden Baru'
)
const headerDescription = computed(() =>
  isEditing.value
    ? 'Perbarui informasi insiden keamanan siber'
    : 'Buat laporan insiden keamanan siber baru untuk ditindaklanjuti'
)
const submitButtonText = computed(() =>
  isEditing.value ? 'Update Laporan' : 'Simpan Laporan'
)

// Format current date properly for the form
const formatDateForInput = (date) => {
  const d = new Date(date)
  return d.toISOString().slice(0, 16) // Returns "YYYY-MM-DDTHH:MM"
}

// Initialize form with default or existing values
const form = useForm({
  reporter_name: props.incident?.reporter_name || '',
  reporter_email: props.incident?.reporter_email || '',
  reporter_phone: props.incident?.reporter_phone || '',
  incident_type_id: props.incident?.incident_type_id || null,
  incident_at: props.incident ? new Date(props.incident.incident_at) : new Date(),
  description: props.incident?.description || '',
  status: props.incident?.status || 'Baru',
  priority: props.incident?.priority || 'Sedang',
  assigned_to: props.incident?.assigned_to || null,
  attachment: null, // File attachment
})

// Watch for changes in props.incident and update form accordingly
onMounted(() => {
  if (props.incident) {
    form.reporter_name = props.incident.reporter_name || ''
    form.reporter_email = props.incident.reporter_email || ''
    form.reporter_phone = props.incident.reporter_phone || ''
    form.incident_type_id = props.incident.incident_type_id || null
    form.incident_at = props.incident.incident_at ? new Date(props.incident.incident_at) : new Date()
    form.description = props.incident.description || ''
    form.status = props.incident.status || 'Baru'
    form.priority = props.incident.priority || 'Sedang'
    form.assigned_to = props.incident.assigned_to || null
  }
})

// File upload functions
const onFileSelect = (event) => {
  const file = event.files[0]
  form.attachment = file
}

const clearFile = () => {
  form.attachment = null
  if (fileUploader.value) {
    fileUploader.value.clear()
  }
}

const triggerFileInput = () => {
  const input = fileUploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
}

// Get file icon color based on file type
const getFileIcon = (fileName) => {
  if (!fileName) return 'text-slate-600'

  const extension = fileName.split('.').pop()?.toLowerCase()

  switch (extension) {
    case 'pdf': return [IconFileTypePdf, 'text-red-600', 'bg-red-50', 'border-red-200']
    case 'doc':
    case 'docx': return [IconFileTypeDoc, 'text-blue-600', 'bg-blue-50', 'border-blue-200']
    case 'zip': return [IconFileTypeZip, 'text-yellow-600', 'bg-yellow-50', 'border-yellow-200']
    case 'jpg':
    case 'jpeg':
    case 'png': return [IconPhoto, 'text-green-600', 'bg-green-50', 'border-green-200']
    default: return [IconFileText, 'text-slate-600', 'bg-slate-50', 'border-slate-200']
  }
}

const statusOptions = [
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' }
]

const priorityOptions = [
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' }
]

// Transform incident types for select
const incidentTypeOptions = computed(() => {
  return props.incidentTypes?.map(type => ({
    label: type.name,
    value: type.id
  })) || []
})

// Transform staff users for select
const staffUserOptions = computed(() => {
  return [
    { label: 'Tidak ditugaskan', value: null },
    ...(props.staffUsers?.map(user => ({
      label: user.name,
      value: user.id
    })) || [])
  ]
})

const submit = () => {
  // Format the date properly before submission
  const formData = { ...form.data() }
  if (formData.incident_at) {
    formData.incident_at = formatDateForInput(formData.incident_at)
  }

  if (isEditing.value) {
    // For updates, we need to use POST with _method override due to file uploads
    // because browsers don't support file uploads with PUT method
    const formData = new FormData()

    // Add all form fields
    formData.append('reporter_name', form.reporter_name || '')
    formData.append('reporter_email', form.reporter_email || '')
    formData.append('reporter_phone', form.reporter_phone || '')
    formData.append('incident_type_id', form.incident_type_id || '')
    formData.append('description', form.description || '')
    formData.append('status', form.status || '')
    formData.append('priority', form.priority || '')
    formData.append('assigned_to', form.assigned_to || '')

    // Handle date formatting
    if (form.incident_at) {
      formData.append('incident_at', formatDateForInput(form.incident_at))
    }

    // Handle file attachment
    if (form.attachment) {
      formData.append('attachment', form.attachment)
    }

    // Add method override for Laravel
    formData.append('_method', 'PUT')

    // Use Inertia's router.post with FormData
    router.post(route('admin.incidents.update', props.incident.id), formData, {
      forceFormData: true,
      preserveState: false,
      onSuccess: () => {
        // Optional: handle success
      },
      onError: (errors) => {
        // Optional: handle errors
        console.error('Update failed:', errors)
      }
    })
  } else {
    // Create new incident
    form.transform(() => formData).post(route('admin.incidents.store'), {
      forceFormData: true
    })
  }
}

const getPrioritySeverity = (priority) => {
  const severities = {
    'Rendah': 'success',
    'Sedang': 'info',
    'Tinggi': 'warn',
    'Kritikal': 'danger'
  }
  return severities[priority] || 'info'
}

const getStatusSeverity = (status) => {
  const severities = {
    'Baru': 'info',
    'Diverifikasi': 'primary',
    'Dalam Penyelidikan': 'warn',
    'Selesai': 'success',
    'Ditutup': 'secondary'
  }
  return severities[status] || 'info'
}

// Get priority button styles based on severity
const getPriorityButtonClasses = (priority, isSelected) => {
  const baseClasses = 'p-2 font-medium border rounded-lg transition-all duration-200 text-center'

  if (isSelected) {
    const selectedClasses = {
      'Rendah': 'border-green-500 bg-green-50 text-green-700 ring-2 ring-green-500 ring-opacity-20',
      'Sedang': 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500 ring-opacity-20',
      'Tinggi': 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-500 ring-opacity-20',
      'Kritikal': 'border-red-500 bg-red-50 text-red-700 ring-2 ring-red-500 ring-opacity-20'
    }
    return `${baseClasses} ${selectedClasses[priority] || selectedClasses['Sedang']}`
  } else {
    const unselectedClasses = {
      'Rendah': 'border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300',
      'Sedang': 'border-sky-200 text-sky-600 hover:bg-sky-50 hover:border-sky-300',
      'Tinggi': 'border-orange-200 text-orange-600 hover:bg-orange-50 hover:border-orange-300',
      'Kritikal': 'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300'
    }
    return `${baseClasses} ${unselectedClasses[priority] || unselectedClasses['Sedang']}`
  }
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('id-ID')
}
</script>

<template>
  <AdminLayout :title="pageTitle">
    <form @submit.prevent="submit">
      <div class="space-y-4 lg:space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
              <h2 class="text-xl lg:text-2xl font-bold text-slate-900">{{ headerTitle }}</h2>
              <p class="text-slate-600">{{ !isEditing ? 'Buat laporan insiden keamanan siber baru untuk ditindaklanjuti' : '' }}</p>
              <!-- Show incident ID when editing -->
              <div v-if="isEditing" class="flex items-center gap-3 mt-2">
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
                <IconArrowLeft size="16"/>
                  Kembali
              </Button>
              <Button
                v-if="!isMobile"
                type="submit"
                severity="primary"
                :disabled="form.processing"
                class="w-full lg:w-auto"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </Button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Reporter Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                  <IconUserExclamation class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Informasi Pelapor</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Data kontak pelapor insiden</p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                <div>
                  <label for="reporter_name" class="block font-medium text-slate-700 mb-2">
                    Nama Pelapor <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="reporter_name"
                    v-model="form.reporter_name"
                    placeholder="Masukkan nama lengkap pelapor"
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.reporter_name }"
                  />
                  <p v-if="form.errors.reporter_name" class="mt-1 text-red-600">
                    {{ form.errors.reporter_name }}
                  </p>
                </div>

                <div>
                  <label for="reporter_email" class="block font-medium text-slate-700 mb-2">
                    Email Pelapor <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="reporter_email"
                    v-model="form.reporter_email"
                    type="email"
                    placeholder="contoh@email.com"
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.reporter_email }"
                  />
                  <p v-if="form.errors.reporter_email" class="mt-1 text-red-600">
                    {{ form.errors.reporter_email }}
                  </p>
                </div>

                <div class="md:col-span-2">
                  <label for="reporter_phone" class="block font-medium text-slate-700 mb-2">
                    Nomor Telepon
                  </label>
                  <InputText
                    id="reporter_phone"
                    v-model="form.reporter_phone"
                    type="tel"
                    placeholder="Contoh: 081234567890"
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.reporter_phone }"
                  />
                  <p v-if="form.errors.reporter_phone" class="mt-1 text-red-600">
                    {{ form.errors.reporter_phone }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Incident Details -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center">
                  <IconUrgent class="text-red-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Detail Insiden</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Informasi lengkap tentang insiden yang terjadi</p>
                </div>
              </div>

              <div class="space-y-4 lg:space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                  <div>
                    <label for="incident_type_id" class="block font-medium text-slate-700 mb-2">
                      Kategori Insiden <span class="text-red-500">*</span>
                    </label>
                    <Select
                      v-model="form.incident_type_id"
                      :options="incidentTypeOptions"
                      optionLabel="label"
                      optionValue="value"
                      placeholder="Pilih kategori insiden"
                      class="w-full"
                      :class="{ 'border-red-300': form.errors.incident_type_id }"
                    />
                    <p v-if="form.errors.incident_type_id" class="mt-1 text-red-600">
                      {{ form.errors.incident_type_id }}
                    </p>
                  </div>

                  <div>
                    <label for="incident_at" class="block font-medium text-slate-700 mb-2">
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
                    <p v-if="form.errors.incident_at" class="mt-1 text-red-600">
                      {{ form.errors.incident_at }}
                    </p>
                  </div>
                </div>

                <div>
                  <label for="description" class="block font-medium text-slate-700 mb-2">
                    Deskripsi Insiden <span class="text-red-500">*</span>
                  </label>
                  <Textarea
                    id="description"
                    v-model="form.description"
                    rows="5"
                    placeholder="Jelaskan detail insiden yang terjadi, termasuk dampak dan langkah yang sudah diambil..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.description }"
                  />
                  <p v-if="form.errors.description" class="mt-1 text-red-600">
                    {{ form.errors.description }}
                  </p>
                  <p class="text-sm text-slate-400">
                    Berikan informasi selengkap mungkin untuk membantu proses penanganan
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-4 lg:space-y-6">
            <!-- File Attachment -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
                  <IconCloudUp class="text-purple-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Lampiran Bukti</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Upload bukti pendukung insiden</p>
                </div>
              </div>

              <!-- Current File Info (Edit Mode) -->
              <div v-if="isEditing && incident?.attachment" class="mb-4">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="getFileIcon(incident.attachment)[2] + ' border ' + getFileIcon(incident.attachment)[3]">
                      <component
                        :is="getFileIcon(incident.attachment)[0]"
                        :class="getFileIcon(incident.attachment)[1]"
                        size="18"
                      />
                    </div>
                    <div class="flex-1">
                      <p class="font-medium text-slate-900">File Saat Ini</p>
                      <!-- <p class="text-sm text-slate-500 break-all">{{ incident.attachment.split('/').pop() }}</p> -->
                      <p class="text-sm text-slate-500">{{ incident.file_size }}</p>
                    </div>
                    <div class="flex gap-2">
                      <a
                        :href="`/storage/${incident.attachment}`"
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
                  Upload file baru untuk mengganti file lama
                </p>
              </div>

              <!-- File Upload -->
              <div>
                <label class="block font-medium text-slate-700 mb-2">
                  Lampiran Bukti
                  <span class="text-slate-500 text-xs">(Opsional, maksimal 2MB)</span>
                </label>

                <FileUpload
                  ref="fileUploader"
                  name="attachment"
                  @select="onFileSelect"
                  :auto="true"
                  :customUpload="true"
                  :showUploadButton="false"
                  :showCancelButton="false"
                  :multiple="false"
                  accept=".jpg,.jpeg,.png,.pdf,.zip,.doc,.docx"
                  :maxFileSize="2097152"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.attachment }"
                >
                  <template #content="{ files }">
                    <div v-if="files[0]" class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                      <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start">
                          <div class="mt-1">
                            <component
                              :is="getFileIcon(files[0].name)[0]"
                              :class="getFileIcon(files[0].name)[1]"
                              class="mr-3"
                              size="18"
                            />
                          </div>
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
                        Drag & drop atau klik untuk memilih file
                      </p>
                      <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF, ZIP, DOC (Maks. 2MB)</p>
                    </div>
                  </template>
                </FileUpload>

                <small v-if="form.errors.attachment" class="p-error block mt-1">{{ form.errors.attachment }}</small>
              </div>
            </div>

            <!-- Status Management -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                  <IconClipboardList class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Manajemen</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Status dan penugasan</p>
                </div>
              </div>

              <div class="space-y-4 lg:space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                  <div>
                    <label for="status" class="block font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <Select
                      v-model="form.status"
                      :options="statusOptions"
                      optionLabel="label"
                      optionValue="value"
                      class="w-full"
                    />
                  </div>

                  <div>
                    <label for="priority" class="block font-medium text-slate-700 mb-2">Prioritas <span class="text-red-500">*</span></label>
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
                  <label for="assigned_to" class="block font-medium text-slate-700 mb-2">
                    Tugaskan Kepada
                  </label>
                  <Select
                    v-model="form.assigned_to"
                    :options="staffUserOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Pilih staf"
                    class="w-full"
                    :class="{ 'border-red-300': form.errors.assigned_to }"
                  />
                  <p v-if="form.errors.assigned_to" class="mt-1 text-red-600">
                    {{ form.errors.assigned_to }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Preview Card -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 lg:p-6">
              <h3 class="text-xl/6 font-semibold text-slate-700 mb-4">
                {{ isEditing ? 'Ringkasan Perubahan' : 'Preview Laporan' }}
              </h3>
              <div class="space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">ID Insiden:</span>
                  <span class="font-mono text-slate-700 bg-slate-200 px-2 py-1 rounded text-xs">
                    {{ isEditing ? incident.case_id : 'Auto Generated' }}
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">Pelapor:</span>
                  <span class="text-slate-700 truncate ml-2">{{ form.reporter_name || 'Belum diisi' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">Status:</span>
                  <Tag
                    :value="form.status"
                    :severity="getStatusSeverity(form.status)"
                    size="small"
                  />
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">Prioritas:</span>
                  <Tag
                    :value="form.priority"
                    :severity="getPrioritySeverity(form.priority)"
                    size="small"
                  />
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">Kategori:</span>
                  <span class="text-slate-700 truncate ml-2">{{ form.incident_type_id ? incidentTypeOptions.find(type => type.value === form.incident_type_id)?.label || 'Belum diisi' : 'Belum diisi' }}</span>
                </div>
                <div v-if="form.incident_at" class="flex justify-between items-start">
                  <span class="text-slate-500">Waktu:</span>
                  <span class="text-slate-700 text-right">{{ formatDateTime(form.incident_at) }}</span>
                </div>
                <div v-if="form.attachment || (isEditing && incident?.attachment)" class="flex justify-between items-center">
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
                class="bg-blue-600 hover:bg-blue-800 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </button>
            </div>

          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
