<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  services: Array,
  posts: Array,
})
</script>

<template>
  <AppLayout title="Selamat Datang">
    <!-- Hero Section -->
    <header class="bg-gray-50">
      <div
        class="container mx-auto px-4 py-20 text-center sm:px-6 md:py-32 lg:px-8"
      >
        <h1
          class="text-4xl font-extrabold tracking-tight text-gray-900 md:text-5xl"
        >
          <span class="block">Menjaga Integritas dan Keamanan</span>
          <span class="block text-blue-600"
            >Aset Digital Pemerintah Kabupaten Bojonegoro</span
          >
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-500 md:text-xl">
          CSIRT Bojonegoro hadir sebagai tim respons insiden keamanan siber
          untuk melindungi data dan sistem informasi di lingkungan Pemkab
          Bojonegoro.
        </p>
        <div class="mt-8 flex justify-center">
          <div class="inline-flex rounded-md shadow">
            <Button
              label="Lapor Insiden Siber"
              severity="info"
              size="large"
              icon="pi pi-shield"
              class="font-semibold"
              @click="router.visit('/insiden')"
            />
          </div>
        </div>
      </div>
    </header>

    <!-- Services Section -->
    <section id="layanan" class="py-20">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2
            class="text-base font-semibold uppercase tracking-wider text-blue-600"
          >
            Layanan Kami
          </h2>
          <p
            class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
          >
            Fokus Utama CSIRT Bojonegoro
          </p>
          <p class="mx-auto mt-4 max-w-2xl text-xl text-gray-500">
            Kami menyediakan beberapa layanan kunci untuk menjaga ekosistem
            digital yang aman.
          </p>
        </div>

        <div class="mt-12">
          <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="service in props.services"
              :key="service.id"
              class="pt-6"
            >
              <div class="flow-root rounded-lg bg-gray-50 px-6 pb-8">
                <div class="-mt-6">
                  <div>
                    <span
                      class="inline-flex items-center justify-center rounded-md bg-blue-500 p-3 shadow-lg"
                    >
                      <i :class="`pi ${service.icon} text-2xl text-white`"></i>
                    </span>
                  </div>
                  <h3
                    class="mt-8 text-lg font-medium tracking-tight text-gray-900"
                  >
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
      </div>
    </section>

    <!-- News Section -->
    <section id="berita" class="bg-gray-50 py-20">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2
            class="text-base font-semibold uppercase tracking-wider text-blue-600"
          >
            Pusat Informasi
          </h2>
          <p
            class="mt-2 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
          >
            Artikel & Panduan Terbaru
          </p>
          <p class="mx-auto mt-4 max-w-2xl text-xl text-gray-500">
            Ikuti informasi terkini seputar keamanan siber untuk meningkatkan
            kewaspadaan.
          </p>
        </div>
        <div
          class="mx-auto mt-12 grid max-w-lg gap-8 lg:max-w-none lg:grid-cols-3"
        >
          <div
            v-for="post in props.posts"
            :key="post.id"
            class="flex flex-col overflow-hidden rounded-lg shadow-lg"
          >
            <div v-if="post.image" class="flex-shrink-0">
              <img
                :src="post.image.startsWith('http') ? post.image : '/storage/' + post.image"
                :alt="post.title"
                class="h-48 w-full object-cover"
              />
            </div>
            <div class="flex flex-1 flex-col justify-between bg-white p-6">
              <div class="flex-1">
                <a class="mt-2 block">
                  <p class="text-xl font-semibold text-gray-900">
                    {{ post.title }}
                  </p>
                  <p class="mt-3 text-base text-gray-500">{{ post.excerpt }}</p>
                </a>
              </div>
              <div class="mt-6 flex items-center">
                <a
                  :href="route('posts.show', { post: post.slug })"
                  class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                  >Baca selengkapnya &rarr;</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
