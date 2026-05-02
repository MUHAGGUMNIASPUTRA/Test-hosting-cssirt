<script setup>
// filepath: resources/js/Pages/Contact/Index.vue

import { onMounted, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useParticles } from '@/Composables/useParticles'

const page = usePage()
const contact = page.props.contact

// Animation refs
const heroRef = ref(null)
const contactRef = ref(null)
const { minimalParticlesOptions } = useParticles()

// Contact details data
const contactDetails = [
  {
    name: 'Telepon',
    value: contact.phone,
    icon: 'pi-phone',
    color: 'text-green-600',
    bgColor: 'bg-green-100',
    type: 'phone',
  },
  {
    name: 'Email',
    value: contact.email,
    icon: 'pi-envelope',
    color: 'text-blue-600',
    bgColor: 'bg-blue-100',
    type: 'email',
  },
]

// Scroll animations
onMounted(() => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px',
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
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
    <section
      ref="heroRef"
      class="relative translate-y-10 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 opacity-0"
    >
      <div class="absolute inset-0 z-0">
        <vue-particles
          id="tsparticles"
          :options="minimalParticlesOptions"
          class="h-full w-full"
        />
      </div>

      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container text-center">
          <div class="animate-fade-in-up">
            <h1
              class="text-5xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl md:text-7xl"
            >
              <span class="">Hubungi</span>
              <span
                class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text pb-2 text-transparent"
              >
                Kami
              </span>
            </h1>
            <p
              class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl"
            >
              Tim CSIRT Bojonegoro siap membantu Anda 24/7. Laporkan insiden
              keamanan siber atau konsultasikan kebutuhan keamanan digital Anda.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Emergency Banner -->
    <section class="bg-red-600 py-4">
      <div class="container">
        <div class="flex items-center justify-center text-center">
          <svg
            class="mr-3 hidden h-5 w-5 animate-pulse text-white lg:flex"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
          <p class="text-xl font-medium text-white">
            <span class="font-bold">DARURAT:</span> Untuk insiden keamanan siber
            segera buat laporan melalui<Link
              :href="route('incident.create')"
              class="ml-1 underline hover:no-underline"
              >Lapor Insiden</Link
            >
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Information -->
    <section
      ref="contactRef"
      class="translate-y-10 bg-white py-8 opacity-0 sm:py-16 lg:py-20"
    >
      <div class="container max-w-7xl">
        <!-- Contact Cards -->
        <div class="mb-8 grid grid-cols-1 gap-8 sm:mb-16 sm:grid-cols-2">
          <div
            v-for="(contact, index) in contactDetails"
            :key="contact.name"
            class="group relative transform rounded-2xl border bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
            :style="{ animationDelay: `${index * 100}ms` }"
            :class="
              contact.type === 'emergency'
                ? 'border-red-200 bg-red-50 hover:border-red-300'
                : 'border-slate-200 hover:border-indigo-300'
            "
          >
            <div class="mb-6 flex items-center">
              <!-- Icon -->
              <div class="relative mr-6">
                <div
                  class="flex h-12 w-12 items-center justify-center rounded-xl transition-shadow duration-300 group-hover:shadow-xl"
                  :class="
                    contact.type === 'emergency'
                      ? 'bg-gradient-to-br from-red-500 to-red-600'
                      : `${contact.bgColor}`
                  "
                >
                  <i
                    :class="[
                      `pi ${contact.icon} !text-xl`,
                      contact.type === 'emergency'
                        ? 'text-white'
                        : contact.color,
                    ]"
                  ></i>
                </div>
                <div
                  class="absolute -inset-2 -z-10 rounded-xl opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                  :class="
                    contact.type === 'emergency'
                      ? 'bg-gradient-to-br from-red-500/20 to-red-600/20'
                      : 'bg-gradient-to-br from-indigo-500/20 to-blue-600/20'
                  "
                ></div>
              </div>

              <!-- Content -->
              <h4
                class="text-2xl font-semibold transition-colors duration-300 group-hover:text-indigo-600"
                :class="
                  contact.type === 'emergency'
                    ? 'text-red-700 group-hover:text-red-600'
                    : 'text-slate-900'
                "
              >
                {{ contact.name }}
                <p
                  v-if="contact.type === 'emergency'"
                  class="text-sm font-normal text-red-500"
                >
                  {{ contact.subtitle }}
                </p>
              </h4>
            </div>

            <p
              class="whitespace-pre-line leading-relaxed"
              :class="
                contact.type === 'emergency'
                  ? 'font-medium text-red-600'
                  : 'text-slate-600'
              "
            >
              {{ contact.value }}
            </p>

            <!-- Action Button -->
            <div v-if="contact.type != 'address'" class="mt-4">
              <a
                v-if="contact.type === 'email'"
                :href="`mailto:${contact.value}`"
                class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
              >
                Kirim Email
                <svg
                  class="ml-2 h-4 w-4 transition-transform duration-200 group-hover/link:translate-x-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  />
                </svg>
              </a>
              <a
                v-else-if="
                  contact.type === 'phone' || contact.type === 'emergency'
                "
                :href="`tel:${contact.value.replace(/[^0-9+]/g, '')}`"
                class="group/link inline-flex items-center font-medium"
                :class="
                  contact.type === 'emergency'
                    ? 'text-red-600 hover:text-red-700'
                    : 'text-indigo-600 hover:text-indigo-700'
                "
              >
                {{
                  contact.type === 'emergency' ? 'Hubungi Sekarang' : 'Telepon'
                }}
                <svg
                  class="ml-2 h-4 w-4 transition-transform duration-200 group-hover/link:translate-x-1"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  />
                </svg>
              </a>
            </div>

            <!-- Hover Effect -->
            <div
              class="pointer-events-none absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300 group-hover:opacity-100"
              :class="
                contact.type === 'emergency'
                  ? 'bg-gradient-to-br from-red-500/5 to-red-600/5'
                  : 'bg-gradient-to-br from-indigo-500/5 to-blue-600/5'
              "
            ></div>
          </div>
        </div>

        <!-- Map -->
        <div class="relative">
          <h4 class="mb-6 text-2xl font-bold text-slate-900">
            Alamat CSIRT Bojonegoro
          </h4>
          <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          >
            <div class="h-[300px] w-full">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1377.5189600133622!2d111.88276332598195!3d-7.151168687398831!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77810f51999bf5%3A0x33a31012c7b7bbfb!2sDinas%20Komunikasi%20dan%20Informatika%20Kabupaten%20Bojonegoro!5e0!3m2!1sen!2sid!4v1755065976481!5m2!1sen!2sid"
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
                <IconMap2 size="20" class="mr-3 mt-1 text-indigo-600" />
                <div>
                  <p class="font-semibold text-slate-900">
                    Dinas Komunikasi dan Informatika
                  </p>
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
