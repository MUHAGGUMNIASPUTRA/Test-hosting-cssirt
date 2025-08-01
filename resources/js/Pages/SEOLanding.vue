<template>
  <SEOLayout title="CSIRT Bojonegoro - Computer Security Incident Response Team">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900 py-24 sm:py-32">
      <div class="container mx-auto px-4">
        <div class="text-center">
          <div class="flex items-center justify-center mb-8">
            <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-2xl">
              <span class="text-white font-bold text-2xl">CSIRT</span>
            </div>
          </div>

          <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
            CSIRT Bojonegoro
          </h1>

          <p class="text-xl md:text-2xl text-blue-200 mb-8 max-w-4xl mx-auto leading-relaxed">
            Computer Security Incident Response Team untuk keamanan siber Kabupaten Bojonegoro.
            Melindungi infrastruktur digital pemerintahan dengan respons cepat dan efektif.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a
              href="/incident"
              class="inline-flex items-center px-8 py-4 text-lg font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
              </svg>
              Lapor Insiden Keamanan
            </a>
            <a
              href="/services"
              class="inline-flex items-center px-8 py-4 text-lg font-semibold text-indigo-600 bg-white hover:bg-indigo-50 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              Lihat Layanan Kami
              <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" class="py-16 sm:py-24 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6">
            Layanan CSIRT Bojonegoro
          </h2>
          <p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
            Kami menyediakan berbagai layanan keamanan siber untuk melindungi infrastruktur digital
            pemerintahan dan masyarakat Kabupaten Bojonegoro.
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
          <div
            v-for="service in services"
            :key="service.id"
            class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 group border border-slate-100"
          >
            <div class="flex items-center mb-6">
              <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1M12 7C13.4 7 14.8 8.6 14.8 10V11.5C15.4 11.5 16 12.1 16 12.7V16.2C16 16.8 15.4 17.3 14.8 17.3H9.2C8.6 17.3 8 16.8 8 16.2V12.7C8 12.1 8.6 11.5 9.2 11.5V10C9.2 8.6 10.6 7 12 7M12 8.2C11.2 8.2 10.5 8.7 10.5 10V11.5H13.5V10C13.5 8.7 12.8 8.2 12 8.2Z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors duration-200">
                {{ service.name }}
              </h3>
            </div>
            <p class="text-slate-600 leading-relaxed">
              {{ service.short_description }}
            </p>
          </div>
        </div>

        <div class="text-center">
          <a
            href="/services"
            class="inline-flex items-center px-6 py-3 border border-indigo-300 text-lg font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-400 transition-all duration-200"
          >
            Lihat Semua Layanan
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 sm:py-24 bg-indigo-600">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">
            Melindungi Bojonegoro Digital
          </h2>
          <p class="text-xl text-indigo-200 max-w-3xl mx-auto">
            Komitmen kami dalam menjaga keamanan siber Kabupaten Bojonegoro
          </p>
        </div>

        <div class="grid md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="text-4xl font-bold text-white mb-2">24/7</div>
            <div class="text-indigo-200">Monitoring Keamanan</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-white mb-2">100+</div>
            <div class="text-indigo-200">Insiden Tertangani</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-white mb-2">15</div>
            <div class="text-indigo-200">Menit Rata-rata Respons</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-white mb-2">50+</div>
            <div class="text-indigo-200">Instansi Terlindungi</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Articles Section -->
    <section id="artikel" class="py-16 sm:py-24 bg-white" v-if="posts && posts.length > 0">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6">
            Artikel & Panduan Keamanan
          </h2>
          <p class="text-xl text-slate-600 max-w-3xl mx-auto">
            Baca artikel terbaru dan panduan keamanan siber untuk meningkatkan kesadaran dan kemampuan proteksi digital.
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
          <article
            v-for="post in posts"
            :key="post.id"
            class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 group border border-slate-100"
          >
            <div class="p-8">
              <h3 class="text-xl font-bold text-slate-900 mb-4 group-hover:text-indigo-600 transition-colors duration-200">
                <a :href="`/posts/${post.slug}`">{{ post.title }}</a>
              </h3>
              <p class="text-slate-600 mb-4 leading-relaxed">
                {{ post.excerpt }}
              </p>
              <div class="flex items-center justify-between text-sm text-slate-500">
                <time :datetime="post.published_at">
                  {{ formatDate(post.published_at) }}
                </time>
                <a
                  :href="`/posts/${post.slug}`"
                  class="text-indigo-600 hover:text-indigo-700 font-medium"
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
            class="inline-flex items-center px-6 py-3 border border-indigo-300 text-lg font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-400 transition-all duration-200"
          >
            Lihat Semua Artikel
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="py-16 sm:py-24 bg-slate-900">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">
            Hubungi CSIRT Bojonegoro
          </h2>
          <p class="text-xl text-slate-300 max-w-3xl mx-auto">
            Tim kami siap membantu Anda 24/7 dalam menangani insiden keamanan siber dan memberikan konsultasi.
          </p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 max-w-4xl mx-auto">
          <div class="bg-slate-800 rounded-xl p-8">
            <h3 class="text-2xl font-bold text-white mb-6">Kontak Darurat</h3>
            <div class="space-y-6">
              <div class="flex items-center">
                <div class="h-12 w-12 rounded-lg bg-red-600 flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                  </svg>
                </div>
                <div>
                  <div class="text-white font-semibold">Hotline Darurat</div>
                  <div class="text-slate-300">+62 353 881826</div>
                </div>
              </div>

              <div class="flex items-center">
                <div class="h-12 w-12 rounded-lg bg-indigo-600 flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                  </svg>
                </div>
                <div>
                  <div class="text-white font-semibold">Email Resmi</div>
                  <div class="text-slate-300">ttis@bojonegorokab.go.id</div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-slate-800 rounded-xl p-8">
            <h3 class="text-2xl font-bold text-white mb-6">Layanan Kami</h3>
            <ul class="space-y-4 text-slate-300">
              <li class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Respons insiden keamanan 24/7</span>
              </li>
              <li class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Analisis forensik digital</span>
              </li>
              <li class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Konsultasi keamanan gratis</span>
              </li>
              <li class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
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
    default: () => []
  },
  posts: {
    type: Array,
    default: () => []
  }
})

// Format date function for SSR compatibility
const formatDate = (dateString) => {
  if (!dateString) return ''
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}
</script>
