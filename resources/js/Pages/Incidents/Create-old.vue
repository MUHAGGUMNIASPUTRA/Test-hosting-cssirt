<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

const props = defineProps({
  incidentTypes: Array,
})

const page = usePage()
const contact = page.props.contact

// Animation refs
const heroRef = ref(null)
const formRef = ref(null)

const form = useForm({
  reporter_name: '',
  reporter_email: '',
  reporter_phone: '',
  incident_type_id: null,
  incident_at: null,
  description: '',
  attachment: null,
})

const uploader = ref(null)
const attachmentPreview = ref(null)

const triggerFileInput = () => {
  const input = uploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
}

const handleFileSelect = (event) => {
  const file = event.files[0]
  form.attachment = file

  if (file && file.type.startsWith('image/')) {
    attachmentPreview.value = URL.createObjectURL(file)
  } else {
    attachmentPreview.value = null
  }
}

const clearAttachment = () => {
  if (uploader.value) {
    uploader.value.clear()
  }
  form.attachment = null
  attachmentPreview.value = null
}

const submit = () => {
  form.post(route('incident.store'), {
    onSuccess: () => {
      form.reset()
      clearAttachment()
    },
    onError: () => {},
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

  if (formRef.value) observer.observe(formRef.value)
})
</script>

<template>
  <AppLayout title="Lapor Insiden Siber">
    <Toast />

    <!-- Hero Section -->
    <section
      ref="heroRef"
      class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
    >
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container max-w-4xl text-center">
          <div class="animate-fade-in-up">
            <!-- Alert Icon -->
            <div
              class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-full bg-red-100/20 backdrop-blur-sm"
            >
              <i-lucide-triangle-alert class="mb-1 h-10 w-10 text-red-400" />
            </div>

            <h1
              class="mb-6 text-5xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl lg:text-7xl"
            >
              Lapor
              <span
                class="bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent"
                >Insiden Siber</span
              >
            </h1>

            <p
              class="mx-auto mb-8 max-w-3xl text-xl text-slate-300 sm:text-2xl"
            >
              Bantu kami melindungi ekosistem digital Indonesia dengan
              melaporkan insiden keamanan siber yang Anda alami atau ketahui
            </p>

            <!-- Quick Stats -->
            <div class="mt-8 grid grid-cols-3 gap-3 sm:mt-12 sm:gap-6">
              <div
                class="rounded-2xl border border-white/20 bg-white/10 p-2 backdrop-blur-sm sm:p-6"
              >
                <div
                  class="mb-0 text-xl font-bold text-white sm:mb-1 sm:text-2xl lg:mb-2 lg:text-4xl"
                >
                  24/7
                </div>
                <div class="text-sm text-slate-300 sm:text-base">
                  Layanan Siaga
                </div>
              </div>
              <div
                class="rounded-2xl border border-white/20 bg-white/10 p-2 backdrop-blur-sm sm:p-6"
              >
                <div
                  class="mb-0 text-xl font-bold text-white sm:mb-1 sm:text-2xl lg:mb-2 lg:text-4xl"
                >
                  < 24 Jam
                </div>
                <div class="text-sm text-slate-300 sm:text-base">
                  Respons Awal
                </div>
              </div>
              <div
                class="rounded-2xl border border-white/20 bg-white/10 p-2 backdrop-blur-sm sm:p-6"
              >
                <div
                  class="mb-0 text-xl font-bold text-white sm:mb-1 sm:text-2xl lg:mb-2 lg:text-4xl"
                >
                  Rahasia
                </div>
                <div class="text-sm text-slate-300 sm:text-base">
                  Data Terlindungi
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section
      ref="formRef"
      class="translate-y-10 bg-white py-8 opacity-0 sm:py-16 lg:py-24"
    >
      <div class="container">
        <div class="mx-auto max-w-7xl">
          <!-- Flash Messages -->
          <div v-if="$page.props.flash?.success" class="mb-8">
            <div class="rounded-xl border border-green-200 bg-green-50 p-4">
              <div class="flex items-center">
                <svg
                  class="mr-3 h-5 w-5 text-green-600"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
                <p class="font-medium text-green-800">
                  {{ $page.props.flash.success?.message }}
                </p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-12">
            <!-- Information Sidebar -->
            <div class="lg:col-span-4">
              <div class="sticky top-8 space-y-8">
                <!-- Important Information -->
                <div
                  class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-8"
                >
                  <div class="mb-6 flex items-center gap-4">
                    <div
                      class="flex h-12 w-12 items-center justify-center rounded-xl border border-blue-200 bg-blue-100"
                    >
                      <i class="pi pi-info-circle !text-lg text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">
                      Informasi Penting
                    </h3>
                  </div>
                  <div class="space-y-4 text-slate-700">
                    <div class="flex items-start">
                      <i class="pi pi-lock mr-3 mt-1 text-blue-600"></i>
                      <div>
                        <p class="mb-1 font-semibold">Kerahasiaan Terjamin</p>
                        <p>
                          Identitas pelapor dan data insiden akan dijaga
                          kerahasiaannya
                        </p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <i class="pi pi-clock mr-3 mt-1 text-blue-600"></i>
                      <div>
                        <p class="mb-1 font-semibold">Respons Cepat</p>
                        <p>
                          Tim kami akan merespons laporan dalam waktu maksimal
                          24 jam
                        </p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <i class="pi pi-shield mr-3 mt-1 text-blue-600"></i>
                      <div>
                        <p class="mb-1 font-semibold">Penanganan Profesional</p>
                        <p>
                          Ditangani oleh tim ahli keamanan siber bersertifikat
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Preparation Checklist -->
                <div
                  class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm"
                >
                  <div class="mb-6 flex items-center gap-4">
                    <div
                      class="flex h-12 w-12 items-center justify-center rounded-xl border border-amber-200 bg-amber-100"
                    >
                      <i class="pi pi-clipboard !text-lg text-amber-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">
                      Sebelum Melapor
                    </h3>
                  </div>
                  <div class="space-y-3">
                    <div class="flex items-start">
                      <div
                        class="mr-3 mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-100"
                      >
                        <svg
                          class="h-3 w-3 text-green-600"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </div>
                      <p class="text-slate-700">
                        Siapkan kronologi kejadian secara rinci dan berurutan
                      </p>
                    </div>
                    <div class="flex items-start">
                      <div
                        class="mr-3 mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-100"
                      >
                        <svg
                          class="h-3 w-3 text-green-600"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </div>
                      <p class="text-slate-700">
                        Kumpulkan bukti seperti screenshot, log, atau email
                        phishing
                      </p>
                    </div>
                    <div class="flex items-start">
                      <div
                        class="mr-3 mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-100"
                      >
                        <svg
                          class="h-3 w-3 text-green-600"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </div>
                      <p class="text-slate-700">
                        Pastikan informasi kontak Anda aktif dan dapat dihubungi
                      </p>
                    </div>
                    <div class="flex items-start">
                      <div
                        class="mr-3 mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-100"
                      >
                        <svg
                          class="h-3 w-3 text-green-600"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </div>
                      <p class="text-slate-700">
                        Catat dampak dan kerugian yang ditimbulkan (jika ada)
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Emergency Contact -->
                <div
                  class="rounded-2xl border border-red-200 bg-gradient-to-br from-red-50 to-pink-50 p-8"
                >
                  <div class="mb-6 flex items-center gap-4">
                    <div
                      class="flex h-12 w-12 items-center justify-center rounded-xl border border-red-200 bg-red-100"
                    >
                      <i class="pi pi-phone !text-lg text-red-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">
                      Kontak Darurat
                    </h3>
                  </div>
                  <p class="mb-4 text-slate-700">
                    Untuk insiden kritikal yang memerlukan penanganan segera:
                  </p>
                  <div class="space-y-2">
                    <p class="font-semibold text-slate-900">
                      <i class="pi pi-phone mr-2 !text-sm"></i> Hotline:
                      {{ contact.phone }}
                    </p>
                    <p class="font-semibold text-slate-900">
                      <i class="pi pi-envelope mr-2 !text-sm"></i> Email:
                      {{ contact.email }}
                    </p>
                    <p class="!mt-4 text-sm text-slate-600">
                      *Layanan 24/7 untuk insiden prioritas tinggi
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Section -->
            <div class="lg:col-span-8">
              <div
                class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl"
              >
                <!-- Form Header -->
                <div
                  class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6"
                >
                  <h2 class="mb-2 text-3xl font-bold text-white">
                    Formulir Pelaporan Insiden
                  </h2>
                  <p class="text-blue-100">
                    Mohon isi semua informasi dengan lengkap dan akurat
                  </p>
                </div>

                <!-- Form Content -->
                <div class="p-8">
                  <form @submit.prevent="submit" class="space-y-8">
                    <!-- Reporter Information Section -->
                    <div>
                      <div class="mb-6 flex items-center">
                        <div
                          class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100"
                        >
                          <i class="pi pi-user !text-sm text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900">
                          Informasi Pelapor
                        </h3>
                      </div>

                      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Reporter Name -->
                        <div>
                          <label
                            for="reporter_name"
                            class="mb-2 block font-semibold text-slate-700"
                          >
                            Nama Lengkap <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            id="reporter_name"
                            v-model="form.reporter_name"
                            class="h-12 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                            :class="{
                              'border-red-300 focus:border-red-500 focus:ring-red-500':
                                form.errors.reporter_name,
                            }"
                            placeholder="Masukkan nama lengkap Anda"
                            required
                          />
                          <div
                            v-if="form.errors.reporter_name"
                            class="mt-2 flex items-center text-red-600"
                          >
                            <svg
                              class="mr-1 h-4 w-4"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                            </svg>
                            {{ form.errors.reporter_name }}
                          </div>
                        </div>

                        <!-- Reporter Email -->
                        <div>
                          <label
                            for="reporter_email"
                            class="mb-2 block font-semibold text-slate-700"
                          >
                            Alamat Email <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            id="reporter_email"
                            v-model="form.reporter_email"
                            type="email"
                            class="h-12 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                            :class="{
                              'border-red-300 focus:border-red-500 focus:ring-red-500':
                                form.errors.reporter_email,
                            }"
                            placeholder="nama@email.com"
                            required
                          />
                          <div
                            v-if="form.errors.reporter_email"
                            class="mt-2 flex items-center text-red-600"
                          >
                            <svg
                              class="mr-1 h-4 w-4"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                            </svg>
                            {{ form.errors.reporter_email }}
                          </div>
                        </div>
                      </div>

                      <!-- Reporter Phone -->
                      <div class="mt-6">
                        <label
                          for="reporter_phone"
                          class="mb-2 block font-semibold text-slate-700"
                        >
                          Nomor Telepon
                          <span class="font-normal text-slate-500"
                            >(Opsional)</span
                          >
                        </label>
                        <InputText
                          id="reporter_phone"
                          v-model="form.reporter_phone"
                          class="h-12 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                          :class="{
                            'border-red-300 focus:border-red-500 focus:ring-red-500':
                              form.errors.reporter_phone,
                          }"
                          placeholder="08123456789"
                        />
                        <div
                          v-if="form.errors.reporter_phone"
                          class="mt-2 flex items-center text-red-600"
                        >
                          <svg
                            class="mr-1 h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                          </svg>
                          {{ form.errors.reporter_phone }}
                        </div>
                      </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-slate-200"></div>

                    <!-- Incident Information Section -->
                    <div>
                      <div class="mb-6 flex items-center">
                        <div
                          class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg bg-red-100"
                        >
                          <i
                            class="pi pi-exclamation-triangle !text-sm text-red-600"
                          ></i>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900">
                          Detail Insiden
                        </h3>
                      </div>

                      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Incident Type -->
                        <div>
                          <label
                            for="incident_type_id"
                            class="mb-2 block font-semibold text-slate-700"
                          >
                            Kategori Insiden <span class="text-red-500">*</span>
                          </label>
                          <Select
                            id="incident_type_id"
                            v-model="form.incident_type_id"
                            :options="props.incidentTypes"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Pilih kategori insiden"
                            class="w-full"
                            :class="{
                              'p-invalid': form.errors.incident_type_id,
                            }"
                            required
                          />
                          <div
                            v-if="form.errors.incident_type_id"
                            class="mt-2 flex items-center text-red-600"
                          >
                            <svg
                              class="mr-1 h-4 w-4"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                            </svg>
                            {{ form.errors.incident_type_id }}
                          </div>
                        </div>

                        <!-- Incident Time -->
                        <div>
                          <label
                            for="incident_at"
                            class="mb-2 block font-semibold text-slate-700"
                          >
                            Waktu Kejadian <span class="text-red-500">*</span>
                          </label>
                          <DatePicker
                            id="incident_at"
                            v-model="form.incident_at"
                            showTime
                            hourFormat="24"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.incident_at }"
                            placeholder="Pilih tanggal dan waktu"
                            showIcon
                            iconDisplay="input"
                            required
                          />
                          <div
                            v-if="form.errors.incident_at"
                            class="mt-2 flex items-center text-red-600"
                          >
                            <svg
                              class="mr-1 h-4 w-4"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                            </svg>
                            {{ form.errors.incident_at }}
                          </div>
                        </div>
                      </div>

                      <!-- Description -->
                      <div class="mt-6">
                        <label
                          for="description"
                          class="mb-2 block font-semibold text-slate-700"
                        >
                          Deskripsi Detail Insiden
                          <span class="text-red-500">*</span>
                        </label>
                        <Textarea
                          id="description"
                          v-model="form.description"
                          rows="7"
                          class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                          :class="{
                            'border-red-300 focus:border-red-500 focus:ring-red-500':
                              form.errors.description,
                          }"
                          placeholder="Ceritakan secara detail kronologi insiden yang terjadi, termasuk:&#10;- Kapan insiden pertama kali terdeteksi&#10;- Apa yang terjadi sebelum insiden&#10;- Dampak yang dirasakan&#10;- Langkah yang sudah diambil&#10;- Informasi lain yang relevan"
                          required
                        />
                        <div
                          v-if="form.errors.description"
                          class="mt-2 flex items-center text-red-600"
                        >
                          <svg
                            class="mr-1 h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                          </svg>
                          {{ form.errors.description }}
                        </div>
                      </div>

                      <!-- Attachment -->
                      <div class="mt-6">
                        <label
                          for="attachment"
                          class="mb-2 block font-semibold text-slate-700"
                        >
                          Lampiran Bukti
                          <span class="font-normal text-slate-500"
                            >(Opsional, maksimal 2MB)</span
                          >
                        </label>
                        <FileUpload
                          ref="uploader"
                          name="attachment"
                          @select="handleFileSelect"
                          :auto="true"
                          :customUpload="true"
                          :showUploadButton="false"
                          :showCancelButton="false"
                          :multiple="false"
                          accept=".jpg,.jpeg,.png,.pdf,.zip,.doc,.docx"
                          :maxFileSize="2097152"
                        >
                          <template #content="{ files }">
                            <div
                              v-if="files[0]"
                              class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                            >
                              <div
                                class="flex items-start justify-between gap-4"
                              >
                                <div class="flex items-start">
                                  <div class="mr-3">
                                    <img
                                      v-if="attachmentPreview"
                                      :src="attachmentPreview"
                                      :alt="files[0].name"
                                      width="64"
                                      height="64"
                                      class="rounded-lg"
                                    />
                                    <IconFileText
                                      v-else
                                      class="mt-1 h-8 w-8 text-slate-400"
                                    />
                                  </div>
                                  <div>
                                    <p
                                      class="break-all font-semibold text-slate-900"
                                    >
                                      {{ files[0].name }}
                                    </p>
                                    <p class="text-slate-500">
                                      {{
                                        (files[0].size / 1024 / 1024).toFixed(2)
                                      }}
                                      MB
                                    </p>
                                  </div>
                                </div>
                                <button
                                  type="button"
                                  @click="clearAttachment"
                                  class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                >
                                  <IconX size="16" />
                                </button>
                              </div>
                            </div>
                          </template>

                          <template #empty>
                            <div
                              class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 px-4 py-6 transition-colors hover:border-blue-600"
                              @click="triggerFileInput"
                            >
                              <IconCloudUp
                                class="mb-2 text-gray-400"
                                size="30"
                              />
                              <p class="mb-2 font-medium text-slate-600">
                                Drag & drop atau klik untuk memilih
                              </p>
                              <p class="text-sm text-slate-500 sm:text-base">
                                Format: JPG, PNG, PDF, ZIP, DOC (Maks. 2MB)
                              </p>
                            </div>
                          </template>
                        </FileUpload>
                        <div
                          v-if="form.errors.attachment"
                          class="mt-2 flex items-center text-red-600"
                        >
                          <svg
                            class="mr-1 h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                          </svg>
                          {{ form.errors.attachment }}
                        </div>
                      </div>
                    </div>

                    <!-- Privacy Notice -->
                    <div
                      class="rounded-xl border border-slate-200 bg-slate-50 p-6"
                    >
                      <div class="flex items-start">
                        <svg
                          class="mr-3 mt-0.5 hidden h-5 w-5 flex-shrink-0 text-slate-500 sm:flex"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                          />
                        </svg>
                        <div class="text-slate-600">
                          <p class="mb-2 font-semibold text-slate-700">
                            Perlindungan Data & Privasi
                          </p>
                          <p class="mb-2">
                            Dengan mengirimkan laporan ini, Anda menyetujui
                            bahwa:
                          </p>
                          <ul class="list-inside list-disc space-y-1 pl-2">
                            <li>
                              Data yang Anda berikan akan digunakan untuk
                              keperluan penanganan insiden
                            </li>
                            <li>
                              Identitas pelapor akan dijaga kerahasiaannya
                              sesuai kebijakan privasi
                            </li>
                            <li>
                              Tim CSIRT dapat menghubungi Anda untuk konfirmasi
                              atau informasi tambahan
                            </li>
                            <li>
                              Laporan dapat dibagikan dengan pihak terkait untuk
                              penanganan yang optimal
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <!-- Submit Button -->
                    <div
                      class="flex justify-end border-t border-slate-200 pt-6"
                    >
                      <Button
                        type="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                      >
                        <svg
                          v-if="!form.processing"
                          class="mr-2 h-5 w-5"
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
                        <svg
                          v-else
                          class="mr-2 h-5 w-5 animate-spin"
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
                        {{
                          form.processing
                            ? 'Mengirim Laporan...'
                            : 'Kirim Laporan'
                        }}
                      </Button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
