<!-- Tujuan: Halaman daftar Subdomain dengan popup CRUD -->
<!-- Caller: SubdomainController@index -->
<!-- Side Effects: none (CRUD via dialog) -->
<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  subdomains: Object,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const paginatedData = computed(() => props.subdomains)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.subdomains.index', {
  search: searchQuery,
})

const dialogVisible = ref(false)
const selectedSubdomain = ref(null)

const openCreate = () => {
  selectedSubdomain.value = null
  dialogVisible.value = true
}

const openEdit = (item) => {
  selectedSubdomain.value = item
  dialogVisible.value = true
}

const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.subdomains.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Daftar Subdomain">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Subdomain"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.subdomain }}</template>
    </DeleteConfirmDialog>

    <SubdomainFormDialog
      v-model:visible="dialogVisible"
      :subdomain="selectedSubdomain"
      @saved="router.reload({ only: ['subdomains', 'stats'] })"
    />

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Subdomain"
        description="Kelola subdomain yang terdaftar dalam sistem."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Subdomain</Button
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
              placeholder="Cari subdomain..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="subdomains?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada subdomain terdaftar.
          </div>
        </template>
        <Column header="Subdomain" class="min-w-48">
          <template #body="{ data }">
            <p class="font-mono font-medium text-slate-800">
              {{ data.subdomain }}
            </p>
          </template>
        </Column>
        <Column header="Deskripsi" class="hidden md:table-cell">
          <template #body="{ data }">
            <span v-if="data.description" class="text-sm text-slate-600">
              {{
                data.description.length > 80
                  ? data.description.substring(0, 80) + '...'
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
