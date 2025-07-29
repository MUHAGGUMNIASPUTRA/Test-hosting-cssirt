<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  incidentTypes: Array,
  staffUsers: Array,
})

// Format current date properly for the form
const formatDateForInput = (date) => {
  const d = new Date(date)
  return d.toISOString().slice(0, 16) // Returns "YYYY-MM-DDTHH:MM"
}

const form = useForm({
  reporter_name: '',
  reporter_email: '',
  reporter_phone: '',
  incident_type_id: null,
  incident_at: new Date(),
  description: '',
  status: 'Baru',
  priority: 'Sedang',
  assigned_to: null,
})

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

  form.transform(() => formData).post(route('admin.incidents.store'))
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

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('id-ID')
}
</script>

<template>
  <AdminLayout title="Lapor Insiden Baru">
    <form @submit.prevent="submit">
      <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-2xl font-bold text-slate-900">Lapor Insiden Baru</h2>
              <p class="text-slate-600">Buat laporan insiden keamanan siber baru untuk ditindaklanjuti</p>
            </div>
            <div class="flex items-center space-x-3">
              <Button
                type="submit"
                :disabled="form.processing"
                :loading="form.processing"
                class="w-full"
                severity="primary"
              >
                <template #default>
                  <i class="pi pi-save"></i>
                  {{ form.processing ? 'Menyimpan...' : 'Simpan Laporan' }}
                </template>
              </Button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Reporter Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-blue-600">record_voice_over</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Informasi Pelapor</h3>
                  <p class="text-slate-600">Data kontak pelapor insiden</p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-red-600">e911_emergency</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Detail Insiden</h3>
                  <p class="text-slate-600">Informasi lengkap tentang insiden yang terjadi</p>
                </div>
              </div>

              <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="incident_type_id" class="block font-medium text-slate-700 mb-2">
                      Jenis Insiden <span class="text-red-500">*</span>
                    </label>
                    <Select
                      v-model="form.incident_type_id"
                      :options="incidentTypeOptions"
                      optionLabel="label"
                      optionValue="value"
                      placeholder="Pilih jenis insiden"
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
                  <p class="mt-1 text-slate-500">
                    Berikan informasi selengkap mungkin untuk membantu proses penanganan
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Status Management -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-green-600">markdown_paste</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Manajemen</h3>
                  <p class="text-slate-600">Status dan penugasan</p>
                </div>
              </div>

              <div class="space-y-6">
                <div>
                  <label for="status" class="block font-medium text-slate-700 mb-2">Status</label>
                  <Select
                    v-model="form.status"
                    :options="statusOptions"
                    optionLabel="label"
                    optionValue="value"
                    class="w-full"
                  />
                </div>

                <div>
                  <label class="block font-medium text-slate-700 mb-3">Prioritas</label>
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
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-6">
              <h3 class="font-medium text-slate-700 mb-4">Preview Laporan</h3>
              <div class="space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">ID Insiden:</span>
                  <span class="font-mono text-slate-700 bg-slate-200 px-2 py-1 rounded text-xs">
                    Auto Generated
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
                <div v-if="form.incident_at" class="flex justify-between items-start">
                  <span class="text-slate-500">Waktu:</span>
                  <span class="text-slate-700 text-right">{{ formatDateTime(form.incident_at) }}</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
