<template>
  <SEOLayout title="Artikel & Panduan Keamanan Siber - CSIRT Bojonegoro">
    <!-- Hero Section -->
    <section
      class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
    >
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container mx-auto text-center">
          <h1
            class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl"
          >
            Artikel &
            <span
              class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"
              >Panduan</span
            >
          </h1>
          <p class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl">
            Ikuti informasi, panduan, dan berita terkini seputar keamanan siber
            untuk meningkatkan kewaspadaan kita bersama
          </p>
        </div>
      </div>
    </section>

    <!-- Featured Post (Sample) -->
    <section class="bg-white py-8 sm:py-16 lg:py-20">
      <div class="container mx-auto px-4">
        <div class="mb-8 text-center sm:mb-12">
          <h2
            class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
          >
            Artikel Terbaru
          </h2>
        </div>

        <article
          class="group relative transform overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl"
        >
          <div class="lg:grid lg:grid-cols-2 lg:gap-0">
            <!-- Featured Image -->
            <div class="relative h-64 overflow-hidden lg:min-h-[400px]">
              <a href="/posts/panduan-keamanan-email" class="block h-full">
                <div
                  class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600"
                >
                  <span class="text-6xl text-white">📧</span>
                </div>
              </a>
            </div>

            <!-- Featured Content -->
            <div class="flex flex-col justify-center p-8 lg:p-12">
              <!-- Categories -->
              <div class="mb-4">
                <span
                  class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800"
                >
                  <a href="/posts/categories/panduan">Panduan Keamanan</a>
                </span>
              </div>

              <!-- Title -->
              <h3
                class="mb-4 line-clamp-3 text-2xl font-bold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600 sm:text-3xl lg:text-4xl"
              >
                <a href="/posts/panduan-keamanan-email">
                  Panduan Keamanan Email untuk Instansi Pemerintah
                </a>
              </h3>

              <!-- Excerpt -->
              <p class="mb-6 line-clamp-3 text-slate-600 sm:text-lg">
                Email merupakan salah satu pintu masuk utama serangan siber.
                Pelajari cara melindungi email instansi Anda dari phishing,
                malware, dan ancaman lainnya dengan panduan lengkap ini.
              </p>

              <!-- Meta Info -->
              <div class="mb-6 flex items-center text-slate-500">
                <div class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span class="font-medium">Tim CSIRT Bojonegoro</span>
                </div>
                <span class="mx-3">•</span>
                <time datetime="2025-01-30">30 Januari 2025</time>
              </div>

              <!-- Read More Button -->
              <a
                href="/posts/panduan-keamanan-email"
                class="group/link inline-flex items-center font-semibold text-indigo-600 hover:text-indigo-700"
              >
                Baca Selengkapnya
                <span
                  class="ml-2 transition-transform duration-200 group-hover/link:translate-x-1"
                  >→</span
                >
              </a>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- Regular Posts Grid -->
    <section class="bg-slate-50 py-8 sm:py-16 lg:py-20">
      <div class="container mx-auto px-4">
        <div class="mb-8 text-center sm:mb-12">
          <h2
            class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
          >
            Artikel Lainnya
          </h2>
        </div>

        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <!-- Article 1: Phishing Prevention -->
          <article
            class="group transform overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
          >
            <div class="relative overflow-hidden">
              <a href="/posts/mengenal-dan-mencegah-phishing" class="block">
                <div
                  class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-red-500 to-pink-600"
                >
                  <span class="text-4xl text-white">🎣</span>
                </div>
              </a>
            </div>

            <div class="p-6">
              <div class="mb-3">
                <span
                  class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800"
                >
                  <a href="/posts/categories/edukasi">Edukasi</a>
                </span>
              </div>

              <h3
                class="mb-3 line-clamp-2 text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
              >
                <a href="/posts/mengenal-dan-mencegah-phishing">
                  Mengenal dan Mencegah Serangan Phishing
                </a>
              </h3>

              <p class="mb-4 line-clamp-3 leading-relaxed text-slate-600">
                Phishing merupakan salah satu jenis serangan siber yang paling
                umum. Pelajari cara mengenali dan melindungi diri dari serangan
                phishing yang semakin canggih.
              </p>

              <div
                class="flex items-center justify-between text-sm text-slate-500"
              >
                <div class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span>Admin CSIRT</span>
                </div>
                <time datetime="2025-01-28">28 Jan</time>
              </div>

              <div class="mt-4 border-t border-slate-200 pt-4">
                <a
                  href="/posts/mengenal-dan-mencegah-phishing"
                  class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
                >
                  Baca Artikel
                  <span
                    class="ml-1 transition-transform duration-200 group-hover/link:translate-x-1"
                    >→</span
                  >
                </a>
              </div>
            </div>
          </article>

          <!-- Article 2: Password Security -->
          <article
            class="group transform overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
          >
            <div class="relative overflow-hidden">
              <a href="/posts/keamanan-password-yang-kuat" class="block">
                <div
                  class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-green-500 to-emerald-600"
                >
                  <span class="text-4xl text-white">🔐</span>
                </div>
              </a>
            </div>

            <div class="p-6">
              <div class="mb-3">
                <span
                  class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800"
                >
                  <a href="/posts/categories/keamanan">Keamanan</a>
                </span>
              </div>

              <h3
                class="mb-3 line-clamp-2 text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
              >
                <a href="/posts/keamanan-password-yang-kuat">
                  Membuat Password yang Kuat dan Aman
                </a>
              </h3>

              <p class="mb-4 line-clamp-3 leading-relaxed text-slate-600">
                Password adalah garis pertahanan pertama dalam keamanan digital.
                Pelajari cara membuat dan mengelola password yang kuat untuk
                melindungi akun penting Anda.
              </p>

              <div
                class="flex items-center justify-between text-sm text-slate-500"
              >
                <div class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span>Tim Keamanan</span>
                </div>
                <time datetime="2025-01-25">25 Jan</time>
              </div>

              <div class="mt-4 border-t border-slate-200 pt-4">
                <a
                  href="/posts/keamanan-password-yang-kuat"
                  class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
                >
                  Baca Artikel
                  <span
                    class="ml-1 transition-transform duration-200 group-hover/link:translate-x-1"
                    >→</span
                  >
                </a>
              </div>
            </div>
          </article>

          <!-- Article 3: Social Engineering -->
          <article
            class="group transform overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
          >
            <div class="relative overflow-hidden">
              <a href="/posts/waspada-social-engineering" class="block">
                <div
                  class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-yellow-500 to-orange-600"
                >
                  <span class="text-4xl text-white">🎭</span>
                </div>
              </a>
            </div>

            <div class="p-6">
              <div class="mb-3">
                <span
                  class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800"
                >
                  <a href="/posts/categories/awareness">Awareness</a>
                </span>
              </div>

              <h3
                class="mb-3 line-clamp-2 text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
              >
                <a href="/posts/waspada-social-engineering">
                  Waspada Social Engineering di Lingkungan Kerja
                </a>
              </h3>

              <p class="mb-4 line-clamp-3 leading-relaxed text-slate-600">
                Social engineering mengexploitasi faktor manusia dalam keamanan
                sistem. Kenali teknik-teknik manipulasi yang digunakan penyerang
                dan cara melindungi diri.
              </p>

              <div
                class="flex items-center justify-between text-sm text-slate-500"
              >
                <div class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span>Specialist CSIRT</span>
                </div>
                <time datetime="2025-01-22">22 Jan</time>
              </div>

              <div class="mt-4 border-t border-slate-200 pt-4">
                <a
                  href="/posts/waspada-social-engineering"
                  class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
                >
                  Baca Artikel
                  <span
                    class="ml-1 transition-transform duration-200 group-hover/link:translate-x-1"
                    >→</span
                  >
                </a>
              </div>
            </div>
          </article>

          <!-- Article 4: Data Backup -->
          <article
            class="group transform overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
          >
            <div class="relative overflow-hidden">
              <a href="/posts/strategi-backup-data-yang-efektif" class="block">
                <div
                  class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-purple-500 to-indigo-600"
                >
                  <span class="text-4xl text-white">💾</span>
                </div>
              </a>
            </div>

            <div class="p-6">
              <div class="mb-3">
                <span
                  class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-sm font-medium text-purple-800"
                >
                  <a href="/posts/categories/teknis">Teknis</a>
                </span>
              </div>

              <h3
                class="mb-3 line-clamp-2 text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
              >
                <a href="/posts/strategi-backup-data-yang-efektif">
                  Strategi Backup Data yang Efektif untuk Pemerintahan
                </a>
              </h3>

              <p class="mb-4 line-clamp-3 leading-relaxed text-slate-600">
                Data merupakan aset berharga yang harus dilindungi. Pelajari
                strategi backup yang tepat untuk memastikan kontinuitas
                operasional instansi pemerintah.
              </p>

              <div
                class="flex items-center justify-between text-sm text-slate-500"
              >
                <div class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span>IT Specialist</span>
                </div>
                <time datetime="2025-01-20">20 Jan</time>
              </div>

              <div class="mt-4 border-t border-slate-200 pt-4">
                <a
                  href="/posts/strategi-backup-data-yang-efektif"
                  class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
                >
                  Baca Artikel
                  <span
                    class="ml-1 transition-transform duration-200 group-hover/link:translate-x-1"
                    >→</span
                  >
                </a>
              </div>
            </div>
          </article>

          <!-- Article 5: Network Security -->
          <article
            class="group transform overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
          >
            <div class="relative overflow-hidden">
              <a href="/posts/keamanan-jaringan-wifi-kantor" class="block">
                <div
                  class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-teal-500 to-cyan-600"
                >
                  <span class="text-4xl text-white">📶</span>
                </div>
              </a>
            </div>

            <div class="p-6">
              <div class="mb-3">
                <span
                  class="inline-flex items-center rounded-full bg-teal-100 px-3 py-1 text-sm font-medium text-teal-800"
                >
                  <a href="/posts/categories/jaringan">Jaringan</a>
                </span>
              </div>

              <h3
                class="mb-3 line-clamp-2 text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
              >
                <a href="/posts/keamanan-jaringan-wifi-kantor">
                  Keamanan Jaringan WiFi di Lingkungan Kantor
                </a>
              </h3>

              <p class="mb-4 line-clamp-3 leading-relaxed text-slate-600">
                WiFi kantor yang tidak aman dapat menjadi celah bagi penyerang.
                Implementasikan konfigurasi keamanan yang tepat untuk melindungi
                jaringan internal.
              </p>

              <div
                class="flex items-center justify-between text-sm text-slate-500"
              >
                <div class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span>Network Admin</span>
                </div>
                <time datetime="2025-01-18">18 Jan</time>
              </div>

              <div class="mt-4 border-t border-slate-200 pt-4">
                <a
                  href="/posts/keamanan-jaringan-wifi-kantor"
                  class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
                >
                  Baca Artikel
                  <span
                    class="ml-1 transition-transform duration-200 group-hover/link:translate-x-1"
                    >→</span
                  >
                </a>
              </div>
            </div>
          </article>

          <!-- Article 6: Malware Protection -->
          <article
            class="group transform overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
          >
            <div class="relative overflow-hidden">
              <a
                href="/posts/perlindungan-dari-malware-dan-virus"
                class="block"
              >
                <div
                  class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-rose-500 to-red-600"
                >
                  <span class="text-4xl text-white">🦠</span>
                </div>
              </a>
            </div>

            <div class="p-6">
              <div class="mb-3">
                <span
                  class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-sm font-medium text-rose-800"
                >
                  <a href="/posts/categories/malware">Malware</a>
                </span>
              </div>

              <h3
                class="mb-3 line-clamp-2 text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
              >
                <a href="/posts/perlindungan-dari-malware-dan-virus">
                  Perlindungan dari Malware dan Virus Terkini
                </a>
              </h3>

              <p class="mb-4 line-clamp-3 leading-relaxed text-slate-600">
                Malware terus berkembang dengan teknik yang semakin canggih.
                Pelajari cara deteksi dini dan pencegahan infeksi malware pada
                sistem komputer.
              </p>

              <div
                class="flex items-center justify-between text-sm text-slate-500"
              >
                <div class="flex items-center">
                  <span class="mr-2">👤</span>
                  <span>Security Analyst</span>
                </div>
                <time datetime="2025-01-15">15 Jan</time>
              </div>

              <div class="mt-4 border-t border-slate-200 pt-4">
                <a
                  href="/posts/perlindungan-dari-malware-dan-virus"
                  class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
                >
                  Baca Artikel
                  <span
                    class="ml-1 transition-transform duration-200 group-hover/link:translate-x-1"
                    >→</span
                  >
                </a>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="bg-white py-12 sm:py-16 lg:py-20">
      <div class="container mx-auto px-4">
        <div class="mb-12 text-center">
          <h2 class="mb-4 text-3xl font-extrabold text-slate-900 sm:text-4xl">
            Kategori Artikel
          </h2>
          <p class="text-xl text-slate-600">
            Temukan artikel berdasarkan topik yang Anda minati
          </p>
        </div>

        <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
          <!-- Category: Security Guides -->
          <a
            href="/posts/categories/panduan"
            class="group block rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 p-6 transition-all duration-300 hover:from-blue-100 hover:to-indigo-200"
          >
            <div class="text-center">
              <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500"
              >
                <span class="text-xl text-white">📋</span>
              </div>
              <h3
                class="font-semibold text-slate-900 group-hover:text-blue-600"
              >
                Panduan Keamanan
              </h3>
              <p class="mt-1 text-sm text-slate-600">12 artikel</p>
            </div>
          </a>

          <!-- Category: Education -->
          <a
            href="/posts/categories/edukasi"
            class="group block rounded-xl bg-gradient-to-br from-green-50 to-emerald-100 p-6 transition-all duration-300 hover:from-green-100 hover:to-emerald-200"
          >
            <div class="text-center">
              <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-green-500"
              >
                <span class="text-xl text-white">🎓</span>
              </div>
              <h3
                class="font-semibold text-slate-900 group-hover:text-green-600"
              >
                Edukasi
              </h3>
              <p class="mt-1 text-sm text-slate-600">8 artikel</p>
            </div>
          </a>

          <!-- Category: Technical -->
          <a
            href="/posts/categories/teknis"
            class="group block rounded-xl bg-gradient-to-br from-purple-50 to-violet-100 p-6 transition-all duration-300 hover:from-purple-100 hover:to-violet-200"
          >
            <div class="text-center">
              <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500"
              >
                <span class="text-xl text-white">⚙️</span>
              </div>
              <h3
                class="font-semibold text-slate-900 group-hover:text-purple-600"
              >
                Teknis
              </h3>
              <p class="mt-1 text-sm text-slate-600">15 artikel</p>
            </div>
          </a>

          <!-- Category: News -->
          <a
            href="/posts/categories/berita"
            class="group block rounded-xl bg-gradient-to-br from-orange-50 to-amber-100 p-6 transition-all duration-300 hover:from-orange-100 hover:to-amber-200"
          >
            <div class="text-center">
              <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-orange-500"
              >
                <span class="text-xl text-white">📰</span>
              </div>
              <h3
                class="font-semibold text-slate-900 group-hover:text-orange-600"
              >
                Berita
              </h3>
              <p class="mt-1 text-sm text-slate-600">6 artikel</p>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section
      class="bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900 py-12 sm:py-16 lg:py-20"
    >
      <div class="container mx-auto px-4 text-center">
        <h2
          class="mb-6 text-3xl font-extrabold text-white sm:text-4xl lg:text-5xl"
        >
          Tetap Update dengan Informasi Terbaru
        </h2>
        <p class="mx-auto mb-8 max-w-2xl text-xl text-slate-300 sm:text-2xl">
          Dapatkan artikel dan panduan keamanan siber terbaru langsung di email
          Anda
        </p>
        <div
          class="mx-auto flex max-w-md flex-col items-center justify-center gap-4 sm:flex-row"
        >
          <div class="w-full">
            <input
              type="email"
              placeholder="Masukkan email Anda"
              class="w-full rounded-full border-0 bg-white/10 px-6 py-3 text-white placeholder-slate-300 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
            />
          </div>
          <button
            class="whitespace-nowrap rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-3 font-semibold text-white transition-all duration-300 hover:from-blue-600 hover:to-indigo-700"
          >
            Berlangganan
          </button>
        </div>
      </div>
    </section>
  </SEOLayout>
</template>

<script>
import SEOLayout from '@/Layouts/SEOLayout.vue'

export default {
  name: 'SEOPosts',
  components: {
    SEOLayout,
  },
  props: {
    posts: {
      type: Object,
      default: () => ({ data: [] }),
    },
    isFirstPage: {
      type: Boolean,
      default: true,
    },
  },
}
</script>
