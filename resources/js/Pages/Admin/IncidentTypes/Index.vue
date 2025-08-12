<script setup>
// filepath: resources/js/Pages/Admin/IncidentTypes/Index.vue

import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
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
          <Link
            :href="route('admin.incident-types.create')"
            class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
          >
            <IconPlus :size="16"/>
            Tambah Kategori
          </Link>
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
              <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery ? 'Tidak ada kategori yang ditemukan' : 'Belum ada kategori insiden' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery ? 'Coba ubah kata kunci pencarian' : 'Tambah kategori untuk mengklasifikasikan insiden' }}
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
                <Link
                  :href="route('admin.incident-types.edit', data.id)"
                  class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Edit"
                >
                  <IconEdit size="14" />
                </Link>
                <button
                  @click="confirmDelete(data)"
                  class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Hapus"
                >
                  <IconTrash size="14" />
                </button>
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
  </AdminLayout>
</template>
