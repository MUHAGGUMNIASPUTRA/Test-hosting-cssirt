<!-- Tujuan: Halaman daftar IP Address dengan popup CRUD -->
<!-- Caller: IpAddressController@index -->
<!-- Side Effects: none (CRUD via dialog) -->
<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  ipAddresses: Object,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const paginatedData = computed(() => props.ipAddresses)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.ip-addresses.index', {
  search: searchQuery,
})

const dialogVisible = ref(false)
const selectedIp = ref(null)

const openCreate = () => {
  selectedIp.value = null
  dialogVisible.value = true
}

const openEdit = (item) => {
  selectedIp.value = item
  dialogVisible.value = true
}

const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.ip-addresses.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Daftar IP Address">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="IP Address"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.private_ip }}</template>
    </DeleteConfirmDialog>

    <IpAddressFormDialog
      v-model:visible="dialogVisible"
      :ip-address="selectedIp"
      @saved="router.reload({ only: ['ipAddresses', 'stats'] })"
    />

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar IP Address"
        description="Kelola daftar alamat IP aset jaringan."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah IP Address</Button
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
              placeholder="Cari IP address..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="ipAddresses?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada IP address terdaftar.
          </div>
        </template>
        <Column header="IP Privat" class="min-w-36">
          <template #body="{ data }">
            <p class="font-mono font-medium text-slate-800">
              {{ data.private_ip }}
            </p>
          </template>
        </Column>
        <Column header="IP Publik" class="hidden sm:table-cell">
          <template #body="{ data }">
            <p v-if="data.public_ip" class="font-mono text-sm text-slate-600">
              {{ data.public_ip }}
            </p>
            <span v-else class="text-slate-400">—</span>
          </template>
        </Column>
        <Column header="Deskripsi" class="hidden md:table-cell">
          <template #body="{ data }">
            <span v-if="data.description" class="text-sm text-slate-600">
              {{
                data.description.length > 60
                  ? data.description.substring(0, 60) + '...'
                  : data.description
              }}
            </span>
            <span v-else class="text-slate-400">—</span>
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
