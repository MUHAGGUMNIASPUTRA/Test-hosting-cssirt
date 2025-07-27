<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'

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
  { label: 'Rendah', value: 'Rendah', color: 'bg-blue-50 text-blue-700 border-blue-200' },
  { label: 'Sedang', value: 'Sedang', color: 'bg-yellow-50 text-yellow-700 border-yellow-200' },
  { label: 'Tinggi', value: 'Tinggi', color: 'bg-orange-50 text-orange-700 border-orange-200' },
  { label: 'Kritis', value: 'Kritis', color: 'bg-red-50 text-red-700 border-red-200' }
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
    { label: 'Pilih staf...', value: null },
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

const getPriorityColor = (priority) => {
  const option = priorityOptions.find(p => p.value === priority)
  return option ? option.color : 'bg-yellow-50 text-yellow-700 border-yellow-200'
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('id-ID')
}
</script>

<template>
  <AdminLayout title="Buat Laporan Insiden Baru">
    <form @submit.prevent="submit" class="space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-slate-900">Buat Laporan Insiden Baru</h1>
            <p class="text-slate-600 mt-1 text-sm">Laporkan insiden keamanan siber yang terjadi</p>
          </div>
          <div class="flex items-center space-x-3">
            <Button
              type="button"
              @click="$inertia.visit(route('admin.incidents.index'))"
              label="Batal"
              severity="secondary"
              size="small"
            />
            <Button
              type="submit"
              :disabled="form.processing"
              :loading="form.processing"
              size="small"
            >
              <template #default>
                <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                </svg>
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
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 ml-3">Informasi Pelapor</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="reporter_name" class="block text-sm font-medium text-slate-700 mb-2">
                  Nama Pelapor <span class="text-red-500">*</span>
                </label>
                <InputText
                  id="reporter_name"
                  v-model="form.reporter_name"
                  placeholder="Masukkan nama lengkap pelapor"
                  required
                  size="small"
                  class="w-full"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.reporter_name }"
                />
                <p v-if="form.errors.reporter_name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.reporter_name }}
                </p>
              </div>

              <div>
                <label for="reporter_email" class="block text-sm font-medium text-slate-700 mb-2">
                  Email Pelapor <span class="text-red-500">*</span>
                </label>
                <InputText
                  id="reporter_email"
                  v-model="form.reporter_email"
                  type="email"
                  placeholder="contoh@email.com"
                  required
                  size="small"
                  class="w-full"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.reporter_email }"
                />
                <p v-if="form.errors.reporter_email" class="mt-1 text-sm text-red-600">
                  {{ form.errors.reporter_email }}
                </p>
              </div>
            </div>
          </div>

          <!-- Incident Details -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 ml-3">Detail Insiden</h3>
            </div>

            <div class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="incident_type_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Jenis Insiden <span class="text-red-500">*</span>
                  </label>
                  <Select
                    v-model="form.incident_type_id"
                    :options="incidentTypeOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Pilih jenis insiden"
                    size="small"
                    class="w-full"
                    :class="{ 'border-red-300': form.errors.incident_type_id }"
                  />
                  <p v-if="form.errors.incident_type_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.incident_type_id }}
                  </p>
                </div>

                <div>
                  <label for="incident_at" class="block text-sm font-medium text-slate-700 mb-2">
                    Waktu Kejadian <span class="text-red-500">*</span>
                  </label>
                  <DatePicker
                    v-model="form.incident_at"
                    showTime
                    hourFormat="24"
                    dateFormat="dd/mm/yy"
                    placeholder="Pilih tanggal dan waktu"
                    size="small"
                    class="w-full"
                    :class="{ 'border-red-300': form.errors.incident_at }"
                  />
                  <p v-if="form.errors.incident_at" class="mt-1 text-sm text-red-600">
                    {{ form.errors.incident_at }}
                  </p>
                </div>
              </div>

              <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                  Deskripsi Insiden <span class="text-red-500">*</span>
                </label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  rows="6"
                  placeholder="Jelaskan detail insiden yang terjadi, termasuk dampak dan langkah yang sudah diambil..."
                  required
                  size="small"
                  class="w-full"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.description }"
                />
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.description }}
                </p>
                <p class="mt-1 text-sm text-slate-500">
                  Berikan informasi selengkap mungkin untuk membantu proses penanganan
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar - Management -->
        <div class="space-y-6">
          <!-- Status Management -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4 0V3a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H9z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 ml-3">Manajemen</h3>
            </div>

            <div class="space-y-4">
              <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                <Select
                  v-model="form.status"
                  :options="statusOptions"
                  optionLabel="label"
                  optionValue="value"
                  size="small"
                  class="w-full"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-3">Prioritas</label>
                <div class="grid grid-cols-2 gap-2">
                  <button
                    v-for="option in priorityOptions"
                    :key="option.value"
                    type="button"
                    @click="form.priority = option.value"
                    class="p-3 text-sm font-medium border rounded-lg transition-all duration-200"
                    :class="form.priority === option.value
                      ? [option.color, 'ring-2 ring-offset-2 ring-indigo-500']
                      : 'border-slate-300 text-slate-700 hover:bg-slate-50'"
                  >
                    {{ option.label }}
                  </button>
                </div>
              </div>

              <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-700 mb-2">
                  Tugaskan Kepada
                </label>
                <Select
                  v-model="form.assigned_to"
                  :options="staffUserOptions"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Pilih staf"
                  size="small"
                  class="w-full"
                  :class="{ 'border-red-300': form.errors.assigned_to }"
                />
                <p v-if="form.errors.assigned_to" class="mt-1 text-sm text-red-600">
                  {{ form.errors.assigned_to }}
                </p>
              </div>
            </div>
          </div>

          <!-- Preview Card -->
          <div class="bg-slate-50 rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-medium text-slate-700 mb-3">Preview</h3>
            <div class="space-y-3 text-sm">
              <div>
                <span class="text-slate-500">ID Insiden:</span>
                <span class="ml-2 font-mono text-slate-700">Akan digenerate otomatis</span>
              </div>
              <div>
                <span class="text-slate-500">Pelapor:</span>
                <span class="ml-2 text-slate-700">{{ form.reporter_name || 'Belum diisi' }}</span>
              </div>
              <div>
                <span class="text-slate-500">Status:</span>
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                  {{ form.status }}
                </span>
              </div>
              <div>
                <span class="text-slate-500">Prioritas:</span>
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border" :class="getPriorityColor(form.priority)">
                  {{ form.priority }}
                </span>
              </div>
              <div v-if="form.incident_at">
                <span class="text-slate-500">Waktu:</span>
                <span class="ml-2 text-slate-700">{{ formatDateTime(form.incident_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
