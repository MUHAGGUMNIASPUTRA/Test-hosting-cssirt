<script setup>
import { useParticles } from '@/Composables/useParticles'

const { minimalParticlesOptions } = useParticles()
</script>

<template>
  <section
    class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
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

    <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
      <div class="container max-w-4xl text-center">
        <div class="animate-fade-in-up">
          <!-- Alert Icon -->
          <div
            class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-full bg-red-100/20 backdrop-blur-sm"
          >
            <IconAlertHexagon size="36" class="text-red-400" />
          </div>

          <h1
            class="mb-6 text-5xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl lg:text-7xl"
          >
            Lapor
            <span
              class="bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent"
              >Insiden Siber</span
            >
          </h1>

          <p class="mx-auto mb-8 max-w-3xl text-xl text-slate-300 sm:text-2xl">
            Buat tiket baru untuk melaporkan insiden keamanan siber atau lacak
            status tiket yang sudah ada
          </p>

          <!-- Quick Stats -->
          <div class="mt-8 grid grid-cols-3 gap-3 sm:mt-12 sm:gap-6">
            <div
              class="rounded-2xl border border-white/20 bg-white/10 p-2 backdrop-blur-sm sm:p-6"
            >
              <div
                class="mb-0 text-xl font-bold text-white sm:mb-1 sm:text-2xl lg:mb-2 lg:text-4xl"
              >
                24/7
              </div>
              <div class="text-sm text-slate-300 sm:text-base">
                Layanan Siaga
              </div>
            </div>
            <div
              class="rounded-2xl border border-white/20 bg-white/10 p-2 backdrop-blur-sm sm:p-6"
            >
              <div
                class="mb-0 text-xl font-bold text-white sm:mb-1 sm:text-2xl lg:mb-2 lg:text-4xl"
              >
                &lt; 24 Jam
              </div>
              <div class="text-sm text-slate-300 sm:text-base">
                Respons Awal
              </div>
            </div>
            <div
              class="rounded-2xl border border-white/20 bg-white/10 p-2 backdrop-blur-sm sm:p-6"
            >
              <div
                class="mb-0 text-xl font-bold text-white sm:mb-1 sm:text-2xl lg:mb-2 lg:text-4xl"
              >
                Rahasia
              </div>
              <div class="text-sm text-slate-300 sm:text-base">
                Data Terlindungi
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
