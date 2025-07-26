<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

// The 'title' prop will be passed from individual pages
defineProps({
  title: String,
})

// State for mobile menu and scroll
const isMenuOpen = ref(false)
const isScrolled = ref(false)
const page = usePage()

// Check if current page is landing/homepage
const isLandingPage = computed(() => {
  return page.url === '/' || page.url === ''
})

// Handle scroll for navbar styling
onMounted(() => {
  const handleScroll = () => {
    isScrolled.value = window.scrollY > 10
  }

  window.addEventListener('scroll', handleScroll)

  // Cleanup
  return () => {
    window.removeEventListener('scroll', handleScroll)
  }
})

// Dynamic navbar classes based on page and scroll state
const navbarClasses = computed(() => {
  if (isLandingPage.value && !isScrolled.value) {
    return 'bg-transparent'
  }
  return 'bg-white/95 backdrop-blur-md shadow-lg border-b border-slate-200/50'
})

// Dynamic text colors for navbar
const navTextClasses = computed(() => {
  if (isLandingPage.value && !isScrolled.value) {
    return {
      logo: 'text-white',
      subtitle: 'text-slate-300',
      link: 'text-slate-300 hover:text-white hover:bg-white/10',
      mobile: 'text-white hover:bg-white/10'
    }
  }
  return {
    logo: 'text-slate-900',
    subtitle: 'text-slate-600',
    link: 'text-slate-700 hover:text-indigo-600 hover:bg-indigo-50',
    mobile: 'text-slate-700 hover:bg-slate-100'
  }
})
</script>

