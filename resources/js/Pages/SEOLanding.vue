<template>
  <SEOLayout
    title="CSIRT Bojonegoro - Computer Security Incident Response Team"
  >
    <!-- Hero Section -->
    <section
      class="relative bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900 py-24 sm:py-32"
    >
      <div class="container mx-auto px-4">
        <div class="text-center">
          <div class="mb-8 flex items-center justify-center">
            <div
              class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-2xl"
            >
              <span class="text-2xl font-bold text-white">CSIRT</span>
            </div>
          </div>

          <h1
            class="mb-6 text-4xl font-bold leading-tight text-white md:text-6xl lg:text-7xl"
          >
            CSIRT Bojonegoro
          </h1>

          <p
            class="mx-auto mb-8 max-w-4xl text-xl leading-relaxed text-blue-200 md:text-2xl"
          >
            Computer Security Incident Response Team untuk keamanan siber
            Kabupaten Bojonegoro. Melindungi infrastruktur digital pemerintahan
            dengan respons cepat dan efektif.
          </p>

          <div
            class="flex flex-col items-center justify-center gap-4 sm:flex-row"
          >
            <a
              href="/incident"
              class="inline-flex transform items-center rounded-xl bg-red-600 px-8 py-4 text-lg font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:bg-red-700 hover:shadow-xl"
            >
              <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z"
                />
              </svg>
              Lapor Insiden Keamanan
            </a>
            <a
              href="/services"
              class="inline-flex items-center rounded-xl bg-white px-8 py-4 text-lg font-semibold text-indigo-600 shadow-lg transition-all duration-200 hover:bg-indigo-50 hover:shadow-xl"
            >
              Lihat Layanan Kami
              <svg
                class="ml-3 h-5 w-5"
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
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" class="bg-gray-50 py-16 sm:py-24">
      <div class="container mx-auto px-4">
        <div class="mb-16 text-center">
          <h2 class="mb-6 text-3xl font-bold text-slate-900 md:text-5xl">
            Layanan CSIRT Bojonegoro
          </h2>
          <p class="mx-auto max-w-3xl text-xl leading-relaxed text-slate-600">
            Kami menyediakan berbagai layanan keamanan siber untuk melindungi
            infrastruktur digital pemerintahan dan masyarakat Kabupaten
            Bojonegoro.
          </p>
        </div>

        <div class="mb-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="service in services"
            :key="service.id"
            class="group rounded-xl border border-slate-100 bg-white p-8 shadow-lg transition-all duration-300 hover:shadow-xl"
          >
            <div class="mb-6 flex items-center">
              <div
                class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600"
              >
                <svg
                  class="h-6 w-6 text-white"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1M12 7C13.4 7 14.8 8.6 14.8 10V11.5C15.4 11.5 16 12.1 16 12.7V16.2C16 16.8 15.4 17.3 14.8 17.3H9.2C8.6 17.3 8 16.8 8 16.2V12.7C8 12.1 8.6 11.5 9.2 11.5V10C9.2 8.6 10.6 7 12 7M12 8.2C11.2 8.2 10.5 8.7 10.5 10V11.5H13.5V10C13.5 8.7 12.8 8.2 12 8.2Z"
                  />
                </svg>
              </div>
              <h3
                class="text-xl font-bold text-slate-900 transition-colors duration-200 group-hover:text-indigo-600"
              >
                {{ service.name }}
              </h3>
            </div>
            <p class="leading-relaxed text-slate-600">
              {{ service.short_description }}
            </p>
          </div>
        </div>

        <div class="text-center">
          <a
            href="/services"
            class="inline-flex items-center rounded-lg border border-indigo-300 bg-indigo-50 px-6 py-3 text-lg font-medium text-indigo-700 transition-all duration-200 hover:border-indigo-400 hover:bg-indigo-100"
          >
            Lihat Semua Layanan
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
          </a>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="bg-indigo-600 py-16 sm:py-24">
      <div class="container mx-auto px-4">
        <div class="mb-16 text-center">
          <h2 class="mb-6 text-3xl font-bold text-white md:text-5xl">
            Melindungi Bojonegoro Digital
          </h2>
          <p class="mx-auto max-w-3xl text-xl text-indigo-200">
            Komitmen kami dalam menjaga keamanan siber Kabupaten Bojonegoro
          </p>
        </div>

        <div class="grid gap-8 md:grid-cols-4">
          <div class="text-center">
            <div class="mb-2 text-4xl font-bold text-white">24/7</div>
            <div class="text-indigo-200">Monitoring Keamanan</div>
          </div>
          <div class="text-center">
            <div class="mb-2 text-4xl font-bold text-white">100+</div>
            <div class="text-indigo-200">Insiden Tertangani</div>
          </div>
          <div class="text-center">
            <div class="mb-2 text-4xl font-bold text-white">15</div>
            <div class="text-indigo-200">Menit Rata-rata Respons</div>
          </div>
          <div class="text-center">
            <div class="mb-2 text-4xl font-bold text-white">50+</div>
            <div class="text-indigo-200">Instansi Terlindungi</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Articles Section -->
    <section
      id="artikel"
      class="bg-white py-16 sm:py-24"
      v-if="posts && posts.length > 0"
    >
      <div class="container mx-auto px-4">
        <div class="mb-16 text-center">
          <h2 class="mb-6 text-3xl font-bold text-slate-900 md:text-5xl">
            Artikel & Panduan Keamanan
          </h2>
          <p class="mx-auto max-w-3xl text-xl text-slate-600">
            Baca artikel terbaru dan panduan keamanan siber untuk meningkatkan
            kesadaran dan kemampuan proteksi digital.
          </p>
        </div>

        <div class="mb-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="post in posts"
            :key="post.id"
            class="group overflow-hidden rounded-xl border border-slate-100 bg-white shadow-lg transition-all duration-300 hover:shadow-xl"
          >
            <div class="p-8">
              <h3
                class="mb-4 text-xl font-bold text-slate-900 transition-colors duration-200 group-hover:text-indigo-600"
              >
                <a :href="`/posts/${post.slug}`">{{ post.title }}</a>
              </h3>
              <p class="mb-4 leading-relaxed text-slate-600">
                {{ post.excerpt }}
              </p>
              <div
                class="flex items-center justify-between text-sm text-slate-500"
              >
                <time :datetime="post.published_at">
                  {{ formatDate(post.published_at) }}
                </time>
                <a
                  :href="`/posts/${post.slug}`"
                  class="font-medium text-indigo-600 hover:text-indigo-700"
                >
                  Baca Selengkapnya →
                </a>
              </div>
            </div>
          </article>
        </div>

        <div class="text-center">
          <a
            href="/posts"
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
          </a>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="bg-slate-900 py-16 sm:py-24">
      <div class="container mx-auto px-4">
        <div class="mb-16 text-center">
          <h2 class="mb-6 text-3xl font-bold text-white md:text-5xl">
            Hubungi CSIRT Bojonegoro
          </h2>
          <p class="mx-auto max-w-3xl text-xl text-slate-300">
            Tim kami siap membantu Anda 24/7 dalam menangani insiden keamanan
            siber dan memberikan konsultasi.
          </p>
        </div>

        <div class="mx-auto grid max-w-4xl gap-12 md:grid-cols-2">
          <div class="rounded-xl bg-slate-800 p-8">
            <h3 class="mb-6 text-2xl font-bold text-white">Kontak Darurat</h3>
            <div class="space-y-6">
              <div class="flex items-center">
                <div
                  class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-red-600"
                >
                  <svg
                    class="h-6 w-6 text-white"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
                    />
                  </svg>
                </div>
                <div>
                  <div class="font-semibold text-white">Hotline Darurat</div>
                  <div class="text-slate-300">+62 353 881826</div>
                </div>
              </div>

              <div class="flex items-center">
                <div
                  class="mr-4 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-600"
                >
                  <svg
                    class="h-6 w-6 text-white"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                    />
                  </svg>
                </div>
                <div>
                  <div class="font-semibold text-white">Email Resmi</div>
                  <div class="text-slate-300">ttis@bojonegorokab.go.id</div>
                </div>
              </div>
            </div>
          </div>

          <div class="rounded-xl bg-slate-800 p-8">
            <h3 class="mb-6 text-2xl font-bold text-white">Layanan Kami</h3>
            <ul class="space-y-4 text-slate-300">
              <li class="flex items-center">
                <svg
                  class="mr-3 h-5 w-5 text-green-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Respons insiden keamanan 24/7</span>
              </li>
              <li class="flex items-center">
                <svg
                  class="mr-3 h-5 w-5 text-green-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Analisis forensik digital</span>
              </li>
              <li class="flex items-center">
                <svg
                  class="mr-3 h-5 w-5 text-green-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Konsultasi keamanan gratis</span>
              </li>
              <li class="flex items-center">
                <svg
                  class="mr-3 h-5 w-5 text-green-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Pelatihan keamanan siber</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </SEOLayout>
</template>

<script setup>
import SEOLayout from '@/Layouts/SEOLayout.vue'

const props = defineProps({
  services: {
    type: Array,
    default: () => [],
  },
  posts: {
    type: Array,
    default: () => [],
  },
})

// Format date function for SSR compatibility
const formatDate = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    })
  } catch (error) {
    return dateString
  }
}
</script>
