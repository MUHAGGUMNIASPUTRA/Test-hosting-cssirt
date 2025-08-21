<script setup>
import { onMounted, ref } from 'vue'
import { useParticles } from '@/Composables/useParticles'

// Animation refs
const heroRef = ref(null)
const aboutRef = ref(null)
const visionRef = ref(null)
const teamRef = ref(null)
const timelineRef = ref(null)
const { minimalParticlesOptions } = useParticles()

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

  if (heroRef.value) observer.observe(heroRef.value)
  if (aboutRef.value) observer.observe(aboutRef.value)
  if (visionRef.value) observer.observe(visionRef.value)
  if (teamRef.value) observer.observe(teamRef.value)
  if (timelineRef.value) observer.observe(timelineRef.value)
})
</script>

<template>
  <AppLayout title="Profil">
    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 py-24 sm:py-32 opacity-0 translate-y-10">
      <div class="absolute inset-0 z-0">
        <vue-particles id="tsparticles" :options="minimalParticlesOptions" class="w-full h-full"/>
      </div>

      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative container">
        <div class="text-center">
          <div class="animate-fade-in-up">
            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl leading-tight">
              <span class="block">Profil</span>
              <span class="block bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent pb-2">
                CSIRT Bojonegoro
              </span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-xl sm:text-2xl text-slate-300">
              Tim Respons Insiden Keamanan Siber yang berkomitmen melindungi aset digital
              Pemerintah Kabupaten Bojonegoro dengan standar internasional
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section ref="aboutRef" class="py-8 sm:py-16 lg:py-20 bg-white opacity-0 translate-y-10">
      <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
              Tentang Kami
            </h2>
            <h3 class="text-4xl font-extrabold text-slate-900 sm:text-5xl mb-6">
              Dedikasi untuk Keamanan Digital
            </h3>
            <p class="text-xl text-slate-600 mb-6">
              Computer Security Incident Response Team (CSIRT) Pemerintah Kabupaten Bojonegoro
              adalah tim khusus yang dibentuk untuk menghadapi tantangan keamanan siber di era digital.
            </p>
            <p class="text-xl text-slate-600">
              Dengan pengalaman dan keahlian yang terus berkembang, kami berkomitmen memberikan
              perlindungan terdepan terhadap ancaman siber yang dapat mengganggu layanan publik
              dan operasional pemerintahan.
            </p>
          </div>

          <div class="flex justify-center lg:justify-end">
            <div class="relative">
              <div class="w-80 h-80 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-3xl flex items-center justify-center">
                <i-lucide-qr-code class="w-40 h-40 text-indigo-600" />
              </div>
              <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Vision & Mission Section -->
    <section ref="visionRef" class="py-8 sm:py-16 lg:py-20 bg-slate-50 opacity-0 translate-y-10 border border-y relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-slate-200 via-white to-green-100 opacity-40 -z-10"></div>

      <div class="container">
        <div class="text-center mb-8 sm:mb-16">
          <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Visi & Misi
          </h2>
          <h3 class="text-4xl font-extrabold text-slate-900 sm:text-5xl">
            Fondasi Komitmen Kami
          </h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12">
          <!-- Vision Card -->
          <div class="relative bg-white rounded-2xl p-8 shadow-sm border border-slate-200 overflow-hidden transform hover:scale-105 transition-all duration-300">
            <!-- Big icon as decorative background -->
            <div class="absolute -top-10 -right-10 w-40 h-40 opacity-5 pointer-events-none">
              <svg class="w-full h-full text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 12a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
              </svg>
            </div>

            <!-- Gradient background -->
            <div class="absolute bottom-0 left-0 w-1/2 h-1/2 bg-gradient-to-tr from-indigo-50 to-white opacity-60 pointer-events-none rounded-br-2xl"></div>

            <!-- Vision -->
            <div class="relative z-10">
              <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                  <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <h4 class="text-3xl font-bold text-slate-900 ml-4">Visi</h4>
              </div>
              <p class="text-xl text-slate-600 leading-relaxed">
                Terwujudnya keamanan siber yang andal dan terpercaya di lingkungan
                Pemerintah Kabupaten Bojonegoro untuk mendukung penyelenggaraan
                pemerintahan dan pelayanan publik yang efektif dan efisien.
              </p>
            </div>
          </div>

          <!-- Mission Card -->
          <div class="relative bg-white rounded-2xl p-8 shadow-sm border border-slate-200 overflow-hidden transform hover:scale-105 transition-all duration-300">
            <!-- Big icon as decorative background -->
            <div class="absolute -top-10 -right-10 w-40 h-40 opacity-5 pointer-events-none">
              <svg class="w-full h-full text-teal-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
              </svg>
            </div>

            <!-- Gradient background -->
            <div class="absolute bottom-0 left-0 w-1/2 h-1/2 bg-gradient-to-tr from-teal-50 to-white opacity-60 pointer-events-none rounded-br-2xl"></div>

            <!-- Mission -->
            <div class="relative z-10">
              <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                  <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                    <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                  </svg>
                </div>
                <h4 class="text-3xl font-bold text-slate-900 ml-4">Misi</h4>
              </div>
              <ul class="space-y-4">
                <li class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center">
                      <svg class="h-3 w-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                  <p class="ml-3 text-slate-700">Membangun sistem deteksi dan pencegahan insiden keamanan siber yang proaktif</p>
                </li>
                <li class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center">
                      <svg class="h-3 w-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                  <p class="ml-3 text-slate-700">Melakukan penanganan insiden siber secara cepat, tepat, dan tuntas</p>
                </li>
                <li class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center">
                      <svg class="h-3 w-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                  <p class="ml-3 text-slate-700">Meningkatkan kesadaran dan kompetensi SDM di bidang keamanan siber</p>
                </li>
                <li class="flex items-start">
                  <div class="flex-shrink-0">
                    <div class="h-6 w-6 rounded-full bg-emerald-100 flex items-center justify-center">
                      <svg class="h-3 w-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                  <p class="ml-3 text-slate-700">Membangun kemitraan dan kolaborasi dalam penanganan insiden siber</p>
                </li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </section>

  </AppLayout>
</template>
