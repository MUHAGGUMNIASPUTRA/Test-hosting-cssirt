<script setup>
// filepath: resources/js/Pages/Admin/Incidents/Index.vue

import { computed, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  incidents: Object,
  filters: Object,
  stats: Object,
  incidentTypes: Array,
  staffUsers: Array,
})

const { isMobile } = useResponsive()

// --- Filter state ---
const searchQuery = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedStatus = ref(props.filters?.status || '')
const selectedPriority = ref(props.filters?.priority || '')
const selectedAssignedTo = ref(props.filters?.assigned_to || '')

// --- Server-side DataTable + pagination ---
const paginatedData = computed(() => props.incidents)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.incidents.index', {
  search: searchQuery,
  category: selectedCategory,
  status: selectedStatus,
  priority: selectedPriority,
  assigned_to: selectedAssignedTo,
})

// --- Computed options ---
const categoryOptions = computed(() =>
  (props.incidentTypes ?? []).map((t) => ({ label: t.name, value: t.id })),
)

const staffUserOptions = computed(() => [
  { label: 'Belum Ditugaskan', value: 'none' },
  ...(props.staffUsers ?? []).map((u) => ({
    label: u.name,
    value: String(u.id),
  })),
])

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
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

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
      command: () => {
        router.get(route('admin.incidents.show', item.id))
      },
    },
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => {
        router.get(route('admin.incidents.edit', item.id))
      },
      visible: item.status !== 'Ditutup',
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => {
        confirmDeleteIncident(item)
      },
    },
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
        <div class="mb-1 flex items-center justify-between">
          <span class="font-medium text-slate-600">ID Insiden:</span>
          <span
            class="rounded bg-slate-200 px-2 py-1 font-mono text-xs text-slate-900"
          >
            {{ incidentToDelete?.case_id }}
          </span>
        </div>
        <div class="flex items-center justify-between">
          <span class="font-medium text-slate-600">Pelapor:</span>
          <span class="text-slate-900">{{
            incidentToDelete?.reporter_name
          }}</span>
        </div>
      </template>
    </DeleteConfirmDialog>

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div
          class="flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
              Daftar Laporan Insiden
            </h2>
            <p class="text-slate-600">
              Kelola dan monitor laporan insiden keamanan siber
            </p>
          </div>
          <Button
            severity="primary"
            @click="() => router.get(route('admin.incidents.create'))"
            class="w-full sm:w-auto"
          >
            <IconBellPlus :size="16" />
            Lapor Insiden Baru
          </Button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div
        class="grid grid-cols-2 gap-4 lg:grid-cols-2 lg:gap-6 xl:grid-cols-4"
      >
        <StatCard color="blue" label="Total Insiden" :value="stats?.total || 0">
          <template #default="{ iconClass, iconSize }">
            <IconMailExclamation :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard
          color="yellow"
          label="Dalam Proses"
          :value="stats?.in_progress || 0"
        >
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
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-xl font-semibold text-slate-900">
            Filter & Pencarian
          </h3>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="font-medium text-blue-600 hover:text-blue-800"
          >
            Reset Filter
          </button>
        </div>

        <div
          class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
        >
          <div>
            <label class="mb-2 block font-medium text-slate-700"
              >Cari Insiden</label
            >
            <IconField class="w-full">
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText
                v-model="searchQuery"
                placeholder="ID, deskripsi, pelapor..."
                class="w-full"
                @keyup.enter="applyFilters"
              />
            </IconField>
          </div>

          <div>
            <label class="mb-2 block font-medium text-slate-700"
              >Kategori</label
            >
            <Select
              v-model="selectedCategory"
              :options="categoryOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Semua Kategori"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>

          <div>
            <label class="mb-2 block font-medium text-slate-700">Status</label>
            <Select
              v-model="selectedStatus"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Semua Status"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>

          <div>
            <label class="mb-2 block font-medium text-slate-700"
              >Prioritas</label
            >
            <Select
              v-model="selectedPriority"
              :options="priorityOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Semua Prioritas"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>

          <div>
            <label class="mb-2 block font-medium text-slate-700">Petugas</label>
            <Select
              v-model="selectedAssignedTo"
              :options="staffUserOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Semua Petugas"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <AdminDataTable
        :value="incidents.data"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-12 text-center">
            <IconMailExclamation
              size="30"
              class="mx-auto mb-4 text-slate-300"
            />
            <p class="text-lg font-medium text-slate-500">
              {{
                hasActiveFilters
                  ? 'Tidak ada insiden yang sesuai filter'
                  : 'Belum ada insiden yang dilaporkan'
              }}
            </p>
            <p class="mt-1 text-sm text-slate-400">
              {{
                hasActiveFilters
                  ? 'Coba ubah kriteria pencarian'
                  : 'Insiden yang dilaporkan akan muncul di sini'
              }}
            </p>
          </div>
        </template>

        <Column field="case_id" header="ID Insiden">
          <template #body="{ data }">
            <Link :href="route('admin.incidents.show', data.id)">
              <Tag
                :value="data.case_id"
                severity="secondary"
                size="small"
                class="font-mono !text-slate-500"
              />
            </Link>
            <div class="mt-1 space-x-1 text-xs text-slate-500 lg:hidden">
              <span>{{ data.reporter_name }}</span>
              <span>•</span>
              <span class="text-slate-400">{{
                data.incident_type?.name || 'N/A'
              }}</span>
            </div>
          </template>
        </Column>

        <Column
          field="reporter_name"
          header="Pelapor"
          class="hidden lg:table-cell"
        >
          <template #body="{ data }">
            <div>
              <div class="text-sm font-medium text-slate-700">
                {{ data.reporter_name }}
              </div>
              <div class="text-sm text-slate-500">
                {{ data.reporter_email }}
              </div>
            </div>
          </template>
        </Column>

        <Column
          field="incident_type"
          header="Kategori"
          class="hidden lg:table-cell"
        >
          <template #body="{ data }">
            <span class="text-sm text-slate-700">{{
              data.incident_type?.name || 'N/A'
            }}</span>
          </template>
        </Column>

        <Column field="status" header="Status" class="hidden lg:table-cell">
          <template #body="{ data }">
            <StatusBadge type="incident-status" :value="data.status" />
          </template>
        </Column>

        <Column
          field="priority"
          header="Prioritas"
          class="hidden lg:table-cell"
        >
          <template #body="{ data }">
            <StatusBadge type="priority" :value="data.priority" />
          </template>
        </Column>

        <Column header="Ditugaskan ke" class="hidden xl:table-cell">
          <template #body="{ data }">
            <span
              v-if="data.assigned_user"
              class="text-sm font-medium text-slate-700"
            >
              {{ data.assigned_user.name }}
            </span>
            <span v-else class="text-sm text-slate-400">—</span>
          </template>
        </Column>

        <Column
          field="reported_at"
          header="Dilaporkan"
          class="hidden lg:table-cell"
        >
          <template #body="{ data }">
            <span class="text-sm text-slate-500">{{
              formatDate(data.reported_at)
            }}</span>
          </template>
        </Column>

        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex items-center justify-end">
              <Button
                variant="text"
                class="!p-0"
                @click="toggleActionMenu($event, data)"
              >
                <template #default>
                  <div
                    class="flex items-center text-slate-400 hover:text-blue-600"
                  >
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
                  itemLabel: { class: 'text-sm' },
                }"
              />
            </div>
          </template>
        </Column>
      </AdminDataTable>
    </div>
  </AdminLayout>
</template>
