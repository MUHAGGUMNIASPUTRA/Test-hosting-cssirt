<!-- filepath: resources/js/Pages/SEODocuments.vue -->
<template>
  <SEOLayout title="Panduan & Dokumentasi Keamanan Siber - CSIRT Bojonegoro">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container mx-auto text-center">
          <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl">
            Panduan & <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">Dokumentasi</span>
          </h1>
          <p class="mx-auto mt-6 max-w-3xl text-xl sm:text-2xl text-slate-300">
            Akses panduan, kebijakan, dan dokumentasi keamanan siber untuk meningkatkan
            keamanan digital instansi pemerintah
          </p>
        </div>
      </div>
    </section>

    <!-- Documents List -->
    <section class="py-12 sm:py-16 lg:py-20 bg-slate-50">
      <div class="container mx-auto px-4">

        <!-- Statistics -->
        <div class="mb-12 sm:mb-16">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-4xl font-bold text-indigo-600">15+</div>
              <div class="text-slate-600 font-medium">Total Dokumen</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-green-600">5</div>
              <div class="text-slate-600 font-medium">Kategori</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold text-blue-600">100%</div>
              <div class="text-slate-600 font-medium">Format PDF</div>
            </div>
          </div>
        </div>

        <!-- Sample Documents -->
        <div class="mb-12 sm:mb-16">
          <div class="flex items-center mb-8">
            <span class="text-3xl mr-4">🚨</span>
            <h2 class="text-3xl font-bold text-slate-900">Panduan Respons Insiden</h2>
          </div>
          <div class="w-24 h-1 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full mb-8"></div>

          <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- RFC 2350 Document -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200">
              <div class="p-6 pb-4">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <h3 class="text-xl font-semibold text-slate-900 mb-2 hover:text-indigo-600 transition-colors duration-200">
                      RFC 2350 - CSIRT Bojonegoro
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        v1.0
                      </span>
                      <time datetime="2025-01-15">15 Januari 2025</time>
                    </div>
                  </div>
                  <div class="flex-shrink-0 ml-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                      <span class="text-red-600 text-xl">📄</span>
                    </div>
                  </div>
                </div>
                <p class="text-slate-600 line-clamp-3 leading-relaxed mb-4">
                  Dokumen deskripsi CSIRT Bojonegoro sesuai standar RFC 2350. Berisi informasi tentang mandat, layanan, dan cara kontak tim keamanan siber.
                </p>
                <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                  <span class="flex items-center">
                    <span class="mr-1">📄</span>
                    PDF Document
                  </span>
                  <span class="text-slate-400">2.5 MB</span>
                </div>
              </div>
              <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <div class="flex gap-3">
                  <a href="/documents/rfc2350-csirt-bjn-v1.pdf" target="_blank"
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                    <span class="mr-2">👁️</span>
                    Lihat
                  </a>
                  <a href="/documents/rfc2350-csirt-bjn-v1.pdf" download
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <span class="mr-2">⬇️</span>
                    Download
                  </a>
                </div>
              </div>
            </div>

            <!-- Incident Response Plan -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200">
              <div class="p-6 pb-4">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <h3 class="text-xl font-semibold text-slate-900 mb-2 hover:text-indigo-600 transition-colors duration-200">
                      Panduan Respons Insiden Keamanan Siber
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        v2.1
                      </span>
                      <time datetime="2025-01-10">10 Januari 2025</time>
                    </div>
                  </div>
                  <div class="flex-shrink-0 ml-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                      <span class="text-red-600 text-xl">🚨</span>
                    </div>
                  </div>
                </div>
                <p class="text-slate-600 line-clamp-3 leading-relaxed mb-4">
                  Prosedur standar untuk penanganan insiden keamanan siber mulai dari deteksi, analisis, containment, hingga recovery dan lessons learned.
                </p>
                <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                  <span class="flex items-center">
                    <span class="mr-1">📄</span>
                    PDF Document
                  </span>
                  <span class="text-slate-400">1.8 MB</span>
                </div>
              </div>
              <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <div class="flex gap-3">
                  <a href="/documents/panduan-respons-insiden-v2.1.pdf" target="_blank"
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                    <span class="mr-2">👁️</span>
                    Lihat
                  </a>
                  <a href="/documents/panduan-respons-insiden-v2.1.pdf" download
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <span class="mr-2">⬇️</span>
                    Download
                  </a>
                </div>
              </div>
            </div>

            <!-- Malware Analysis Guide -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200">
              <div class="p-6 pb-4">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <h3 class="text-xl font-semibold text-slate-900 mb-2 hover:text-indigo-600 transition-colors duration-200">
                      Panduan Analisis Malware
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        v1.5
                      </span>
                      <time datetime="2025-01-05">5 Januari 2025</time>
                    </div>
                  </div>
                  <div class="flex-shrink-0 ml-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                      <span class="text-red-600 text-xl">🦠</span>
                    </div>
                  </div>
                </div>
                <p class="text-slate-600 line-clamp-3 leading-relaxed mb-4">
                  Panduan teknis untuk analisis malware termasuk static analysis, dynamic analysis, dan reverse engineering untuk tim teknis CSIRT.
                </p>
                <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                  <span class="flex items-center">
                    <span class="mr-1">📄</span>
                    PDF Document
                  </span>
                  <span class="text-slate-400">3.2 MB</span>
                </div>
              </div>
              <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <div class="flex gap-3">
                  <a href="/documents/panduan-analisis-malware-v1.5.pdf" target="_blank"
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                    <span class="mr-2">👁️</span>
                    Lihat
                  </a>
                  <a href="/documents/panduan-analisis-malware-v1.5.pdf" download
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <span class="mr-2">⬇️</span>
                    Download
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Security Policy Category -->
        <div class="mb-12 sm:mb-16">
          <div class="flex items-center mb-8">
            <span class="text-3xl mr-4">📋</span>
            <h2 class="text-3xl font-bold text-slate-900">Kebijakan Keamanan</h2>
          </div>
          <div class="w-24 h-1 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mb-8"></div>

          <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Security Policy -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200">
              <div class="p-6 pb-4">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <h3 class="text-xl font-semibold text-slate-900 mb-2 hover:text-indigo-600 transition-colors duration-200">
                      Kebijakan Keamanan Informasi
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        v3.0
                      </span>
                      <time datetime="2024-12-20">20 Desember 2024</time>
                    </div>
                  </div>
                  <div class="flex-shrink-0 ml-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                      <span class="text-green-600 text-xl">📋</span>
                    </div>
                  </div>
                </div>
                <p class="text-slate-600 line-clamp-3 leading-relaxed mb-4">
                  Kebijakan keamanan informasi Pemerintah Kabupaten Bojonegoro yang mengatur standar keamanan untuk seluruh SKPD dan ASN.
                </p>
                <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                  <span class="flex items-center">
                    <span class="mr-1">📄</span>
                    PDF Document
                  </span>
                  <span class="text-slate-400">1.5 MB</span>
                </div>
              </div>
              <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <div class="flex gap-3">
                  <a href="/documents/kebijakan-keamanan-informasi-v3.0.pdf" target="_blank"
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                    <span class="mr-2">👁️</span>
                    Lihat
                  </a>
                  <a href="/documents/kebijakan-keamanan-informasi-v3.0.pdf" download
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <span class="mr-2">⬇️</span>
                    Download
                  </a>
                </div>
              </div>
            </div>

            <!-- Password Policy -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200">
              <div class="p-6 pb-4">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <h3 class="text-xl font-semibold text-slate-900 mb-2 hover:text-indigo-600 transition-colors duration-200">
                      Standar Keamanan Password
                    </h3>
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        v2.0
                      </span>
                      <time datetime="2024-12-15">15 Desember 2024</time>
                    </div>
                  </div>
                  <div class="flex-shrink-0 ml-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                      <span class="text-blue-600 text-xl">🔐</span>
                    </div>
                  </div>
                </div>
                <p class="text-slate-600 line-clamp-3 leading-relaxed mb-4">
                  Standar keamanan password yang harus diterapkan oleh seluruh ASN dan sistem informasi pemerintah daerah.
                </p>
                <div class="flex items-center justify-between text-sm text-slate-500 mb-4">
                  <span class="flex items-center">
                    <span class="mr-1">📄</span>
                    PDF Document
                  </span>
                  <span class="text-slate-400">850 KB</span>
                </div>
              </div>
              <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <div class="flex gap-3">
                  <a href="/documents/standar-keamanan-password-v2.0.pdf" target="_blank"
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                    <span class="mr-2">👁️</span>
                    Lihat
                  </a>
                  <a href="/documents/standar-keamanan-password-v2.0.pdf" download
                     class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <span class="mr-2">⬇️</span>
                    Download
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- Call to Action -->
    <section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-900">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-6">
          Butuh Bantuan Implementasi?
        </h2>
        <p class="mx-auto max-w-2xl text-xl sm:text-2xl text-slate-300 mb-8">
          Tim CSIRT siap membantu implementasi panduan keamanan di instansi Anda
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a
            href="/contact"
            class="inline-flex items-center px-8 py-4 text-lg font-semibold text-slate-900 bg-white rounded-lg hover:bg-slate-100 transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
          >
            <span class="mr-2">📧</span>
            Hubungi Kami
          </a>
          <a
            href="/incident"
            class="inline-flex items-center px-8 py-4 text-lg font-semibold text-white bg-red-600 border-2 border-red-600 rounded-lg hover:bg-red-700 hover:border-red-700 transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
          >
            <span class="mr-2">🚨</span>
            Lapor Insiden
          </a>
        </div>
      </div>
    </section>
  </SEOLayout>
</template>

<script>
export default {
  name: 'SEODocuments'
}
</script>
