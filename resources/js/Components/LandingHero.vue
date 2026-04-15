<script setup>
import { useParticles } from '@/Composables/useParticles'
import { router } from '@inertiajs/vue3'

const { heroParticlesOptions } = useParticles()
</script>

<template>
  <section
    class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
  >
    <div class="absolute inset-0 z-0">
      <vue-particles
        id="tsParticles"
        :options="heroParticlesOptions"
        class="h-full w-full"
      />
    </div>
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
      <div
        class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
      ></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 flex min-h-screen items-center justify-center">
      <div class="container text-center">
        <div class="animate-fade-in-up">
          <h1
            class="text-5xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl md:text-7xl lg:text-8xl"
          >
            <span class="block">Menjaga Integritas dan</span>
            <span
              class="block bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text pb-2 text-transparent"
            >
              Keamanan Digital
            </span>
          </h1>
          <p
            class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl lg:text-3xl"
          >
            CSIRT Bojonegoro hadir sebagai tim respons insiden keamanan siber
            untuk melindungi aset digital Pemerintah Kabupaten Bojonegoro
          </p>
          <div class="mt-10 flex justify-center">
            <button
              @click="router.visit('/incident')"
              class="group relative inline-flex transform items-center rounded-full bg-gradient-to-r from-red-600 to-red-700 px-8 py-4 text-lg font-semibold text-white shadow-xl transition-all duration-300 hover:scale-105 hover:from-red-700 hover:to-red-800 hover:shadow-2xl"
            >
              <svg
                class="mr-3 h-5 w-5 animate-pulse"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
              Lapor Insiden Siber
              <div
                class="absolute inset-0 rounded-full bg-white opacity-0 transition-opacity duration-300 group-hover:opacity-20"
              ></div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div
      class="absolute bottom-8 left-1/2 -translate-x-1/2 transform animate-bounce"
    >
      <svg
        class="h-6 w-6 text-white/60"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M19 14l-7 7m0 0l-7-7m7 7V3"
        />
      </svg>
    </div>
  </section>
</template>
