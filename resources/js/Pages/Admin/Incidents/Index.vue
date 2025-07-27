<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useToast } from "primevue/usetoast"
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'

const props = defineProps({
  incidents: Object,
  filters: Object
})

const toast = useToast()
const searchQuery = ref(props.filters?.search || '')
const selectedStatus = ref(props.filters?.status || '')
const selectedPriority = ref(props.filters?.priority || '')
const showDeleteDialog = ref(false)
const incidentToDelete = ref(null)

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)
  if (selectedPriority.value) params.set('priority', selectedPriority.value)

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
      toast.add({
        severity: 'success',
        summary: 'Berhasil',
        detail: 'Insiden berhasil dihapus',
        life: 3000
      })
      showDeleteDialog.value = false
      incidentToDelete.value = null
    },
    onError: () => {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Gagal menghapus insiden',
        life: 3000
      })
    }
  })
}

const getStatusSeverity = (status) => {
  const severities = {
    'Baru': 'info',
    'Diverifikasi': 'warn',
    'Dalam Penyelidikan': 'warn',
    'Selesai': 'success',
    'Ditutup': 'secondary'
  }
  return severities[status] || 'info'
}

const getPrioritySeverity = (priority) => {
  const severities = {
    'Rendah': 'info',
    'Sedang': 'warn',
    'Tinggi': 'warn',
    'Kritis': 'danger'
  }
  return severities[priority] || 'warn'
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

const statusOptions = [
  { label: 'Semua Status', value: '' },
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' }
]

const priorityOptions = [
  { label: 'Semua Prioritas', value: '' },
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritis', value: 'Kritis' }
]

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  selectedPriority.value = ''
  applyFilters()
}
</script>

<template>
  <AdminLayout title="Daftar Laporan Insiden">
    <Toast position="top-right" />

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
              <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
              <p class="text-slate-700 mb-2 text-sm">
                Apakah Anda yakin ingin menghapus insiden berikut?
              </p>
              <div class="bg-slate-50 rounded-lg p-3 text-left">
                <div class="text-sm">
                  <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-slate-600">ID Insiden:</span>
                    <span class="font-mono text-slate-900 bg-slate-200 px-2 py-1 rounded text-xs">
                      {{ incidentToDelete?.case_id }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium text-slate-600">Pelapor:</span>
                    <span class="text-slate-900 text-sm">{{ incidentToDelete?.reporter_name }}</span>
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
            <h1 class="text-2xl font-bold text-slate-900">Daftar Laporan Insiden</h1>
            <p class="text-slate-600 mt-1 text-sm">Kelola dan monitor laporan insiden keamanan siber</p>
          </div>
          <Link
            :href="route('admin.incidents.create')"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Lapor Insiden Baru
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600">Total Insiden</p>
              <p class="text-2xl font-bold text-slate-900">{{ incidents.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600">Dalam Proses</p>
              <p class="text-2xl font-bold text-slate-900">
                {{ incidents.data?.filter(i => ['Baru', 'Diverifikasi', 'Dalam Penyelidikan'].includes(i.status)).length || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600">Kritikal</p>
              <p class="text-2xl font-bold text-slate-900">
                {{ incidents.data?.filter(i => i.priority === 'Kritis').length || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600">Selesai</p>
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
          <h3 class="text-lg font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="searchQuery || selectedStatus || selectedPriority"
            @click="clearFilters"
            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Cari Insiden</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <InputText
                v-model="searchQuery"
                placeholder="Cari berdasarkan ID insiden, pelapor..."
                class="w-full pl-10"
                size="small"
                @keyup.enter="applyFilters"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Filter Status</label>
            <Select
              v-model="selectedStatus"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Status"
              class="w-full"
              size="small"
              @change="applyFilters"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Filter Prioritas</label>
            <Select
              v-model="selectedPriority"
              :options="priorityOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Prioritas"
              class="w-full"
              size="small"
              @change="applyFilters"
            />
          </div>

          <div class="flex items-end">
            <Button
              @click="applyFilters"
              label="Terapkan Filter"
              class="w-full"
              severity="secondary"
              size="small"
            />
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <DataTable
          :value="incidents.data"
          :paginator="false"
          stripedRows
          :pt="{
            table: 'w-full text-sm',
            thead: 'bg-slate-50',
            tbody: 'bg-white',
            headerRow: 'border-b border-slate-200',
            row: 'border-b border-slate-100 hover:bg-slate-50 transition-colors',
            headerCell: 'px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider',
            bodyCell: 'px-6 py-4 text-sm'
          }"
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
              <span class="text-sm font-mono text-slate-900 bg-slate-100 px-2 py-1 rounded">
                {{ data.case_id }}
              </span>
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

          <Column header="Aksi" class="text-right">
            <template #body="{ data }">
              <div class="flex items-center justify-end space-x-2">
                <Link
                  :href="route('admin.incidents.show', data.id)"
                  class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
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

        <!-- Pagination -->
        <div v-if="incidents.data.length > 0" class="px-6 py-4 bg-slate-50 border-t border-slate-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-slate-700">
              Menampilkan {{ incidents.from }} sampai {{ incidents.to }} dari {{ incidents.total }} hasil
            </div>
            <div class="flex items-center space-x-2">
              <Link
                v-if="incidents.prev_page_url"
                :href="incidents.prev_page_url"
                class="px-3 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-300 rounded-md hover:bg-slate-50"
              >
                Sebelumnya
              </Link>
              <Link
                v-if="incidents.next_page_url"
                :href="incidents.next_page_url"
                class="px-3 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-300 rounded-md hover:bg-slate-50"
              >
                Selanjutnya
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
