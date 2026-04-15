<script setup>
import { useParticles } from '@/Composables/useParticles'

defineProps({
  contact: Object,
})

const { minimalParticlesOptions } = useParticles()
</script>

<template>
  <section
    id="kontak"
    class="relative overflow-hidden bg-slate-900 py-8 sm:py-16 lg:py-20"
  >
    <div class="pointer-events-none absolute inset-0 z-0">
      <vue-particles
        id="landing2particles"
        :options="minimalParticlesOptions"
        class="h-full w-full"
      />
    </div>

    <div class="container relative z-10">
      <div class="mb-8 text-center sm:mb-16">
        <h2
          class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-400"
        >
          Hubungi Kami
        </h2>
        <h3 class="mb-4 text-4xl font-extrabold text-white sm:text-5xl">
          Siap Membantu 24/7
        </h3>
        <p class="mx-auto max-w-2xl text-xl text-slate-300 lg:text-2xl">
          Tim CSIRT Bojonegoro siap merespons setiap laporan insiden keamanan
          siber
        </p>
      </div>

      <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Lapor Insiden -->
        <a href="/incident" class="block hover:no-underline">
          <div
            class="z-20 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 p-6 text-center text-white transition-transform duration-200 hover:scale-105 sm:p-8"
          >
            <div
              class="mx-auto mb-4 hidden h-16 w-16 items-center justify-center rounded-full bg-white/20 sm:flex"
            >
              <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <h4 class="mb-0 text-2xl font-semibold sm:mb-2">Lapor Insiden</h4>
            <p class="mb-0 text-red-100 sm:mb-4">
              Buat laporan insiden siber baru
            </p>
            <span
              class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-4 py-1.5 text-sm font-medium"
            >
              Buat Tiket Sekarang →
            </span>
          </div>
        </a>

        <!-- Darurat 24/7 -->
        <div
          class="z-20 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-700 p-6 text-center text-white sm:p-8"
        >
          <div
            class="mx-auto mb-4 hidden h-16 w-16 items-center justify-center rounded-full bg-white/20 sm:flex"
          >
            <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
              <path
                fill-rule="evenodd"
                d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <h4 class="mb-0 text-2xl font-semibold sm:mb-2">Darurat 24/7</h4>
          <p class="mb-0 text-orange-100 sm:mb-2">
            Laporan insiden keamanan siber
          </p>
          <p class="text-3xl font-bold">{{ contact.phone }}</p>
        </div>

        <!-- Email -->
        <a :href="`mailto:${contact.email}`" class="block hover:no-underline">
          <div
            class="z-20 rounded-2xl border border-slate-200 bg-gradient-to-br from-gray-100 to-indigo-100 p-6 text-center sm:p-8"
          >
            <div
              class="mx-auto mb-4 hidden h-16 w-16 items-center justify-center rounded-full bg-indigo-100 sm:flex"
            >
              <svg
                class="h-8 w-8 text-indigo-600"
                fill="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z"
                />
                <path
                  d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z"
                />
              </svg>
            </div>
            <h4 class="mb-0 text-2xl font-semibold text-slate-900 sm:mb-2">
              Email
            </h4>
            <p class="mb-0 text-slate-600 sm:mb-2">
              Kontak umum dan konsultasi
            </p>
            <p class="text-lg font-medium text-indigo-600">
              {{ contact.email }}
            </p>
          </div>
        </a>

        <!-- Alamat Kantor -->
        <div
          class="z-20 rounded-2xl border border-slate-200 bg-gradient-to-br from-gray-100 to-indigo-100 p-6 text-center sm:p-8"
        >
          <div
            class="mx-auto mb-4 hidden h-16 w-16 items-center justify-center rounded-full bg-indigo-100 sm:flex"
          >
            <svg
              class="h-8 w-8 text-indigo-600"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <h4 class="mb-0 text-2xl font-semibold text-slate-900 sm:mb-2">
            Alamat Kantor
          </h4>
          <p class="mb-0 text-slate-600 sm:mb-2">
            Dinas Komunikasi dan Informatika
          </p>
          <p class="text-slate-700">
            Jl. P. Mas Tumapel No.1, Bojonegoro, Jawa Timur 62115
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
