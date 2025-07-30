<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useConfirm } from "primevue/useconfirm"
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  posts: Object,
  filters: Object,
})

const { isMobile, dtConfig } = useResponsive()
const confirm = useConfirm()

// Search and filters - exactly like incidents
const searchQuery = ref(props.filters?.search || '')
const selectedStatus = ref(props.filters?.status || '')
const showDeleteDialog = ref(false)
const postToDelete = ref(null)

// Add pagination state
const lazyParams = ref({
  first: 0,
  rows: props.posts.per_page || 10,
  page: props.posts.current_page || 1
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.posts.index') + (queryString ? '?' + queryString : '')

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
  const url = route('admin.posts.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const statusOptions = [
  { label: 'Draft', value: 'Draft' },
  { label: 'Diterbitkan', value: 'Published' },
]

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  lazyParams.value.page = 1
  lazyParams.value.first = 0
  applyFilters()
}

const confirmDeletePost = (post) => {
  postToDelete.value = post
  showDeleteDialog.value = true
}

const deletePost = () => {
  if (!postToDelete.value) return

  router.delete(route('admin.posts.destroy', postToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      postToDelete.value = null
    },
    onError: () => {}
  })
}

const getStatusSeverity = (status) => {
  const severities = {
    'Published': 'success',
    'Draft': 'warn'
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

const truncateText = (text, length = 60) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Stats computed
const stats = computed(() => {
  const allData = props.posts.data || []
  const published = allData.filter(post => post.status === 'Published').length
  const draft = allData.filter(post => post.status === 'Draft').length

  return { published, draft }
})

// Server-side DataTable configuration
const serverSideConfig = computed(() => {
  return {
    ...dtConfig(),
    lazy: true,
    totalRecords: props.posts.total,
    first: (props.posts.current_page - 1) * props.posts.per_page,
    rows: props.posts.per_page,
  }
})
</script>

<template>
  <AdminLayout title="Daftar Artikel">
    <ConfirmDialog />

    <!-- Custom Delete Confirmation Dialog -->
    <Dialog
      v-model:visible="showDeleteDialog"
      :modal="true"
      :closable="false"
      class="w-full max-w-md"
    >
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
          <!-- Header -->
          <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-lg font-semibold text-white">Konfirmasi Penghapusan</h3>
                <p class="text-red-100 text-sm">Tindakan ini tidak dapat dibatalkan</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <div class="text-center mb-6">
              <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              <p class="text-slate-700 mb-2">
                Apakah Anda yakin ingin menghapus artikel berikut?
              </p>
              <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-left">
                <div class="">
                  <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-slate-600">Judul:</span>
                    <span class="text-slate-900 text-right max-w-48 truncate">
                      {{ postToDelete?.title }}
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium text-slate-600">Status:</span>
                    <Tag
                      :value="postToDelete?.status === 'Published' ? 'Diterbitkan' : 'Draft'"
                      :severity="getStatusSeverity(postToDelete?.status)"
                      size="small"
                    />
                  </div>
                </div>
              </div>
              <p class="text-sm text-red-600 mt-3">
                <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
              <Button
                @click="closeCallback"
                label="Batal"
                severity="secondary"
                size="small"
              />
              <Button
                @click="deletePost"
                label="Ya, Hapus Artikel"
                severity="danger"
                size="small"
              />
            </div>
          </div>
        </div>
      </template>
    </Dialog>

    <div class="space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Daftar Artikel</h2>
            <p class="text-slate-600">Kelola artikel dan konten website</p>
          </div>
          <Link
            :href="route('admin.posts.create')"
            class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
          >
            <span class="material-symbols-outlined !text-xl">post_add</span>
              Tambah Artikel
          </Link>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Artikel</label>
            <div class="relative">
              <IconField class="w-full">
                <InputIcon>
                  <i class="pi pi-search" />
                </InputIcon>
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari berdasarkan judul artikel..."
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
          :value="posts.data"
          @page="onPage"
        >
          <template #empty>
            <div class="text-center py-12">
              <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedStatus ? 'Tidak ada artikel yang sesuai filter' : 'Belum ada artikel yang dibuat' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedStatus ? 'Coba ubah kriteria pencarian' : 'Artikel yang dibuat akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column :header="`Artikel (${posts.total})`">
            <template #body="{ data }">
              <div class="flex items-center gap-3">
                <div class="hidden lg:flex flex-shrink-0 relative w-16 h-16 overflow-hidden rounded-xl group">
                  <PostImage
                    :post="data"
                    class="object-cover block h-full group-hover:scale-110 transition-transform duration-300"
                  />
                </div>

                <div class="flex-1 min-w-0">
                  <h3 class="font-medium mb-1 line-clamp-1">
                    {{ data.title }}
                  </h3>

                  <div class="flex items-center gap-3 text-slate-500">
                    <span v-if="data.published_by" class="lg:hidden">
                      <span class="material-symbols-outlined icon-wght-300 !text-xl mr-1">article_person</span>
                      <span class="text-sm">{{ data.published_by }}</span>
                    </span>
                    <span v-if="data.categories?.length > 0">
                      <span class="material-symbols-outlined icon-wght-300 !text-xl mr-1">category</span>
                      <span class="text-sm">{{ !isMobile ? data.categories.map(cat => cat.name).join(', ') : data.categories[0].name }}</span>
                    </span>
                  </div>
                </div>
              </div>
            </template>
          </Column>

          <Column field="published_by" header="Penulis" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">
                {{ data.published_by || 'Tidak diketahui' }}
              </span>
            </template>
          </Column>

          <Column field="status" header="Status" class="hidden sm:table-cell">
            <template #body="{ data }">
              <Tag
                :value="data.status === 'Published' ? 'Diterbitkan' : 'Draft'"
                :severity="getStatusSeverity(data.status)"
                size="small"
              />
            </template>
          </Column>

          <Column field="published_at" header="Dipublikasi" class="hidden sm:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.published_at || data.created_at) }}</span>
            </template>
          </Column>

          <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
            <template #body="{ data }">
              <div class="flex items-center justify-end space-x-2">
                <Link
                  :href="route('admin.posts.edit', data.id)"
                  class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
                <button
                  @click="confirmDeletePost(data)"
                  class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Hapus"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
  </AdminLayout>
</template>
