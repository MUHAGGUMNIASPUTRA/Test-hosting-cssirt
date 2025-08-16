<script setup>
// filepath: resources/js/Pages/Admin/Posts/Index.vue

import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
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
      label: 'Baca',
      icon: 'pi pi-eye',
      command: () => { router.get(route('posts.show', item.slug)); },
    },
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { router.get(route('admin.posts.edit', item.id)); },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { confirmDeletePost(item); },
    }
  ];
});
</script>

<template>
  <AdminLayout title="Daftar Artikel">
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
              <p class="text-slate-700 mb-4 sm:mb-6">Apakah Anda yakin ingin menghapus artikel berikut?</p>
              <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-left">
                <div class="">
                  <div class="flex justify-between items-center mb-1">
                    <span class="font-medium text-slate-600">Judul:</span>
                    <span class="text-slate-900 text-right line-clamp-1">
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
              <p class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg py-4 mt-3">
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
                @click="deletePost"
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
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Daftar Artikel</h2>
            <p class="text-slate-600">Kelola artikel dan konten website</p>
          </div>
          <Button
            severity="primary"
            @click="() => router.get(route('admin.posts.create'))"
            class="w-full sm:w-auto"
          >
            <IconTextPlus size="16" />
              Tambah Artikel
          </Button>
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
              <IconArticle size="30" class="text-slate-300 mx-auto mb-4" />
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedStatus ? 'Tidak ada artikel ditemukan' : 'Belum ada artikel yang dibuat' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedStatus ? 'Coba ubah kriteria pencarian' : 'Artikel yang dibuat akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column :header="`Artikel (${posts.total})`">
            <template #body="{ data }">
              <div class="flex items-center gap-3">
                <div class="hidden lg:flex flex-shrink-0 relative w-12 h-12 overflow-hidden rounded-xl group">
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
                    <span v-if="data.published_by" class="lg:hidden flex items-center gap-1">
                      <IconUserEdit size="14" stroke-width="1.5"/>
                      <span class="text-sm">{{ data.published_by }}</span>
                    </span>
                    <span v-if="data.categories?.length > 0" class="flex items-center gap-1">
                      <IconCategory size="14" stroke-width="1.5"/>
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

          <Column field="status" header="Status" class="hidden lg:table-cell">
            <template #body="{ data }">
              <Tag
                :value="data.status === 'Published' ? 'Diterbitkan' : 'Draft'"
                :severity="getStatusSeverity(data.status)"
                size="small"
              />
            </template>
          </Column>

          <Column field="published_at" header="Diterbitkan" class="hidden lg:table-cell">
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
