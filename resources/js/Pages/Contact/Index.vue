<script setup>
// filepath: resources/js/Pages/Contact/Index.vue

import { onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3';

// Animation refs
const heroRef = ref(null)
const contactRef = ref(null)

// Contact details data
const contactDetails = [
  {
    name: 'Email',
    value: 'ttis@bojonegorokab.go.id',
    icon: 'pi-envelope',
    color: 'text-blue-600',
    bgColor: 'bg-blue-100',
    type: 'email'
  },
  {
    name: 'Telepon',
    value: '0353-881826',
    icon: 'pi-phone',
    color: 'text-green-600',
    bgColor: 'bg-green-100',
    type: 'phone'
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
  if (contactRef.value) observer.observe(contactRef.value)
})
</script>

<template>
  <AppLayout title="Hubungi Kami">
    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 opacity-0 translate-y-10">
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container text-center">
          <div class="animate-fade-in-up">
            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl leading-tight">
              <span class="">Hubungi</span>
              <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent pb-2">
                Kami
              </span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-xl sm:text-2xl text-slate-300">
              Tim CSIRT Bojonegoro siap membantu Anda 24/7. Laporkan insiden keamanan siber
              atau konsultasikan kebutuhan keamanan digital Anda.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Emergency Banner -->
    <section class="bg-red-600 py-4">
      <div class="container">
        <div class="flex items-center justify-center text-center">
          <svg class="hidden lg:flex h-5 w-5 text-white mr-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <p class="text-xl text-white font-medium">
            <span class="font-bold">DARURAT:</span> Untuk insiden keamanan siber segera buat laporan melalui<Link :href="route('incident.create')" class="underline hover:no-underline ml-1">Lapor Insiden</Link>
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Information -->
    <section ref="contactRef" class="py-8 sm:py-16 lg:py-20 bg-white opacity-0 translate-y-10">
      <div class="container max-w-7xl">

        <!-- Contact Cards -->
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 mb-8 sm:mb-16">
          <div
            v-for="(contact, index) in contactDetails"
            :key="contact.name"
            class="group relative bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border"
            :style="{ animationDelay: `${index * 100}ms` }"
            :class="contact.type === 'emergency' ? 'border-red-200 bg-red-50 hover:border-red-300' : 'border-slate-200 hover:border-indigo-300'"
          >
            <div class="flex items-center mb-6">
              <!-- Icon -->
              <div class="relative mr-6">
                <div
                  class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:shadow-xl transition-shadow duration-300"
                  :class="contact.type === 'emergency' ? 'bg-gradient-to-br from-red-500 to-red-600' : `${contact.bgColor}`"
                >
                  <i :class="[`pi ${contact.icon} !text-xl`, contact.type === 'emergency' ? 'text-white' : contact.color]"></i>
                </div>
                <div
                  class="absolute -inset-2 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"
                  :class="contact.type === 'emergency' ? 'bg-gradient-to-br from-red-500/20 to-red-600/20' : 'bg-gradient-to-br from-indigo-500/20 to-blue-600/20'"
                ></div>
              </div>

              <!-- Content -->
              <h4 class="text-2xl font-semibold group-hover:text-indigo-600 transition-colors duration-300" :class="contact.type === 'emergency' ? 'text-red-700 group-hover:text-red-600' : 'text-slate-900'">
                {{ contact.name }}
                <p v-if="contact.type === 'emergency'" class="text-sm text-red-500 font-normal">{{ contact.subtitle }}</p>
              </h4>
            </div>

            <p class="leading-relaxed whitespace-pre-line" :class="contact.type === 'emergency' ? 'text-red-600 font-medium' : 'text-slate-600'">
              {{ contact.value }}
            </p>

            <!-- Action Button -->
            <div v-if="contact.type != 'address'" class="mt-4">
              <a
                v-if="contact.type === 'email'"
                :href="`mailto:${contact.value}`"
                class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium group/link"
              >
                Kirim Email
                <svg class="ml-2 h-4 w-4 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
              <a
                v-else-if="contact.type === 'phone' || contact.type === 'emergency'"
                :href="`tel:${contact.value.replace(/[^0-9+]/g, '')}`"
                class="inline-flex items-center font-medium group/link"
                :class="contact.type === 'emergency' ? 'text-red-600 hover:text-red-700' : 'text-indigo-600 hover:text-indigo-700'"
              >
                {{ contact.type === 'emergency' ? 'Hubungi Sekarang' : 'Telepon' }}
                <svg class="ml-2 h-4 w-4 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            </div>

            <!-- Hover Effect -->
            <div
              class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
              :class="contact.type === 'emergency' ? 'bg-gradient-to-br from-red-500/5 to-red-600/5' : 'bg-gradient-to-br from-indigo-500/5 to-blue-600/5'"
            ></div>
          </div>
        </div>

        <!-- Map -->
        <div class="relative">
          <h4 class="text-2xl font-bold text-slate-900 mb-6">Alamat CSIRT Bojonegoro</h4>
          <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200">
            <div class="h-80 w-full">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.411656819588!2d111.878965!3d-7.1501326!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7781a5963f7d6b%3A0x7c0d1b3e8e1f4b0!2sDinas%20Komunikasi%20dan%20Informatika%20Kabupaten%20Bojonegoro!5e0!3m2!1sen!2sid!4v1678886400000"
                width="100%"
                height="100%"
                style="border: 0"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
            <div class="p-6">
              <div class="flex items-start">
                <IconMap2 size="20" class="text-indigo-600 mt-1 mr-3"/>
                <div>
                  <p class="font-semibold text-slate-900">Dinas Komunikasi dan Informatika</p>
                  <p class="text-slate-600">Pemerintah Kabupaten Bojonegoro</p>
                  <p class="text-slate-600">Jl. P. Mas Tumapel No. 1</p>
                  <p class="text-slate-600">Bojonegoro, Jawa Timur 62115</p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </AppLayout>
</template>
