<script setup>
import { onMounted, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

const page = usePage()
const contact = page.props.contact

// Animation refs
const heroRef = ref(null)
const contactRef = ref(null)
const formRef = ref(null)

// Contact details data
const contactDetails = [
  {
    name: 'Alamat Kantor',
    value:
      'Dinas Komunikasi dan Informatika\nJl. P. Mas Tumapel No. 1, Bojonegoro,\nJawa Timur 62115',
    icon: 'pi-map-marker',
    color: 'text-indigo-600',
    bgColor: 'bg-indigo-100',
    type: 'address',
  },
  {
    name: 'Email Resmi',
    value: contact.email,
    icon: 'pi-envelope',
    color: 'text-blue-600',
    bgColor: 'bg-blue-100',
    type: 'email',
  },
  {
    name: 'Telepon Kantor',
    value: '(0353) 881-234',
    icon: 'pi-phone',
    color: 'text-green-600',
    bgColor: 'bg-green-100',
    type: 'phone',
  },
  {
    name: 'Hotline Darurat',
    value: contact.phone,
    subtitle: 'Laporan Insiden 24/7',
    icon: 'pi-exclamation-triangle',
    color: 'text-red-600',
    bgColor: 'bg-red-100',
    type: 'emergency',
  },
]

// Working hours data
const workingHours = [
  { day: 'Senin - Kamis', hours: '07:30 - 16:00 WIB' },
  { day: 'Jumat', hours: '07:30 - 16:00 WIB' },
  { day: 'Sabtu - Minggu', hours: 'Tutup' },
  { day: 'Hotline Darurat', hours: '24/7' },
]

// Form data using Inertia useForm
const form = useForm({
  name: '',
  email: '',
  subject: '',
  message: '',
  type: 'general',
})

// Form submission
const submitForm = () => {
  form.post(route('contact.store'), {
    onSuccess: (page) => {
      // Reset form
      form.reset()
    },
    onError: (errors) => {
      // Handle error messages
    },
  })
}

// Scroll animations
onMounted(() => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px',
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up')
      }
    })
  }, observerOptions)

  if (heroRef.value) observer.observe(heroRef.value)
  if (contactRef.value) observer.observe(contactRef.value)
  if (formRef.value) observer.observe(formRef.value)
})
</script>

