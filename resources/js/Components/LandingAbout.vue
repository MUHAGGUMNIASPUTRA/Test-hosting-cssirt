<template>
  <section
    id="tentang"
    ref="aboutRef"
    class="translate-y-10 bg-white py-8 opacity-0 sm:py-16 lg:py-20"
  >
    <div class="container">
      <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
        <div>
          <h2
            class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
          >
            Tentang CSIRT Bojonegoro
          </h2>
          <h3 class="mb-6 text-4xl font-extrabold text-slate-900 sm:text-5xl">
            Melindungi Aset Digital Pemerintahan
          </h3>
          <p class="mb-8 text-xl text-slate-600">
            Computer Security Incident Response Team (CSIRT) Kabupaten
            Bojonegoro berkomitmen untuk menjaga keamanan dan integritas sistem
            informasi pemerintahan.
          </p>

          <div class="space-y-4">
            <div
              v-for="item in missionPoints"
              :key="item"
              class="flex items-start"
            >
              <div class="flex-shrink-0">
                <div
                  class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 lg:mt-0.5"
                >
                  <svg
                    class="h-3 w-3 text-indigo-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
              </div>
              <p class="ml-3 text-base text-slate-700 lg:text-lg">{{ item }}</p>
            </div>
          </div>
        </div>

        <div class="flex justify-center lg:justify-end">
          <div class="relative">
            <div
              class="flex h-80 w-80 items-center justify-center rounded-3xl bg-gradient-to-br from-indigo-100 to-blue-100"
            >
              <svg
                class="h-40 w-40 text-indigo-600"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div
              class="absolute -right-4 -top-4 flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-500"
            >
              <svg
                class="h-12 w-12 text-white"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const aboutRef = ref(null)

const missionPoints = [
  'Respons cepat terhadap insiden keamanan siber',
  'Koordinasi keamanan lintas instansi pemerintahan',
  'Edukasi dan peningkatan kesadaran keamanan siber',
]

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting)
          entry.target.classList.add('animate-fade-in-up')
      })
    },
    { threshold: 0.1, rootMargin: '0px 0px -50px 0px' },
  )
  if (aboutRef.value) observer.observe(aboutRef.value)
})
</script>
