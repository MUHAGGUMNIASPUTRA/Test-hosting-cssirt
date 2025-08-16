<script setup>
// filepath: resources/js/Pages/Admin/IncidentTypes/Index.vue

import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  incidentTypes: Object,
  filters: Object
})

const { isMobile, dtConfig } = useResponsive()

const searchQuery = ref(props.filters?.search || '')
const showDeleteDialog = ref(false)
const typeToDelete = ref(null)

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)

  const queryString = params.toString()
  const url = route('admin.incident-types.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const confirmDelete = (incidentType) => {
  typeToDelete.value = incidentType
  showDeleteDialog.value = true
}

const deleteIncidentType = () => {
  if (!typeToDelete.value) return

  router.delete(route('admin.incident-types.destroy', typeToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      typeToDelete.value = null
    },
    onError: () => {}
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  applyFilters()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Action menu handling
const actionMenu = ref();
const selectedMenu = ref(null);
const toggleActionMenu = (event, item) => {
  selectedMenu.value = item;
  actionMenu.value.toggle(event);
};

const actionMenuItems = computed(() => {
  if (!selectedMenu.value) return [];
  const item = selectedMenu.value;
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { router.get(route('admin.incident-types.edit', item.id)); },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { confirmDelete(item); },
      visible: item.incidents_count === 0,
    }
  ];
});
</script>

<template>
  <AdminLayout title="Kategori Insiden">
    <!-- Delete Confirmation Dialog -->
    <Dialog
      v-model:visible="showDeleteDialog"
      :modal="true"
      :closable="false"
      class="w-full max-w-lg"
      :style="{ width: isMobile ? '95vw' : undefined }"
    >
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
          <!-- Header -->
          <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 sm:p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <IconAlertTriangle class="text-white" />
              </div>
              <div class="ml-3">
                <h3 class="text-lg/6 font-semibold text-white">Konfirmasi Penghapusan</h3>
                <p class="text-red-100 text-sm">Tindakan ini tidak dapat dibatalkan</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-4 sm:p-6">
            <div class="text-center mb-4 sm:mb-6">
              <p class="text-slate-700 mb-4 sm:mb-6">Apakah Anda yakin ingin menghapus kategori berikut?</p>
              <div class="bg-slate-100 border border-slate-200 rounded-lg p-4 text-left">
                <div class="flex justify-between items-center mb-2">
                  <span class="font-medium text-slate-600">Nama:</span>
                  <span class="text-slate-900 font-semibold">{{ typeToDelete?.name }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="font-medium text-slate-600">Digunakan di:</span>
                  <span class="text-slate-900">{{ typeToDelete?.incidents_count || 0 }} insiden</span>
                </div>
              </div>
              <p v-if="typeToDelete?.incidents_count > 0" class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-4 mt-3">
                <strong>Peringatan:</strong> Kategori ini digunakan dalam {{ typeToDelete.incidents_count }} insiden dan tidak dapat dihapus.
              </p>
              <p v-else class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-4 mt-3">
                <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between space-x-3">
              <Button
                @click="closeCallback"
                severity="secondary"
                variant="outlined"
              >
                <template #default>
                  <IconX size="16"/>Batal
                </template>
              </Button>
              <Button
                v-if="!typeToDelete?.incidents_count"
                @click="deleteIncidentType"
                severity="danger"
              >
                <template #default>
                  <IconTrash size="16"/>Hapus
                </template>
              </Button>
            </div>
          </div>
        </div>
      </template>
    </Dialog>

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Kategori Insiden</h2>
            <p class="text-slate-600">Kelola kategori untuk klasifikasi insiden keamanan siber</p>
          </div>
          <Button
            severity="primary"
            @click="() => router.get(route('admin.incident-types.create'))"
            class="w-full sm:w-auto"
          >
            <IconPlus :size="16"/>
            Tambah Kategori
          </Button>
        </div>
      </div>

      <!-- Search Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Pencarian</h3>
          <button
            v-if="searchQuery"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset
          </button>
        </div>

        <IconField class="w-full">
          <InputIcon>
            <i class="pi pi-search" />
          </InputIcon>
          <InputText
            v-model="searchQuery"
            placeholder="Cari kategori..."
            class="w-full pl-10"
            @keyup.enter="applyFilters"
          />
        </IconField>
      </div>

      <!-- DataTable -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <DataTable
          v-bind="dtConfig()"
          :value="incidentTypes.data"
          :totalRecords="incidentTypes.total"
        >
          <template #empty>
            <div class="text-center py-12">
              <IconTicTac size="30" class="text-slate-300 mx-auto mb-4" />
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery ? 'Tidak ada kategori ditemukan' : 'Belum ada kategori insiden' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery ? 'Coba ubah kriteria pencarian' : 'Tambah kategori untuk mengklasifikasikan insiden' }}
              </p>
            </div>
          </template>

          <Column field="name" :header="'Nama Kategori (' +  incidentTypes.total + ')'">
            <template #body="{ data }">
              <div>
                <div class="font-semibold text-slate-900">{{ data.name }}</div>
                <div class="text-xs text-slate-500 font-mono">{{ data.slug }}</div>
              </div>
            </template>
          </Column>

          <Column field="description" header="Deskripsi" class="hidden lg:table-cell">
            <template #body="{ data }">
              <div>
                <p class="text-sm text-slate-600 line-clamp-2">
                  {{ data.description || 'Tidak ada deskripsi' }}
                </p>
              </div>
            </template>
          </Column>

          <Column field="incidents_count" header="Digunakan" class="hidden sm:table-cell">
            <template #body="{ data }">
              <Tag
                :value="`${data.incidents_count} insiden`"
                :severity="data.incidents_count > 0 ? 'success' : 'secondary'"
                size="small"
              />
            </template>
          </Column>

          <Column field="created_at" header="Dibuat" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.created_at) }}</span>
            </template>
          </Column>

          <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
            <template #body="{ data }">
              <div class="flex items-center justify-end">
                <Button
                  variant="text"
                  class="!p-0"
                  @click="toggleActionMenu($event, data)"
                >
                  <template #default>
                    <div class="flex items-center text-slate-400 hover:text-blue-600">
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
                    itemLabel: { class: 'text-sm' }
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
