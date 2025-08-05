<script setup>
// filepath: resources/js/Pages/RFC2350/Index.vue

import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  document: Object,
})

const { isMobile } = useResponsive()
const pdfViewer = ref(null)

const viewPDF = () => {
  if (pdfViewer.value) {
    pdfViewer.value.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<template>
  <AppLayout title="RFC 2350">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container text-center">
          <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl">
            RFC <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">2350</span>
          </h1>
          <p class="mx-auto mt-6 max-w-3xl text-xl sm:text-2xl text-slate-300">
            Dokumen Deskripsi CSIRT Bojonegoro sesuai standar RFC 2350 yang berisi informasi komprehensif tentang tim, layanan, dan prosedur
          </p>

          <!-- No Document Available -->
          <div v-if="!document || !document.file_exists" class="mt-10">
            <div class="bg-yellow-100/20 backdrop-blur-sm rounded-2xl p-8 max-w-md mx-auto">
              <div class="w-16 h-16 bg-yellow-100/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-white mb-2">Dokumen Belum Tersedia</h3>
              <p class="text-slate-300">
                Dokumen RFC 2350 sedang dalam proses penyusunan dan akan segera tersedia.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Document Info Section (only show if document exists) -->
    <section v-if="document && document.file_exists" class="py-8 sm:py-16 lg:py-20 bg-slate-50">
      <div class="container">
        <!-- PDF Viewer Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="p-4 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-slate-900">{{ document.title }}</h3>
              <div class="hidden sm:flex items-center gap-3">
                <span class="text-sm text-slate-500">Format: PDF | Ukuran: {{ document.file_size }}</span>
                <a
                  :href="route('rfc2350.download')"
                  class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200"
                >
                  <IconFileDownload size="14" class="mr-2"/>
                  Download
                </a>
              </div>
            </div>
          </div>

          <!-- PDF Embed -->
          <div class="relative" style="height: 800px;">
            <iframe
              ref="pdfViewer"
              :src="route('rfc2350.view') + '#toolbar=1&navpanes=1&scrollbar=1'"
              class="w-full h-full border-0"
              :title="document.title"
            >
              <!-- Fallback for browsers that don't support PDF embedding -->
              <div class="flex flex-col items-center justify-center h-full bg-slate-50">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-6">
                  <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 mb-2">Browser tidak mendukung tampilan PDF</h3>
                <p class="text-slate-600 mb-4 text-center max-w-md">
                  Browser Anda tidak mendukung tampilan PDF secara langsung. Silakan download dokumen untuk membacanya.
                </p>
                <a
                  :href="route('rfc2350.download')"
                  class="inline-flex items-center px-6 py-3 text-lg font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200"
                >
                  <IconFileDownload size="14" class="mr-2"/>
                  Download RFC 2350
                </a>
              </div>
            </iframe>
          </div>
        </div>
      </div>
    </section>

    <!-- Related Documents -->
    <section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900">
      <div class="container text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-6">Dokumen Lainnya</h2>
        <p class="mx-auto max-w-2xl text-xl sm:text-2xl text-slate-300 mb-8">Lihat dokumen panduan dan kebijakan keamanan siber lainnya</p>

        <Link
          :href="route('documents.index')"
          class="inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 sm:text-lg font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-600 rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300"
        >
          <IconFileStack :size="isMobile ? 16 : 20" class="mr-3"/>
          Lihat Semua Dokumen
        </Link>
      </div>
    </section>
  </AppLayout>
</template>
