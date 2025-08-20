<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Index.vue

import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  incidents: Object,
  filters: Object,
  stats: Object
})

const { isMobile, isDesktop, dtConfig } = useResponsive();

const searchQuery = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedStatus = ref(props.filters?.status || '')
const selectedPriority = ref(props.filters?.priority || '')
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
  if (selectedStatus.value) params.set('status', selectedStatus.value)
  if (selectedPriority.value) params.set('priority', selectedPriority.value)

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
  if (selectedStatus.value) params.set('status', selectedStatus.value)
  if (selectedPriority.value) params.set('priority', selectedPriority.value)

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

const getPrioritySeverity = (priority) => {
  const severities = {
    'Rendah': 'success',
    'Sedang': 'info',
    'Tinggi': 'warn',
    'Kritikal': 'danger'
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

const categoryOptions = [
  { label: 'Phishing', value: 1 },
  { label: 'Malware', value: 2 },
  { label: 'Defacement', value: 3 },
  { label: 'Serangan DDoS', value: 4 },
  { label: 'Kebocoran Data', value: 5 },
];

const statusOptions = [
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' },
];

const priorityOptions = [
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' },
];

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  selectedStatus.value = '';
  selectedPriority.value = '';
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

// Action menu handling
const actionMenu = ref();
const selectedMenu = ref(null);
const toggleActionMenu = (event, item) => {
  selectedMenu.value = item;
  actionMenu.value.toggle(event);
};

const actionMenuItems = computed(() => {
  if (!selectedMenu.value) return [];
  const item = selectedMenu.value;
  return [
    {
      label: 'Detail',
      icon: 'pi pi-eye',
      command: () => { router.get(route('admin.incidents.show', item.id)); },
    },
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { router.get(route('admin.incidents.edit', item.id)); },
      visible: item.status !== 'Ditutup',
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { confirmDeleteIncident(item); },
    }
  ];
});
</script>

<template>
  <AdminLayout title="Daftar Laporan Insiden">
    <!-- Custom Delete Confirmation Dialog -->
    <Dialog
      v-model:visible="showDeleteDialog"
      :modal="true"
      :closable="false"
      class="w-full max-w-lg"
      :style="{ width: isMobile ? '95vw' : undefined }"
    >
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
          <!-- Header -->
          <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 sm:p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <IconAlertTriangle class="text-white" />
              </div>
              <div class="ml-3">
                <h3 class="text-lg/6 font-semibold text-white">Konfirmasi Penghapusan</h3>
                <p class="text-red-100 text-sm">Tindakan ini tidak dapat dibatalkan</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-4 sm:p-6">
            <div class="text-center mb-4 sm:mb-6">
              <p class="text-slate-700 mb-4 sm:mb-6">Apakah Anda yakin ingin menghapus insiden berikut?</p>
              <div class="bg-slate-100 border border-slate-200 rounded-lg p-4 text-left">
                <div class="">
                  <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-slate-600">ID Insiden:</span>
                    <span class="font-mono text-slate-900 bg-slate-300 px-2 py-1 rounded text-xs">
                      {{ incidentToDelete?.case_id }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium text-slate-600">Pelapor:</span>
                    <span class="text-slate-900">{{ incidentToDelete?.reporter_name }}</span>
                  </div>
                </div>
              </div>
              <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg py-4 mt-3">
                <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between space-x-3">
              <Button
                @click="closeCallback"
                severity="secondary"
                variant="outlined"
              >
                <template #default>
                  <IconX size="16"/>Batal
                </template>
              </Button>
              <Button
                @click="deleteIncident"
                severity="danger"
              >
                <template #default>
                  <IconTrash size="16"/>Hapus
                </template>
              </Button>
            </div>
          </div>
        </div>
      </template>
    </Dialog>

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Daftar Laporan Insiden</h2>
            <p class="text-slate-600">Kelola dan monitor laporan insiden keamanan siber</p>
          </div>
          <Button
            severity="primary"
            @click="() => router.get(route('admin.incidents.create'))"
            class="w-full sm:w-auto"

          >
            <IconBellPlus :size="16"/>
              Lapor Insiden Baru
          </Button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <IconMailExclamation class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Total Insiden</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats?.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center justify-center">
              <IconRefresh class="text-yellow-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Dalam Proses</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats?.in_progress || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center">
              <IconAlertHexagon class="text-red-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Kritikal</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats?.critical || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <IconCircleCheck class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Selesai</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats?.completed || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="searchQuery || selectedStatus || selectedPriority"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Insiden</label>
            <div class="relative">
              <IconField class="w-full lg:w-auto">
                <InputIcon>
                  <i class="pi pi-search" />
                </InputIcon>
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari berdasarkan ID, deskripsi, pelapor..."
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
              <IconMailExclamation size="30" class="text-slate-300 mx-auto mb-4" />
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedStatus || selectedPriority ? 'Tidak ada insiden ditemukan' : 'Belum ada insiden yang dilaporkan' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedStatus || selectedPriority ? 'Coba ubah kriteria pencarian' : 'Insiden yang dilaporkan akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column field="case_id" header="ID Insiden">
            <template #body="{ data }">
              <Button
                size="small"
                :label="data.case_id"
                severity="secondary"
                class="!py-0.5 !px-1.5 !font-mono border !border-slate-200 dark:!border-slate-700"
                @click="() => router.get(route('admin.incidents.show', data.id))"
              />
              <div class="lg:hidden text-xs text-slate-500 space-x-1 mt-1">
                <span>{{ data.reporter_name }}</span>
                <span>•</span>
                <span class="text-slate-400">{{ data.incident_type?.name || 'N/A' }}</span>
              </div>
            </template>
          </Column>

          <Column field="reporter_name" header="Pelapor" class="hidden lg:table-cell">
            <template #body="{ data }">
              <div>
                <div class="text-sm text-slate-700 font-medium">{{ data.reporter_name }}</div>
                <div class="text-sm text-slate-500">{{ data.reporter_email }}</div>
              </div>
            </template>
          </Column>

          <Column field="incident_type" header="Kategori" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-700">{{ data.incident_type?.name || 'N/A' }}</span>
            </template>
          </Column>

          <Column field="status" header="Status" class="hidden lg:table-cell">
            <template #body="{ data }">
              <Tag
                :value="data.status"
                :severity="getStatusSeverity(data.status)"
                size="small"
              />
            </template>
          </Column>

          <Column field="priority" header="Prioritas" class="hidden lg:table-cell">
            <template #body="{ data }">
              <Tag
                :value="data.priority"
                :severity="getPrioritySeverity(data.priority)"
                size="small"
              />
            </template>
          </Column>

          <Column field="reported_at" header="Dilaporkan" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.reported_at) }}</span>
            </template>
          </Column>

          <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
            <template #body="{ data }">
              <div class="flex items-center justify-end">
                <Button
                  variant="text"
                  class="!p-0"
                  @click="toggleActionMenu($event, data)"
                >
                  <template #default>
                    <div class="flex items-center text-slate-400 hover:text-blue-600">
                      <IconChevronDown size="22" stroke-width="1.5" />
                    </div>
                  </template>
                </Button>

                <Menu
                  ref="actionMenu"
                  :model="actionMenuItems"
                  :popup="true"
                  class="!min-w-28"
                  :pt="{
                    itemIcon: { class: '!text-sm mr-1' },
                    itemLabel: { class: 'text-sm' }
                  }"
                />
              </div>
            </template>
          </Column>
        </DataTable>

      </div>
    </div>
  </AdminLayout>
</template>
