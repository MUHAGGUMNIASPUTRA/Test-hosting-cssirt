<script setup>
// filepath: resources/js/Pages/Admin/Services/Index.vue

import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { useConfirm } from "primevue/useconfirm"
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  services: Object,
  filters: Object,
})

const { isMobile, isDesktop, dtConfig } = useResponsive()
const confirm = useConfirm()

// Search and filters
const searchQuery = ref(props.filters?.search || '')
const selectedStatus = ref(props.filters?.status || '')
const showDeleteDialog = ref(false)
const serviceToDelete = ref(null)

// Add pagination state
const lazyParams = ref({
  first: 0,
  rows: props.services.per_page || 10,
  page: props.services.current_page || 1
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.services.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

// Handle pagination change
const onPage = (event) => {
  lazyParams.value.first = event.first
  lazyParams.value.rows = event.rows
  lazyParams.value.page = Math.floor(event.first / event.rows) + 1

  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.services.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const statusOptions = [
  { label: 'Aktif', value: 'active' },
  { label: 'Tidak Aktif', value: 'inactive' },
]

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  lazyParams.value.page = 1
  lazyParams.value.first = 0
  applyFilters()
}

const confirmDeleteService = (service) => {
  serviceToDelete.value = service
  showDeleteDialog.value = true
}

const deleteService = () => {
  if (!serviceToDelete.value) return

  router.delete(route('admin.services.destroy', serviceToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      serviceToDelete.value = null
    },
    onError: () => {}
  })
}

const getStatusSeverity = (isActive) => {
  return isActive ? 'success' : 'secondary'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const truncateText = (text, length = 80) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Stats computed
const stats = computed(() => {
  const allData = props.services.data || []
  const active = allData.filter(service => service.is_active).length
  const inactive = allData.filter(service => !service.is_active).length

  return { active, inactive }
})

// Server-side DataTable configuration
const serverSideConfig = computed(() => {
  return {
    ...dtConfig(),
    size: 'normal',
    lazy: true,
    totalRecords: props.services.total,
    first: (props.services.current_page - 1) * props.services.per_page,
    rows: props.services.per_page,
  }
})

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
      label: 'Detail',
      icon: 'pi pi-eye',
      command: () => { router.get(route('admin.services.show', item.id)); },
    },
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { router.get(route('admin.services.edit', item.id)); },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { confirmDeleteService(item); },
    }
  ];
});
</script>

<template>
  <AdminLayout title="Kelola Layanan">
    <ConfirmDialog />

    <!-- Custom Delete Confirmation Dialog -->
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
              <p class="text-slate-700 mb-4 sm:mb-6">Apakah Anda yakin ingin menghapus layanan berikut?</p>
              <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-left">
                <div class="">
                  <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-slate-600">Nama:</span>
                    <span class="text-slate-900 text-right max-w-48 truncate">
                      {{ serviceToDelete?.name }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium text-slate-600">Status:</span>
                    <Tag
                      :value="serviceToDelete?.is_active ? 'Aktif' : 'Tidak Aktif'"
                      :severity="getStatusSeverity(serviceToDelete?.is_active)"
                      size="small"
                    />
                  </div>
                </div>
              </div>
              <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg py-4 mt-3">
                <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between space-x-3">
              <Button
                @click="closeCallback"
                icon="pi pi-times"
                label="Batal"
                severity="secondary"
                variant="outlined"
              />
              <Button
                @click="deleteService"
                icon="pi pi-trash"
                label="Hapus"
                severity="danger"
              />
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
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Kelola Layanan</h2>
            <p class="text-slate-600">Kelola layanan yang disediakan organisasi</p>
          </div>
          <Link
            :href="route('admin.services.create')"
            class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
          >
            <IconHeartPlus size="16" />
              Tambah Layanan
          </Link>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <IconHeartHandshake class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Total Layanan</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ services.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <IconHeartCheck class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Layanan Aktif</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.active }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-orange-50 border border-orange-200 rounded-lg flex items-center justify-center">
              <IconHeartX class="text-orange-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Tidak Aktif</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.inactive }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="searchQuery || selectedStatus"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 lg:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Layanan</label>
            <div class="relative">
              <IconField class="w-full">
                <InputIcon>
                  <i class="pi pi-search" />
                </InputIcon>
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari berdasarkan nama layanan..."
                  class="w-full pl-10"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </div>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Status</label>
            <Select
              v-model="selectedStatus"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Status"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <DataTable
          v-bind="serverSideConfig"
          :value="services.data"
          @page="onPage"
        >
          <template #empty>
            <div class="text-center py-12">
              <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedStatus ? 'Tidak ada layanan yang sesuai filter' : 'Belum ada layanan yang dibuat' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedStatus ? 'Coba ubah kriteria pencarian' : 'Layanan yang dibuat akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column field="icon" header="Ikon" :style="{ width: '60px' }" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span v-if="data.icon" class="material-symbols-outlined text-slate-500">{{ data.icon }}</span>
              <span v-else class="material-symbols-outlined text-slate-500">volunteer_activism</span>
            </template>
          </Column>

          <Column field="name" header="Layanan">
            <template #body="{ data }">
              <div>
                <h3 class="font-medium text-slate-700 line-clamp-2">{{ data.name }}</h3>
              </div>
            </template>
          </Column>

          <Column field="short_description" header="Deskripsi Singkat" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500 line-clamp-2">{{ truncateText(data.short_description, 80) }}</span>
            </template>
          </Column>

          <Column field="is_active" header="Status">
            <template #body="{ data }">
              <Tag
                :value="data.is_active ? 'Aktif' : 'Tidak Aktif'"
                :severity="getStatusSeverity(data.is_active)"
                size="small"
              />
            </template>
          </Column>

          <Column field="updated_at" header="Diperbarui" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.updated_at) }}</span>
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
