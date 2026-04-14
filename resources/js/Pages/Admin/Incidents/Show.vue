<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Show.vue

import { useResponsive } from '@/Composables/useResponsive'
import { formatDatetime } from '@/utils/date'
import { router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  incident: Object,
  staffUsers: Array,
})

const { isDesktop } = useResponsive()

// --- Management form ---
const managementForm = useForm({
  status: props.incident.status,
  priority: props.incident.priority,
  assigned_to: props.incident.assigned_to,
})

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

const staffUserOptions = [
  { label: 'Tidak ditugaskan', value: null },
  ...props.staffUsers.map((user) => ({ label: user.name, value: user.id })),
]

const submitManagement = () => {
  managementForm.put(
    route('admin.incidents.management.update', props.incident.id),
    { preserveScroll: true },
  )
}

const getPriorityButtonClasses = (priority, isSelected) => {
  const base =
    'p-2 font-medium border rounded-lg transition-all duration-200 text-center text-sm'
  const selected = {
    Rendah:
      'border-green-500 bg-green-50 text-green-700 ring-2 ring-green-500 ring-opacity-20',
    Sedang:
      'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500 ring-opacity-20',
    Tinggi:
      'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-500 ring-opacity-20',
    Kritikal:
      'border-red-500 bg-red-50 text-red-700 ring-2 ring-red-500 ring-opacity-20',
  }
  const unselected = {
    Rendah:
      'border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300',
    Sedang:
      'border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300',
    Tinggi:
      'border-orange-200 text-orange-600 hover:bg-orange-50 hover:border-orange-300',
    Kritikal:
      'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300',
  }
  return `${base} ${isSelected ? selected[priority] : unselected[priority]}`
}

// --- Add log form ---
const logAttachmentMode = ref('none') // 'none' | 'file' | 'link'
const logFileInput = ref(null)

const logForm = useForm({
  log_message: '',
  is_public: false,
  attachment_type: null,
  attachment: null,
  attachment_link: '',
})

const onLogFileChange = (e) => {
  logForm.attachment = e.target.files[0] ?? null
}

const submitLog = () => {
  logForm.attachment_type =
    logAttachmentMode.value !== 'none' ? logAttachmentMode.value : null
  logForm.post(route('admin.incidents.logs.store', props.incident.id), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      logForm.reset()
      logForm.attachment = null
      logAttachmentMode.value = 'none'
      if (logFileInput.value) logFileInput.value.value = ''
    },
  })
}

// --- Edit log ---
const editingLogId = ref(null)
const editAttachmentMode = ref('none')
const editFileInput = ref(null)

const editForm = useForm({
  log_message: '',
  is_public: false,
  attachment_type: null,
  attachment: null,
  attachment_link: '',
})

const startEdit = (log) => {
  editingLogId.value = log.id
  editForm.log_message = log.log_message
  editForm.is_public = log.is_public
  editForm.attachment = null
  if (log.attachment_type === 'link') {
    editAttachmentMode.value = 'link'
    editForm.attachment_link = log.attachment
  } else if (log.attachment_type === 'file') {
    editAttachmentMode.value = 'file'
    editForm.attachment_link = ''
  } else {
    editAttachmentMode.value = 'none'
    editForm.attachment_link = ''
  }
}

const cancelEdit = () => {
  editingLogId.value = null
  editForm.reset()
  editAttachmentMode.value = 'none'
}

const onEditFileChange = (e) => {
  editForm.attachment = e.target.files[0] ?? null
}

const submitEdit = (log) => {
  editForm.attachment_type =
    editAttachmentMode.value !== 'none' ? editAttachmentMode.value : 'none'
  console.log(editForm)
  editForm.put(
    route('admin.incidents.logs.update', {
      incident: props.incident.id,
      log: log.id,
    }),
    {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: () => {
        editingLogId.value = null
        editForm.reset()
        editAttachmentMode.value = 'none'
      },
    },
  )
}

// --- Delete log ---
const deleteLogId = ref(null)

const confirmDeleteLog = (logId) => {
  deleteLogId.value = logId
}

const handleDeleteLog = () => {
  router.delete(
    route('admin.incidents.logs.destroy', {
      incident: props.incident.id,
      log: deleteLogId.value,
    }),
    {
      preserveScroll: true,
      onSuccess: () => {
        deleteLogId.value = null
      },
    },
  )
}

