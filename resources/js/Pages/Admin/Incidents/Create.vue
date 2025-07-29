<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
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
                  <svg class="w-6 h-6 text-blue-600" viewBox="0 -960 960 960" fill="currentColor">
                    <path d="M920-591q0 80-24.5 142T826-333q-10 12-24.5 13T776-330q-11-11-10.5-26t10.5-27q36-44 55-92.5T850-591q0-67-19-115.5T776-799q-10-12-10.5-27t10.5-26q11-11 25.5-10t24.5 13q45 54 69.5 116T920-591Zm-200 0q0 32-9.5 61T684-475q-8 12-22 13t-25-10q-11-11-12-25t7-27q8-14 13-31t5-36q0-19-5-36t-13-31q-8-13-7-27t12-25q11-11 25-10t22 13q17 26 26.5 55t9.5 61ZM360-441q-66 0-108-42t-42-108q0-66 42-108t108-42q66 0 108 42t42 108q0 66-42 108t-108 42ZM40-180v-34q0-38 19-64.5t49-41.5q51-26 120.5-43T360-380q62 0 131 17t120 43q30 15 49.5 41.5T680-214v34q0 25-17.5 42.5T620-120H100q-25 0-42.5-17.5T40-180Z"/>
                  </svg>
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
                  <svg class="w-6 h-6 text-red-600" viewBox="0 -960 960 960" fill="currentColor">
                    <path d="M240-160q-12.75 0-21.37-8.68-8.63-8.67-8.63-21.5 0-12.82 8.63-21.32 8.62-8.5 21.37-8.5h31l84-277q6-19 21.5-31t35.5-12h136q20 0 35.5 12t21.5 31l84 277h31q12.75 0 21.38 8.68 8.62 8.67 8.62 21.5 0 12.82-8.62 21.32-8.63 8.5-21.38 8.5H240Zm210-530v-120q0-12.75 8.68-21.38 8.67-8.62 21.5-8.62 12.82 0 21.32 8.62 8.5 8.63 8.5 21.38v120q0 12.75-8.68 21.37-8.67 8.63-21.5 8.63-12.82 0-21.32-8.63-8.5-8.62-8.5-21.37Zm214 64 85-85q9-8 21.1-8.5 12.1-.5 20.9 8.5 9 9 9 21t-9 21l-85 86q-9 9-21 9t-21-9.05q-9-9.06-9-21.5 0-12.45 9-21.45Zm106 196h120q12.75 0 21.38 8.68 8.62 8.67 8.62 21.5 0 12.82-8.62 21.32-8.63 8.5-21.38 8.5H770q-12.75 0-21.37-8.68-8.63-8.67-8.63-21.5 0-12.82 8.63-21.32 8.62-8.5 21.37-8.5ZM254-584l-85-85q-8-9-8.5-21.1-.5-12.1 8.5-20.9 9-9 21-9t21 9l86 85q9 9 9 21t-9.05 21q-9.06 9-21.5 9-12.45 0-21.45-9ZM70-370q-12.75 0-21.37-8.68-8.63-8.67-8.63-21.5 0-12.82 8.63-21.32Q57.25-430 70-430h120q12.75 0 21.38 8.68 8.62 8.67 8.62 21.5 0 12.82-8.62 21.32-8.63 8.5-21.38 8.5H70Z"/>
                  </svg>
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
                  <svg class="w-6 h-6 text-green-600" viewBox="0 -960 960 960" fill="currentColor">
                    <path d="m702-165 154-154q8.8-9 20.9-9 12.1 0 21.1 9.05 9 9.06 9 21.5 0 12.45-9 21.45L723-101q-8.8 9-20.9 9-12.1 0-21.1-9l-88-88q-9-8.8-9-20.9 0-12.1 9.21-21.1 9.22-9 21.5-9 12.29 0 21.29 9l66 66ZM300-450q12 0 21-9t9-21q0-12-9-21t-21-9q-12 0-21 9t-9 21q0 12 9 21t21 9Zm0-164q12 0 21-9t9-21q0-12-9-21t-21-9q-12 0-21 9t-9 21q0 12 9 21t21 9Zm346 164q12.75 0 21.38-8.68 8.62-8.67 8.62-21.5 0-12.82-8.62-21.32-8.63-8.5-21.38-8.5H462q-12.75 0-21.37 8.68-8.63 8.67-8.63 21.5 0 12.82 8.63 21.32 8.62 8.5 21.37 8.5h184Zm0-164q12.75 0 21.38-8.68 8.62-8.67 8.62-21.5 0-12.82-8.62-21.32-8.63-8.5-21.38-8.5H462q-12.75 0-21.37 8.68-8.63 8.67-8.63 21.5 0 12.82 8.63 21.32 8.62 8.5 21.37 8.5h184ZM180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h600q24 0 42 18t18 42v367q0 12.44-5 23.72T822-370L702-250l-46-45q-18-18-42.94-18-24.94 0-42.06 18l-42 43q-8.5 9-12.75 19.87Q512-221.25 512-210q0 14.38 6 26.19T534-161q11 12 5.04 26.5Q533.09-120 518-120H180Z"/>
                  </svg>
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
                      class="p-2 font-medium border rounded-lg transition-all duration-200 text-center"
                      :class="form.priority === option.value
                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-500 ring-opacity-20'
                        : 'border-slate-300 text-slate-700 hover:bg-slate-50 hover:border-slate-400'"
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
