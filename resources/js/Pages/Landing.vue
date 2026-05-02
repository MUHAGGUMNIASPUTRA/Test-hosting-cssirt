<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  services: Array,
  posts: Array,
})

const page = usePage()
const contact = page.props.contact

const servicesRef = ref(null)

// Dynamic grid classes based on services count
const serviceGridClasses = computed(() => {
  const count = props.services?.length || 0
  if (count <= 1) return 'grid-cols-1'
  if (count <= 2) return 'grid-cols-1 sm:grid-cols-2'
  if (count <= 3) return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
  return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
})

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
  if (servicesRef.value) observer.observe(servicesRef.value)
})
</script>

<template>
  <AppLayout title="Selamat Datang">
    <LandingHero />

    <LandingAbout />

    <!-- Services Section -->
    <section
      id="layanan"
      ref="servicesRef"
      class="relative translate-y-10 overflow-hidden border border-y bg-slate-50 py-8 opacity-0 sm:py-16 lg:py-20"
    >
      <div
        class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-100 via-white to-indigo-200 opacity-40"
      ></div>
      <div class="container">
        <div class="mb-8 text-center sm:mb-16">
          <h2
            class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
          >
            Layanan Kami
          </h2>
          <h3 class="mb-4 text-4xl font-extrabold text-slate-900 sm:text-5xl">
            Fokus Utama CSIRT Bojonegoro
          </h3>
          <p class="mx-auto max-w-2xl text-xl text-slate-600 lg:text-2xl">
            Kami menyediakan layanan komprehensif untuk menjaga ekosistem
            digital yang aman dan terpercaya
          </p>
        </div>
        <div class="grid gap-8" :class="serviceGridClasses">
          <ServiceCard
            v-for="(service, index) in props.services"
            :key="service.id"
            :service="service"
            :animation-delay="index * 100"
          />
        </div>
      </div>
    </section>

    <!-- Articles Section -->
    <section id="berita" class="bg-white py-8 sm:py-16 lg:py-20">
      <div class="container">
        <div class="mb-8 text-center sm:mb-16">
          <h2
            class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
          >
            Pusat Informasi
          </h2>
          <h3 class="mb-4 text-4xl font-extrabold text-slate-900 sm:text-5xl">
            Artikel & Panduan Terbaru
          </h3>
          <p class="mx-auto max-w-2xl text-xl text-slate-600 lg:text-2xl">
            Ikuti informasi terkini seputar keamanan siber untuk meningkatkan
            kewaspadaan
          </p>
        </div>
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
          <PostCard
            v-for="(post, index) in props.posts"
            :key="post.id"
            :post="post"
            :animation-delay="index * 150"
          />
        </div>
        <div class="mt-8 text-center sm:mt-12">
          <Link
            :href="route('posts.index')"
            class="inline-flex items-center rounded-lg border border-indigo-300 bg-indigo-50 px-6 py-3 text-lg font-medium text-indigo-700 transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-100"
          >
            Lihat Semua Artikel
            <svg
              class="ml-2 h-5 w-5"
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
          </Link>
        </div>
      </div>
    </section>

    <LandingContact :contact="contact" />
  </AppLayout>
</template>
