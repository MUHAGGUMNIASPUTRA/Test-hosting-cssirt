<!-- Tujuan: Daftar aplikasi web admin dengan filter, pagination, dan quick-add IP/Subdomain -->
<!-- Caller: WebApplicationController@index -->
<!-- Side Effects: none -->
<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  webApplications: Object,
  organizations: Array,
  stageOptions: Array,
  appStatusOptions: Array,
  httpsStatusOptions: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedStage = ref(props.filters?.stage || '')
const selectedAppStatus = ref(props.filters?.app_status || '')
const selectedHttpsStatus = ref(props.filters?.https_status || '')
const selectedOwnerOrg = ref(props.filters?.owner_org_id || '')
const paginatedData = computed(() => props.webApplications)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.web-applications.index', {
  search: searchQuery,
  stage: selectedStage,
  app_status: selectedAppStatus,
  https_status: selectedHttpsStatus,
  owner_org_id: selectedOwnerOrg,
})

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)

const stageSeverity = (val) => {
  const map = {
    draft: 'secondary',
    pengajuan: 'info',
    pengujian: 'warn',
    revisi: 'danger',
    persiapan: 'warn',
    diterima: 'success',
  }
  return map[val] ?? 'secondary'
}

const statusSeverity = (val) =>
  ({ aktif: 'success', idle: 'warn', nonaktif: 'secondary' })[val] ??
  'secondary'

const httpsSeverity = (val) =>
  ({ aktif: 'success', expired: 'danger', nonaktif: 'secondary' })[val] ??
  'secondary'

// Delete
const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.web-applications.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}

// Quick-add IP Address for a row
const showIpDialog = ref(false)
const showSubdomainDialog = ref(false)

const onIpSaved = () => {
  showIpDialog.value = false
  router.reload({ only: ['webApplications'] })
}

const onSubdomainSaved = () => {
  showSubdomainDialog.value = false
  router.reload({ only: ['webApplications'] })
}

// ─── Export CSV / Excel ───────────────────────────────────────────────────────

/**
 * Ubah data webApplications halaman aktif menjadi array of objects
 * yang siap di-export.
 */
const buildExportRows = () =>
  (props.webApplications?.data ?? []).map((item) => {
    const net = item.networks?.[0]
    return {
      'Nama Aplikasi': item.name ?? '',
      Pemilik: item.owner_org?.name ?? '',
      Tahap: item.stage ?? '',
      'Status Aplikasi': item.app_status ?? '',
      'Status HTTPS': item.https_status ?? '',
      'Domain / Subdomain': net?.subdomain?.subdomain ?? '',
      'IP Private': net?.ip_address?.private_ip ?? '',
      'IP Public': net?.ip_address?.public_ip ?? '',
    }
  })

/** Export sebagai Excel (.xlsx) menggunakan SheetJS */
const isExportingXlsx = ref(false)

const exportXlsx = () => {
  window.location.href = route('admin.web-applications.export')
}

// ─────────────────────────────────────────────────────────────────────────────
</script>

<template>
  <AdminLayout title="Aplikasi Web">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aplikasi Web"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <IpAddressFormDialog
      :visible="showIpDialog"
      @update:visible="showIpDialog = $event"
      @saved="onIpSaved"
    />

    <SubdomainFormDialog
      :visible="showSubdomainDialog"
      @update:visible="showSubdomainDialog = $event"
      @saved="onSubdomainSaved"
    />

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Aplikasi Web"
        description="Kelola inventaris aplikasi web yang dimiliki organisasi."
      >
        <template #action>
          <!-- ── Tombol Export (baru) ── -->
          <Button severity="secondary" variant="outlined" @click="exportXlsx">
            <IconFileTypeXls size="16" class="mr-1" />
            Export Excel
          </Button>
          <!-- ─────────────────────── -->
          <Link :href="route('admin.web-applications.create')">
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
            v-model="selectedHttpsStatus"
            :options="httpsStatusOptions"
            option-label="name"
            option-value="value"
            placeholder="Status HTTPS"
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
        :value="webApplications?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada aplikasi web.
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
        <Column header="Status App" class="hidden md:table-cell">
          <template #body="{ data }">
            <Tag
              :value="data.app_status"
              :severity="statusSeverity(data.app_status)"
              class="capitalize"
            />
          </template>
        </Column>
        <Column header="HTTPS" class="hidden md:table-cell">
          <template #body="{ data }">
            <Tag
              :value="data.https_status"
              :severity="httpsSeverity(data.https_status)"
              class="capitalize"
            />
          </template>
        </Column>
        <Column header="Jaringan Utama" class="hidden lg:table-cell">
          <template #body="{ data }">
            <div v-if="data.networks?.length" class="text-sm">
              <!-- Domain / Subdomain -->
              <p
                v-if="data.networks[0].subdomain"
                class="font-medium text-slate-700"
              >
                {{ data.networks[0].subdomain.subdomain }}
              </p>
              <button
                v-else
                type="button"
                class="text-xs text-blue-500 hover:underline"
                @click="showSubdomainDialog = true"
              >
                Belum ada domain? Tambah →
              </button>
              <!-- IP Address -->
              <p
                v-if="data.networks[0].ip_address"
                class="font-mono text-xs text-slate-500"
              >
                {{ data.networks[0].ip_address.private_ip }}
                <span
                  v-if="data.networks[0].ip_address.public_ip"
                  class="text-slate-400"
                >
                  / {{ data.networks[0].ip_address.public_ip }}
                </span>
              </p>
              <button
                v-else
                type="button"
                class="text-xs text-blue-500 hover:underline"
                @click="showIpDialog = true"
              >
                Belum ada IP? Tambah →
              </button>
            </div>
            <span v-else class="text-slate-400">—</span>
          </template>
        </Column>
        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex justify-end gap-1">
              <Link :href="route('admin.web-applications.show', data.id)">
                <Button size="small" severity="info" variant="outlined"
                  ><IconEye size="15"
                /></Button>
              </Link>
              <Link :href="route('admin.web-applications.edit', data.id)">
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
