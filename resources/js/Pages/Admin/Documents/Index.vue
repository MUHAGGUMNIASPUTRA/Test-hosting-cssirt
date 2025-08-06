<script setup>
// filepath: resources/js/Pages/Admin/Documents/Index.vue

import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
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

const getStatusSeverity = (status) => {
  const severities = {
    'Published': 'success',
    'Draft': 'warn',
    'Scheduled': 'info'
  }
  return severities[status] || 'info'
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
</script>

<template>
  <AdminLayout title="Kelola Panduan">
    <ConfirmDialog :style="{ width: isMobile ? '95vw' : undefined }" />

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Kelola Panduan & Dokumentasi</h2>
            <p class="text-slate-600">Kelola dokumen panduan keamanan siber dan dokumentasi teknis</p>
          </div>
          <Link
            :href="route('admin.documents.create')"
            class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
          >
            <IconFilePlus size="16" />
            Tambah Dokumen
          </Link>
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

        <div class="grid grid-cols-1 gap-4">
          <div>
            <InputText
              id="search"
              v-model="searchQuery"
              placeholder="Cari berdasarkan judul, deskripsi, atau versi..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </div>
        </div>
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
              <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <h3 class="text-lg font-medium text-slate-900 mb-2">Belum ada dokumen</h3>
              <p class="text-slate-500 mb-4">Mulai dengan menambahkan dokumen pertama Anda.</p>
              <Link
                :href="route('admin.documents.create')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                <IconFilePlus size="16" class="mr-2" />
                Tambah Dokumen
              </Link>
            </div>
          </template>

          <Column field="title" :header="'Dokumen (' + documents.total + ')'" :style="!isMobile ? 'min-width: 200px' : undefined" class="text-lg">
            <template #body="{ data }">
              <div class="flex items-start gap-3">
                <div class="flex-1 min-w-0">
                  <h4 class="flex items-center gap-2 font-semibold text-slate-900">
                    {{ data.title }}
                    <span v-if="data.version" class="hidden sm:flex inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">
                      {{ data.version }}
                    </span>
                    <span v-if="!data.file_exists" class="hidden sm:flex inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
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
                <a
                  v-if="data.file_exists"
                  :href="`/storage/${data.file_path}`"
                  target="_blank"
                  class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  title="Lihat File"
                >
                  <IconEye size="14" />
                </a>
                <a
                  v-if="data.file_exists"
                  :href="route('documents.download', data.slug)"
                  class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                  title="Download"
                >
                  <IconDownload size="14" />
                </a>
                <Link
                  :href="route('admin.documents.edit', data.id)"
                  class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Edit"
                >
                  <IconEdit size="14" />
                </Link>
                <button
                  @click="confirmDeleteDocument(data)"
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
