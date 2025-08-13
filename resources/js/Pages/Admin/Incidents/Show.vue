<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Show.vue

import { Link, useForm } from '@inertiajs/vue3';
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  incident: Object,
  staffUsers: Array,
});

const { isDesktop } = useResponsive();

const logForm = useForm({
  log_message: '',
});

const managementForm = useForm({
  status: props.incident.status,
  priority: props.incident.priority,
  assigned_to: props.incident.assigned_to,
});

const statusOptions = [
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' }
];

const priorityOptions = [
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' }
];

// Transform staff users for select
const staffUserOptions = [
  { label: 'Tidak ditugaskan', value: null },
  ...props.staffUsers.map(user => ({
    label: user.name,
    value: user.id
  }))
];

const submitLog = () => {
  logForm.post(route('admin.incidents.logs.store', props.incident.id), {
    preserveScroll: true,
    onSuccess: () => {
      logForm.reset();
    },
  });
};

const submitManagement = () => {
  managementForm.put(route('admin.incidents.management.update', props.incident.id), {
    preserveScroll: true,
    onSuccess: () => {}
  });
};

const getStatusSeverity = (status) => {
  const severities = {
    'Baru': 'info',
    'Diverifikasi': 'primary',
    'Dalam Penyelidikan': 'warn',
    'Selesai': 'success',
    'Ditutup': 'secondary'
  };
  return severities[status] || 'info';
};

const getPrioritySeverity = (priority) => {
  const severities = {
    'Rendah': 'success',
    'Sedang': 'info',
    'Tinggi': 'warn',
    'Kritikal': 'danger'
  };
  return severities[priority] || 'info';
};

