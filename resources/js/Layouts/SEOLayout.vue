<template>
  <div>
    <Head :title="title" />

    <div class="bg-white text-slate-800 antialiased">
      <!-- Modern SEO-friendly Navbar -->
      <nav
        class="fixed left-0 right-0 top-0 z-50 border-b border-slate-200/50 bg-white/95 shadow-lg backdrop-blur-md"
      >
        <div class="container mx-auto px-4">
          <div class="flex h-16 items-center justify-between">
            <!-- Logo Section -->
            <div class="flex-shrink-0">
              <a href="/" class="group flex items-center space-x-3">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg"
                >
                  <span class="text-xs font-bold text-white">CSIRT</span>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-slate-900">
                    CSIRT Bojonegoro
                  </h2>
                </div>
              </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden items-center space-x-1 lg:flex">
              <!-- Regular Navigation Links -->
              <div class="flex items-center space-x-1 pr-4">
                <a
                  href="/"
                  class="relative rounded-lg px-4 py-2 font-medium text-slate-700 transition-all duration-200 hover:bg-indigo-50 hover:text-indigo-600"
                >
                  <span class="relative z-10">Beranda</span>
                </a>
                <a
                  href="/profile"
                  class="relative rounded-lg px-4 py-2 font-medium text-slate-700 transition-all duration-200 hover:bg-indigo-50 hover:text-indigo-600"
                >
                  <span class="relative z-10">Profil</span>
                </a>
                <a
                  href="/services"
                  class="relative rounded-lg px-4 py-2 font-medium text-slate-700 transition-all duration-200 hover:bg-indigo-50 hover:text-indigo-600"
                >
                  <span class="relative z-10">Layanan</span>
                </a>
                <a
                  href="/posts"
                  class="relative rounded-lg px-4 py-2 font-medium text-slate-700 transition-all duration-200 hover:bg-indigo-50 hover:text-indigo-600"
                >
                  <span class="relative z-10">Artikel</span>
                </a>
                <a
                  href="/faq"
                  class="relative rounded-lg px-4 py-2 font-medium text-slate-700 transition-all duration-200 hover:bg-indigo-50 hover:text-indigo-600"
                >
                  <span class="relative z-10">FAQ</span>
                </a>
                <a
                  href="/contact"
                  class="relative rounded-lg px-4 py-2 font-medium text-slate-700 transition-all duration-200 hover:bg-indigo-50 hover:text-indigo-600"
                >
                  <span class="relative z-10">Kontak</span>
                </a>
                <a
                  href="/login"
                  class="relative rounded-lg px-4 py-2 font-medium text-slate-700 transition-all duration-200 hover:bg-indigo-50 hover:text-indigo-600"
                >
                  <span class="relative z-10">Login</span>
                </a>
              </div>

              <!-- Report Incident Button -->
              <div class="ml-8 border-l border-slate-300 pl-8">
                <a
                  href="/incident"
                  class="inline-flex items-center rounded-lg border-red-600 bg-red-600 px-4 py-2 font-semibold text-white transition-all duration-200 hover:border-red-700 hover:bg-red-700"
                >
                  <svg
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="mr-2 h-3 w-3"
                  >
                    <path
                      d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z"
                    />
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
          <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
            <!-- Brand Section -->
            <div class="md:col-span-1">
              <div class="mb-4 flex items-center space-x-3">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 px-1 shadow-lg"
                >
                  <span class="text-xs font-bold text-white">CSIRT</span>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">CSIRT Bojonegoro</h3>
                </div>
              </div>
              <p class="leading-relaxed text-slate-400">
                Tim Respons Insiden Keamanan Siber Pemerintah Kabupaten
                Bojonegoro yang berkomitmen melindungi aset digital
                pemerintahan.
              </p>
            </div>

            <!-- Quick Links -->
            <div>
              <h4 class="mb-4 font-semibold text-white">Tautan Cepat</h4>
              <div class="flex items-center gap-12">
                <ul class="space-y-1">
                  <li>
                    <a
                      href="/"
                      class="transition-colors duration-200 hover:text-white"
                      >Beranda</a
                    >
                  </li>
                  <li>
                    <a
                      href="/profile"
                      class="transition-colors duration-200 hover:text-white"
                      >Profil</a
                    >
                  </li>
                  <li>
                    <a
                      href="/services"
                      class="transition-colors duration-200 hover:text-white"
                      >Layanan</a
                    >
                  </li>
                </ul>
                <ul class="space-y-1">
                  <li>
                    <a
                      href="/posts"
                      class="transition-colors duration-200 hover:text-white"
                      >Artikel</a
                    >
                  </li>
                  <li>
                    <a
                      href="/faq"
                      class="transition-colors duration-200 hover:text-white"
                      >FAQ</a
                    >
                  </li>
                  <li>
                    <a
                      href="/contact"
                      class="transition-colors duration-200 hover:text-white"
                      >Kontak</a
                    >
                  </li>
                </ul>
              </div>
            </div>

            <!-- Contact Info -->
            <div>
              <h4 class="mb-4 font-semibold text-white">Kontak Darurat</h4>
              <div class="space-y-3">
                <div class="flex items-start">
                  <svg
                    class="mr-2 mt-1 h-4 w-4 flex-shrink-0 text-red-400"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
                    />
                  </svg>
                  <div>
                    <p class="font-semibold text-red-400">Hotline Darurat</p>
                    <p class="text-slate-400">+62 353 881826</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <svg
                    class="mr-2 mt-1.5 h-4 w-4 flex-shrink-0 text-indigo-400"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                    />
                  </svg>
                  <a
                    :href="`mailto:${contact.email}`"
                    class="hover:no-underline"
                  >
                    <span class="text-slate-400">{{ contact.email }}</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- Address -->
            <div>
              <h4 class="mb-4 font-semibold text-white">Alamat</h4>
              <div class="flex items-start">
                <svg
                  class="mr-2 mt-1 h-4 w-4 flex-shrink-0 text-indigo-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <div class="leading-relaxed text-slate-400">
                  <p class="font-medium">Diskominfo Bojonegoro</p>
                  <p>Jl. P. Mas Tumapel No. 1</p>
                  <p>Bojonegoro, Jawa Timur 62115</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Bar -->
          <div class="mt-8 border-t border-slate-800 pt-8 sm:mt-12">
            <div class="flex flex-col items-center justify-between md:flex-row">
              <p class="text-slate-400">
                &copy; 2025 CSIRT Kabupaten Bojonegoro. Seluruh hak cipta
                dilindungi.
              </p>
              <p class="mt-2 text-slate-500 md:mt-0">
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
  },
})
</script>
