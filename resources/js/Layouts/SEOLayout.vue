<template>
  <div>
    <Head :title="title" />

    <div class="bg-white text-slate-800 antialiased">
      <!-- Modern SEO-friendly Navbar -->
      <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-lg border-b border-slate-200/50">
        <div class="container mx-auto px-4">
          <div class="flex h-16 items-center justify-between">
            <!-- Logo Section -->
            <div class="flex-shrink-0">
              <a href="/" class="flex items-center space-x-3 group">
                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                  <span class="text-white font-bold text-xs">CSIRT</span>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-slate-900">CSIRT Bojonegoro</h2>
                </div>
              </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-1">
              <!-- Regular Navigation Links -->
              <div class="flex items-center space-x-1 pr-4">
                <a href="/" class="relative px-4 py-2 font-medium rounded-lg transition-all duration-200 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50">
                  <span class="relative z-10">Beranda</span>
                </a>
                <a href="/profile" class="relative px-4 py-2 font-medium rounded-lg transition-all duration-200 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50">
                  <span class="relative z-10">Profil</span>
                </a>
                <a href="/services" class="relative px-4 py-2 font-medium rounded-lg transition-all duration-200 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50">
                  <span class="relative z-10">Layanan</span>
                </a>
                <a href="/posts" class="relative px-4 py-2 font-medium rounded-lg transition-all duration-200 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50">
                  <span class="relative z-10">Artikel</span>
                </a>
                <a href="/faq" class="relative px-4 py-2 font-medium rounded-lg transition-all duration-200 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50">
                  <span class="relative z-10">FAQ</span>
                </a>
                <a href="/contact" class="relative px-4 py-2 font-medium rounded-lg transition-all duration-200 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50">
                  <span class="relative z-10">Kontak</span>
                </a>
                <a href="/login" class="relative px-4 py-2 font-medium rounded-lg transition-all duration-200 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50">
                  <span class="relative z-10">Login</span>
                </a>
              </div>

              <!-- Report Incident Button -->
              <div class="pl-8 ml-8 border-l border-slate-300">
                <a href="/incident" class="inline-flex items-center px-4 py-2 font-semibold rounded-lg transition-all duration-200 bg-red-600 hover:bg-red-700 text-white border-red-600 hover:border-red-700">
                  <svg viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 mr-2">
                    <path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                  </svg>
                  Lapor Insiden
                </a>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <main class="pt-16">
        <slot />
      </main>

      <!-- Modern Footer -->
      <footer class="bg-slate-900 text-slate-300">
        <div class="container mx-auto px-4 py-8 sm:py-16">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Brand Section -->
            <div class="md:col-span-1">
              <div class="flex items-center space-x-3 mb-4">
                <div class="h-10 w-10 px-1 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                  <span class="text-white font-bold text-xs">CSIRT</span>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">CSIRT Bojonegoro</h3>
                </div>
              </div>
              <p class="text-slate-400 leading-relaxed">
                Tim Respons Insiden Keamanan Siber Pemerintah Kabupaten Bojonegoro yang berkomitmen melindungi aset digital pemerintahan.
              </p>
            </div>

            <!-- Quick Links -->
            <div>
              <h4 class="text-white font-semibold mb-4">Tautan Cepat</h4>
              <div class="flex items-center gap-12">
                <ul class="space-y-1">
                  <li>
                    <a href="/" class="hover:text-white transition-colors duration-200">Beranda</a>
                  </li>
                  <li>
                    <a href="/profile" class="hover:text-white transition-colors duration-200">Profil</a>
                  </li>
                  <li>
                    <a href="/services" class="hover:text-white transition-colors duration-200">Layanan</a>
                  </li>
                </ul>
                <ul class="space-y-1">
                  <li>
                    <a href="/posts" class="hover:text-white transition-colors duration-200">Artikel</a>
                  </li>
                  <li>
                    <a href="/faq" class="hover:text-white transition-colors duration-200">FAQ</a>
                  </li>
                  <li>
                    <a href="/contact" class="hover:text-white transition-colors duration-200">Kontak</a>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Contact Info -->
            <div>
              <h4 class="text-white font-semibold mb-4">Kontak Darurat</h4>
              <div class="space-y-3">
                <div class="flex items-start">
                  <svg class="h-4 w-4 mt-1 mr-2 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                  </svg>
                  <div>
                    <p class="text-red-400 font-semibold">Hotline Darurat</p>
                    <p class="text-slate-400">+62 353 881826</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <svg class="h-4 w-4 mt-1.5 mr-2 text-indigo-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                  </svg>
                  <span class="text-slate-400">ttis@bojonegorokab.go.id</span>
                </div>
              </div>
            </div>

            <!-- Address -->
            <div>
              <h4 class="text-white font-semibold mb-4">Alamat</h4>
              <div class="flex items-start">
                <svg class="h-4 w-4 mt-1 mr-2 text-indigo-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <div class="text-slate-400 leading-relaxed">
                  <p class="font-medium">Diskominfo Bojonegoro</p>
                  <p>Jl. P. Mas Tumapel No. 1</p>
                  <p>Bojonegoro, Jawa Timur 62115</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Bar -->
          <div class="mt-8 sm:mt-12 pt-8 border-t border-slate-800">
            <div class="flex flex-col md:flex-row justify-between items-center">
              <p class="text-slate-400">
                &copy; 2025 CSIRT Kabupaten Bojonegoro. Seluruh hak cipta dilindungi.
              </p>
              <p class="text-slate-500 mt-2 md:mt-0">
                Dikelola oleh Diskominfo Bojonegoro
              </p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
})

// Mock Head component for SSR compatibility
import { defineComponent } from 'vue'

const Head = defineComponent({
  props: ['title'],
  render() {
    // In SSR, we'll handle this differently
    return null
  }
})
</script>
