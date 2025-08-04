<script setup>
// filepath: resources/js/Pages/Documents/Index.vue
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  documents: Object,
  filters: Object,
})

const { isMobile, dtConfig } = useResponsive()

// Reactive state
const searchQuery = ref(props.filters?.search || '')

// Pagination
const lazyParams = ref({
  first: 0,
  rows: 10,
  page: 1,
})

const clearFilters = () => {
  searchQuery.value = ''
  lazyParams.value.page = 1
  lazyParams.value.first = 0
  applyFilters()
}

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('documents.index') + (queryString ? '?' + queryString : '')

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

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const truncateText = (text, length = 100) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Calculate total file size display
const totalFileSize = computed(() => {
  return props.documents.total + ' files'
})
</script>

<template>
  <AppLayout title="Panduan Keamanan Siber">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container mx-auto text-center">
          <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl">
            Panduan & <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">Dokumentasi</span>
          </h1>
          <p class="mx-auto mt-6 max-w-3xl text-xl sm:text-2xl text-slate-300">
            Akses panduan, kebijakan, dan dokumentasi keamanan siber untuk meningkatkan
            keamanan digital instansi pemerintah
          </p>
        </div>
      </div>
    </section>

    <!-- Documents List -->
    <section class="py-8 sm:py-16 lg:py-20 bg-slate-50">
      <div class="container mx-auto max-w-7xl">

        <!-- Search Filter -->
        <div class="flex flex-row gap-2 mb-8">
          <div class="flex-1">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                id="search"
                v-model="searchQuery"
                type="text"
                class="block w-full pl-12 pr-4 py-4 text-lg border border-slate-300 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Cari berdasarkan judul, deskripsi, atau versi..."
                @keyup.enter="applyFilters"
              />
            </div>
          </div>
          <Button
            v-if="searchQuery"
            @click="clearFilters"
            icon="pi pi-times"
            variant="text"
            severity="secondary"
            class="!text-red-600"
          />
        </div>

        <!-- Documents DataTable -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <DataTable
            v-bind="dtConfig()"
            :value="documents.data"
            @page="onPage"
            size="large"
          >
            <template #empty>
              <div class="text-center py-16">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                  <IconFileText class="text-slate-400" size="30" />
                </div>
                <h3 class="text-xl font-semibold text-slate-900 mb-2">
                  {{ searchQuery ? 'Tidak ada dokumen yang sesuai' : 'Belum Ada Dokumen' }}
                </h3>
                <p class="text-slate-600">
                  {{ searchQuery ? 'Coba gunakan kata kunci yang berbeda' : 'Panduan dan dokumentasi akan segera tersedia di sini.' }}
                </p>
              </div>
            </template>

            <Column field="title" header="Dokumen" :style="!isMobile ? 'min-width: 200px' : undefined" class="text-lg">
              <template #body="{ data }">
                  <div class="flex-1 min-w-0">
                    <h4 class="flex items-center gap-2 font-semibold text-slate-900">
                      <a
                        :href="route('documents.view', data.slug)"
                        target="_blank"
                        class="hover:text-blue-600 cursor-pointer transition-colors duration-200"
                      >
                        {{ data.title }}.pdf
                      </a>
                      <span v-if="data.version" class="hidden sm:flex inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">
                        v{{ data.version }}
                      </span>
                    </h4>
                    <p v-if="data.description" class="text-sm text-slate-500 mt-1">
                      {{ data.description }}
                    </p>
                    <div class="sm:hidden flex items-center gap-2 mt-2">
                      <span v-if="data.version" class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-700">
                        v{{ data.version }}
                      </span>
                      <span class="text-xs text-slate-500">{{ data.file_size }}</span>
                      <span v-if="!data.file_exists" class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-700">
                        File Hilang
                      </span>
                    </div>
                  </div>
              </template>
            </Column>

            <Column header="Ukuran" style="min-width: 120px;" class="hidden sm:table-cell text-lg">
              <template #body="{ data }">
                <span class="text-sm text-slate-600">{{ data.file_size || 'N/A' }}</span>
              </template>
            </Column>

            <Column header="Aksi" :style="!isMobile ? 'min-width: 120px' : undefined" class="text-lg" :pt="{columnHeaderContent: 'justify-end' }">
              <template #body="{ data }">
                <div class="flex items-center justify-end gap-2">
                  <a
                    v-if="data.file_exists"
                    :href="route('documents.view', data.slug)"
                    target="_blank"
                    class="bg-indigo-50 p-2 rounded-lg text-indigo-600 hover:bg-indigo-200 transition-colors duration-200"
                    title="Lihat Dokumen"
                  >
                    <IconEye size="16" />
                  </a>
                  <a
                    v-if="data.file_exists"
                    :href="route('documents.download', data.slug)"
                    class="bg-blue-50 p-2 rounded-lg text-blue-600 hover:bg-blue-200 transition-colors duration-200"
                    title="Download Dokumen"
                  >
                    <IconDownload size="16" />
                  </a>
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
