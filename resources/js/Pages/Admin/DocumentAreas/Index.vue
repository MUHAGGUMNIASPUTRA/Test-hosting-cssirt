<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  documentAreas: Object,
  filters: Object,
})

const { isMobile, dtConfig } = useResponsive()

const searchQuery = ref(props.filters?.search || '')
const showDeleteDialog = ref(false)
const areaToDelete = ref(null)

const applyFilters = () => {
  const params = new URLSearchParams()
  if (searchQuery.value) params.set('search', searchQuery.value)
  const qs = params.toString()
  router.get(
    route('admin.document-areas.index') + (qs ? '?' + qs : ''),
    {},
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}

const clearFilters = () => {
  searchQuery.value = ''
  applyFilters()
}

const confirmDelete = (area) => {
  areaToDelete.value = area
  showDeleteDialog.value = true
}

const deleteArea = () => {
  if (!areaToDelete.value) return
  router.delete(route('admin.document-areas.destroy', areaToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      areaToDelete.value = null
    },
    onError: () => {},
  })
}

const formatDate = (dateString) =>
  new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })

// Action menu
const actionMenu = ref()
const selectedMenu = ref(null)
const toggleActionMenu = (event, item) => {
  selectedMenu.value = item
  actionMenu.value.toggle(event)
}
const actionMenuItems = computed(() => {
  if (!selectedMenu.value) return []
  const item = selectedMenu.value
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => {
        router.get(route('admin.document-areas.edit', item.id))
      },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => {
        confirmDelete(item)
      },
      visible: item.documents_count === 0,
    },
  ]
})
</script>

