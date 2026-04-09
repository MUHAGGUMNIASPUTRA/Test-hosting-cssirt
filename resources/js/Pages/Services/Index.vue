<script setup>
import { onMounted, ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useParticles } from '@/Composables/useParticles'

const props = defineProps({
  services: Array,
})

// Animation refs
const heroRef = ref(null)
const capabilitiesRef = ref(null)
const servicesRef = ref(null)
const processRef = ref(null)
const { minimalParticlesOptions } = useParticles()

// Process steps data
const processSteps = [
  {
    step: '01',
    title: 'Deteksi & Pelaporan',
    description: 'Sistem monitoring 24/7 mendeteksi ancaman atau menerima laporan insiden dari berbagai sumber.',
    icon: 'pi-search'
  },
  {
    step: '02',
    title: 'Analisis & Klasifikasi',
    description: 'Tim ahli menganalisis tingkat keparahan dan mengklasifikasikan jenis insiden keamanan.',
    icon: 'pi-chart-line'
  },
  {
    step: '03',
    title: 'Respons & Mitigasi',
    description: 'Implementasi langkah-langkah darurat untuk menahan dan meminimalkan dampak insiden.',
    icon: 'pi-shield'
  },
  {
    step: '04',
    title: 'Pemulihan & Evaluasi',
    description: 'Pemulihan sistem dan evaluasi menyeluruh untuk mencegah insiden serupa di masa depan.',
    icon: 'pi-refresh'
  }
]

// Capabilities data
const capabilities = [
  {
    title: 'Response Time',
    value: '< 1 jam',
    description: 'Waktu respons rata-rata untuk insiden kritikal',
    color: 'from-red-500 to-red-600'
  },
  {
    title: 'Availability',
    value: '24/7',
    description: 'Layanan monitoring dan respons sepanjang waktu',
    color: 'from-green-500 to-green-600'
  },
  {
    title: 'Coverage',
    value: '100%',
    description: 'Cakupan sistem pemerintahan kabupaten',
    color: 'from-blue-500 to-blue-600'
  },
  {
    title: 'Recovery Rate',
    value: '99.5%',
    description: 'Tingkat keberhasilan pemulihan sistem',
    color: 'from-purple-500 to-purple-600'
  }
]

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
  if (capabilitiesRef.value) observer.observe(capabilitiesRef.value)
  if (servicesRef.value) observer.observe(servicesRef.value)
  if (processRef.value) observer.observe(processRef.value)
})

// Dynamic grid classes
const serviceGridClasses = computed(() => {
  const count = props?.services?.length || 0
  if (count <= 1) return 'grid-cols-1'
  if (count <= 2) return 'grid-cols-1 sm:grid-cols-2'
  if (count <= 3) return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
  return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
})
</script>

