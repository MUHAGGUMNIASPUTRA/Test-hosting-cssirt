<script setup>
// filepath: resources/js/Pages/Admin/Posts/Index.vue

import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  posts: Object,
  filters: Object,
})

const { isMobile } = useResponsive()

// --- Filter state ---
const searchQuery    = ref(props.filters?.search || '')
const selectedStatus = ref(props.filters?.status || '')

// --- Server-side DataTable + pagination ---
const paginatedData = computed(() => props.posts)

const { serverSideConfig, applyFilters, onPage, clearFilters, hasActiveFilters } = useAdminTable(
  paginatedData,
  'admin.posts.index',
  { search: searchQuery, status: selectedStatus }
)

// --- Delete dialog ---
const showDeleteDialog = ref(false)
const postToDelete = ref(null)

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
  })
}

// --- Helpers ---
const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

const statusOptions = [
  { label: 'Draft',       value: 'Draft' },
  { label: 'Diterbitkan', value: 'Published' },
]

// Action menu handling
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
      label: 'Baca',
      icon: 'pi pi-eye',
      command: () => { router.get(route('posts.show', item.slug)) },
    },
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { router.get(route('admin.posts.edit', item.id)) },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { confirmDeletePost(item) },
    }
  ]
})
</script>

<template>
  <AdminLayout title="Daftar Artikel">
    <!-- Delete Confirmation Dialog -->
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="artikel berikut"
      delete-label="Ya, Hapus Artikel"
      @confirm="deletePost"
    >
      <template #item-info>
        <div class="flex justify-between items-center mb-1">
          <span class="font-medium text-slate-600">Judul:</span>
          <span class="text-slate-900 text-right max-w-48 truncate">{{ postToDelete?.title }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="font-medium text-slate-600">Status:</span>
          <StatusBadge v-if="postToDelete" type="post-status" :value="postToDelete.status" />
        </div>
      </template>
    </DeleteConfirmDialog>

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
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Artikel</label>
            <IconField class="w-full">
              <InputIcon><i class="pi pi-search" /></InputIcon>
              <InputText
                v-model="searchQuery"
                placeholder="Cari berdasarkan judul artikel..."
                class="w-full"
                @keyup.enter="applyFilters"
              />
            </IconField>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Status</label>
            <Select v-model="selectedStatus" :options="statusOptions"
              optionLabel="label" optionValue="value"
              placeholder="Pilih Status" class="w-full" showClear @change="applyFilters" />
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <AdminDataTable :value="posts.data" :server-config="serverSideConfig" @page="onPage">
        <template #empty>
          <div class="text-center py-12">
            <IconArticle size="30" class="text-slate-300 mx-auto mb-4" />
            <p class="text-slate-500 text-lg font-medium">
              {{ hasActiveFilters ? 'Tidak ada artikel yang sesuai filter' : 'Belum ada artikel yang dibuat' }}
            </p>
            <p class="text-slate-400 mt-1 text-sm">
              {{ hasActiveFilters ? 'Coba ubah kriteria pencarian' : 'Artikel yang dibuat akan muncul di sini' }}
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
                <h3 class="font-medium mb-1 line-clamp-1">{{ data.title }}</h3>
                <div class="flex items-center gap-3 text-slate-500">
                  <span v-if="data.published_by" class="lg:hidden flex items-center gap-1">
                    <IconUserEdit size="14" stroke-width="1.5" />
                    <span class="text-sm">{{ data.published_by }}</span>
                  </span>
                  <span v-if="data.categories?.length > 0" class="flex items-center gap-1">
                    <IconCategory size="14" stroke-width="1.5" />
                    <span class="text-sm">
                      {{ !isMobile ? data.categories.map(c => c.name).join(', ') : data.categories[0].name }}
                    </span>
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
            <StatusBadge type="post-status" :value="data.status" />
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
      </AdminDataTable>
    </div>
  </AdminLayout>
</template>
