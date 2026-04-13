<script setup>
// filepath: resources/js/Pages/Documents/Index.vue
import { useParticles } from '@/Composables/useParticles'
import { useResponsive } from '@/Composables/useResponsive'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  documents: Object,
  filters: Object,
})

const { minimalParticlesOptions } = useParticles()
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

  router.get(
    url,
    {},
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}

// Handle pagination change
const onPage = (event) => {
  lazyParams.value.first = event.first
  lazyParams.value.rows = event.rows
  lazyParams.value.page = event.page + 1
  applyFilters()
}

const isLink = (path) =>
  path && (path.startsWith('http://') || path.startsWith('https://'))
</script>

<template>
  <AppLayout title="Panduan Keamanan Siber">
    <!-- Hero Section -->
    <section
      class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
    >
      <div class="absolute inset-0 z-0">
        <vue-particles
          id="tsparticles"
          :options="minimalParticlesOptions"
          class="h-full w-full"
        />
      </div>

      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container text-center">
          <h1
            class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl"
          >
            Dokumen
            <span
              class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"
              >Panduan</span
            >
          </h1>
          <p class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl">
            Akses panduan dan kebijakan keamanan siber untuk melindungi sistem
            digital instansi pemerintah dari berbagai ancaman
          </p>
        </div>
      </div>
    </section>

    <!-- Documents List -->
    <section class="bg-slate-50 py-8 sm:py-16 lg:py-20">
      <div class="container max-w-7xl">
        <!-- Search Section -->
        <div class="mb-8 sm:mb-12">
          <div class="mx-auto max-w-2xl">
            <div class="relative">
              <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
              >
                <svg
                  class="h-5 w-5 text-slate-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </div>
              <input
                v-model="searchQuery"
                @keyup.enter="applyFilters"
                type="text"
                class="block w-full rounded-2xl border border-slate-300 bg-white py-4 pl-12 pr-12 text-lg leading-5 placeholder-slate-500 focus:border-indigo-500 focus:placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                placeholder="Cari panduan berdasarkan judul, deskripsi, atau versi..."
              />
              <div
                v-if="searchQuery"
                class="absolute inset-y-0 right-0 flex items-center pr-4"
              >
                <button
                  @click="clearFilters"
                  class="rounded-full p-1 transition-colors duration-200 hover:bg-slate-100"
                >
                  <svg
                    class="h-5 w-5 text-slate-400 hover:text-slate-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Documents DataTable -->
        <div
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
          <DataTable
            v-bind="dtConfig()"
            :value="documents.data"
            @page="onPage"
            size="large"
          >
            <template #empty>
              <div class="py-16 text-center">
                <div
                  class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-100"
                >
                  <IconFileText class="text-slate-400" size="30" />
                </div>
                <h3 class="mb-2 text-xl font-semibold text-slate-900">
                  {{
                    searchQuery
                      ? 'Tidak ada dokumen yang sesuai'
                      : 'Belum Ada Dokumen'
                  }}
                </h3>
                <p class="text-slate-600">
                  {{
                    searchQuery
                      ? 'Coba gunakan kata kunci yang berbeda'
                      : 'Panduan dan dokumentasi akan segera tersedia di sini.'
                  }}
                </p>
              </div>
            </template>

            <Column
              field="title"
              header="Dokumen"
              :style="!isMobile ? 'min-width: 200px' : undefined"
              class="text-lg"
            >
              <template #body="{ data }">
                <div class="min-w-0 flex-1">
                  <h4
                    class="flex items-center gap-2 font-semibold text-slate-900"
                  >
                    <a
                      :href="route('documents.view', data.slug)"
                      target="_blank"
                      class="cursor-pointer transition-colors duration-200 hover:text-blue-600"
                    >
                      {{ data.title }}
                    </a>
                    <span
                      v-if="data.document_area"
                      class="hidden items-center rounded-lg bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700 sm:inline-flex"
                    >
                      {{ data.document_area.name }}
                    </span>
                    <span
                      v-if="!data.official_file_path"
                      class="hidden items-center rounded-lg bg-red-100 px-2 py-1 text-xs font-medium text-red-700 sm:inline-flex"
                    >
                      File Hilang
                    </span>
                  </h4>
                  <p
                    v-if="data.description"
                    class="mb-1 text-sm text-slate-500"
                  >
                    {{ data.description }}
                  </p>
                  <div class="flex items-center gap-2 sm:hidden">
                    <span
                      v-if="data.document_area"
                      class="inline-flex items-center text-xs font-medium text-indigo-600"
                    >
                      {{ data.document_area.name }}
                    </span>
                    <span
                      v-if="!data.official_file_path"
                      class="inline-flex items-center text-xs font-medium text-red-600"
                    >
                      File Hilang
                    </span>
                  </div>
                </div>
              </template>
            </Column>

            <Column
              header="Aksi"
              :style="!isMobile ? 'min-width: 120px' : undefined"
              class="text-lg"
              :pt="{ columnHeaderContent: 'justify-end' }"
            >
              <template #body="{ data }">
                <div class="flex items-center justify-end gap-2">
                  <a
                    v-if="data.official_file_path"
                    :href="route('documents.view', data.slug)"
                    :target="
                      isLink(data.official_file_path) ? '_blank' : '_blank'
                    "
                    class="rounded-lg bg-indigo-50 p-2 text-indigo-600 transition-colors duration-200 hover:bg-indigo-200"
                    title="Lihat Dokumen"
                  >
                    <IconEye size="16" />
                  </a>
                  <a
                    v-if="
                      data.official_file_path &&
                      !isLink(data.official_file_path)
                    "
                    :href="route('documents.download', data.slug)"
                    class="rounded-lg bg-blue-50 p-2 text-blue-600 transition-colors duration-200 hover:bg-blue-200"
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