<template>
  <div>
    <Head :title="title" />
    <loading-page />

    <div class="bg-white text-slate-800 antialiased">
      <!-- Modern Navbar -->
      <nav
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
        :class="navbarClasses"
      >
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 items-center justify-between">
            <!-- Logo Section -->
            <div class="flex-shrink-0">
              <Link :href="route('landing')" class="flex items-center space-x-3 group">
                <div class="relative">
                  <!-- Government Crest -->
                  <div v-if="isLandingPage && !isScrolled" class="h-9 w-9 rounded-full bg-white/10 border-2 border-white/20 flex items-center justify-center mr-2">
                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <!-- Regular logo for other states -->
                  <div v-else class="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-xs">CSIRT</span>
                  </div>
                </div>
                <div class="hidden sm:block">
                  <h2
                    class="text-lg font-bold transition-colors duration-200"
                    :class="navTextClasses.logo"
                  >
                    CSIRT Bojonegoro
                  </h2>
                  <p
                    class="text-xs transition-colors duration-200"
                    :class="navTextClasses.subtitle"
                  >
                    Kabupaten Bojonegoro
                  </p>
                </div>
              </Link>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:block">
              <div class="flex items-center space-x-1">
                <Link
                  :href="route('landing')"
                  class="relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Beranda</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('profile.show')"
                  class="relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Profil</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('services.index')"
                  class="relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Layanan</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('posts.index')"
                  class="relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Artikel</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('contact.index')"
                  class="relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Kontak</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
              </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex md:hidden">
              <button
                @click="isMenuOpen = !isMenuOpen"
                type="button"
                class="inline-flex items-center justify-center rounded-lg p-2.5 transition-colors duration-200"
                :class="navTextClasses.mobile"
                aria-controls="mobile-menu"
                aria-expanded="false"
              >
                <span class="sr-only">Buka menu</span>
                <svg
                  class="h-5 w-5 transition-transform duration-200"
                  :class="isMenuOpen ? 'rotate-90' : ''"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    v-if="!isMenuOpen"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    v-else
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

        <!-- Mobile Navigation Menu -->
        <div
          v-show="isMenuOpen"
          class="md:hidden bg-white/95 backdrop-blur-md border-t border-slate-200/50"
          id="mobile-menu"
        >
          <div class="px-4 py-6 space-y-1">
            <Link
              :href="route('landing')"
              class="block px-4 py-3 text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Beranda
            </Link>
            <Link
              :href="route('profile.show')"
              class="block px-4 py-3 text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Profil
            </Link>
            <Link
              :href="route('services.index')"
              class="block px-4 py-3 text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Layanan
            </Link>
            <Link
              :href="route('posts.index')"
              class="block px-4 py-3 text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Artikel
            </Link>
            <Link
              :href="route('contact.index')"
              class="block px-4 py-3 text-base font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Kontak
            </Link>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <main :class="isLandingPage ? '' : 'pt-16'">
        <slot />
      </main>

      <!-- Modern Footer -->
      <footer class="bg-slate-900 text-slate-300">
        <div class="container mx-auto px-4 py-16 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand Section -->
            <div class="md:col-span-1">
              <div class="flex items-center space-x-3 mb-6">
                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                  <span class="text-white font-bold text-xs">CSIRT</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-white">CSIRT Bojonegoro</h3>
                  <p class="text-xs text-slate-400">Kabupaten Bojonegoro</p>
                </div>
              </div>
              <p class="text-sm text-slate-400 leading-relaxed">
                Tim Respons Insiden Keamanan Siber Pemerintah Kabupaten Bojonegoro yang berkomitmen melindungi aset digital pemerintahan.
              </p>
            </div>

            <!-- Quick Links -->
            <div>
              <h4 class="text-white font-semibold mb-4">Tautan Cepat</h4>
              <ul class="space-y-3 text-sm">
                <li>
                  <Link
                    :href="route('landing')"
                    class="hover:text-white transition-colors duration-200 flex items-center group"
                  >
                    <svg class="h-4 w-4 mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Beranda
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('profile.show')"
                    class="hover:text-white transition-colors duration-200 flex items-center group"
                  >
                    <svg class="h-4 w-4 mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>
                    Profil
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('services.index')"
                    class="hover:text-white transition-colors duration-200 flex items-center group"
                  >
                    <svg class="h-4 w-4 mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Layanan
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('posts.index')"
                    class="hover:text-white transition-colors duration-200 flex items-center group"
                  >
                    <svg class="h-4 w-4 mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                    </svg>
                    Artikel
                  </Link>
                </li>
                <li>
                  <Link
                    :href="route('contact.index')"
                    class="hover:text-white transition-colors duration-200 flex items-center group"
                  >
                    <svg class="h-4 w-4 mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Kontak
                  </Link>
                </li>
              </ul>
            </div>

            <!-- Contact Info -->
            <div>
              <h4 class="text-white font-semibold mb-4">Kontak Darurat</h4>
              <div class="space-y-3 text-sm">
                <div class="flex items-start">
                  <svg class="h-4 w-4 mt-0.5 mr-3 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                  <div>
                    <p class="text-slate-400 font-medium">0353-881234</p>
                    <p class="text-slate-500 text-xs">24/7 Emergency</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <svg class="h-4 w-4 mt-0.5 mr-3 text-indigo-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                  <span class="text-slate-400">csirt@bojonegorokab.go.id</span>
                </div>
              </div>
            </div>

            <!-- Address -->
            <div>
              <h4 class="text-white font-semibold mb-4">Alamat</h4>
              <div class="flex items-start text-sm">
                <svg class="h-4 w-4 mt-0.5 mr-3 text-indigo-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                <div class="text-slate-400 leading-relaxed">
                  <p class="font-medium">Diskominfo Bojonegoro</p>
                  <p>Jl. P. Mastumapel No. 1</p>
                  <p>Bojonegoro, Jawa Timur</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Bar -->
          <div class="mt-12 pt-8 border-t border-slate-800">
            <div class="flex flex-col md:flex-row justify-between items-center">
              <p class="text-sm text-slate-400">
                &copy; {{ new Date().getFullYear() }} CSIRT Kabupaten Bojonegoro. Seluruh hak cipta dilindungi.
              </p>
              <p class="text-sm text-slate-500 mt-2 md:mt-0">
                Dikelola oleh Diskominfo Bojonegoro
              </p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>
