<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const contactDetails = [
  {
    name: 'Alamat Kantor',
    value:
      'Dinas Komunikasi dan Informatika\nJl. P. Mastumapel No. 1, Bojonegoro, Jawa Timur',
    icon: 'pi pi-map-marker',
  },
  {
    name: 'Email Resmi',
    value: 'csirt@bojonegorokab.go.id',
    icon: 'pi pi-envelope',
  },
  {
    name: 'Telepon',
    value: '(0353) 123-456',
    icon: 'pi pi-phone',
  },
]
</script>

<template>
  <AppLayout title="Hubungi Kami">
    <div class="bg-white">
      <!-- Header -->
      <div class="bg-gray-50 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
          <div class="mx-auto max-w-2xl lg:mx-0">
            <h2
              class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
            >
              Hubungi Kami
            </h2>
            <p class="mt-6 text-lg leading-8 text-gray-600">
              Kami siap menerima laporan, pertanyaan, atau masukan Anda terkait
              keamanan siber di lingkungan Pemerintah Kabupaten Bojonegoro.
            </p>
          </div>
        </div>
      </div>

      <!-- Contact Info and Map -->
      <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 lg:px-8">
        <div class="grid grid-cols-1 gap-x-16 gap-y-12 lg:grid-cols-2">
          <!-- Contact Details -->
          <div>
            <h3 class="text-2xl font-bold text-gray-900">Informasi Kontak</h3>
            <dl class="mt-8 space-y-8 text-base text-gray-600">
              <div
                v-for="detail in contactDetails"
                :key="detail.name"
                class="flex gap-6"
              >
                <dt class="flex-none">
                  <span class="sr-only">{{ detail.name }}</span>
                  <i
                    :class="detail.icon"
                    class="h-6 w-6 text-xl text-gray-400"
                    aria-hidden="true"
                  ></i>
                </dt>
                <dd class="whitespace-pre-wrap">{{ detail.value }}</dd>
              </div>
            </dl>
          </div>

          <!-- Map -->
          <div>
            <h3 class="text-2xl font-bold text-gray-900">Peta Lokasi</h3>
            <div class="mt-8 h-[20rem] overflow-hidden rounded-lg shadow-lg">
              <!-- src="https://www.google.com/maps/embed?@-7.1499956,111.8774279,15.97z/data=!4m6!3m5!1s0x2e77810f51999bf5:0x33a31012c7b7bbfb!8m2!3d-7.1501326!4d111.8815399!16s%2Fg%2F11fqggd7lh?entry=ttu&g_ep=EgoyMDI1MDcyMS4wIKXMDSoASAFQAw%3D%3D" -->
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
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
