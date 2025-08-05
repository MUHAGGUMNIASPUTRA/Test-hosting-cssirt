<script setup>
import { ref, onMounted, nextTick, computed, watch } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { useResponsive } from "@/Composables/useResponsive";

// The 'title' prop will be passed from individual pages
defineProps({
  title: String,
});

// State for mobile menu and scroll
const isMenuOpen = ref(false);
const isScrolled = ref(false);
const page = usePage();
const isLoggedIn = computed(() => !!(page.props.auth && page.props.auth.user));

// Toast notification setup (SSR-safe)
let toast = null
if (typeof window !== 'undefined') {
  toast = useToast()
}

const { isMobile } = useResponsive();

// Check if current page is landing/homepage
const isLandingPage = computed(() => {
  return page.url === '/' || page.url === '';
});

// Handle scroll for navbar styling (SSR-safe)
onMounted(() => {
  if (typeof window !== 'undefined') {
    const handleScroll = () => {
      isScrolled.value = window.scrollY > 10
    }

    window.addEventListener('scroll', handleScroll)

    // Cleanup
    return () => {
      window.removeEventListener('scroll', handleScroll)
    }
  }
});

// Dynamic navbar classes based on page and scroll state
const navbarClasses = computed(() => {
  if (!isScrolled.value) {
    return 'bg-transparent'
  }
  return 'bg-white/95 backdrop-blur-md shadow-lg border-b border-slate-200/50'
});

// Dynamic text colors for navbar
const navTextClasses = computed(() => {
  if (!isScrolled.value) {
    return {
      logo: 'text-white',
      subtitle: 'text-slate-300',
      link: 'text-slate-300 hover:text-white hover:bg-white/10',
      mobile: 'text-white hover:bg-white/10',
      reportBtn: 'bg-red-600 hover:bg-red-700 text-white border-red-600 hover:border-red-700'
    }
  }
  return {
    logo: 'text-slate-900',
    subtitle: 'text-slate-600',
    link: 'text-slate-700 hover:text-indigo-600 hover:bg-indigo-50',
    mobile: 'text-slate-700 hover:bg-slate-100',
    reportBtn: 'bg-red-600 hover:bg-red-700 text-white border-red-600 hover:border-red-700'
  }
});

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      nextTick(() => {
        toast.add({
          severity: "success",
          summary: flash.success?.title || "Berhasil",
          detail: flash.success?.message || flash.success || "Operasi berhasil.",
          life: 4000,
        });
      });
    } else if (flash?.info) {
      nextTick(() => {
        toast.add({
          severity: "info",
          summary: flash.info?.title || "Informasi",
          detail: flash.info?.message || flash.info || "Informasi penting.",
          life: 4000,
        });
      });
    } else if (flash?.warning) {
      nextTick(() => {
        toast.add({
          severity: "warning",
          summary: flash.warning?.title || "Peringatan",
          detail: flash.warning?.message || flash.warning || "Perhatian diperlukan.",
          life: 4000,
        });
      });
    } else if (flash?.error) {
      nextTick(() => {
        toast.add({
          severity: "error",
          summary: flash.error?.title || "Kesalahan",
          detail: flash.error?.message || flash.error || "Terjadi kesalahan.",
          life: 4000,
        });
      });
    }
  },
  { immediate: true }
);
</script>

