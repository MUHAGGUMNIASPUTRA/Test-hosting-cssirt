<script setup>
import { onMounted, ref } from 'vue'

// Animation refs
const heroRef = ref(null)
const contactRef = ref(null)
const formRef = ref(null)

// Contact details data
const contactDetails = [
  {
    name: 'Alamat Kantor',
    value: 'Dinas Komunikasi dan Informatika\nJl. P. Mastumapel No. 1, Bojonegoro, Jawa Timur 62115',
    icon: 'pi-map-marker',
    color: 'text-indigo-600',
    bgColor: 'bg-indigo-100',
    type: 'address'
  },
  {
    name: 'Email Resmi',
    value: 'csirt@bojonegorokab.go.id',
    icon: 'pi-envelope',
    color: 'text-blue-600',
    bgColor: 'bg-blue-100',
    type: 'email'
  },
  {
    name: 'Telepon Kantor',
    value: '(0353) 881-234',
    icon: 'pi-phone',
    color: 'text-green-600',
    bgColor: 'bg-green-100',
    type: 'phone'
  },
  {
    name: 'Hotline Darurat',
    value: '0353-881234',
    subtitle: 'Laporan Insiden 24/7',
    icon: 'pi-exclamation-triangle',
    color: 'text-red-600',
    bgColor: 'bg-red-100',
    type: 'emergency'
  }
]

// Working hours data
const workingHours = [
  { day: 'Senin - Kamis', hours: '07:30 - 16:00 WIB' },
  { day: 'Jumat', hours: '07:30 - 16:30 WIB' },
  { day: 'Sabtu - Minggu', hours: 'Tutup' },
  { day: 'Hotline Darurat', hours: '24/7' }
]

// Form data
const form = ref({
  name: '',
  email: '',
  subject: '',
  message: '',
  type: 'general'
})

// Form submission
const submitForm = () => {
  // Handle form submission
  console.log('Form submitted:', form.value)
}

// Scroll animations
onMounted(() => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up')
      }
    })
  }, observerOptions)

  if (contactRef.value) observer.observe(contactRef.value)
  if (formRef.value) observer.observe(formRef.value)
})
</script>