// Get priority button styles based on severity
const getPriorityButtonClasses = (priority, isSelected) => {
  const baseClasses = 'p-2 font-medium border rounded-lg transition-all duration-200 text-center'

  if (isSelected) {
    const selectedClasses = {
      'Rendah': 'border-green-500 bg-green-50 text-green-700 ring-2 ring-green-500 ring-opacity-20',
      'Sedang': 'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500 ring-opacity-20',
      'Tinggi': 'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-500 ring-opacity-20',
      'Kritikal': 'border-red-500 bg-red-50 text-red-700 ring-2 ring-red-500 ring-opacity-20'
    }
    return `${baseClasses} ${selectedClasses[priority] || selectedClasses['Sedang']}`
  } else {
    const unselectedClasses = {
      'Rendah': 'border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300',
      'Sedang': 'border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300',
      'Tinggi': 'border-orange-200 text-orange-600 hover:bg-orange-50 hover:border-orange-300',
      'Kritikal': 'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300'
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
    minute: '2-digit'
  });
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

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
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Detail Insiden</h2>
            <div class="flex items-center gap-3 mt-2">
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
            <Link
              :href="route('admin.incidents.index')"
              class="bg-slate-100 hover:bg-slate-200 text-slate-600 w-full lg:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
            >
              <IconArrowLeft size="16"/>
                Kembali
            </Link>
            <Link
              :href="route('admin.incidents.edit', incident.id)"
              class="bg-blue-600 hover:bg-blue-800 text-white w-full lg:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
            >
              <IconEdit size="16"/>
              Edit Insiden
            </Link>
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
                <label class="block font-medium text-slate-700 mb-2">Nama Pelapor</label>
                <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                  <p class="text-slate-900">{{ incident.reporter_name }}</p>
                </div>
              </div>

              <div>
                <label class="block font-medium text-slate-700 mb-2">Email Pelapor</label>
                <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                  <p class="text-slate-900">{{ incident.reporter_email }}</p>
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="block font-medium text-slate-700 mb-2">Nomor Telepon</label>
                <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                  <p class="text-slate-900">{{ incident.reporter_phone || 'Tidak tersedia' }}</p>
                </div>
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

            <div class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                <div>
                  <label class="block font-medium text-slate-700 mb-2">Kategori Insiden</label>
                  <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                    <p class="text-slate-900">{{ incident.incident_type.name }}</p>
                  </div>
                </div>

                <div>
                  <label class="block font-medium text-slate-700 mb-2">Waktu Kejadian</label>
                  <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                    <p class="text-slate-900">{{ formatDateTime(incident.incident_at) }}</p>
                  </div>
                </div>

                <div>
                  <label class="block font-medium text-slate-700 mb-2">Waktu Dilaporkan</label>
                  <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                    <p class="text-slate-900">{{ formatDateTime(incident.reported_at) }}</p>
                  </div>
                </div>

                <div>
                  <label class="block font-medium text-slate-700 mb-2">Ditugaskan Kepada</label>
                  <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                    <p class="text-slate-900">{{ incident.assigned_user?.name || 'Belum ditugaskan' }}</p>
                  </div>
                </div>
              </div>

              <div>
                <label class="block font-medium text-slate-700 mb-2">Deskripsi Insiden</label>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                  <p class="text-slate-900 whitespace-pre-wrap leading-relaxed">{{ incident.description }}</p>
                </div>
              </div>

              <div v-if="incident.attachment">
                <label class="block font-medium text-slate-700 mb-2">Lampiran</label>
                <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg">
                  <a
                    :href="`/storage/${incident.attachment}`"
                    target="_blank"
                    class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium"
                  >
                    <IconPaperclip size="18" class="mr-2" />
                    Lihat Lampiran
                  </a>
                </div>
              </div>

              <div v-if="incident.access_token">
                <label class="block font-medium text-slate-700 mb-2">Akses Token</label>
                <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg flex justify-between items-center">
                  <p class="text-slate-900 whitespace-pre-wrap leading-relaxed break-all">{{ incident.access_token }}</p>
                  <a
                    :href="route('incident.show', { caseId: incident.case_id, token: incident.access_token })"
                    target="_blank"
                    class="text-blue-600 hover:text-blue-800 font-medium"
                    title="Link Publik"
                  >
                    <IconTicket size="18"/>
                  </a>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
          <!-- Timeline/Logs -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
                <IconTimeline class="text-purple-600" :size="!isDesktop ? 18 : undefined"/>
              </div>
              <div class="ml-3">
                <h3 class="text-xl/6 font-semibold text-slate-900">Riwayat Penanganan</h3>
                <p class="text-xs lg:text-sm text-slate-600">Log aktivitas penanganan</p>
              </div>
            </div>

            <!-- Timeline -->
            <div v-if="incident.incident_logs.length > 0" class="space-y-4 mb-4 lg:mb-6">
              <div
                v-for="(log, index) in incident.incident_logs"
                :key="log.id"
                class="relative flex items-start gap-4"
              >
                <!-- Vertical line connector -->
                <div v-if="index < incident.incident_logs.length - 1"class="absolute left-4 top-8 h-full w-px bg-slate-200"></div>

                <!-- Icon -->
                <div
                  class="relative z-10 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border"
                  :class="getLogIconColor(index, incident.incident_logs.length)"
                >
                  <span class="material-symbols-outlined !text-lg">{{ getLogIcon(index, incident.incident_logs.length) }}</span>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0 pb-0">
                  <div class="flex items-center gap-2">
                    <p class="font-medium text-slate-900">{{ log.user.name }}</p>
                    <span class="text-xs text-slate-400">{{ formatDate(log.created_at) }}</span>
                  </div>
                  <p class="text-sm text-slate-500 leading-relaxed">{{ log.log_message }}</p>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8 mb-6">
              <div class="w-12 h-12 lg:w-14 lg:h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <IconHistory class="text-slate-400" :size="!isDesktop ? 18 : undefined"/>
              </div>
              <p class="text-slate-500">Belum ada riwayat penanganan</p>
              <p class="text-slate-400 mt-1">Log aktivitas akan muncul di sini</p>
            </div>

            <!-- Add new log form -->
            <form @submit.prevent="submitLog" class="space-y-4">
              <div>
                <label for="log_message" class="block font-medium text-slate-700 mb-2">
                  Tambah Catatan Baru
                </label>
                <Textarea
                  id="log_message"
                  v-model="logForm.log_message"
                  placeholder="Tambahkan catatan atau update status penanganan..."
                  rows="3"
                  class="w-full"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': logForm.errors.log_message }"
                  required
                />
                <p v-if="logForm.errors.log_message" class="mt-1 text-red-600">
                  {{ logForm.errors.log_message }}
                </p>
              </div>

              <button
                type="submit"
                :disabled="logForm.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <IconLoader3 v-if="logForm.processing" class="animate-spin" size="16"/>
                <IconSticker2 v-else :size="16" />
                {{ logForm.processing ? 'Menambahkan...' : 'Tambah Catatan' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
