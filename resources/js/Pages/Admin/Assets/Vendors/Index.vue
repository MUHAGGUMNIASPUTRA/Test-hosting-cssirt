<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  vendors: Object,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const paginatedData = computed(() => props.vendors)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.vendors.index', { search: searchQuery })

// Dialog
const dialogVisible = ref(false)
const selectedVendor = ref(null)

const openCreate = () => {
  selectedVendor.value = null
  dialogVisible.value = true
}

const openEdit = (item) => {
  selectedVendor.value = item
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
  router.delete(route('admin.vendors.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Vendor">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Vendor"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.company_name }}</template>
    </DeleteConfirmDialog>

    <VendorFormDialog
      v-model:visible="dialogVisible"
      :vendor="selectedVendor"
    />

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Vendor"
        description="Kelola data vendor pengembang aset."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Vendor</Button
          >
        </template>
      </AdminPageHeader>

      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <IconField class="w-full sm:w-72">
          <InputIcon><i class="pi pi-search" /></InputIcon>
          <InputText
            v-model="searchQuery"
            placeholder="Cari vendor..."
            class="w-full"
            @keyup.enter="applyFilters"
          />
        </IconField>
      </AdminFilterBar>

      <AdminDataTable
        :value="vendors?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">Belum ada vendor.</div>
        </template>
        <Column header="Perusahaan" class="min-w-48">
          <template #body="{ data }">
            <div>
              <p class="font-medium text-slate-800">{{ data.company_name }}</p>
              <p class="text-sm text-slate-500">{{ data.location ?? '' }}</p>
            </div>
          </template>
        </Column>
        <Column header="Kontak" class="hidden md:table-cell">
          <template #body="{ data }">
            <div class="space-y-0.5 text-sm">
              <p v-if="data.phone" class="text-slate-600">{{ data.phone }}</p>
              <p v-if="data.email" class="text-slate-600">{{ data.email }}</p>
            </div>
          </template>
        </Column>
        <Column header="PIC" class="hidden lg:table-cell">
          <template #body="{ data }">
            <div v-if="data.pic_name" class="text-sm">
              <p class="font-medium text-slate-700">{{ data.pic_name }}</p>
              <p class="text-slate-500">{{ data.pic_phone }}</p>
            </div>
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
