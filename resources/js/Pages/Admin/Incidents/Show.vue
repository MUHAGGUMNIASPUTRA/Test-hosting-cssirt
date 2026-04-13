<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Show.vue

import { router, useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  incident: Object,
  staffUsers: Array,
})

const { isDesktop } = useResponsive()

const logForm = useForm({
  log_message: '',
})

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

// Transform staff users for select
const staffUserOptions = [
  { label: 'Tidak ditugaskan', value: null },
  ...props.staffUsers.map((user) => ({
    label: user.name,
    value: user.id,
  })),
]

const submitLog = () => {
  logForm.post(route('admin.incidents.logs.store', props.incident.id), {
    preserveScroll: true,
    onSuccess: () => {
      logForm.reset()
    },
  })
}

const submitManagement = () => {
  managementForm.put(
    route('admin.incidents.management.update', props.incident.id),
    {
      preserveScroll: true,
      onSuccess: () => {},
    },
  )
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

const getPrioritySeverity = (priority) => {
  const severities = {
    Rendah: 'success',
    Sedang: 'info',
    Tinggi: 'warn',
    Kritikal: 'danger',
  }
  return severities[priority] || 'info'
}

// Get priority button styles based on severity
const getPriorityButtonClasses = (priority, isSelected) => {
  const baseClasses =
    'p-2 font-medium border rounded-lg transition-all duration-200 text-center'

  if (isSelected) {
    const selectedClasses = {
      Rendah:
        'border-green-500 bg-green-50 text-green-700 ring-2 ring-green-500 ring-opacity-20',
      Sedang:
        'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500 ring-opacity-20',
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
        'border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300',
      Tinggi:
        'border-orange-200 text-orange-600 hover:bg-orange-50 hover:border-orange-300',
      Kritikal:
        'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300',
    }
    return `${baseClasses} ${unselectedClasses[priority] || unselectedClasses['Sedang']}`
  }
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getLogIcon = (index, total) => {
  if (index === 0) return 'add_circle'
  if (index === total - 1) return 'sticky_note_2'
  return 'radio_button_checked'
}

const getLogIconColor = (index, total) => {
  if (index === 0) return 'text-green-600 bg-green-50 border-green-200'
  if (index === total - 1) return 'text-blue-600 bg-blue-50 border-blue-200'
  return 'text-slate-600 bg-slate-50 border-slate-200'
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
                      {{ formatDateTime(incident.incident_at) }}
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
                      {{ formatDateTime(incident.reported_at) }}
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
                  <p
                    class="whitespace-pre-wrap break-all leading-relaxed text-slate-900"
                  >
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
                    class="font-medium text-blue-600 hover:text-blue-700"
                    title="Link Publik"
                  >
                    <IconTicket size="18" />
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
          <!-- Timeline/Logs -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
          >
            <div class="mb-6 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-purple-200 bg-purple-50 lg:h-12 lg:w-12"
              >
                <IconTimeline
                  class="text-purple-600"
                  :size="!isDesktop ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="text-xl/6 font-semibold text-slate-900">
                  Riwayat Penanganan
                </h3>
                <p class="text-xs text-slate-600 lg:text-sm">
                  Log aktivitas penanganan
                </p>
              </div>
            </div>

            <!-- Timeline -->
            <div
              v-if="incident.incident_logs.length > 0"
              class="mb-4 space-y-4 lg:mb-6"
            >
              <div
                v-for="(log, index) in incident.incident_logs"
                :key="log.id"
                class="relative flex items-start gap-4"
              >
                <!-- Vertical line connector -->
                <div
                  v-if="index < incident.incident_logs.length - 1"
                  class="absolute left-4 top-8 h-full w-px bg-slate-200"
                ></div>

                <!-- Icon -->
                <div
                  class="relative z-10 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border"
                  :class="getLogIconColor(index, incident.incident_logs.length)"
                >
                  <span class="material-symbols-outlined !text-lg">{{
                    getLogIcon(index, incident.incident_logs.length)
                  }}</span>
                </div>

                <!-- Content -->
                <div class="min-w-0 flex-1 pb-0">
                  <div class="flex items-center gap-2">
                    <p class="font-medium text-slate-900">
                      {{ log.user.name }}
                    </p>
                    <span class="text-xs text-slate-400">{{
                      formatDate(log.created_at)
                    }}</span>
                  </div>
                  <p class="text-sm leading-relaxed text-slate-500">
                    {{ log.log_message }}
                  </p>
                </div>
              </div>
            </div>

            <div v-else class="mb-6 py-8 text-center">
              <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 lg:h-14 lg:w-14"
              >
                <IconHistory
                  class="text-slate-400"
                  :size="!isDesktop ? 18 : undefined"
                />
              </div>
              <p class="text-slate-500">Belum ada riwayat penanganan</p>
              <p class="mt-1 text-slate-400">
                Log aktivitas akan muncul di sini
              </p>
            </div>

            <!-- Add new log form -->
            <form
              v-if="incident.status != 'Ditutup'"
              @submit.prevent="submitLog"
              class="space-y-4"
            >
              <div>
                <label
                  for="log_message"
                  class="mb-2 block font-medium text-slate-700"
                >
                  Tambah Catatan Baru
                </label>
                <Textarea
                  id="log_message"
                  v-model="logForm.log_message"
                  placeholder="Tambahkan catatan atau update status penanganan..."
                  rows="3"
                  class="w-full"
                  :class="{
                    'border-red-300 focus:border-red-500 focus:ring-red-500':
                      logForm.errors.log_message,
                  }"
                  required
                />
                <p v-if="logForm.errors.log_message" class="mt-1 text-red-600">
                  {{ logForm.errors.log_message }}
                </p>
              </div>

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
                <IconSticker2 v-else :size="16" />
                {{ logForm.processing ? 'Menambahkan...' : 'Tambah Catatan' }}
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