<template>
  <AdminLayout title="Area Dokumen">
    <!-- Delete Confirmation Dialog -->
    <Dialog
      v-model:visible="showDeleteDialog"
      :modal="true"
      :closable="false"
      class="w-full max-w-lg"
      :style="{ width: isMobile ? '95vw' : undefined }"
    >
      <template #container="{ closeCallback }">
        <div
          class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl"
        >
          <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 sm:p-6">
            <div class="flex items-center">
              <div
                class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20"
              >
                <IconAlertTriangle class="text-white" />
              </div>
              <div class="ml-3">
                <h3 class="text-lg/6 font-semibold text-white">
                  Konfirmasi Penghapusan
                </h3>
                <p class="text-sm text-red-100">
                  Tindakan ini tidak dapat dibatalkan
                </p>
              </div>
            </div>
          </div>
          <div class="p-4 sm:p-6">
            <div class="mb-4 text-center sm:mb-6">
              <p class="mb-4 text-slate-700 sm:mb-6">
                Apakah Anda yakin ingin menghapus area berikut?
              </p>
              <div
                class="rounded-lg border border-slate-200 bg-slate-100 p-4 text-left"
              >
                <div class="mb-2 flex items-center justify-between">
                  <span class="font-medium text-slate-600">Nama:</span>
                  <span class="font-semibold text-slate-900">{{
                    areaToDelete?.name
                  }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="font-medium text-slate-600">Digunakan di:</span>
                  <span class="text-slate-900"
                    >{{ areaToDelete?.documents_count || 0 }} dokumen</span
                  >
                </div>
              </div>
              <p
                v-if="areaToDelete?.documents_count > 0"
                class="mt-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-600"
              >
                <strong>Peringatan:</strong> Area ini digunakan dalam
                {{ areaToDelete.documents_count }} dokumen dan tidak dapat
                dihapus.
              </p>
              <p
                v-else
                class="mt-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-600"
              >
                <strong>Peringatan:</strong> Data yang dihapus tidak dapat
                dikembalikan
              </p>
            </div>
            <div class="flex items-center justify-between space-x-3">
              <Button
                @click="closeCallback"
                severity="secondary"
                variant="outlined"
              >
                <template #default><IconX size="16" />Batal</template>
              </Button>
              <Button
                v-if="!areaToDelete?.documents_count"
                @click="deleteArea"
                severity="danger"
              >
                <template #default><IconTrash size="16" />Hapus</template>
              </Button>
            </div>
          </div>
        </div>
      </template>
    </Dialog>

    <div class="space-y-4 lg:space-y-6">
      <!-- Header -->
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div
          class="flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
              Area Dokumen
            </h2>
            <p class="text-slate-600">
              Kelola area/kategori untuk pengelompokan dokumen
            </p>
          </div>
          <Button
            severity="primary"
            @click="() => router.get(route('admin.document-areas.create'))"
            class="w-full sm:w-auto"
          >
            <IconPlus :size="16" />
            Tambah Area
          </Button>
        </div>
      </div>

      <!-- Search -->
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-xl font-semibold text-slate-900">Pencarian</h3>
          <button
            v-if="searchQuery"
            @click="clearFilters"
            class="font-medium text-blue-600 hover:text-blue-800"
          >
            Reset
          </button>
        </div>
        <IconField class="w-full">
          <InputIcon><i class="pi pi-search" /></InputIcon>
          <InputText
            v-model="searchQuery"
            placeholder="Cari area dokumen..."
            class="w-full pl-10"
            @keyup.enter="applyFilters"
          />
        </IconField>
      </div>

      <!-- DataTable -->
      <div
        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
      >
        <DataTable
          v-bind="dtConfig()"
          :value="documentAreas.data"
          :totalRecords="documentAreas.total"
        >
          <template #empty>
            <div class="py-12 text-center">
              <IconFolders size="30" class="mx-auto mb-4 text-slate-300" />
              <p class="text-lg font-medium text-slate-500">
                {{
                  searchQuery
                    ? 'Tidak ada area ditemukan'
                    : 'Belum ada area dokumen'
                }}
              </p>
              <p class="mt-1 text-sm text-slate-400">
                {{
                  searchQuery
                    ? 'Coba ubah kriteria pencarian'
                    : 'Tambah area untuk mengelompokkan dokumen'
                }}
              </p>
            </div>
          </template>

          <Column
            field="name"
            :header="'Nama Area (' + documentAreas.total + ')'"
          >
            <template #body="{ data }">
              <div>
                <div class="font-semibold text-slate-900">{{ data.name }}</div>
                <div class="font-mono text-xs text-slate-500">
                  {{ data.slug }}
                </div>
              </div>
            </template>
          </Column>

          <Column
            field="description"
            header="Deskripsi"
            class="hidden lg:table-cell"
          >
            <template #body="{ data }">
              <p class="line-clamp-2 text-sm text-slate-600">
                {{ data.description || 'Tidak ada deskripsi' }}
              </p>
            </template>
          </Column>

          <Column
            field="documents_count"
            header="Dokumen"
            class="hidden sm:table-cell"
          >
            <template #body="{ data }">
              <Tag
                :value="`${data.documents_count} dokumen`"
                :severity="data.documents_count > 0 ? 'success' : 'secondary'"
                size="small"
              />
            </template>
          </Column>

          <Column
            field="created_at"
            header="Dibuat"
            class="hidden lg:table-cell"
          >
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{
                formatDate(data.created_at)
              }}</span>
            </template>
          </Column>

          <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
            <template #body="{ data }">
              <div class="flex items-center justify-end">
                <Button
                  variant="text"
                  class="!p-0"
                  @click="toggleActionMenu($event, data)"
                >
                  <template #default>
                    <div
                      class="flex items-center text-slate-400 hover:text-blue-600"
                    >
                      <IconChevronDown size="22" stroke-width="1.5" />
                    </div>
                  </template>
                </Button>
                <Menu
                  ref="actionMenu"
                  :model="actionMenuItems"
                  :popup="true"
                  class="!min-w-28"
                  :pt="{
                    itemIcon: { class: '!text-sm mr-1' },
                    itemLabel: { class: 'text-sm' },
                  }"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
  </AdminLayout>
</template>
