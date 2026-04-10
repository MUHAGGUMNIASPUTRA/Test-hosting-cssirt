<script setup>
import { useParticles } from '@/Composables/useParticles'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  services: Array,
  posts: Array,
})

const page = usePage()
const contact = page.props.contact

// Animation refs
const heroRef = ref(null)
const aboutRef = ref(null)
const servicesRef = ref(null)
const { heroParticlesOptions, minimalParticlesOptions } = useParticles()

// Dynamic grid classes based on services count
const serviceGridClasses = computed(() => {
  const count = props.services?.length || 0
  if (count <= 1) return 'grid-cols-1'
  if (count <= 2) return 'grid-cols-1 sm:grid-cols-2'
  if (count <= 3) return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
  return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
})

// Scroll animations
onMounted(() => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up')
      }
    })
  }, observerOptions)

  if (aboutRef.value) observer.observe(aboutRef.value)
  if (servicesRef.value) observer.observe(servicesRef.value)
})
</script>

<template>
  <AppLayout title="Selamat Datang">
    <!-- Hero Section -->
    <section ref="heroRef"
      class="relative min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 overflow-hidden">
      <div class="absolute inset-0 z-0">
        <vue-particles id="tsParticles" :options="heroParticlesOptions" class="w-full h-full" />
      </div>
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]">
        </div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 flex items-center justify-center min-h-screen">
        <div class="container text-center">
          <div class="animate-fade-in-up">
            <h1
              class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl lg:text-8xl leading-tight">
              <span class="block">Menjaga Integritas dan</span>
              <span class="block bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent pb-2">
                Keamanan Digital
              </span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-slate-300 text-xl sm:text-2xl lg:text-3xl">
              CSIRT Bojonegoro hadir sebagai tim respons insiden keamanan siber untuk melindungi
              aset digital Pemerintah Kabupaten Bojonegoro
            </p>
            <div class="mt-10 flex justify-center">
              <button @click="router.visit('/incident')"
                class="group relative inline-flex items-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 hover:from-red-700 hover:to-red-800">
                <svg class="mr-3 h-5 w-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
                </svg>
                Lapor Insiden Siber
                <div
                  class="absolute inset-0 rounded-full bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Scroll Indicator -->
      <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="h-6 w-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </div>
    </section>

    <!-- About Section -->
    <section id="tentang" ref="aboutRef" class="py-8 sm:py-16 lg:py-20 bg-white opacity-0 translate-y-10">
      <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
              Tentang CSIRT Bojonegoro
            </h2>
            <h3 class="text-4xl font-extrabold text-slate-900 sm:text-5xl mb-6">
              Melindungi Aset Digital Pemerintahan
            </h3>
            <p class="text-xl text-slate-600 mb-8">
              Computer Security Incident Response Team (CSIRT) Kabupaten Bojonegoro berkomitmen
              untuk menjaga keamanan dan integritas sistem informasi pemerintahan.
            </p>

            <!-- Mission Points -->
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="h-6 w-6 lg:mt-0.5 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <p class="ml-3 text-slate-700 text-base lg:text-lg">Respons cepat terhadap insiden keamanan siber</p>
              </div>
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="h-6 w-6 lg:mt-0.5 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <p class="ml-3 text-slate-700 text-base lg:text-lg">Koordinasi keamanan lintas instansi pemerintahan</p>
              </div>
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="h-6 w-6 lg:mt-0.5 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <p class="ml-3 text-slate-700 text-base lg:text-lg">Edukasi dan peningkatan kesadaran keamanan siber</p>
              </div>
            </div>
          </div>

          <!-- Illustration/Icon -->
          <div class="flex justify-center lg:justify-end">
            <div class="relative">
              <div
                class="w-80 h-80 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-3xl flex items-center justify-center">
                <svg class="w-40 h-40 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
                </svg>
              </div>
              <div
                class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" ref="servicesRef"
      class="py-8 sm:py-16 lg:py-20 bg-slate-50 opacity-0 translate-y-10 border border-y relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-slate-100 via-white to-indigo-200 opacity-40 -z-10"></div>

      <div class="container">
        <div class="text-center mb-8 sm:mb-16">
          <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Layanan Kami
          </h2>
          <h3 class="text-4xl font-extrabold text-slate-900 sm:text-5xl mb-4">
            Fokus Utama CSIRT Bojonegoro
          </h3>
          <p class="mx-auto max-w-2xl text-xl lg:text-2xl text-slate-600">
            Kami menyediakan layanan komprehensif untuk menjaga ekosistem digital yang aman dan terpercaya
          </p>
        </div>

        <div class="grid gap-8" :class="serviceGridClasses">
          <ServiceCard v-for="(service, index) in props.services" :key="service.id" :service="service"
            :animation-delay="index * 100" />
        </div>
      </div>
    </section>

    <!-- Articles Section -->
    <section id="berita" class="py-8 sm:py-16 lg:py-20 bg-white">
      <div class="container">
        <div class="text-center mb-8 sm:mb-16">
          <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Pusat Informasi
          </h2>
          <h3 class="text-4xl font-extrabold text-slate-900 sm:text-5xl mb-4">
            Artikel & Panduan Terbaru
          </h3>
          <p class="mx-auto max-w-2xl text-xl lg:text-2xl text-slate-600">
            Ikuti informasi terkini seputar keamanan siber untuk meningkatkan kewaspadaan
          </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <PostCard v-for="(post, index) in props.posts" :key="post.id" :post="post" :animation-delay="index * 150" />
        </div>

        <!-- View All Button -->
        <div class="text-center mt-8 sm:mt-12">
          <Link :href="route('posts.index')"
            class="inline-flex items-center px-6 py-3 border border-indigo-300 text-lg font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-400 transition-all duration-200">
            Lihat Semua Artikel
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="relative py-8 sm:py-16 lg:py-20 bg-slate-900 overflow-hidden">
      <div class="absolute inset-0 z-0 pointer-events-none">
        <vue-particles id="landing2particles" :options="minimalParticlesOptions" class="w-full h-full" />
      </div>

      <div class="container">
        <div class="text-center mb-8 sm:mb-16">
          <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-400 mb-2">
            Hubungi Kami
          </h2>
          <h3 class="text-4xl font-extrabold text-white sm:text-5xl mb-4">
            Siap Membantu 24/7
          </h3>
          <p class="mx-auto max-w-2xl text-xl lg:text-2xl text-slate-300">
            Tim CSIRT Bojonegoro siap merespons setiap laporan insiden keamanan siber
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Emergency Contact -->
          <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-6 sm:p-8 text-center text-white z-20">
            <div class="hidden sm:flex w-16 h-16 bg-white/20 rounded-full items-center justify-center mx-auto mb-4">
              <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                  d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z"
                  clip-rule="evenodd" />
              </svg>
            </div>
            <h4 class="text-2xl font-semibold mb-0 sm:mb-2">Darurat 24/7</h4>
            <p class="text-red-100 mb-0 sm:mb-2">Laporan insiden keamanan siber</p>
            <p class="text-3xl font-bold">{{ contact.phone }}</p>
          </div>

          <!-- General Contact -->
          <a :href="`mailto:${contact.email}`" class="block hover:no-underline">
            <div
              class="bg-gradient-to-br from-gray-100 to-indigo-100 rounded-2xl p-6 sm:p-8 text-center border border-slate-200 z-20">
              <div class="hidden sm:flex w-16 h-16 bg-indigo-100 rounded-full items-center justify-center mx-auto mb-4">
                <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                  <path
                    d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                </svg>
              </div>
              <h4 class="text-2xl font-semibold text-slate-900 mb-0 sm:mb-2">Email</h4>
              <p class="text-slate-600 mb-0 sm:mb-2">Kontak umum dan konsultasi</p>
              <p class="text-lg font-medium text-indigo-600">{{ contact.email }}</p>
            </div>
          </a>

          <!-- Office Address -->
          <div
            class="bg-gradient-to-br from-gray-100 to-indigo-100 rounded-2xl p-6 sm:p-8 text-center border border-slate-200 z-20">
            <div class="hidden sm:flex w-16 h-16 bg-indigo-100 rounded-full items-center justify-center mx-auto mb-4">
              <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                  d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                  clip-rule="evenodd" />
              </svg>
            </div>
            <h4 class="text-2xl font-semibold text-slate-900 mb-0 sm:mb-2">Alamat Kantor</h4>
            <p class="text-slate-600 mb-0 sm:mb-2">Dinas Komunikasi dan Informatika</p>
            <p class="text-slate-700">Jl. P. Mas Tumapel No.1, Bojonegoro, Jawa Timur 62115</p>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
