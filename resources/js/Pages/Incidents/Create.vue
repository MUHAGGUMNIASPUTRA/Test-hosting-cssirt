<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
  incidentTypes: Array,
});

// Animation refs
const heroRef = ref(null);
const formRef = ref(null);

const form = useForm({
  reporter_name: '',
  reporter_email: '',
  reporter_phone: '',
  incident_type_id: null,
  incident_at: null,
  description: '',
  attachment: null,
});

const uploader = ref(null);
const attachmentPreview = ref(null);

const handleFileSelect = (event) => {
  const file = event.files[0];
  form.attachment = file;

  if (file && file.type.startsWith('image/')) {
    attachmentPreview.value = URL.createObjectURL(file);
  } else {
    attachmentPreview.value = null;
  }
};

const clearAttachment = () => {
  if (uploader.value) {
    uploader.value.clear();
  }
  form.attachment = null;
  attachmentPreview.value = null;
};

const submit = () => {
  form.post(route('incident.store'), {
    onSuccess: () => {
      form.reset();
      clearAttachment();
    },
    onError: () => {}
  });
};

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

  if (formRef.value) observer.observe(formRef.value)
})
</script>

<template>
  <AppLayout title="Lapor Insiden Siber">
    <Toast />

    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container max-w-4xl text-center">
          <div class="animate-fade-in-up">
            <!-- Alert Icon -->
            <div class="w-20 h-20 bg-red-100/20 rounded-full flex items-center justify-center mx-auto mb-8 backdrop-blur-sm">
                <i-lucide-triangle-alert class="w-10 h-10 text-red-400 mb-1" />
            </div>

            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl mb-6 leading-tight">
              Lapor <span class="bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent">Insiden Siber</span>
            </h1>

            <p class="text-xl sm:text-2xl text-slate-300 mb-8 max-w-3xl mx-auto">
              Bantu kami melindungi ekosistem digital Indonesia dengan melaporkan insiden keamanan siber yang Anda alami atau ketahui
            </p>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-3 sm:gap-6 mt-8 sm:mt-12">
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-2 sm:p-6 border border-white/20">
                <div class="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-0 sm:mb-1 lg:mb-2">24/7</div>
                <div class="text-sm sm:text-base text-slate-300">Layanan Siaga</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-2 sm:p-6 border border-white/20">
                <div class="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-0 sm:mb-1 lg:mb-2">< 24 Jam</div>
                <div class="text-sm sm:text-base text-slate-300">Respons Awal</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-2 sm:p-6 border border-white/20">
                <div class="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-0 sm:mb-1 lg:mb-2">Rahasia</div>
                <div class="text-sm sm:text-base text-slate-300">Data Terlindungi</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section ref="formRef" class="py-8 sm:py-16 lg:py-24 bg-white opacity-0 translate-y-10">
      <div class="container">
        <div class="max-w-7xl mx-auto">

          <!-- Flash Messages -->
          <div v-if="$page.props.flash?.success" class="mb-8">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ $page.props.flash.success?.message }}</p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

            <!-- Information Sidebar -->
            <div class="lg:col-span-4">
              <div class="sticky top-8 space-y-8">

                <!-- Important Information -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-200">
                  <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-xl flex items-center justify-center">
                      <i class="pi pi-info-circle !text-lg text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Informasi Penting</h3>
                  </div>
                  <div class="space-y-4 text-slate-700">
                    <div class="flex items-start">
                      <i class="pi pi-lock text-blue-600 mt-1 mr-3"></i>
                      <div>
                        <p class="font-semibold mb-1">Kerahasiaan Terjamin</p>
                        <p>Identitas pelapor dan data insiden akan dijaga kerahasiaannya</p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <i class="pi pi-clock text-blue-600 mt-1 mr-3"></i>
                      <div>
                        <p class="font-semibold mb-1">Respons Cepat</p>
                        <p>Tim kami akan merespons laporan dalam waktu maksimal 24 jam</p>
                      </div>
                    </div>
                    <div class="flex items-start">
                      <i class="pi pi-shield text-blue-600 mt-1 mr-3"></i>
                      <div>
                        <p class="font-semibold mb-1">Penanganan Profesional</p>
                        <p>Ditangani oleh tim ahli keamanan siber bersertifikat</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Preparation Checklist -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                  <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-amber-100 border border-amber-200 rounded-xl flex items-center justify-center">
                      <i class="pi pi-clipboard !text-lg text-amber-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Sebelum Melapor</h3>
                  </div>
                  <div class="space-y-3">
                    <div class="flex items-start">
                      <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="text-slate-700">Siapkan kronologi kejadian secara rinci dan berurutan</p>
                    </div>
                    <div class="flex items-start">
                      <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="text-slate-700">Kumpulkan bukti seperti screenshot, log, atau email phishing</p>
                    </div>
                    <div class="flex items-start">
                      <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="text-slate-700">Pastikan informasi kontak Anda aktif dan dapat dihubungi</p>
                    </div>
                    <div class="flex items-start">
                      <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="text-slate-700">Catat dampak dan kerugian yang ditimbulkan (jika ada)</p>
                    </div>
                  </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-8 border border-red-200">
                  <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-red-100 border border-red-200 rounded-xl flex items-center justify-center">
                      <i class="pi pi-phone !text-lg text-red-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Kontak Darurat</h3>
                  </div>
                  <p class="text-slate-700 mb-4">Untuk insiden kritis yang memerlukan penanganan segera:</p>
                  <div class="space-y-2">
                    <p class="font-semibold text-slate-900"><i class="pi pi-phone !text-sm mr-2"></i> Hotline: 14000</p>
                    <p class="font-semibold text-slate-900"><i class="pi pi-envelope !text-sm mr-2"></i> Email: csirt@kominfo.go.id</p>
                    <p class="text-sm text-slate-600 !mt-4">*Layanan 24/7 untuk insiden prioritas tinggi</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Section -->
            <div class="lg:col-span-8">
              <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6">
                  <h2 class="text-3xl font-bold text-white mb-2">Formulir Pelaporan Insiden</h2>
                  <p class="text-blue-100">Mohon isi semua informasi dengan lengkap dan akurat</p>
                </div>

                <!-- Form Content -->
                <div class="p-8">
                  <form @submit.prevent="submit" class="space-y-8">

                    <!-- Reporter Information Section -->
                    <div>
                      <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                          <i class="pi pi-user !text-sm text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900">Informasi Pelapor</h3>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Reporter Name -->
                        <div>
                          <label for="reporter_name" class="block font-semibold text-slate-700 mb-2">
                            Nama Lengkap *
                          </label>
                          <InputText
                            id="reporter_name"
                            v-model="form.reporter_name"
                            class="w-full h-12 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.reporter_name }"
                            placeholder="Masukkan nama lengkap Anda"
                            required
                          />
                          <div v-if="form.errors.reporter_name" class="mt-2 text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.reporter_name }}
                          </div>
                        </div>

                        <!-- Reporter Email -->
                        <div>
                          <label for="reporter_email" class="block font-semibold text-slate-700 mb-2">
                            Alamat Email *
                          </label>
                          <InputText
                            id="reporter_email"
                            v-model="form.reporter_email"
                            type="email"
                            class="w-full h-12 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.reporter_email }"
                            placeholder="nama@email.com"
                            required
                          />
                          <div v-if="form.errors.reporter_email" class="mt-2 text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.reporter_email }}
                          </div>
                        </div>
                      </div>

                      <!-- Reporter Phone -->
                      <div class="mt-6">
                        <label for="reporter_phone" class="block font-semibold text-slate-700 mb-2">
                          Nomor Telepon <span class="text-slate-500 font-normal">(Opsional)</span>
                        </label>
                        <InputText
                          id="reporter_phone"
                          v-model="form.reporter_phone"
                          class="w-full h-12 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                          :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.reporter_phone }"
                          placeholder="08123456789"
                        />
                        <div v-if="form.errors.reporter_phone" class="mt-2 text-red-600 flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          {{ form.errors.reporter_phone }}
                        </div>
                      </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-slate-200"></div>

                    <!-- Incident Information Section -->
                    <div>
                      <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                          <!-- <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                          </svg> -->
                          <i class="pi pi-exclamation-triangle !text-sm text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900">Detail Insiden</h3>
                      </div>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Incident Type -->
                        <div>
                          <label for="incident_type_id" class="block font-semibold text-slate-700 mb-2">
                            Kategori Insiden *
                          </label>
                          <Select
                            id="incident_type_id"
                            v-model="form.incident_type_id"
                            :options="props.incidentTypes"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Pilih kategori insiden"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.incident_type_id }"
                            required
                          />
                          <div v-if="form.errors.incident_type_id" class="mt-2 text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.incident_type_id }}
                          </div>
                        </div>

                        <!-- Incident Time -->
                        <div>
                          <label for="incident_at" class="block font-semibold text-slate-700 mb-2">
                            Waktu Kejadian *
                          </label>
                          <DatePicker
                            id="incident_at"
                            v-model="form.incident_at"
                            showTime
                            hourFormat="24"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.incident_at }"
                            placeholder="Pilih tanggal dan waktu"
                            required
                          />
                          <div v-if="form.errors.incident_at" class="mt-2 text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ form.errors.incident_at }}
                          </div>
                        </div>
                      </div>

                      <!-- Description -->
                      <div class="mt-6">
                        <label for="description" class="block font-semibold text-slate-700 mb-2">
                          Deskripsi Detail Insiden *
                        </label>
                        <Textarea
                          id="description"
                          v-model="form.description"
                          rows="6"
                          class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                          :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.description }"
                          placeholder="Ceritakan secara detail kronologi insiden yang terjadi, termasuk:&#10;- Kapan insiden pertama kali terdeteksi&#10;- Apa yang terjadi sebelum insiden&#10;- Dampak yang dirasakan&#10;- Langkah yang sudah diambil&#10;- Informasi lain yang relevan"
                          required
                        />
                        <div v-if="form.errors.description" class="mt-2 text-red-600 flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          {{ form.errors.description }}
                        </div>
                      </div>

                      <!-- Attachment -->
                      <div class="mt-6">
                        <label for="attachment" class="block font-semibold text-slate-700 mb-2">
                          Lampiran Bukti <span class="text-slate-500 font-normal">(Opsional, maksimal 5MB)</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl hover:border-indigo-400 transition-colors duration-200">
                          <FileUpload
                            ref="uploader"
                            name="attachment"
                            @select="handleFileSelect"
                            :showUploadButton="false"
                            :showCancelButton="false"
                            :multiple="false"
                            accept=".jpg,.jpeg,.png,.pdf,.zip,.doc,.docx"
                            :maxFileSize="5000000"
                          >
                            <template #content="{ files }">
                              <div v-if="files[0]" class="p-6 bg-slate-50">
                                <div class="flex items-center justify-between">
                                  <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-white rounded-xl shadow-sm flex items-center justify-center overflow-hidden">
                                      <img v-if="attachmentPreview" :src="attachmentPreview" :alt="files[0].name" class="w-full h-full object-cover" />
                                      <svg v-else class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                      </svg>
                                    </div>
                                    <div>
                                      <p class="font-semibold text-slate-900">{{ files[0].name }}</p>
                                      <p class="text-slate-500">{{ (files[0].size / 1024 / 1024).toFixed(2) }} MB</p>
                                    </div>
                                  </div>
                                  <button
                                    type="button"
                                    @click="clearAttachment"
                                    class="w-8 h-8 bg-red-100 hover:bg-red-200 rounded-lg flex items-center justify-center transition-colors duration-200"
                                  >
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                  </button>
                                </div>
                              </div>
                            </template>
                            <template #empty>
                              <div class="p-8 text-center">
                                <svg class="w-12 h-12 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-slate-600 font-medium mb-2">Seret file ke sini atau klik untuk memilih</p>
                                <p class="text-slate-500">Format: JPG, PNG, PDF, ZIP, DOC (Maks. 5MB)</p>
                              </div>
                            </template>
                          </FileUpload>
                        </div>
                        <div v-if="form.errors.attachment" class="mt-2 text-red-600 flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          {{ form.errors.attachment }}
                        </div>
                      </div>
                    </div>

                    <!-- Privacy Notice -->
                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
                      <div class="flex items-start">
                        <svg class="hidden sm:flex w-5 h-5 text-slate-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div class="text-slate-600">
                          <p class="font-semibold text-slate-700 mb-2">Perlindungan Data & Privasi</p>
                          <p class="mb-2">Dengan mengirimkan laporan ini, Anda menyetujui bahwa:</p>
                          <ul class="list-disc list-inside space-y-1 pl-2">
                            <li>Data yang Anda berikan akan digunakan untuk keperluan penanganan insiden</li>
                            <li>Identitas pelapor akan dijaga kerahasiaannya sesuai kebijakan privasi</li>
                            <li>Tim CSIRT dapat menghubungi Anda untuk konfirmasi atau informasi tambahan</li>
                            <li>Laporan dapat dibagikan dengan pihak terkait untuk penanganan yang optimal</li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t border-slate-200">
                      <Button
                        type="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                      >
                        <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <svg v-else class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Mengirim Laporan...' : 'Kirim Laporan' }}
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
