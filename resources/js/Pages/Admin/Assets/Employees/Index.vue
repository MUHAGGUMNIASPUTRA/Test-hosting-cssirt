<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  employees: Object,
  organizations: Array,
  positions: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedOrg = ref(props.filters?.organization_id || '')
const selectedStatus = ref(props.filters?.status || '')
const paginatedData = computed(() => props.employees)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.employees.index', {
  search: searchQuery,
  organization_id: selectedOrg,
  status: selectedStatus,
})

const page = usePage()
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin')

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)
const statusOptions = [
  { label: 'Aktif', value: 'aktif' },
  { label: 'Nonaktif', value: 'nonaktif' },
]

// Dialog
const dialogVisible = ref(false)
const selectedEmployee = ref(null)

const openCreate = () => {
  selectedEmployee.value = null
  dialogVisible.value = true
}

const openEdit = (item) => {
  selectedEmployee.value = item
  dialogVisible.value = true
}

// Delete
const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.employees.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Pegawai">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Pegawai"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <EmployeeFormDialog
      v-model:visible="dialogVisible"
      :employee="selectedEmployee"
      :positions="positions"
      :is-admin="isAdmin"
    />

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Pegawai"
        description="Kelola data pegawai organisasi."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Pegawai</Button
          >
        </template>
      </AdminPageHeader>

      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <div class="flex flex-wrap gap-3">
          <IconField class="w-full sm:w-64">
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText
              v-model="searchQuery"
              placeholder="Cari nama..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <Select
            v-model="selectedOrg"
            :options="orgOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Organisasi"
            class="w-full sm:w-56"
            show-clear
            @change="applyFilters"
          />
          <Select
            v-model="selectedStatus"
            :options="statusOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Status"
            class="w-full sm:w-40"
            show-clear
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="employees?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">Belum ada pegawai.</div>
        </template>
        <Column header="Pegawai" class="min-w-48">
          <template #body="{ data }">
            <div>
              <p class="font-medium text-slate-800">{{ data.name }}</p>
              <p class="font-mono text-sm text-slate-500">
                {{ data.email_masked ?? '—' }}
              </p>
            </div>
          </template>
        </Column>
        <Column header="NIP / NIK" class="hidden lg:table-cell">
          <template #body="{ data }">
            <div class="space-y-0.5 font-mono text-sm text-slate-600">
              <div v-if="data.nip_masked">NIP: {{ data.nip_masked }}</div>
              <div v-if="data.nik_masked">NIK: {{ data.nik_masked }}</div>
              <span
                v-if="!data.nip_masked && !data.nik_masked"
                class="text-slate-400"
                >—</span
              >
            </div>
          </template>
        </Column>
        <Column header="Jabatan / Bidang" class="hidden lg:table-cell">
          <template #body="{ data }">
            <div class="text-sm">
              <p class="text-slate-700">{{ data.position?.name ?? '—' }}</p>
              <p class="text-slate-500">
                {{ data.position?.department?.organization?.name ?? '' }}
              </p>
            </div>
          </template>
        </Column>
        <Column header="Telepon" class="hidden xl:table-cell">
          <template #body="{ data }">
            <span class="font-mono text-sm">{{
              data.phone_masked ?? '—'
            }}</span>
          </template>
        </Column>
        <Column header="Status" class="hidden sm:table-cell">
          <template #body="{ data }">
            <Tag
              :severity="data.is_active ? 'success' : 'secondary'"
              :value="data.is_active ? 'Aktif' : 'Nonaktif'"
            />
          </template>
        </Column>
        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex justify-end gap-1">
              <Button
                size="small"
                severity="secondary"
                variant="outlined"
                @click="openEdit(data)"
                ><IconEdit size="15"
              /></Button>
              <Button
                size="small"
                severity="danger"
                variant="outlined"
                @click="confirmDelete(data)"
                ><IconTrash size="15"
              /></Button>
            </div>
          </template>
        </Column>
      </AdminDataTable>
    </div>
  </AdminLayout>
</template>