<template>
  <AppLayout title="Hubungi Kami">
    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container mx-auto text-center">
          <div class="animate-fade-in-up">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
              Hubungi <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">CSIRT</span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-lg text-slate-300 sm:text-xl">
              Tim CSIRT Bojonegoro siap membantu Anda 24/7. Laporkan insiden keamanan siber
              atau konsultasikan kebutuhan keamanan digital Anda.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Emergency Banner -->
    <section class="bg-red-600 py-4">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center text-center">
          <svg class="h-5 w-5 text-white mr-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <p class="text-white font-medium">
            <span class="font-bold">DARURAT:</span> Untuk laporan insiden keamanan siber segera hubungi
            <a href="tel:0353881234" class="underline hover:no-underline ml-1">0353-881234</a>
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Information -->
    <section ref="contactRef" class="py-20 bg-white opacity-0 translate-y-10">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-600 mb-2">
            Informasi Kontak
          </h2>
          <h3 class="text-3xl font-extrabold text-slate-900 sm:text-4xl mb-4">
            Beragam Cara Menghubungi Kami
          </h3>
          <p class="mx-auto max-w-2xl text-xl text-slate-600">
            Pilih cara yang paling sesuai untuk kebutuhan Anda
          </p>
        </div>

        <!-- Contact Cards -->
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 mb-16">
          <div
            v-for="(contact, index) in contactDetails"
            :key="contact.name"
            class="group relative bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 hover:border-indigo-300"
            :style="{ animationDelay: `${index * 100}ms` }"
            :class="contact.type === 'emergency' ? 'ring-2 ring-red-200 bg-red-50' : ''"
          >
            <!-- Icon -->
            <div class="relative mb-6">
              <div
                class="w-16 h-16 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300"
                :class="contact.type === 'emergency' ? 'bg-gradient-to-br from-red-500 to-red-600' : `${contact.bgColor}`"
              >
                <i
                  :class="[
                    `pi ${contact.icon} text-2xl`,
                    contact.type === 'emergency' ? 'text-white' : contact.color
                  ]"
                ></i>
              </div>
              <div
                class="absolute -inset-2 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"
                :class="contact.type === 'emergency' ? 'bg-gradient-to-br from-red-500/20 to-red-600/20' : 'bg-gradient-to-br from-indigo-500/20 to-blue-600/20'"
              ></div>
            </div>

            <!-- Content -->
            <h4
              class="text-xl font-semibold mb-3 group-hover:text-indigo-600 transition-colors duration-300"
              :class="contact.type === 'emergency' ? 'text-red-700' : 'text-slate-900'"
            >
              {{ contact.name }}
            </h4>

            <p
              class="leading-relaxed whitespace-pre-line"
              :class="contact.type === 'emergency' ? 'text-red-600 font-medium' : 'text-slate-600'"
            >
              {{ contact.value }}
            </p>

            <p v-if="contact.subtitle" class="text-sm text-red-500 mt-2 font-medium">
              {{ contact.subtitle }}
            </p>

            <!-- Action Button -->
            <div class="mt-4">
              <a
                v-if="contact.type === 'email'"
                :href="`mailto:${contact.value}`"
                class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium group/link"
              >
                Kirim Email
                <svg class="ml-2 h-4 w-4 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
              <a
                v-else-if="contact.type === 'phone' || contact.type === 'emergency'"
                :href="`tel:${contact.value.replace(/[^0-9+]/g, '')}`"
                class="inline-flex items-center font-medium group/link"
                :class="contact.type === 'emergency' ? 'text-red-600 hover:text-red-700' : 'text-indigo-600 hover:text-indigo-700'"
              >
                {{ contact.type === 'emergency' ? 'Hubungi Sekarang' : 'Telepon' }}
                <svg class="ml-2 h-4 w-4 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </a>
            </div>

            <!-- Hover Effect -->
            <div
              class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"
              :class="contact.type === 'emergency' ? 'bg-gradient-to-br from-red-500/5 to-red-600/5' : 'bg-gradient-to-br from-indigo-500/5 to-blue-600/5'"
            ></div>
          </div>
        </div>

        <!-- Working Hours -->
        <div class="bg-slate-50 rounded-2xl p-8 mb-16">
          <h4 class="text-2xl font-bold text-slate-900 mb-6 text-center">Jam Operasional</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
              v-for="(schedule, index) in workingHours"
              :key="schedule.day"
              class="bg-white rounded-lg p-4 text-center shadow-sm"
              :class="schedule.day === 'Hotline Darurat' ? 'bg-red-50 border border-red-200' : ''"
            >
              <p
                class="font-semibold mb-2"
                :class="schedule.day === 'Hotline Darurat' ? 'text-red-700' : 'text-slate-900'"
              >
                {{ schedule.day }}
              </p>
              <p
                class="text-sm"
                :class="schedule.day === 'Hotline Darurat' ? 'text-red-600 font-medium' : 'text-slate-600'"
              >
                {{ schedule.hours }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form & Map -->
    <section ref="formRef" class="py-20 bg-slate-50 opacity-0 translate-y-10">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
          <!-- Contact Form -->
          <div>
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
              <h3 class="text-2xl font-bold text-slate-900 mb-6">Kirim Pesan</h3>

              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Message Type -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    Jenis Pesan
                  </label>
                  <select
                    v-model="form.type"
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                  >
                    <option value="general">Informasi Umum</option>
                    <option value="consultation">Konsultasi Keamanan</option>
                    <option value="report">Laporan Non-Darurat</option>
                    <option value="partnership">Kerjasama</option>
                  </select>
                </div>

                <!-- Name -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    Nama Lengkap *
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    placeholder="Masukkan nama lengkap Anda"
                  />
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    Email *
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    placeholder="nama@email.com"
                  />
                </div>

                <!-- Subject -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    Subjek *
                  </label>
                  <input
                    v-model="form.subject"
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    placeholder="Ringkasan singkat pesan Anda"
                  />
                </div>

                <!-- Message -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">
                    Pesan *
                  </label>
                  <textarea
                    v-model="form.message"
                    rows="6"
                    required
                    class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 resize-none"
                    placeholder="Tuliskan pesan atau pertanyaan Anda dengan detail..."
                  ></textarea>
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-indigo-700 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl"
                >
                  <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                  </svg>
                  Kirim Pesan
                </button>
              </form>

              <!-- Note -->
              <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                  <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                  <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">Catatan Penting:</p>
                    <p>Untuk laporan insiden keamanan yang mendesak, segera hubungi hotline darurat kami di <strong>0353-881234</strong> (24/7).</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Map & Location Info -->
          <div class="space-y-8">
            <!-- Map -->
            <div class="relative">
              <h3 class="text-2xl font-bold text-slate-900 mb-6">Lokasi Kantor</h3>
              <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200">
                <div class="h-80 w-full">
                  <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.411656819588!2d111.878965!3d-7.1501326!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7781a5963f7d6b%3A0x7c0d1b3e8e1f4b0!2sDinas%20Komunikasi%20dan%20Informatika%20Kabupaten%20Bojonegoro!5e0!3m2!1sen!2sid!4v1678886400000"
                    width="100%"
                    height="100%"
                    style="border: 0"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                  ></iframe>
                </div>
                <div class="p-6">
                  <div class="flex items-start">
                    <svg class="h-5 w-5 text-indigo-600 mt-1 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    <div>
                      <p class="font-semibold text-slate-900">Dinas Komunikasi dan Informatika</p>
                      <p class="text-slate-600">Kabupaten Bojonegoro</p>
                      <p class="text-slate-600">Jl. P. Mastumapel No. 1, Bojonegoro</p>
                      <p class="text-slate-600">Jawa Timur 62115</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Info -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
              <h4 class="font-bold text-slate-900 mb-4">Informasi Tambahan</h4>
              <div class="space-y-3 text-sm text-slate-600">
                <div class="flex items-center">
                  <svg class="h-4 w-4 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span>Parkir kendaraan tersedia</span>
                </div>
                <div class="flex items-center">
                  <svg class="h-4 w-4 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span>Akses kursi roda</span>
                </div>
                <div class="flex items-center">
                  <svg class="h-4 w-4 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <span>Layanan konsultasi gratis</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