<template>
  <AppLayout title="Hubungi Kami">
    <!-- Hero Section -->
    <section
      ref="heroRef"
      class="relative translate-y-10 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 opacity-0"
    >
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container text-center">
          <div class="animate-fade-in-up">
            <h1
              class="text-5xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl md:text-7xl"
            >
              <span class="">Hubungi</span>
              <span
                class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text pb-2 text-transparent"
              >
                Kami
              </span>
            </h1>
            <p
              class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl"
            >
              Tim CSIRT Bojonegoro siap membantu Anda 24/7. Laporkan insiden
              keamanan siber atau konsultasikan kebutuhan keamanan digital Anda.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Emergency Banner -->
    <section class="bg-red-600 py-4">
      <div class="container">
        <div class="flex items-center justify-center text-center">
          <svg
            class="mr-3 hidden h-5 w-5 animate-pulse text-white sm:flex"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
          <p class="text-xl font-medium text-white">
            <span class="font-bold">DARURAT:</span> Untuk laporan insiden
            keamanan siber segera hubungi
            <a href="tel:0353881826" class="ml-1 underline hover:no-underline"
              >0353-881826</a
            >
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Information -->
    <section
      ref="contactRef"
      class="translate-y-10 bg-white py-8 opacity-0 sm:py-16 lg:py-20"
    >
      <div class="container">
        <div class="mb-8 text-center sm:mb-16">
          <h2
            class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
          >
            Informasi Kontak
          </h2>
          <h3
            class="mb-4 text-3xl font-extrabold text-slate-900 sm:text-4xl lg:text-5xl"
          >
            Beragam Cara Menghubungi Kami
          </h3>
          <p class="mx-auto max-w-2xl text-xl text-slate-600 sm:text-2xl">
            Pilih cara yang paling sesuai untuk kebutuhan Anda
          </p>
        </div>

        <!-- Contact Cards -->
        <div
          class="mb-8 grid grid-cols-1 gap-8 sm:mb-16 sm:grid-cols-2 lg:grid-cols-4"
        >
          <div
            v-for="(contact, index) in contactDetails"
            :key="contact.name"
            class="group relative transform rounded-2xl border bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
            :style="{ animationDelay: `${index * 100}ms` }"
            :class="
              contact.type === 'emergency'
                ? 'border-red-200 bg-red-50 hover:border-red-300'
                : 'border-slate-200 hover:border-indigo-300'
            "
          >
            <div class="mb-6 flex items-center">
              <!-- Icon -->
              <div class="relative mr-6">
                <div
                  class="flex h-12 w-12 items-center justify-center rounded-xl transition-shadow duration-300 group-hover:shadow-xl"
                  :class="
                    contact.type === 'emergency'
                      ? 'bg-gradient-to-br from-red-500 to-red-600'
                      : `${contact.bgColor}`
                  "
                >
                  <i
                    :class="[
                      `pi ${contact.icon} !text-xl`,
                      contact.type === 'emergency'
                        ? 'text-white'
                        : contact.color,
                    ]"
                  ></i>
                </div>
                <div
                  class="absolute -inset-2 -z-10 rounded-xl opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                  :class="
                    contact.type === 'emergency'
                      ? 'bg-gradient-to-br from-red-500/20 to-red-600/20'
                      : 'bg-gradient-to-br from-indigo-500/20 to-blue-600/20'
                  "
                ></div>
              </div>

              <!-- Content -->
              <h4
                class="text-2xl font-semibold transition-colors duration-300 group-hover:text-indigo-600"
                :class="
                  contact.type === 'emergency'
                    ? 'text-red-700 group-hover:text-red-600'
                    : 'text-slate-900'
                "
              >
                {{ contact.name }}
                <p
                  v-if="contact.type === 'emergency'"
                  class="text-sm font-normal text-red-500"
                >
                  {{ contact.subtitle }}
                </p>
              </h4>
            </div>

            <p
              class="whitespace-pre-line leading-relaxed"
              :class="
                contact.type === 'emergency'
                  ? 'font-medium text-red-600'
                  : 'text-slate-600'
              "
            >
              {{ contact.value }}
            </p>

            <!-- Action Button -->
            <div v-if="contact.type != 'address'" class="mt-4">
              <a
                v-if="contact.type === 'email'"
                :href="`mailto:${contact.value}`"
                class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
              >
                Kirim Email
                <svg
                  class="ml-2 h-4 w-4 transition-transform duration-200 group-hover/link:translate-x-1"
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
              <a
                v-else-if="
                  contact.type === 'phone' || contact.type === 'emergency'
                "
                :href="`tel:${contact.value.replace(/[^0-9+]/g, '')}`"
                class="group/link inline-flex items-center font-medium"
                :class="
                  contact.type === 'emergency'
                    ? 'text-red-600 hover:text-red-700'
                    : 'text-indigo-600 hover:text-indigo-700'
                "
              >
                {{
                  contact.type === 'emergency' ? 'Hubungi Sekarang' : 'Telepon'
                }}
                <svg
                  class="ml-2 h-4 w-4 transition-transform duration-200 group-hover/link:translate-x-1"
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

            <!-- Hover Effect -->
            <div
              class="pointer-events-none absolute inset-0 rounded-2xl opacity-0 transition-opacity duration-300 group-hover:opacity-100"
              :class="
                contact.type === 'emergency'
                  ? 'bg-gradient-to-br from-red-500/5 to-red-600/5'
                  : 'bg-gradient-to-br from-indigo-500/5 to-blue-600/5'
              "
            ></div>
          </div>
        </div>

        <!-- Working Hours -->
        <div class="rounded-2xl border p-10">
          <h4
            class="mb-10 text-center text-3xl font-extrabold tracking-tight text-slate-900"
          >
            Jam Operasional
          </h4>
          <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div
              v-for="(schedule, index) in workingHours"
              :key="schedule.day"
              class="relative rounded-2xl border bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md"
              :class="
                schedule.day === 'Hotline Darurat'
                  ? 'border-red-300 bg-red-50/60'
                  : 'border-slate-200'
              "
            >
              <div class="absolute right-4 top-4">
                <span
                  v-if="schedule.day === 'Hotline Darurat'"
                  class="inline-flex items-center gap-1 rounded-xl bg-red-100 px-3 py-1 text-xs font-semibold text-red-700"
                >
                  <i class="pi pi-phone !text-sm"></i> Darurat
                </span>
              </div>
              <p
                class="mb-1 text-lg font-semibold"
                :class="
                  schedule.day === 'Hotline Darurat'
                    ? 'text-red-700'
                    : 'text-slate-800'
                "
              >
                {{ schedule.day }}
              </p>
              <p
                class="text-base"
                :class="
                  schedule.day === 'Hotline Darurat'
                    ? 'text-red-600'
                    : 'text-slate-600'
                "
              >
                {{ schedule.hours }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form & Map -->
    <section
      ref="formRef"
      class="relative translate-y-10 overflow-hidden border border-t bg-slate-50 py-8 opacity-0 sm:py-16 lg:py-20"
    >
      <div
        class="absolute inset-0 -z-10 bg-gradient-to-br from-slate-100 via-white to-blue-200 opacity-40"
      ></div>

      <div class="container">
        <div class="grid grid-cols-1 gap-8 sm:gap-12 lg:grid-cols-2">
          <!-- Contact Form -->
          <div>
            <div
              class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm"
            >
              <h3 class="mb-6 text-3xl font-bold text-slate-900">
                Kirim Pesan
              </h3>

              <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Message Type -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Jenis Pesan
                  </label>
                  <select
                    v-model="form.type"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 transition-colors duration-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    :class="{ 'border-red-300': form.errors.type }"
                  >
                    <option value="general">Informasi Umum</option>
                    <option value="consultation">Konsultasi Keamanan</option>
                    <option value="report">Laporan Non-Darurat</option>
                    <option value="partnership">Kerjasama</option>
                  </select>
                  <div v-if="form.errors.type" class="mt-1 text-red-600">
                    {{ form.errors.type }}
                  </div>
                </div>

                <!-- Name -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Nama Lengkap *
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 transition-colors duration-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    :class="{ 'border-red-300': form.errors.name }"
                    placeholder="Masukkan nama lengkap Anda"
                  />
                  <div v-if="form.errors.name" class="mt-1 text-red-600">
                    {{ form.errors.name }}
                  </div>
                </div>

                <!-- Email -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Email *
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 transition-colors duration-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    :class="{ 'border-red-300': form.errors.email }"
                    placeholder="nama@email.com"
                  />
                  <div v-if="form.errors.email" class="mt-1 text-red-600">
                    {{ form.errors.email }}
                  </div>
                </div>

                <!-- Subject -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Subjek *
                  </label>
                  <input
                    v-model="form.subject"
                    type="text"
                    required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 transition-colors duration-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    :class="{ 'border-red-300': form.errors.subject }"
                    placeholder="Ringkasan singkat pesan Anda"
                  />
                  <div v-if="form.errors.subject" class="mt-1 text-red-600">
                    {{ form.errors.subject }}
                  </div>
                </div>

                <!-- Message -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Pesan *
                  </label>
                  <textarea
                    v-model="form.message"
                    rows="6"
                    required
                    class="w-full resize-none rounded-lg border border-slate-300 px-4 py-3 transition-colors duration-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                    :class="{ 'border-red-300': form.errors.message }"
                    placeholder="Tuliskan pesan atau pertanyaan Anda dengan detail..."
                  ></textarea>
                  <div v-if="form.errors.message" class="mt-1 text-red-600">
                    {{ form.errors.message }}
                  </div>
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="w-full transform rounded-lg bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-3 font-semibold text-white shadow-lg transition-all duration-200 hover:scale-105 hover:from-indigo-700 hover:to-blue-700 hover:shadow-xl disabled:transform-none disabled:cursor-not-allowed disabled:opacity-50"
                >
                  <svg
                    v-if="form.processing"
                    class="mr-2 inline-block h-5 w-5 animate-spin"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"
                    ></circle>
                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                  </svg>
                  <svg
                    v-else
                    class="mr-2 inline-block h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                    />
                  </svg>
                  {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
                </button>
              </form>

              <!-- Note -->
              <div
                class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4"
              >
                <div class="flex items-start">
                  <i class="pi pi-info-circle mr-3 mt-1 text-blue-600"></i>
                  <div class="text-blue-700">
                    <p class="mb-1 font-medium">Catatan Penting:</p>
                    <p>
                      Untuk laporan insiden keamanan yang mendesak, segera
                      hubungi hotline darurat kami di
                      <strong>0353-881826</strong> (24/7).
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Map & Location Info -->
          <div class="space-y-8">
            <!-- Map -->
            <div class="relative">
              <h3 class="mb-6 text-3xl font-bold text-slate-900">
                Lokasi Kantor
              </h3>
              <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
              >
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
                    <i-lucide-map-pinned
                      fill="yellow"
                      class="mr-3 mt-1 h-5 w-5 flex-shrink-0 text-indigo-600"
                    />
                    <div>
                      <p class="font-semibold text-slate-900">
                        Dinas Komunikasi dan Informatika
                      </p>
                      <p class="text-slate-600">
                        Pemerintah Kabupaten Bojonegoro
                      </p>
                      <p class="text-slate-600">
                        Jl. P. Mas Tumapel No. 1, Bojonegoro
                      </p>
                      <p class="text-slate-600">Jawa Timur 62115</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Info -->
            <div
              class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
              <h4 class="mb-4 font-bold text-slate-900">Informasi Tambahan</h4>
              <div class="space-y-3 text-slate-600">
                <div class="flex items-center">
                  <svg
                    class="mr-3 h-4 w-4 text-green-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span>Parkir kendaraan tersedia</span>
                </div>
                <div class="flex items-center">
                  <svg
                    class="mr-3 h-4 w-4 text-green-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span>Akses kursi roda</span>
                </div>
                <div class="flex items-center">
                  <svg
                    class="mr-3 h-4 w-4 text-green-600"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"
                    />
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
