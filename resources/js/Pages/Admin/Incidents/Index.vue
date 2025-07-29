<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  incidents: Object,
  filters: Object
})

const { dtConfig } = useResponsive();

const searchQuery = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedPriority = ref(props.filters?.priority || '')
const selectedStatus = ref(props.filters?.status || '')
const showDeleteDialog = ref(false)
const incidentToDelete = ref(null)

// Add pagination state
const lazyParams = ref({
  first: 0,
  rows: props.incidents.per_page || 10,
  page: props.incidents.current_page || 1
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedCategory.value) params.set('category', selectedCategory.value)
  if (selectedPriority.value) params.set('priority', selectedPriority.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.incidents.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

// Handle pagination change
const onPage = (event) => {
  lazyParams.value.first = event.first
  lazyParams.value.rows = event.rows
  lazyParams.value.page = Math.floor(event.first / event.rows) + 1

  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedCategory.value) params.set('category', selectedCategory.value)
  if (selectedPriority.value) params.set('priority', selectedPriority.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.incidents.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const confirmDeleteIncident = (incident) => {
  incidentToDelete.value = incident
  showDeleteDialog.value = true
}

const deleteIncident = () => {
  if (!incidentToDelete.value) return

  router.delete(route('admin.incidents.destroy', incidentToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      incidentToDelete.value = null
    },
    onError: () => {}
  })
}

const getPrioritySeverity = (priority) => {
  const severities = {
    'Rendah': 'success',
    'Sedang': 'info',
    'Tinggi': 'warn',
    'Kritikal': 'danger'
  }
  return severities[priority] || 'warn'
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

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const categoryOptions = [
  { label: 'Phishing', value: 1 },
  { label: 'Malware', value: 2 },
  { label: 'Defacement', value: 3 },
  { label: 'Serangan DDoS', value: 4 },
  { label: 'Kebocoran Data', value: 5 },
];

const priorityOptions = [
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' },
];

const statusOptions = [
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' },
];

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  selectedPriority.value = '';
  selectedStatus.value = '';
  lazyParams.value.page = 1;
  lazyParams.value.first = 0;
  applyFilters();
};

// Server-side DataTable configuration
const serverSideConfig = computed(() => {
  return {
    ...dtConfig(),
    lazy: true,
    totalRecords: props.incidents.total,
    first: (props.incidents.current_page - 1) * props.incidents.per_page,
    rows: props.incidents.per_page,
  }
})
</script>

<template>
  <AdminLayout title="Daftar Laporan Insiden">
    <!-- Custom Delete Confirmation Dialog -->
    <Dialog
      v-model:visible="showDeleteDialog"
      :modal="true"
      :closable="false"
      class="w-full max-w-md"
    >
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
          <!-- Header -->
          <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-lg font-semibold text-white">Konfirmasi Penghapusan</h3>
                <p class="text-red-100 text-sm">Tindakan ini tidak dapat dibatalkan</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <div class="text-center mb-6">
              <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              <p class="text-slate-700 mb-2">
                Apakah Anda yakin ingin menghapus insiden berikut?
              </p>
              <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-left">
                <div class="">
                  <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-slate-600">ID Insiden:</span>
                    <span class="font-mono text-slate-900 bg-slate-200 px-2 py-1 rounded text-xs">
                      {{ incidentToDelete?.case_id }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium text-slate-600">Pelapor:</span>
                    <span class="text-slate-900">{{ incidentToDelete?.reporter_name }}</span>
                  </div>
                </div>
              </div>
              <p class="text-sm text-red-600 mt-3">
                <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
              <Button
                @click="closeCallback"
                label="Batal"
                severity="secondary"
                size="small"
              />
              <Button
                @click="deleteIncident"
                label="Ya, Hapus Insiden"
                severity="danger"
                size="small"
              />
            </div>
          </div>
        </div>
      </template>
    </Dialog>

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-slate-900">Daftar Laporan Insiden</h2>
            <p class="text-slate-600">Kelola dan monitor laporan insiden keamanan siber</p>
          </div>
          <Button
            as="a"
            :href="route('admin.incidents.create')"
            severity="primary"
          >
            <template #default>
              <i class="pi pi-plus-circle"></i>
              Lapor Insiden Baru
            </template>
          </Button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-blue-600">problem</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Total Insiden</p>
              <p class="text-2xl font-bold text-slate-900">{{ incidents.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-yellow-600">cycle</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Dalam Proses</p>
              <p class="text-2xl font-bold text-slate-900">
                {{ incidents.data?.filter(i => ['Baru', 'Diverifikasi', 'Dalam Penyelidikan'].includes(i.status)).length || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-red-600">warning</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Kritikal</p>
              <p class="text-2xl font-bold text-slate-900">
                {{ incidents.data?.filter(i => i.priority === 'Kritikal').length || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-green-600">done_all</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Selesai</p>
              <p class="text-2xl font-bold text-slate-900">
                {{ incidents.data?.filter(i => i.status === 'Selesai').length || 0 }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="searchQuery || selectedStatus || selectedPriority"
            @click="clearFilters"
            class="text-indigo-600 hover:text-indigo-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Insiden</label>
            <div class="relative">
              <IconField class="w-full sm:w-auto">
                <InputIcon>
                  <i class="pi pi-search" />
                </InputIcon>
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari berdasarkan ID insiden, pelapor..."
                  class="w-full pl-10"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </div>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Kategori</label>
            <Select
              v-model="selectedCategory"
              :options="categoryOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Kategori"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Prioritas</label>
            <Select
              v-model="selectedPriority"
              :options="priorityOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Prioritas"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Status</label>
            <Select
              v-model="selectedStatus"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Status"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>

        </div>
      </div>

      <!-- DataTable -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <DataTable
          v-bind="serverSideConfig"
          :value="incidents.data"
          @page="onPage"
        >
          <template #empty>
            <div class="text-center py-12">
              <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedStatus || selectedPriority ? 'Tidak ada insiden yang sesuai filter' : 'Belum ada insiden yang dilaporkan' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedStatus || selectedPriority ? 'Coba ubah kriteria pencarian' : 'Insiden yang dilaporkan akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column field="case_id" header="ID Insiden">
            <template #body="{ data }">
              <Tag
                :value="data.case_id"
                severity="secondary"
                size="small"
                class="font-mono !text-slate-500"
              />
            </template>
          </Column>

          <Column field="reporter_name" header="Pelapor">
            <template #body="{ data }">
              <div>
                <div class="text-sm text-slate-900 font-medium">{{ data.reporter_name }}</div>
                <div class="text-sm text-slate-500">{{ data.reporter_email }}</div>
              </div>
            </template>
          </Column>

          <Column field="incident_type" header="Jenis">
            <template #body="{ data }">
              <span class="text-sm text-slate-900">{{ data.incident_type?.name || 'N/A' }}</span>
            </template>
          </Column>

          <Column field="priority" header="Prioritas">
            <template #body="{ data }">
              <Tag
                :value="data.priority"
                :severity="getPrioritySeverity(data.priority)"
                size="small"
              />
            </template>
          </Column>

          <Column field="status" header="Status">
            <template #body="{ data }">
              <Tag
                :value="data.status"
                :severity="getStatusSeverity(data.status)"
                size="small"
              />
            </template>
          </Column>

          <Column field="reported_at" header="Dilaporkan">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.reported_at) }}</span>
            </template>
          </Column>

          <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
            <template #body="{ data }">
              <div class="flex items-center justify-end space-x-2">
                <Link
                  :href="route('admin.incidents.show', data.id)"
                  class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                  title="Lihat Detail"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </Link>
                <Link
                  :href="route('admin.incidents.edit', data.id)"
                  class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                  title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <button
                  @click="confirmDeleteIncident(data)"
                  class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Hapus"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </template>
          </Column>
        </DataTable>

      </div>
    </div>
  </AdminLayout>
</template>
