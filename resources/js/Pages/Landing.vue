<script setup>
import { router } from '@inertiajs/vue3'
import { onMounted, ref, computed } from 'vue'

const props = defineProps({
  services: Array,
  posts: Array,
})

// Animation refs
const heroRef = ref(null)
const aboutRef = ref(null)
const servicesRef = ref(null)

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
    <section ref="heroRef" class="relative min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 flex items-center justify-center min-h-screen">
        <div class="container mx-auto px-4 py-20 text-center sm:px-6 lg:px-8">
          <div class="animate-fade-in-up">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl lg:text-7xl">
              <span class="block">Menjaga Integritas dan</span>
              <span class="block bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">
                Keamanan Digital
              </span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-lg text-slate-300 sm:text-xl md:text-2xl">
              CSIRT Bojonegoro hadir sebagai tim respons insiden keamanan siber untuk melindungi
              aset digital Pemerintah Kabupaten Bojonegoro
            </p>
            <div class="mt-10 flex justify-center">
              <button
                @click="router.visit('/incident')"
                class="group relative inline-flex items-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 hover:from-red-700 hover:to-red-800"
              >
                <svg class="mr-3 h-5 w-5 group-hover:animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Lapor Insiden Siber
                <div class="absolute inset-0 rounded-full bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
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
    <section id="tentang" ref="aboutRef" class="py-20 bg-white opacity-0 translate-y-10">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-600 mb-2">
              Tentang CSIRT Bojonegoro
            </h2>
            <h3 class="text-3xl font-extrabold text-slate-900 sm:text-4xl mb-6">
              Melindungi Aset Digital Pemerintahan
            </h3>
            <p class="text-lg text-slate-600 mb-8">
              Computer Security Incident Response Team (CSIRT) Kabupaten Bojonegoro berkomitmen
              untuk menjaga keamanan dan integritas sistem informasi pemerintahan.
            </p>

            <!-- Mission Points -->
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <p class="ml-3 text-slate-700">Respons cepat terhadap insiden keamanan siber</p>
              </div>
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <p class="ml-3 text-slate-700">Koordinasi keamanan lintas instansi pemerintahan</p>
              </div>
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <p class="ml-3 text-slate-700">Edukasi dan peningkatan kesadaran keamanan siber</p>
              </div>
            </div>
          </div>

          <!-- Illustration/Icon -->
          <div class="flex justify-center lg:justify-end">
            <div class="relative">
              <div class="w-80 h-80 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-3xl flex items-center justify-center">
                <svg class="w-40 h-40 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" ref="servicesRef" class="py-20 bg-slate-50 opacity-0 translate-y-10">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Layanan Kami
          </h2>
          <h3 class="text-3xl font-extrabold text-slate-900 sm:text-4xl mb-4">
            Fokus Utama CSIRT Bojonegoro
          </h3>
          <p class="mx-auto max-w-2xl text-xl text-slate-600">
            Kami menyediakan layanan komprehensif untuk menjaga ekosistem digital yang aman dan terpercaya
          </p>
        </div>

        <div class="grid gap-8" :class="serviceGridClasses">
          <div
            v-for="(service, index) in props.services"
            :key="service.id"
            class="group relative bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 hover:border-indigo-300"
            :style="{ animationDelay: `${index * 100}ms` }"
          >
            <!-- Service Icon -->
            <div class="relative mb-6">
              <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                <i :class="`pi ${service.icon} text-2xl text-white`"></i>
              </div>
              <div class="absolute -inset-2 bg-gradient-to-br from-indigo-500/20 to-blue-600/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
            </div>

            <!-- Service Content -->
            <h4 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300">
              {{ service.name }}
            </h4>
            <p class="text-slate-600 leading-relaxed">
              {{ service.short_description }}
            </p>

            <!-- Hover Effect -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- News Section -->
    <section id="berita" class="py-20 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Pusat Informasi
          </h2>
          <h3 class="text-3xl font-extrabold text-slate-900 sm:text-4xl mb-4">
            Artikel & Panduan Terbaru
          </h3>
          <p class="mx-auto max-w-2xl text-xl text-slate-600">
            Ikuti informasi terkini seputar keamanan siber untuk meningkatkan kewaspadaan
          </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <article
            v-for="(post, index) in props.posts"
            :key="post.id"
            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-slate-200 overflow-hidden"
            :style="{ animationDelay: `${index * 150}ms` }"
          >
            <!-- Post Image -->
            <div v-if="post.image" class="relative overflow-hidden">
              <img
                :src="post.image.startsWith('http') ? post.image : '/storage/' + post.image"
                :alt="post.title"
                class="h-48 w-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Post Content -->
            <div class="p-6">
              <div class="flex items-center text-sm text-slate-500 mb-3">
                <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                {{ new Date(post.created_at).toLocaleDateString('id-ID', {
                  day: 'numeric',
                  month: 'long',
                  year: 'numeric'
                }) }}
              </div>

              <h4 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300 line-clamp-2">
                {{ post.title }}
              </h4>

              <p class="text-slate-600 mb-4 line-clamp-3">
                {{ post.excerpt }}
              </p>

              <a
                :href="route('posts.show', { post: post.slug })"
                class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium group/link"
              >
                Baca selengkapnya
                <svg class="ml-2 h-4 w-4 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            </div>
          </article>
        </div>

        <!-- View All Button -->
        <div class="text-center mt-12">
          <a
            :href="route('posts.index')"
            class="inline-flex items-center px-6 py-3 border border-indigo-300 text-base font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-400 transition-all duration-200"
          >
            Lihat Semua Artikel
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="py-20 bg-slate-900">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-400 mb-2">
            Hubungi Kami
          </h2>
          <h3 class="text-3xl font-extrabold text-white sm:text-4xl mb-4">
            Siap Membantu 24/7
          </h3>
          <p class="mx-auto max-w-2xl text-xl text-slate-300">
            Tim CSIRT Bojonegoro siap merespons setiap laporan insiden keamanan siber
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Emergency Contact -->
          <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-2xl p-8 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
              </svg>
            </div>
            <h4 class="text-xl font-semibold mb-2">Darurat 24/7</h4>
            <p class="text-red-100 mb-3">Laporan insiden keamanan siber</p>
            <p class="text-2xl font-bold">0353-881234</p>
          </div>

          <!-- General Contact -->
          <div class="bg-white rounded-2xl p-8 text-center border border-slate-200">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
              </svg>
            </div>
            <h4 class="text-xl font-semibold text-slate-900 mb-2">Email</h4>
            <p class="text-slate-600 mb-3">Kontak umum dan konsultasi</p>
            <p class="text-lg font-medium text-indigo-600">csirt@bojonegorokab.go.id</p>
          </div>

          <!-- Office Address -->
          <div class="bg-white rounded-2xl p-8 text-center border border-slate-200">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="h-8 w-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
              </svg>
            </div>
            <h4 class="text-xl font-semibold text-slate-900 mb-2">Alamat Kantor</h4>
            <p class="text-slate-600 mb-3">Kantor Pemerintah Kabupaten</p>
            <p class="text-sm text-slate-700">Jl. Teuku Umar No.1, Bojonegoro, Jawa Timur 62115</p>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
