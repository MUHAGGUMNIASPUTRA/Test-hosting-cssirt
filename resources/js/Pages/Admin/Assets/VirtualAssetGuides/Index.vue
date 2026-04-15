<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'

const props = defineProps({
  guides: Object,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedType = ref(props.filters?.type || '')
const paginatedData = computed(() => props.guides)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.virtual-asset-guides.index', {
  search: searchQuery,
  type: selectedType,
})

const typeOptions = [
  { label: 'Aplikasi Web', value: 'web' },
  { label: 'Aplikasi Mobile', value: 'mobile' },
]

const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(
    route('admin.virtual-asset-guides.destroy', toDelete.value.id),
    {
      onSuccess: () => {
        showDeleteDialog.value = false
        toDelete.value = null
      },
    },
  )
}
</script>

<template>
  <AdminLayout title="Panduan Aset Virtual">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Panduan"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Panduan Aset Virtual"
        description="Kelola panduan pengembangan dan pengelolaan aset virtual."
      >
        <template #action>
          <Link :href="route('admin.virtual-asset-guides.create')">
            <Button><IconPlus size="16" class="mr-1" />Tambah Panduan</Button>
          </Link>
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
              placeholder="Cari panduan..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <Select
            v-model="selectedType"
            :options="typeOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Tipe"
            class="w-full sm:w-44"
            show-clear
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="guides?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">Belum ada panduan.</div>
        </template>
        <Column header="Nama Panduan" class="min-w-48">
          <template #body="{ data }">
            <p class="font-medium text-slate-800">{{ data.name }}</p>
          </template>
        </Column>
        <Column header="Tipe" class="hidden sm:table-cell">
          <template #body="{ data }">
            <Tag
              :value="data.type === 'web' ? 'Aplikasi Web' : 'Aplikasi Mobile'"
              :severity="data.type === 'web' ? 'info' : 'success'"
            />
          </template>
        </Column>
        <Column header="Lampiran" class="hidden md:table-cell">
          <template #body="{ data }">
            <Tag
              :value="`${data.guide_attachments_count ?? 0} file`"
              severity="secondary"
            />
          </template>
        </Column>
        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex justify-end gap-1">
              <Link :href="route('admin.virtual-asset-guides.edit', data.id)">
                <Button size="small" severity="secondary" variant="outlined"
                  ><IconEdit size="15"
                /></Button>
              </Link>
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
