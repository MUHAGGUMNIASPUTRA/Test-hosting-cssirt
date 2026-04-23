<!-- Tujuan: Halaman daftar laporan insiden dengan filter, stats, dan tabel server-side -->
<!-- Caller: IncidentController@index -->
<!-- Side Effects: Inertia GET admin.incidents.index -->
<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'

const props = defineProps({
  incidents: Object,
  filters: Object,
  stats: Object,
  incidentTypes: Array,
  staffUsers: Array,
})

const searchQuery = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedStatus = ref(props.filters?.status || '')
const selectedPriority = ref(props.filters?.priority || '')
const selectedAssignedTo = ref(props.filters?.assigned_to || '')

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
</script>

<template>
  <AdminLayout title="Daftar Laporan Insiden">
    <div class="space-y-4 lg:space-y-6">
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <AdminPageHeader
          title="Daftar Laporan Insiden"
          description="Kelola dan monitor laporan insiden keamanan siber"
        >
          <template #action>
            <Button
              severity="primary"
              class="w-full sm:w-auto"
              @click="() => router.get(route('admin.incidents.create'))"
            >
              <IconBellPlus :size="16" />
              Lapor Insiden Baru
            </Button>
          </template>
        </AdminPageHeader>
      </div>

      <IncidentStatCards :stats="stats" />

      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <div
          class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
        >
          <div>
            <label class="mb-2 block font-medium text-slate-700"
              >Cari Insiden</label
            >
            <IconField class="w-full">
              <InputIcon><i class="pi pi-search" /></InputIcon>
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
              option-label="label"
              option-value="value"
              placeholder="Semua Kategori"
              class="w-full"
              show-clear
              @change="applyFilters"
            />
          </div>
          <div>
            <label class="mb-2 block font-medium text-slate-700">Status</label>
            <Select
              v-model="selectedStatus"
              :options="statusOptions"
              option-label="label"
              option-value="value"
              placeholder="Semua Status"
              class="w-full"
              show-clear
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
              option-label="label"
              option-value="value"
              placeholder="Semua Prioritas"
              class="w-full"
              show-clear
              @change="applyFilters"
            />
          </div>
          <div>
            <label class="mb-2 block font-medium text-slate-700">Petugas</label>
            <Select
              v-model="selectedAssignedTo"
              :options="staffUserOptions"
              option-label="label"
              option-value="value"
              placeholder="Semua Petugas"
              class="w-full"
              show-clear
              @change="applyFilters"
            />
          </div>
        </div>
      </AdminFilterBar>

      <IncidentDataTable
        :data="incidents.data"
        :server-config="serverSideConfig"
        :has-active-filters="hasActiveFilters"
        @page="onPage"
      />
    </div>
  </AdminLayout>
</template>
