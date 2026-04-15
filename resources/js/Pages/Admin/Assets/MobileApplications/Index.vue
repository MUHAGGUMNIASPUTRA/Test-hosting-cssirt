<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  mobileApplications: Object,
  organizations: Array,
  stageOptions: Array,
  appStatusOptions: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedStage = ref(props.filters?.stage || '')
const selectedAppStatus = ref(props.filters?.app_status || '')
const selectedOwnerOrg = ref(props.filters?.owner_org_id || '')
const paginatedData = computed(() => props.mobileApplications)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.mobile-applications.index', {
  search: searchQuery,
  stage: selectedStage,
  app_status: selectedAppStatus,
  owner_org_id: selectedOwnerOrg,
})

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)

const stageSeverity = (val) =>
  ({
    draft: 'secondary',
    pengajuan: 'info',
    pengujian: 'warn',
    revisi: 'danger',
    persiapan: 'warn',
    diterima: 'success',
  })[val] ?? 'secondary'

const statusSeverity = (val) =>
  ({ aktif: 'success', idle: 'warn', nonaktif: 'secondary' })[val] ??
  'secondary'

const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.mobile-applications.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Aplikasi Mobile">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aplikasi Mobile"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Aplikasi Mobile"
        description="Kelola inventaris aplikasi mobile yang dimiliki organisasi."
      >
        <template #action>
          <Link :href="route('admin.mobile-applications.create')">
            <Button><IconPlus size="16" class="mr-1" />Tambah Aplikasi</Button>
          </Link>
        </template>
      </AdminPageHeader>

      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <div class="flex flex-wrap gap-3">
          <IconField class="w-full sm:w-56">
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText
              v-model="searchQuery"
              placeholder="Cari nama aplikasi..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <Select
            v-model="selectedStage"
            :options="stageOptions"
            option-label="name"
            option-value="value"
            placeholder="Semua Tahap"
            class="w-full sm:w-40"
            show-clear
            @change="applyFilters"
          />
          <Select
            v-model="selectedAppStatus"
            :options="appStatusOptions"
            option-label="name"
            option-value="value"
            placeholder="Status Aplikasi"
            class="w-full sm:w-40"
            show-clear
            @change="applyFilters"
          />
          <Select
            v-model="selectedOwnerOrg"
            :options="orgOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Pemilik"
            class="w-full sm:w-52"
            show-clear
            filter
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="mobileApplications?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada aplikasi mobile.
          </div>
        </template>
        <Column header="Aplikasi" class="min-w-48">
          <template #body="{ data }">
            <p class="font-medium text-slate-800">{{ data.name }}</p>
            <p v-if="data.owner_org" class="text-sm text-slate-500">
              {{ data.owner_org.name }}
            </p>
          </template>
        </Column>
        <Column header="Tahap" class="hidden sm:table-cell">
          <template #body="{ data }">
            <Tag
              :value="data.stage"
              :severity="stageSeverity(data.stage)"
              class="capitalize"
            />
          </template>
        </Column>
        <Column header="Kondisi" class="hidden md:table-cell">
          <template #body="{ data }">
            <div class="space-y-1">
              <Tag
                :value="data.app_status"
                :severity="statusSeverity(data.app_status)"
                class="capitalize"
              />
              <p v-if="data.current_version" class="text-xs text-slate-500">
                v{{ data.current_version }}
              </p>
            </div>
          </template>
        </Column>
        <Column header="Link Aplikasi" class="hidden lg:table-cell">
          <template #body="{ data }">
            <a
              v-if="data.app_link"
              :href="data.app_link"
              target="_blank"
              class="flex items-center gap-1 text-sm text-blue-600 hover:underline"
            >
              <IconExternalLink size="13" />Buka
            </a>
            <span v-else class="text-slate-400">—</span>
          </template>
        </Column>
        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex justify-end gap-1">
              <Link :href="route('admin.mobile-applications.edit', data.id)">
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
