<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

// The 'title' prop will be passed from individual pages
defineProps({
  title: String,
})

// State for mobile menu is now managed here
const isMenuOpen = ref(false)
</script>

<template>
  <div>
    <Head :title="title" />
    <loading-page />

    <div class="bg-white text-gray-800 antialiased">
      <!-- Navbar -->
      <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 items-center justify-between">
            <div class="flex-shrink-0">
              <Link href="/" class="flex items-center space-x-3">
                <img
                  class="h-8 w-auto"
                  src="/logo-bojonegoro.png"
                  alt="Logo Bojonegoro"
                />
                <span class="text-lg font-bold text-gray-700"
                  >CSIRT Bojonegoro</span
                >
              </Link>
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <!-- Use Inertia Link for internal navigation -->
                <Link
                  :href="route('landing')"
                  class="rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                  >Beranda</Link
                >
                <Link
                  :href="route('profile.show')"
                  class="rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                  >Profil</Link
                >
                <Link
                  :href="route('services.index')"
                  class="rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                  >Layanan</Link
                >
                <Link
                  :href="route('posts.index')"
                  class="rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                  >Artikel</Link
                >
                <Link
                  :href="route('contact.index')"
                  class="rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                  >Kontak</Link
                >
              </div>
            </div>
            <div class="-mr-2 flex md:hidden">
              <button
                @click="isMenuOpen = !isMenuOpen"
                type="button"
                class="inline-flex items-center justify-center rounded-md bg-gray-100 p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-100"
                aria-controls="mobile-menu"
                aria-expanded="false"
              >
                <span class="sr-only">Buka menu</span>
                <i v-if="!isMenuOpen" class="pi pi-bars"></i>
                <i v-else class="pi pi-times"></i>
              </button>
            </div>
          </div>
        </div>

        <div v-show="isMenuOpen" class="md:hidden" id="mobile-menu">
          <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
            <Link
              :href="route('landing')"
              class="block rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
              >Beranda</Link
            >
            <Link
              :href="route('profile.show')"
              class="block rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
              >Profil</Link
            >
            <Link
              :href="route('services.index')"
              class="block rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
              >Layanan</Link
            >
            <Link
              :href="route('posts.index')"
              class="block rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
              >Artikel</Link
            >
            <Link
              :href="route('contact.index')"
              class="block rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900"
              >Kontak</Link
            >
          </div>
        </div>
      </nav>

      <!-- This is where the page-specific content will be injected -->
      <main>
        <slot />
      </main>

      <!-- Footer -->
      <footer id="kontak" class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
          <div class="xl:grid xl:grid-cols-3 xl:gap-8">
            <div class="space-y-8 xl:col-span-1">
              <img
                class="h-10"
                src="/logo-bojonegoro.png"
                alt="Logo Pemkab Bojonegoro"
              />
              <p class="text-base text-gray-300">
                Tim Respons Insiden Keamanan Siber <br />
                Pemerintah Kabupaten Bojonegoro
              </p>
            </div>
            <div class="mt-12 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
              <div class="md:grid md:grid-cols-2 md:gap-8">
                <div>
                  <h3
                    class="text-sm font-semibold uppercase tracking-wider text-gray-200"
                  >
                    Kontak
                  </h3>
                  <ul role="list" class="mt-4 space-y-4">
                    <li>
                      <span class="text-base text-gray-300"
                        >Email: csirt@bojonegorokab.go.id</span
                      >
                    </li>
                    <li>
                      <span class="text-base text-gray-300"
                        >Telepon: (0353) 123-456</span
                      >
                    </li>
                  </ul>
                </div>
                <div class="mt-12 md:mt-0">
                  <h3
                    class="text-sm font-semibold uppercase tracking-wider text-gray-200"
                  >
                    Alamat
                  </h3>
                  <p class="mt-4 text-base text-gray-300">
                    Dinas Komunikasi dan Informatika <br />
                    Jl. P. Mastumapel No. 1<br />
                    Bojonegoro, Jawa Timur
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-12 border-t border-gray-700 pt-8">
            <p class="text-center text-base text-gray-400">
              &copy; {{ new Date().getFullYear() }} CSIRT Bojonegoro. Dikelola
              oleh Diskominfo Bojonegoro.
            </p>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>
