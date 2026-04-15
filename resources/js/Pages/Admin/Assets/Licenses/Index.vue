<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { formatDate } from '@/utils/date'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  licenses: Object,
  organizations: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedStatus = ref(props.filters?.is_active || '')
const selectedOwnerOrg = ref(props.filters?.owner_org_id || '')
const paginatedData = computed(() => props.licenses)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.licenses.index', {
  search: searchQuery,
  is_active: selectedStatus,
  owner_org_id: selectedOwnerOrg,
})

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)

const statusOptions = [
  { label: 'Aktif', value: 'aktif' },
  { label: 'Nonaktif', value: 'nonaktif' },
]

const isExpiringSoon = (expiredAt) => {
  if (!expiredAt) return false
  const diff = new Date(expiredAt) - new Date()
  return diff > 0 && diff < 30 * 24 * 60 * 60 * 1000
}

const isExpired = (expiredAt) => {
  if (!expiredAt) return false
  return new Date(expiredAt) < new Date()
}

const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.licenses.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Lisensi">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Lisensi"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Lisensi"
        description="Kelola inventaris lisensi perangkat lunak yang dimiliki organisasi."
      >
        <template #action>
          <Link :href="route('admin.licenses.create')">
            <Button><IconPlus size="16" class="mr-1" />Tambah Lisensi</Button>
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
              placeholder="Cari nama lisensi..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <Select
            v-model="selectedStatus"
            :options="statusOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Status"
            class="w-full sm:w-36"
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
        :value="licenses?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">Belum ada lisensi.</div>
        </template>
        <Column header="Lisensi" class="min-w-48">
          <template #body="{ data }">
            <p class="font-medium text-slate-800">{{ data.name }}</p>
            <p v-if="data.version" class="text-sm text-slate-500">
              v{{ data.version }}
            </p>
          </template>
        </Column>
        <Column header="Status" class="hidden sm:table-cell">
          <template #body="{ data }">
            <Tag
              :value="data.is_active ? 'Aktif' : 'Nonaktif'"
              :severity="data.is_active ? 'success' : 'secondary'"
            />
          </template>
        </Column>
        <Column header="Masa Berlaku" class="hidden md:table-cell">
          <template #body="{ data }">
            <div v-if="data.expired_at">
              <p
                class="text-sm font-medium"
                :class="{
                  'text-red-600': isExpired(data.expired_at),
                  'text-amber-600': isExpiringSoon(data.expired_at),
                  'text-slate-700':
                    !isExpired(data.expired_at) &&
                    !isExpiringSoon(data.expired_at),
                }"
              >
                {{ formatDate(data.expired_at) }}
              </p>
              <Tag
                v-if="isExpired(data.expired_at)"
                value="Kedaluwarsa"
                severity="danger"
                class="!text-xs"
              />
              <Tag
                v-else-if="isExpiringSoon(data.expired_at)"
                value="Segera Habis"
                severity="warn"
                class="!text-xs"
              />
            </div>
            <span v-else class="text-slate-400">—</span>
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
              <Link :href="route('admin.licenses.edit', data.id)">
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
