<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Index.vue

import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  incidents: Object,
  filters: Object,
  stats: Object
})

const { isMobile } = useResponsive()

// --- Filter state ---
const searchQuery      = ref(props.filters?.search   || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedStatus   = ref(props.filters?.status   || '')
const selectedPriority = ref(props.filters?.priority || '')

// --- Server-side DataTable + pagination ---
const paginatedData = computed(() => props.incidents)

const { serverSideConfig, applyFilters, onPage, clearFilters, hasActiveFilters } = useAdminTable(
  paginatedData,
  'admin.incidents.index',
  {
    search:   searchQuery,
    category: selectedCategory,
    status:   selectedStatus,
    priority: selectedPriority,
  }
)

// --- Delete dialog ---
const showDeleteDialog = ref(false)
const incidentToDelete = ref(null)

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
  })
}

// --- Helpers ---
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

const categoryOptions = [
  { label: 'Phishing',       value: 1 },
  { label: 'Malware',        value: 2 },
  { label: 'Defacement',     value: 3 },
  { label: 'Serangan DDoS',  value: 4 },
  { label: 'Kebocoran Data', value: 5 },
]

const statusOptions = [
  { label: 'Baru',               value: 'Baru' },
  { label: 'Diverifikasi',       value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai',            value: 'Selesai' },
  { label: 'Ditutup',            value: 'Ditutup' },
]

const priorityOptions = [
  { label: 'Rendah',   value: 'Rendah' },
  { label: 'Sedang',   value: 'Sedang' },
  { label: 'Tinggi',   value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' },
]

// Action menu handling
const actionMenu = ref()
const selectedMenu = ref(null)
const toggleActionMenu = (event, item) => {
  selectedMenu.value = item
  actionMenu.value.toggle(event)
}

const actionMenuItems = computed(() => {
  if (!selectedMenu.value) return []
  const item = selectedMenu.value
  return [
    {
      label: 'Detail',
      icon: 'pi pi-eye',
      command: () => { router.get(route('admin.incidents.show', item.id)) },
    },
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { router.get(route('admin.incidents.edit', item.id)) },
      visible: item.status !== 'Ditutup',
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { confirmDeleteIncident(item) },
    }
  ]
})
</script>

<template>
  <AdminLayout title="Daftar Laporan Insiden">
    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="insiden berikut"
      delete-label="Ya, Hapus Insiden"
      @confirm="deleteIncident"
    >
      <template #item-info>
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
      </template>
    </DeleteConfirmDialog>

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
        <StatCard color="blue" label="Total Insiden" :value="stats?.total || 0">
          <template #default="{ iconClass, iconSize }">
            <IconMailExclamation :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard color="yellow" label="Dalam Proses" :value="stats?.in_progress || 0">
          <template #default="{ iconClass, iconSize }">
            <IconRefresh :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard color="red" label="Kritikal" :value="stats?.critical || 0">
          <template #default="{ iconClass, iconSize }">
            <IconAlertHexagon :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard color="green" label="Selesai" :value="stats?.completed || 0">
          <template #default="{ iconClass, iconSize }">
            <IconCircleCheck :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Insiden</label>
            <IconField class="w-full">
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText
                v-model="searchQuery"
                placeholder="Cari berdasarkan ID, deskripsi, pelapor..."
                class="w-full"
                @keyup.enter="applyFilters"
              />
            </IconField>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Kategori</label>
            <Select v-model="selectedCategory" :options="categoryOptions"
              optionLabel="label" optionValue="value"
              placeholder="Pilih Kategori" class="w-full" showClear @change="applyFilters" />
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Status</label>
            <Select v-model="selectedStatus" :options="statusOptions"
              optionLabel="label" optionValue="value"
              placeholder="Pilih Status" class="w-full" showClear @change="applyFilters" />
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Prioritas</label>
            <Select v-model="selectedPriority" :options="priorityOptions"
              optionLabel="label" optionValue="value"
              placeholder="Pilih Prioritas" class="w-full" showClear @change="applyFilters" />
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <AdminDataTable :value="incidents.data" :server-config="serverSideConfig" @page="onPage">
        <template #empty>
          <div class="text-center py-12">
            <IconMailExclamation size="30" class="text-slate-300 mx-auto mb-4" />
            <p class="text-slate-500 text-lg font-medium">
              {{ hasActiveFilters ? 'Tidak ada insiden yang sesuai filter' : 'Belum ada insiden yang dilaporkan' }}
            </p>
            <p class="text-slate-400 mt-1 text-sm">
              {{ hasActiveFilters ? 'Coba ubah kriteria pencarian' : 'Insiden yang dilaporkan akan muncul di sini' }}
            </p>
          </div>
        </template>

        <Column field="case_id" header="ID Insiden">
          <template #body="{ data }">
            <Link :href="route('admin.incidents.show', data.id)">
              <Tag :value="data.case_id" severity="secondary" size="small" class="font-mono !text-slate-500" />
            </Link>
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
            <StatusBadge type="incident-status" :value="data.status" />
          </template>
        </Column>

        <Column field="priority" header="Prioritas" class="hidden lg:table-cell">
          <template #body="{ data }">
            <StatusBadge type="priority" :value="data.priority" />
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
      </AdminDataTable>
    </div>
  </AdminLayout>
</template>
