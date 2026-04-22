<!-- Tujuan: Halaman daftar Aset Informasi -->
<!-- Caller: InformationAssetController@index -->
<!-- Side Effects: none -->
<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  informationAssets: Object,
  organizations: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedOrg = ref(props.filters?.owner_org_id || '')
const paginatedData = computed(() => props.informationAssets)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.information-assets.index', {
  search: searchQuery,
  owner_org_id: selectedOrg,
})

const orgOptions = computed(() =>
  (props.organizations ?? []).map((o) => ({ label: o.name, value: o.id })),
)

const storageFormatLabel = (val) =>
  ({ file_dokumen: 'File Dokumen', cetak: 'Cetak', keduanya: 'Keduanya' })[
    val
  ] ?? val

const storageFormatSeverity = (val) =>
  ({ file_dokumen: 'info', cetak: 'warn', keduanya: 'success' })[val] ??
  'secondary'

const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.information-assets.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Aset Informasi">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aset Informasi"
      @confirm="handleDelete"
    >
      <template #item-info>{{
        toDelete?.document?.title ?? 'Aset Informasi'
      }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Aset Informasi"
        description="Kelola dokumen dan informasi aset organisasi."
      >
        <template #action>
          <a :href="route('admin.information-assets.create')">
            <Button
              ><IconPlus size="16" class="mr-1" />Tambah Aset Informasi</Button
            >
          </a>
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
              placeholder="Cari aset informasi..."
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
            class="w-full sm:w-52"
            show-clear
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="informationAssets?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada aset informasi terdaftar.
          </div>
        </template>
        <Column header="Dokumen" class="min-w-48">
          <template #body="{ data }">
            <p class="font-medium text-slate-800">
              {{ data.document?.title ?? '(Tanpa Dokumen)' }}
            </p>
          </template>
        </Column>
        <Column header="Format Penyimpanan" class="hidden sm:table-cell">
          <template #body="{ data }">
            <Tag
              :value="storageFormatLabel(data.storage_format)"
              :severity="storageFormatSeverity(data.storage_format)"
            />
          </template>
        </Column>
        <Column header="Lokasi" class="hidden md:table-cell">
          <template #body="{ data }">
            <span class="text-sm text-slate-600">{{
              data.location?.name ?? '—'
            }}</span>
          </template>
        </Column>
        <Column header="Pemilik" class="hidden lg:table-cell">
          <template #body="{ data }">
            <span class="text-sm text-slate-600">{{
              data.owner_org?.name ?? '—'
            }}</span>
          </template>
        </Column>
        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex justify-end gap-1">
              <a :href="route('admin.information-assets.show', data.id)">
                <Button size="small" severity="secondary" variant="outlined"
                  ><IconEye size="15"
                /></Button>
              </a>
              <a :href="route('admin.information-assets.edit', data.id)">
                <Button size="small" severity="secondary" variant="outlined"
                  ><IconEdit size="15"
                /></Button>
              </a>
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
