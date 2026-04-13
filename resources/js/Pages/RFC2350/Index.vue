<script setup>
// filepath: resources/js/Pages/RFC2350/Index.vue

import { useParticles } from '@/Composables/useParticles'
import { useResponsive } from '@/Composables/useResponsive'
import { Link } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

const props = defineProps({
  document: Object,
})

const { minimalParticlesOptions } = useParticles()

const { isMobile } = useResponsive()
const pdfViewer = ref(null)
const pdfUrl = route('rfc2350.view')
const isPdfAvailable = ref(false)

onMounted(async () => {
  try {
    const res = await fetch(pdfUrl, { method: 'HEAD' })
    isPdfAvailable.value = res.ok
  } catch (e) {
    isPdfAvailable.value = false
  }
})
</script>

<template>
  <AppLayout title="RFC 2350">
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
            RFC
            <span
              class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"
              >2350</span
            >
          </h1>
          <p class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl">
            Dokumen Deskripsi CSIRT Bojonegoro sesuai standar RFC 2350 yang
            berisi informasi komprehensif tentang tim, layanan, dan prosedur
          </p>

          <!-- No Document Available -->
          <div v-if="!document || !document.file_exists" class="mt-10">
            <div
              class="mx-auto max-w-md rounded-2xl bg-yellow-100/20 p-8 backdrop-blur-sm"
            >
              <div
                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100/30"
              >
                <svg
                  class="h-8 w-8 text-yellow-300"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                  />
                </svg>
              </div>
              <h3 class="mb-2 text-xl font-semibold text-white">
                Dokumen Belum Tersedia
              </h3>
              <p class="text-slate-300">
                Dokumen RFC 2350 sedang dalam proses penyusunan dan akan segera
                tersedia.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Document Info Section (only show if document exists) -->
    <section
      v-if="document && document.file_exists"
      class="bg-slate-50 py-8 sm:py-16 lg:py-20"
    >
      <div class="container">
        <!-- PDF Viewer Section -->
        <div
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >
          <div class="border-b border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-slate-900">
                {{ document.title }}
              </h3>
              <div
                v-if="isPdfAvailable"
                class="hidden items-center gap-3 sm:flex"
              >
                <span class="text-sm text-slate-500"
                  >Format: PDF | Ukuran: {{ document.file_size }}</span
                >
                <a
                  :href="route('rfc2350.download')"
                  class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white transition-colors duration-200 hover:bg-blue-700"
                >
                  <IconFileDownload size="14" class="mr-2" />
                  Download
                </a>
              </div>
            </div>
          </div>

          <!-- PDF Embed -->
          <div
            class="relative"
            :class="
              !isMobile
                ? isPdfAvailable
                  ? 'h-[800px]'
                  : 'h-[400px]'
                : 'h-[450px]'
            "
          >
            <iframe
              v-if="isPdfAvailable"
              ref="pdfViewer"
              :src="pdfUrl + '#toolbar=1&navpanes=1&scrollbar=1'"
              class="h-full w-full border-0"
              :title="document.title"
              @error="onIframeError"
            >
            </iframe>

            <!-- Fallback for browsers that don't support PDF embedding -->
            <div
              v-else
              class="flex h-full flex-col items-center justify-center bg-slate-50 p-4"
            >
              <div
                class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-red-100"
              >
                <IconFileTypePdf size="40" class="text-red-600" />
              </div>
              <h3 class="mb-1 text-xl font-semibold text-slate-900">
                Browser tidak mendukung tampilan PDF
              </h3>
              <p class="mb-4 text-center text-slate-600">
                Browser Anda tidak mendukung tampilan PDF secara langsung.
                Silakan download dokumen untuk membacanya.
              </p>
              <p class="mb-4 text-sm text-slate-400">
                Ukuran: {{ document.file_size }}
              </p>
              <a
                :href="route('rfc2350.download')"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition-colors duration-200 hover:bg-blue-700"
              >
                <IconFileDownload size="16" class="mr-2" />
                Download RFC 2350
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Related Documents -->
    <section
      class="bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900 py-12 sm:py-16 lg:py-20"
    >
      <div class="container text-center">
        <h2
          class="mb-6 text-3xl font-extrabold text-white sm:text-4xl lg:text-5xl"
        >
          Dokumen Lainnya
        </h2>
        <p class="mx-auto mb-8 max-w-2xl text-xl text-slate-300 sm:text-2xl">
          Lihat dokumen panduan dan kebijakan keamanan siber lainnya
        </p>

        <Link
          :href="route('documents.index')"
          class="inline-flex transform items-center rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-3 font-semibold text-white shadow-xl transition-all duration-300 hover:scale-105 hover:shadow-2xl sm:px-8 sm:py-4 sm:text-lg"
        >
          <IconFileStack :size="isMobile ? 16 : 20" class="mr-3" />
          Lihat Semua Dokumen
        </Link>
      </div>
    </section>
  </AppLayout>
</template>
