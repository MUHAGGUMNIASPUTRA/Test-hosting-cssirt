<script setup>
// filepath: resources/js/Pages/Admin/Documents/Index.vue

import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  documents: Object,
  filters: Object,
})

const { isMobile, isDesktop, dtConfig } = useResponsive()
const confirm = useConfirm()

// Reactive state
const searchQuery = ref(props.filters?.search || '')
const selectedStatus = ref(props.filters?.status || '')
const showDeleteDialog = ref(false)
const documentToDelete = ref(null)

// Pagination
const lazyParams = ref({
  first: 0,
  rows: 10,
  page: 1,
})

const statusOptions = [
  { label: 'Draft', value: 'draft' },
  { label: 'Diterbitkan', value: 'published' },
]

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  lazyParams.value.page = 1
  lazyParams.value.first = 0
  applyFilters()
}

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.documents.index') + (queryString ? '?' + queryString : '')

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
  lazyParams.value.page = event.page + 1
  applyFilters()
}

const confirmDeleteDocument = (document) => {
  documentToDelete.value = document
  showDeleteDialog.value = true
}

const deleteDocument = () => {
  if (!documentToDelete.value) return

  router.delete(route('admin.documents.destroy', documentToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      documentToDelete.value = null
    },
    onError: () => {}
  })
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
  const allData = props.documents.data || []
  const published = allData.filter(doc => doc.status === 'Published').length
  const draft = allData.filter(doc => doc.status === 'Draft').length

  return {
    total: allData.length,
    published,
    draft
  }
})

// Server-side DataTable configuration
const serverSideConfig = computed(() => {
  return {
    ...dtConfig(),
    lazy: true,
    totalRecords: props.documents.total,
    first: (props.documents.current_page - 1) * props.documents.per_page,
    rows: props.documents.per_page,
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
      label: 'Lihat',
      icon: 'pi pi-eye',
      url: `/storage/${item.file_path}`,
      target: '_blank',
      visible: item.file_exists,
    },
    {
      label: 'Download',
      icon: 'pi pi-download',
      url: route('documents.download', item.slug),
      visible: item.file_exists,
    },
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { router.get(route('admin.documents.edit', item.id)); },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { confirmDeleteDocument(item); },
    }
  ];
});
</script>

<template>
  <AdminLayout title="Daftar Dokumen Panduan">
    <ConfirmDialog :style="{ width: isMobile ? '95vw' : undefined }" />

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Daftar Dokumen Panduan</h2>
            <p class="text-slate-600">Kelola dokumen panduan untuk keamanan siber</p>
          </div>
          <Button
            severity="primary"
            @click="() => router.get(route('admin.documents.create'))"
            class="w-full sm:w-auto"
          >
            <IconFilePlus size="16" />
            Tambah Panduan
          </Button>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Pencarian</h3>
          <button
            v-if="searchQuery || selectedStatus"
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
            placeholder="Cari berdasarkan judul, deskripsi, atau versi..."
            class="w-full pl-10"
            @keyup.enter="applyFilters"
          />
        </IconField>
      </div>

      <!-- DataTable -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <DataTable
          v-bind="serverSideConfig"
          :value="documents.data"
          @page="onPage"
          size="large"
        >
          <template #empty>
            <div class="text-center py-12">
              <IconNotebook size="30" class="text-slate-300 mx-auto mb-4" />
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery ? 'Tidak ada dokumen panduan ditemukan' : 'Belum ada dokumen panduan' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery ? 'Coba ubah kriteria pencarian' : 'Dokumen panduan yang dibuat akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column field="title" :header="'Dokumen (' + documents.total + ')'" :style="!isMobile ? 'min-width: 200px' : undefined" class="text-lg">
            <template #body="{ data }">
              <div class="flex items-start gap-3">
                <div class="flex-1 min-w-0">
                  <h4 class="flex items-center gap-2 font-semibold text-slate-900">
                    {{ data.title }}
                    <span v-if="data.version" class="hidden sm:inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-600">
                      {{ data.version }}
                    </span>
                    <span v-if="!data.file_exists" class="hidden sm:inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-600">
                      File Hilang
                    </span>
                  </h4>
                  <p v-if="data.description" class="text-sm text-slate-500 line-clamp-2 mb-1">
                    {{ truncateText(data.description, 100) }}
                  </p>
                  <div class="sm:hidden flex items-center gap-2">
                    <span v-if="data.version" class="inline-flex items-center text-xs font-medium text-orange-600">
                      {{ data.version }}
                    </span>
                    <span class="text-xs text-slate-500">{{ data.file_size }}</span>
                    <span v-if="!data.file_exists" class="inline-flex items-center text-xs font-medium text-red-600">
                      File Hilang
                    </span>
                  </div>
                </div>
              </div>
            </template>
          </Column>

          <Column field="file_size" header="Ukuran" style="min-width: 120px;" class="hidden sm:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ data.file_size || 'Tidak tersedia' }}</span>
            </template>
          </Column>

          <Column field="published_at" header="Diterbitkan" style="min-width: 150px;" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.published_at || data.created_at) }}</span>
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
                  class="!min-w-32"
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

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="showDeleteDialog" :modal="true" class="w-full max-w-md">
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-md">
          <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <IconAlertCircle class="text-red-600" size="24" />
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-slate-900">Hapus Dokumen</h3>
                <p class="text-slate-600 mt-1">Apakah Anda yakin ingin menghapus dokumen ini?</p>
              </div>
            </div>

            <div v-if="documentToDelete" class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-6">
              <div class="flex items-center gap-3">
                <IconFileText class="text-slate-400" size="20" />
                <div>
                  <p class="font-medium text-slate-900">{{ documentToDelete.title }}</p>
                  <p class="text-sm text-slate-500">{{ documentToDelete.file_size }}</p>
                </div>
              </div>
            </div>

            <div class="flex gap-3">
              <button
                @click="closeCallback"
                class="flex-1 px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors font-medium"
              >
                Batal
              </button>
              <button
                @click="deleteDocument"
                class="flex-1 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors font-medium"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
