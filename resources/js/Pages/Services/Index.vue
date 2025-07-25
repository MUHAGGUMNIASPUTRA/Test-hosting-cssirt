<script setup>
defineProps({
  services: Array,
})
</script>

<template>
  <AppLayout title="Layanan Kami">
    <div class="bg-white">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="text-center">
          <h2
            class="text-base font-semibold uppercase tracking-wider text-blue-600"
          >
            Layanan
          </h2>
          <p
            class="mt-1 text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl"
          >
            Layanan Unggulan CSIRT Bojonegoro
          </p>
          <p class="mx-auto mt-5 max-w-xl text-xl text-gray-500">
            Kami menyediakan berbagai layanan untuk menjaga keamanan dan
            integritas aset digital di lingkungan Pemerintah Kabupaten
            Bojonegoro.
          </p>
        </div>

        <div
          class="mt-20 grid grid-cols-1 gap-y-12 sm:grid-cols-2 sm:gap-x-6 lg:grid-cols-3 lg:gap-x-8"
        >
          <div v-for="service in services" :key="service.id" class="relative">
            <div class="flex flex-col items-center text-center">
              <span
                class="flex h-12 w-12 items-center justify-center rounded-md bg-blue-500 text-white"
              >
                <i :class="`pi ${service.icon} text-2xl`"></i>
              </span>
              <h3 class="mt-8 text-lg font-medium text-gray-900">
                {{ service.name }}
              </h3>
              <p class="mt-5 text-base text-gray-500">
                {{ service.short_description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