// --- Helpers ---
const getStatusSeverity = (status) => {
  const map = {
    Baru: 'info',
    Diverifikasi: 'primary',
    'Dalam Penyelidikan': 'warn',
    Selesai: 'success',
    Ditutup: 'secondary',
  }
  return map[status] || 'info'
}

const getPrioritySeverity = (priority) => {
  const map = {
    Rendah: 'success',
    Sedang: 'info',
    Tinggi: 'warn',
    Kritikal: 'danger',
  }
  return map[priority] || 'info'
}

const isEdited = (log) =>
  log.updated_at &&
  log.created_at &&
  new Date(log.updated_at) > new Date(log.created_at)

const isExternalUrl = (path) =>
  path && (path.startsWith('http://') || path.startsWith('https://'))

const logAttachmentUrl = (log) => {
  if (!log.attachment) return null
  if (log.attachment_type === 'link' || isExternalUrl(log.attachment))
    return log.attachment
  return `/storage/${log.attachment}`
}

const logAttachmentLabel = (log) => {
  if (log.attachment_type === 'link') return 'Buka Link'
  return log.attachment ? log.attachment.split('/').pop() : 'Lihat Lampiran'
}
</script>

<template>
  <AdminLayout :title="`Detail Insiden: ${incident.case_id}`">
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
              Detail Insiden
            </h2>
            <div class="mt-2 flex items-center gap-3">
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
              v-if="incident.status !== 'Ditutup'"
              severity="primary"
              @click="
                () => router.get(route('admin.incidents.edit', incident.id))
              "
              class="w-full lg:w-auto"
            >
              <IconEdit size="16" />
              Edit Insiden
            </Button>
          </div>
        </div>
      </div>

      <!-- Body: 3+2 grid on large screens -->
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-5 lg:gap-6">
        <!-- Main Content (3 cols) -->
        <div class="space-y-4 lg:col-span-3 lg:space-y-6">
          <!-- Kelola Insiden -->
          <div
            v-if="incident.status !== 'Ditutup'"
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
          >
            <div class="mb-4 flex items-center lg:mb-6">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
              >
                <IconSettings
                  class="text-orange-600"
                  :size="!isDesktop ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="text-xl/6 font-semibold text-slate-900">
                  Kelola Insiden
                </h3>
                <p class="text-xs text-slate-600 lg:text-sm">
                  Perbarui status dan penugasan
                </p>
              </div>
            </div>

            <form @submit.prevent="submitManagement" class="space-y-4">
              <div>
                <label class="mb-2 block font-medium text-slate-700"
                  >Status</label
                >
                <Select
                  v-model="managementForm.status"
                  :options="statusOptions"
                  optionLabel="label"
                  optionValue="value"
                  class="w-full"
                />
              </div>

              <div>
                <label class="mb-2 block font-medium text-slate-700"
                  >Prioritas</label
                >
                <div class="grid grid-cols-2 gap-2">
                  <button
                    v-for="p in priorityOptions"
                    :key="p.value"
                    type="button"
                    :class="
                      getPriorityButtonClasses(
                        p.value,
                        managementForm.priority === p.value,
                      )
                    "
                    @click="managementForm.priority = p.value"
                  >
                    {{ p.label }}
                  </button>
                </div>
              </div>

              <div>
                <label class="mb-2 block font-medium text-slate-700"
                  >Ditugaskan ke</label
                >
                <Select
                  v-model="managementForm.assigned_to"
                  :options="staffUserOptions"
                  optionLabel="label"
                  optionValue="value"
                  class="w-full"
                />
              </div>

              <Button
                type="submit"
                severity="primary"
                :disabled="managementForm.processing"
                class="w-full"
              >
                <IconLoader3
                  v-if="managementForm.processing"
                  class="animate-spin"
                  size="16"
                />
                <IconDeviceFloppy v-else size="16" />
                {{
                  managementForm.processing
                    ? 'Menyimpan...'
                    : 'Simpan Perubahan'
                }}
              </Button>
            </form>
          </div>

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
                <label class="mb-2 block font-medium text-slate-700"
                  >Nama Pelapor</label
                >
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
                  <p class="text-slate-900">{{ incident.reporter_name }}</p>
                </div>
              </div>
              <div>
                <label class="mb-2 block font-medium text-slate-700"
                  >Email Pelapor</label
                >
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
                  <p class="text-slate-900">{{ incident.reporter_email }}</p>
                </div>
              </div>
              <div class="md:col-span-2">
                <label class="mb-2 block font-medium text-slate-700"
                  >Nomor Telepon</label
                >
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
                  <p class="text-slate-900">
                    {{ incident.reporter_phone || 'Tidak tersedia' }}
                  </p>
                </div>
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
            <div class="space-y-6">
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
                <div>
                  <label class="mb-2 block font-medium text-slate-700"
                    >Kategori Insiden</label
                  >
                  <div
                    class="rounded-lg border border-slate-200 bg-slate-50 p-2"
                  >
                    <p class="text-slate-900">
                      {{ incident.incident_type.name }}
                    </p>
                  </div>
                </div>
                <div>
                  <label class="mb-2 block font-medium text-slate-700"
                    >Waktu Kejadian</label
                  >
                  <div
                    class="rounded-lg border border-slate-200 bg-slate-50 p-2"
                  >
                    <p class="text-slate-900">
                      {{ formatDatetime(incident.incident_at) }}
                    </p>
                  </div>
                </div>
                <div>
                  <label class="mb-2 block font-medium text-slate-700"
                    >Waktu Dilaporkan</label
                  >
                  <div
                    class="rounded-lg border border-slate-200 bg-slate-50 p-2"
                  >
                    <p class="text-slate-900">
                      {{ formatDatetime(incident.reported_at) }}
                    </p>
                  </div>
                </div>
                <div>
                  <label class="mb-2 block font-medium text-slate-700"
                    >Ditugaskan Kepada</label
                  >
                  <div
                    class="rounded-lg border border-slate-200 bg-slate-50 p-2"
                  >
                    <p class="text-slate-900">
                      {{ incident.assigned_user?.name || 'Belum ditugaskan' }}
                    </p>
                  </div>
                </div>
              </div>
              <div>
                <label class="mb-2 block font-medium text-slate-700"
                  >Deskripsi Insiden</label
                >
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                  <p class="whitespace-pre-wrap leading-relaxed text-slate-900">
                    {{ incident.description }}
                  </p>
                </div>
              </div>
              <div v-if="incident.attachment">
                <label class="mb-2 block font-medium text-slate-700"
                  >Lampiran</label
                >
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
                  <a
                    :href="`/storage/${incident.attachment}`"
                    target="_blank"
                    class="inline-flex items-center font-medium text-blue-600 hover:text-blue-700"
                  >
                    <IconPaperclip size="18" class="mr-2" />
                    Lihat Lampiran
                  </a>
                </div>
              </div>
              <div v-if="incident.access_token">
                <label class="mb-2 block font-medium text-slate-700"
                  >Akses Token</label
                >
                <div
                  class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-2"
                >
                  <p class="break-all font-mono text-sm text-slate-900">
                    {{ incident.access_token }}
                  </p>
                  <a
                    :href="
                      route('incident.show', {
                        caseId: incident.case_id,
                        token: incident.access_token,
                      })
                    "
                    target="_blank"
                    class="ml-2 shrink-0 font-medium text-blue-600 hover:text-blue-700"
                    title="Link Publik"
                  >
                    <IconTicket size="18" />
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar (2 cols) -->
        <div class="space-y-4 lg:col-span-2 lg:space-y-6">
          <!-- Riwayat Penanganan -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
          >
            <div class="mb-4 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-purple-200 bg-purple-50"
              >
                <IconTimeline class="text-purple-600" size="20" />
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Riwayat Penanganan</h3>
                <p class="text-xs text-slate-500">
                  {{ incident.incident_logs.length }} catatan
                </p>
              </div>
            </div>

            <!-- Add new log form -->
            <div
              v-if="incident.status !== 'Ditutup'"
              class="mb-5 rounded-lg border border-blue-100 bg-blue-50 p-4"
            >
              <h4
                class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800"
              >
                <IconSticker2 size="16" class="text-blue-600" />
                Tambah Catatan Penanganan
              </h4>

              <form @submit.prevent="submitLog" class="space-y-3">
                <div>
                  <Textarea
                    v-model="logForm.log_message"
                    placeholder="Tulis catatan penanganan, update progres, atau informasi penting..."
                    rows="4"
                    class="w-full text-sm"
                    :class="{ 'border-red-300': logForm.errors.log_message }"
                    required
                  />
                  <p
                    v-if="logForm.errors.log_message"
                    class="mt-1 text-xs text-red-500"
                  >
                    {{ logForm.errors.log_message }}
                  </p>
                </div>

                <!-- Attachment mode selector -->
                <div>
                  <label
                    class="mb-1.5 block text-xs font-medium text-slate-600"
                  >
                    Lampiran (opsional)
                  </label>
                  <div class="flex gap-2">
                    <button
                      v-for="mode in ['none', 'file', 'link']"
                      :key="mode"
                      type="button"
                      class="rounded border px-2.5 py-1 text-xs font-medium transition"
                      :class="
                        logAttachmentMode === mode
                          ? 'border-blue-500 bg-blue-600 text-white'
                          : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:text-slate-700'
                      "
                      @click="logAttachmentMode = mode"
                    >
                      {{
                        mode === 'none'
                          ? 'Tanpa Lampiran'
                          : mode === 'file'
                            ? 'Upload File'
                            : 'Link URL'
                      }}
                    </button>
                  </div>
                  <div v-if="logAttachmentMode === 'file'" class="mt-2">
                    <input
                      ref="logFileInput"
                      type="file"
                      class="w-full rounded border border-slate-200 bg-white p-1.5 text-xs text-slate-600 file:mr-2 file:rounded file:border-0 file:bg-blue-50 file:px-2 file:py-1 file:text-xs file:text-blue-600"
                      @change="onLogFileChange"
                    />
                    <p
                      v-if="logForm.errors.attachment"
                      class="mt-1 text-xs text-red-500"
                    >
                      {{ logForm.errors.attachment }}
                    </p>
                  </div>
                  <div v-if="logAttachmentMode === 'link'" class="mt-2">
                    <InputText
                      v-model="logForm.attachment_link"
                      placeholder="https://..."
                      class="w-full text-sm"
                    />
                    <p
                      v-if="logForm.errors.attachment_link"
                      class="mt-1 text-xs text-red-500"
                    >
                      {{ logForm.errors.attachment_link }}
                    </p>
                  </div>
                </div>

                <!-- is_public toggle -->
                <label class="flex cursor-pointer items-center gap-2 text-sm">
                  <ToggleSwitch v-model="logForm.is_public" />
                  <div>
                    <span class="font-medium text-slate-700"
                      >Tampilkan ke pelapor</span
                    >
                    <p class="text-xs text-slate-500">
                      Catatan publik akan terlihat di halaman tiket pelapor
                    </p>
                  </div>
                </label>

                <Button
                  type="submit"
                  severity="primary"
                  :disabled="logForm.processing"
                  class="w-full"
                >
                  <IconLoader3
                    v-if="logForm.processing"
                    class="animate-spin"
                    size="16"
                  />
                  <IconSticker2 v-else size="16" />
                  {{ logForm.processing ? 'Menambahkan...' : 'Tambah Catatan' }}
                </Button>
              </form>
            </div>

            <!-- Log entries -->
            <div v-if="incident.incident_logs.length > 0" class="space-y-3">
              <div
                v-for="log in incident.incident_logs"
                :key="log.id"
                class="rounded-lg border border-slate-200 bg-slate-50 p-3"
              >
                <!-- Inline edit form -->
                <template v-if="editingLogId === log.id">
                  <div class="space-y-3">
                    <Textarea
                      v-model="editForm.log_message"
                      rows="3"
                      class="w-full text-sm"
                      placeholder="Edit catatan..."
                    />

                    <!-- Attachment mode -->
                    <div>
                      <label
                        class="mb-1 block text-xs font-medium text-slate-600"
                        >Lampiran</label
                      >
                      <div class="flex gap-2">
                        <button
                          v-for="mode in ['none', 'file', 'link']"
                          :key="mode"
                          type="button"
                          class="rounded border px-2 py-1 text-xs transition"
                          :class="
                            editAttachmentMode === mode
                              ? 'border-blue-500 bg-blue-50 text-blue-700'
                              : 'border-slate-200 text-slate-500 hover:border-slate-300'
                          "
                          @click="editAttachmentMode = mode"
                        >
                          {{
                            mode === 'none'
                              ? 'Hapus'
                              : mode === 'file'
                                ? 'File'
                                : 'Link'
                          }}
                        </button>
                      </div>
                      <div v-if="editAttachmentMode === 'file'" class="mt-2">
                        <input
                          ref="editFileInput"
                          type="file"
                          class="w-full text-xs text-slate-600"
                          @change="onEditFileChange"
                        />
                      </div>
                      <div v-if="editAttachmentMode === 'link'" class="mt-2">
                        <InputText
                          v-model="editForm.attachment_link"
                          placeholder="https://..."
                          class="w-full text-sm"
                        />
                      </div>
                    </div>

                    <!-- is_public toggle -->
                    <label
                      class="flex cursor-pointer items-center gap-2 text-sm"
                    >
                      <ToggleSwitch v-model="editForm.is_public" />
                      <span class="text-slate-600"
                        >Tampilkan ke pelapor (publik)</span
                      >
                    </label>

                    <p
                      v-if="editForm.errors.log_message"
                      class="text-xs text-red-500"
                    >
                      {{ editForm.errors.log_message }}
                    </p>

                    <div class="flex gap-2">
                      <Button
                        size="small"
                        severity="primary"
                        :disabled="editForm.processing"
                        @click="submitEdit(log)"
                        class="flex-1"
                      >
                        <IconLoader3
                          v-if="editForm.processing"
                          class="animate-spin"
                          size="14"
                        />
                        <IconCheck v-else size="14" />
                        Simpan
                      </Button>
                      <Button
                        size="small"
                        severity="secondary"
                        @click="cancelEdit"
                        class="flex-1"
                      >
                        Batal
                      </Button>
                    </div>
                  </div>
                </template>

                <!-- Normal display -->
                <template v-else>
                  <!-- Header -->
                  <div class="mb-2 flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2">
                      <div
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600"
                      >
                        {{ log.user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                      </div>
                      <div>
                        <span class="text-sm font-medium text-slate-800">{{
                          log.user?.name
                        }}</span>
                        <div
                          class="flex items-center gap-1.5 text-xs text-slate-400"
                        >
                          <span>{{ formatDatetime(log.created_at) }}</span>
                          <span v-if="isEdited(log)" class="italic"
                            >· Diedit {{ formatDatetime(log.updated_at) }}</span
                          >
                        </div>
                      </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-1">
                      <Tag
                        :value="log.is_public ? 'Publik' : 'Internal'"
                        :severity="log.is_public ? 'success' : 'secondary'"
                        class="!text-xs"
                      />
                      <Button
                        icon="pi pi-pencil"
                        size="small"
                        severity="secondary"
                        text
                        rounded
                        v-tooltip="'Edit catatan'"
                        @click="startEdit(log)"
                      />
                      <Button
                        icon="pi pi-trash"
                        size="small"
                        severity="danger"
                        text
                        rounded
                        v-tooltip="'Hapus catatan'"
                        @click="confirmDeleteLog(log.id)"
                      />
                    </div>
                  </div>

                  <!-- Message body -->
                  <p
                    class="whitespace-pre-wrap text-sm leading-relaxed text-slate-700"
                  >
                    {{ log.log_message }}
                  </p>

                  <!-- Attachment -->
                  <div v-if="log.attachment" class="mt-2">
                    <a
                      :href="logAttachmentUrl(log)"
                      target="_blank"
                      rel="noopener"
                      class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-blue-600 hover:text-blue-800"
                    >
                      <IconExternalLink
                        v-if="log.attachment_type === 'link'"
                        size="12"
                      />
                      <IconPaperclip v-else size="12" />
                      {{ logAttachmentLabel(log) }}
                    </a>
                  </div>
                </template>
              </div>
            </div>

            <div v-else class="mb-5 py-6 text-center">
              <IconHistory class="mx-auto mb-2 text-slate-300" size="32" />
              <p class="text-sm text-slate-500">Belum ada riwayat penanganan</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Log Confirmation Dialog -->
    <Dialog
      v-model:visible="deleteLogId"
      modal
      header="Hapus Catatan"
      :style="{ width: '360px' }"
    >
      <p class="text-slate-600">
        Catatan ini akan dihapus permanen beserta lampirannya. Lanjutkan?
      </p>
      <template #footer>
        <Button severity="secondary" @click="deleteLogId = null">Batal</Button>
        <Button severity="danger" @click="handleDeleteLog">Ya, Hapus</Button>
      </template>
    </Dialog>
  </AdminLayout>
</template>
