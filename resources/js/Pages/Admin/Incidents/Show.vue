<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  incident: Object,
  staffUsers: Array,
});

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
    <div class="space-y-4 sm:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Detail Insiden</h2>
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
              class="bg-slate-100 hover:bg-slate-200 text-slate-600 w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
            >
              <span class="material-symbols-outlined !text-xl">west</span>
                Kembali
            </Link>
            <Link
              :href="route('admin.incidents.edit', incident.id)"
              class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
            >
              <span class="material-symbols-outlined !text-xl">edit_notifications</span>
              Edit Insiden
            </Link>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
          <!-- Reporter Information -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-4 sm:mb-6">
              <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600">record_voice_over</span>
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Informasi Pelapor</h3>
                <p class="text-slate-600">Data kontak pelapor insiden</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
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
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-4 sm:mb-6">
              <div class="w-12 h-12 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600">e911_emergency</span>
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Detail Insiden</h3>
                <p class="text-slate-600">Informasi lengkap tentang insiden yang terjadi</p>
              </div>
            </div>

            <div class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                  <label class="block font-medium text-slate-700 mb-2">Jenis Insiden</label>
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
                    <i class="pi pi-paperclip mr-2"></i>
                    Lihat Lampiran
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Management Form -->
          <!-- <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600">markdown_paste</span>
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Manajemen Insiden</h3>
                <p class="text-slate-600">Update status dan penugasan insiden</p>
              </div>
            </div>

            <form @submit.prevent="submitManagement" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="status" class="block font-medium text-slate-700 mb-2">Status</label>
                  <Select
                    id="status"
                    v-model="managementForm.status"
                    :options="statusOptions"
                    optionLabel="label"
                    optionValue="value"
                    class="w-full"
                  />
                </div>

                <div>
                  <label for="assigned_to" class="block font-medium text-slate-700 mb-2">Ditugaskan Kepada</label>
                  <Select
                    id="assigned_to"
                    v-model="managementForm.assigned_to"
                    :options="staffUserOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Pilih Staf"
                    class="w-full"
                  />
                </div>
              </div>

              <div>
                <label class="block font-medium text-slate-700 mb-3">Prioritas</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                  <button
                    v-for="option in priorityOptions"
                    :key="option.value"
                    type="button"
                    @click="managementForm.priority = option.value"
                    :class="getPriorityButtonClasses(option.value, managementForm.priority === option.value)"
                  >
                    {{ option.label }}
                  </button>
                </div>
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  :disabled="managementForm.processing"
                  class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
                >
                  <span class="material-symbols-outlined !text-xl" :class="{ 'animate-spin': managementForm.processing }">
                    {{ managementForm.processing ? 'progress_activity' : 'save' }}
                  </span>
                  {{ managementForm.processing ? 'Menyimpan...' : 'Update Status' }}
                </button>
              </div>
            </form>
          </div> -->
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
          <!-- Summary Card -->
          <div class="bg-slate-50 rounded-xl border border-slate-200 p-6">
            <h3 class="font-medium text-slate-700 mb-4">Ringkasan Insiden</h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-slate-500">ID Insiden:</span>
                <span class="font-mono text-slate-700 bg-slate-200 px-2 py-1 rounded text-xs">
                  {{ incident.case_id }}
                </span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-slate-500">Status:</span>
                <Tag
                  :value="incident.status"
                  :severity="getStatusSeverity(incident.status)"
                  size="small"
                />
              </div>
              <div class="flex justify-between items-center">
                <span class="text-slate-500">Prioritas:</span>
                <Tag
                  :value="incident.priority"
                  :severity="getPrioritySeverity(incident.priority)"
                  size="small"
                />
              </div>
              <div class="flex justify-between items-start">
                <span class="text-slate-500">Dilaporkan:</span>
                <span class="text-slate-700 text-right">{{ formatDate(incident.reported_at) }}</span>
              </div>
              <div v-if="incident.resolved_at" class="flex justify-between items-start">
                <span class="text-slate-500">Diselesaikan:</span>
                <span class="text-slate-700 text-right">{{ formatDate(incident.resolved_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Timeline/Logs -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-4 sm:mb-6">
              <div class="w-12 h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600">timeline</span>
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Riwayat Penanganan</h3>
                <p class="text-slate-600">Log aktivitas penanganan</p>
              </div>
            </div>

            <!-- Timeline -->
            <div v-if="incident.incident_logs.length > 0" class="space-y-4 mb-4 sm:mb-6">
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
                  <span class="material-symbols-outlined !text-xl">{{ getLogIcon(index, incident.incident_logs.length) }}</span>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0 pb-0 sm:pb-4">
                  <div class="flex items-center gap-2 mb-1">
                    <p class="font-medium text-slate-900">{{ log.user.name }}</p>
                    <span class="text-xs text-slate-400">{{ formatDate(log.created_at) }}</span>
                  </div>
                  <p class="text-slate-700 leading-relaxed">{{ log.log_message }}</p>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8 mb-6">
              <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-slate-400">history</span>
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
                <span class="material-symbols-outlined !text-xl" :class="{ 'animate-spin': logForm.processing }">
                  {{ logForm.processing ? 'progress_activity' : 'note_stack' }}
                </span>
                {{ logForm.processing ? 'Menambahkan...' : 'Tambah Catatan' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