<template>
  <AppLayout title="Layanan">
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
              <span class="block">Layanan</span>
              <span class="block bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent pb-2">
                CSIRT Bojonegoro
              </span>
            </h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Capabilities Section -->
    <section ref="capabilitiesRef" class="py-12 sm:py-16 lg:py-20 bg-white opacity-0 translate-y-10">
      <div class="container">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="(capability, index) in capabilities"
            :key="capability.title"
            class="text-center group"
            :style="{ animationDelay: `${index * 100}ms` }"
          >
            <div class="relative">
              <div :class="`w-24 h-24 mx-auto rounded-2xl bg-gradient-to-br ${capability.color} flex items-center justify-center mb-4 shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-110`">
                <span class="text-2xl font-bold text-white">{{ capability.value }}</span>
              </div>
              <div :class="`absolute inset-0 w-24 h-24 mx-auto rounded-2xl bg-gradient-to-br ${capability.color} opacity-20 group-hover:opacity-30 transition-opacity duration-300 scale-110`"></div>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">{{ capability.title }}</h3>
            <p class="text-slate-600">{{ capability.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section ref="servicesRef" class="py-8 sm:py-16 lg:py-20 bg-slate-50 opacity-0 translate-y-10 border border-y relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-slate-100 via-white to-indigo-200 opacity-40 -z-10"></div>

      <div class="container">
        <div class="text-center mb-8 sm:mb-16">
          <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Layanan Unggulan
          </h2>
          <h3 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-4">
            Perlindungan Menyeluruh
          </h3>
          <p class="mx-auto max-w-2xl text-xl sm:text-2xl text-slate-600">
            Berbagai layanan terintegrasi untuk menjaga keamanan dan integritas
            sistem informasi pemerintahan
          </p>
        </div>

        <div class="grid gap-8" :class="serviceGridClasses">
          <ServiceCard
            v-for="(service, index) in services"
            :key="service.id"
            :service="service"
            :animation-delay="index * 100"
          />
        </div>
      </div>
    </section>

    <!-- Process Section -->
    <section ref="processRef" class="py-8 sm:py-16 lg:py-20 bg-white opacity-0 translate-y-10">
      <div class="container">
        <div class="text-center mb-8 sm:mb-16">
          <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Alur Kerja
          </h2>
          <h3 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-4">
            Proses Penanganan Insiden
          </h3>
          <p class="mx-auto max-w-2xl text-xl sm:text-2xl text-slate-600">
            Metodologi terstruktur untuk respons insiden yang efektif dan efisien
          </p>
        </div>

        <!-- Desktop Layout with Flow Lines -->
        <div class="hidden lg:block">
          <div class="relative">
            <!-- Connecting Lines -->
            <div class="absolute top-16 left-0 right-0 flex items-center justify-between px-8">
              <div class="flex-1 h-0.5 bg-gradient-to-r from-transparent via-indigo-300 to-indigo-300 mx-8"></div>
              <div class="flex-1 h-0.5 bg-gradient-to-r from-indigo-300 to-indigo-300 mx-8"></div>
              <div class="flex-1 h-0.5 bg-gradient-to-r from-indigo-300 to-transparent mx-8"></div>
            </div>

            <!-- Process Steps -->
            <div class="grid grid-cols-4 gap-8 relative z-10">
              <div
                v-for="(step, index) in processSteps"
                :key="step.step"
                class="group text-center"
                :style="{ animationDelay: `${index * 150}ms` }"
              >
                <!-- Step Number Circle -->
                <div class="relative mb-6">
                  <div class="w-16 h-16 mx-auto bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-110 relative z-20">
                    <span class="text-2xl font-bold text-white">{{ step.step }}</span>
                  </div>
                  <!-- Glow effect -->
                  <div class="absolute inset-0 w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-indigo-500/20 to-blue-600/20 group-hover:opacity-100 opacity-0 transition-opacity duration-300 scale-125"></div>
                </div>

                <!-- Step Icon -->
                <div class="w-12 h-12 mx-auto bg-slate-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-indigo-50 transition-colors duration-300">
                  <i :class="`pi ${step.icon} text-indigo-600`"></i>
                </div>

                <!-- Step Content -->
                <h4 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300">
                  {{ step.title }}
                </h4>
                <p class="text-slate-600 leading-relaxed">
                  {{ step.description }}
                </p>

                <!-- Step Arrow (except last) -->
                <div v-if="index < processSteps.length - 1" class="absolute top-16 -right-4 transform -translate-y-1/2 z-30">
                  <svg class="w-8 h-8 text-indigo-400 group-hover:text-indigo-600 transition-colors duration-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Layout (Vertical Flow) -->
        <div class="lg:hidden">
          <div class="space-y-8">
            <div
              v-for="(step, index) in processSteps"
              :key="step.step"
              class="relative group"
              :style="{ animationDelay: `${index * 150}ms` }"
            >
              <div class="flex items-start space-x-4">
                <!-- Step Number and Connector -->
                <div class="flex flex-col items-center">
                  <!-- Step Number Circle -->
                  <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-110 relative z-10">
                    <span class="text-2xl font-bold text-white">{{ step.step }}</span>
                  </div>

                  <!-- Vertical Connector Line (except last) -->
                  <div v-if="index < processSteps.length - 1" class="w-0.5 h-16 bg-gradient-to-b from-indigo-300 to-indigo-200 mt-4"></div>
                </div>

                <!-- Step Content -->
                <div class="flex-1 pt-0 sm:pt-2">
                  <!-- Step Icon -->
                  <div class="hidden sm:flex w-12 h-12 bg-slate-100 rounded-lg items-center justify-center mb-4 group-hover:bg-indigo-50 transition-colors duration-300">
                    <i :class="`pi ${step.icon} text-2xl text-indigo-600`"></i>
                  </div>

                  <!-- Step Title and Description -->
                  <h4 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300">
                    {{ step.title }}
                  </h4>
                  <p class="text-slate-600 leading-relaxed">
                    {{ step.description }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Process Flow Legend -->
        <div class="pt-8 sm:mt-12 text-center">
          <div class="inline-flex items-center space-x-2 bg-slate-100 rounded-full px-4 py-2">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-indigo-600 icon icon-tabler icons-tabler-outline icon-tabler-progress-check">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" /><path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" /><path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" />
            </svg>
            <span class="text-slate-600 font-medium">Proses berjalan secara berurutan untuk hasil optimal</span>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900">
      <div class="container text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-6">
          Butuh Bantuan Keamanan Siber?
        </h2>
        <p class="mx-auto max-w-2xl text-xl sm:text-2xl text-slate-300 mb-8">
          Tim CSIRT Bojonegoro siap membantu Anda 24/7 untuk mengatasi
          berbagai tantangan keamanan siber
        </p>
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
          <Link
            href="/contact"
            class="inline-flex items-center px-8 py-4 text-xl font-semibold text-white bg-gradient-to-r from-indigo-600 to-blue-600 rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300"
          >
            <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
              <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
            </svg>
            Hubungi Kami
          </Link>
          <Link
            href="/incident"
            class="inline-flex items-center px-8 py-4 text-xl font-semibold text-white bg-gradient-to-r from-red-600 to-red-700 rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300"
          >
            <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Lapor Insiden
          </Link>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
