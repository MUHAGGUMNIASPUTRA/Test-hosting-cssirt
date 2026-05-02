<!-- Tujuan: Halaman daftar Aset Fisik -->
<!-- Caller: PhysicalAssetController@index -->
<!-- Side Effects: none -->
<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  physicalAssets: Object,
  organizations: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedOrg = ref(props.filters?.owner_org_id || '')
const paginatedData = computed(() => props.physicalAssets)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.physical-assets.index', {
  search: searchQuery,
  owner_org_id: selectedOrg,
})

const orgOptions = computed(() =>
  (props.organizations ?? []).map((o) => ({ label: o.name, value: o.id })),
)

const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.physical-assets.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Aset Fisik">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aset Fisik"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Aset Fisik"
        description="Kelola perangkat keras dan infrastruktur fisik."
      >
        <template #action>
          <a :href="route('admin.physical-assets.create')">
            <Button
              ><IconPlus size="16" class="mr-1" />Tambah Aset Fisik</Button
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
              placeholder="Cari nama atau kode aset..."
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
        :value="physicalAssets?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada aset fisik terdaftar.
          </div>
        </template>
        <Column header="Kode Aset" style="width: 8rem">
          <template #body="{ data }">
            <Tag :value="data.asset_code" severity="secondary" />
          </template>
        </Column>
        <Column header="Nama" class="min-w-40">
          <template #body="{ data }">
            <p class="font-medium text-slate-800">{{ data.name }}</p>
            <p v-if="data.description" class="text-sm text-slate-500">
              {{
                data.description.length > 60
                  ? data.description.substring(0, 60) + '...'
                  : data.description
              }}
            </p>
          </template>
        </Column>
        <Column header="Tahun" class="hidden sm:table-cell" style="width: 6rem">
          <template #body="{ data }">
            <span class="text-sm text-slate-600">{{ data.year ?? '—' }}</span>
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
              <a :href="route('admin.physical-assets.show', data.id)">
                <Button size="small" severity="secondary" variant="outlined"
                  ><IconEye size="15"
                /></Button>
              </a>
              <a :href="route('admin.physical-assets.edit', data.id)">
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