<template>
  <div>
    <Head :title="title" />

    <loading-page />
    <Toast />

    <div class="bg-white text-slate-800 antialiased">
      <!-- Modern Navbar -->
      <nav
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
        :class="navbarClasses"
      >
        <div class="container">
          <div class="flex h-16 items-center justify-between">
            <!-- Logo Section -->
            <div class="flex-shrink-0">
              <Link :href="route('landing')" class="flex items-center space-x-3 group">
                <div class="relative">
                  <!-- Government Crest -->
                  <div v-if="!isScrolled" class="h-9 w-9 rounded-full bg-white/10 border-2 border-white/20 flex items-center justify-center mr-1">
                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <!-- Regular logo for other states -->
                  <div v-else class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-xs">CSIRT</span>
                  </div>
                </div>
                <div :class="!isScrolled && isMobile ? 'hidden' : 'block'">
                  <h2 class="text-xl font-bold transition-colors duration-200" :class="navTextClasses.logo">CSIRT Bojonegoro</h2>
                  <!-- <p class="text-sm transition-colors duration-200" :class="navTextClasses.subtitle">Bojonegoro</p> -->
                </div>
              </Link>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-1">
              <!-- Regular Navigation Links -->
              <div class="flex items-center space-x-1 pr-2">
                <Link
                  :href="route('landing')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Beranda</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('profile.show')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Profil</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('services.index')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Layanan</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('posts.index')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Artikel</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('documents.index')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Panduan</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('rfc2350.index')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">RFC2350</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('faq.index')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">FAQ</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="route('contact.index')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">Kontak</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
                <Link
                  :href="isLoggedIn ? route('admin.dashboard') : route('login')"
                  class="relative px-3 py-2 font-medium rounded-lg transition-all duration-200 group"
                  :class="navTextClasses.link"
                >
                  <span class="relative z-10">{{ isLoggedIn ? 'Dashboard' : 'Login' }}</span>
                  <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-indigo-500 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                </Link>
              </div>

              <!-- Report Incident Button -->
              <div class="pl-6 ml-6 border-l border-slate-300">
                <Link
                  :href="route('incident.create')"
                  class="inline-flex items-center px-3 py-2 font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-xl"
                  :class="navTextClasses.reportBtn"
                >
                  Lapor Insiden
                </Link>
              </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex lg:hidden">
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
          class="lg:hidden bg-white/95 backdrop-blur-md border-t border-slate-200/50"
          id="mobile-menu"
        >
          <div class="px-4 py-6 space-y-1">
            <Link
              :href="route('landing')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Beranda
            </Link>
            <Link
              :href="route('profile.show')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Profil
            </Link>
            <Link
              :href="route('services.index')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Layanan
            </Link>
            <Link
              :href="route('posts.index')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Artikel
            </Link>
            <Link
              :href="route('documents.index')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Panduan
            </Link>
            <Link
              :href="route('rfc2350.index')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              RFC2350
            </Link>
            <Link
              :href="route('faq.index')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              FAQ
            </Link>
            <Link
              :href="route('contact.index')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              Kontak
            </Link>
            <Link
              :href="isLoggedIn ? route('admin.dashboard') : route('login')"
              class="block px-4 py-3 text-lg font-medium text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
              @click="isMenuOpen = false"
            >
              <span class="relative z-10">{{ isLoggedIn ? 'Dashboard' : 'Login' }}</span>
            </Link>

            <!-- Mobile Report Incident Button -->
            <div class="pt-4 mt-4 border-t border-slate-200">
              <Link
                :href="route('incident.create')"
                class="flex items-center justify-center w-full px-4 py-3 text-lg font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all duration-200 transform hover:scale-105"
                @click="isMenuOpen = false"
              >
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-2 icon icon-tabler icons-tabler-filled icon-tabler-alert-triangle">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                </svg>
                Lapor Insiden
              </Link>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <main>
        <slot />
      </main>

      <!-- Modern Footer -->
      <footer class="bg-slate-900 text-slate-300">
        <div class="container py-8 sm:py-16">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Brand Section -->
            <div class="md:col-span-1">
              <div class="flex items-center space-x-3 mb-4">
                <div class="h-10 w-10 px-1 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                  <span class="text-white font-bold text-xs">CSIRT</span>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-white">CSIRT Bojonegoro</h3>
                  <!-- <p class="text-sm text-slate-400">Bojonegoro</p> -->
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
                    <Link
                      :href="route('landing')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconHome size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      Beranda
                    </Link>
                  </li>
                  <li>
                    <Link
                      :href="route('profile.show')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconUserCircle size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      Profil
                    </Link>
                  </li>
                  <li>
                    <Link
                      :href="route('services.index')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconShieldCheck size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      Layanan
                    </Link>
                  </li>
                  <li>
                    <Link
                      :href="route('posts.index')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconNews size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      Artikel
                    </Link>
                  </li>
                </ul>
                <ul class="space-y-1">
                  <li>
                    <Link
                      :href="route('documents.index')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconFile size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      Panduan
                    </Link>
                  </li>
                  <li>
                    <Link
                      :href="route('rfc2350.index')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconFileTypePdf size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      RFC2350
                    </Link>
                  </li>
                  <li>
                    <Link
                      :href="route('faq.index')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconHelp size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      FAQ
                    </Link>
                  </li>
                  <li>
                    <Link
                      :href="route('contact.index')"
                      class="hover:text-white transition-colors duration-200 flex items-center group"
                    >
                      <IconMail size="14" class="mr-2 text-slate-500 group-hover:text-indigo-400 transition-colors duration-200" />
                      Kontak
                    </Link>
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
                    <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd" />
                  </svg>
                  <div>
                    <p class="text-slate-400 font-medium">0353-881826</p>
                    <p class="text-slate-500 text-sm">24/7 Emergency</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <svg class="h-4 w-4 mt-1.5 mr-2 text-indigo-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                    <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
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
                &copy; {{ new Date().getFullYear() }} CSIRT Kabupaten Bojonegoro. Seluruh hak cipta dilindungi.
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
