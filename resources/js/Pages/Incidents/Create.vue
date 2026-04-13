<script setup>
// filepath: resources/js/Pages/Incidents/Create.vue

import { router, useForm, usePage } from '@inertiajs/vue3'
import { ref, onMounted, computed, watch } from 'vue'
import { useParticles } from '@/Composables/useParticles'
import {
  IconFile,
  IconFileTypeDoc,
  IconFileTypeDocx,
  IconFileTypeJpg,
  IconFileTypePdf,
  IconFileTypePng,
  IconFileTypeZip,
} from '@tabler/icons-vue'

const page = usePage()

const props = defineProps({
  incidentTypes: Array,
})

// Animation refs
const heroRef = ref(null)
const formRef = ref(null)
const { minimalParticlesOptions } = useParticles()

// Step management
const activeStep = ref(0)
const steps = [
  {
    key: 'choice',
    title: 'Pilih Layanan',
    description: 'Buat tiket baru atau cek tiket yang ada',
    icon: 'pi pi-directions',
  },
  {
    key: 'form',
    title: 'Isi Formulir',
    description: 'Lengkapi informasi tiket Anda',
    icon: 'pi pi-file-edit',
  },
  {
    key: 'confirmation',
    title: 'Konfirmasi',
    description: 'Tinjau dan kirim tiket Anda',
    icon: 'pi pi-check-circle',
  },
]

// Service choice
const selectedService = ref(null) // Start with null, not 'create'

// Captcha
const captcha = ref({
  question: '',
  answer: '',
  userAnswer: '',
})

// Forms
const form = useForm({
  reporter_name: '',
  reporter_email: '',
  reporter_phone: '',
  incident_type_id: null,
  incident_at: null,
  description: '',
  priority: 'Sedang',
  attachment_type: 'file',
  attachment: null,
  attachment_links: '',
  captcha_answer: '',
  captcha_expected: '',
})

const searchForm = useForm({
  case_id: '',
  email: '',
  captcha_answer: '',
  captcha_expected: '',
})

// Selected incident type info
const selectedType = computed(() => {
  if (!form.incident_type_id) return null
  return props.incidentTypes.find((t) => t.id === form.incident_type_id) || null
})

// Attachment mode toggle
const attachmentMode = ref('file') // 'file' | 'link'

const setAttachmentMode = (mode) => {
  attachmentMode.value = mode
  form.attachment_type = mode
  form.attachment = null
  form.attachment_links = ''
  if (mode === 'file') {
    attachmentPreview.value = null
    if (uploader.value) uploader.value.clear()
  }
}

const uploader = ref(null)
const attachmentPreview = ref(null)

// Search captcha reuses the same generator
const searchCaptcha = ref({ question: '', answer: '', userAnswer: '' })
const generateSearchCaptcha = () => {
  generateCaptcha()
  // Mirror the generated captcha into the search form UI/state
  searchCaptcha.value = { ...captcha.value }
  searchForm.captcha_expected = captcha.value.answer.toString()
}

watch(
  () => searchCaptcha.value.userAnswer,
  (newValue) => {
    searchForm.captcha_answer = newValue
  },
)

const searchCaptchaRequired = computed(() => {
  const f = page.props.flash || {}
  // If an incident is found, do not require captcha on this view
  if (f.incident_found) return false
  const e = searchForm.errors || {}
  return Boolean(
    f.captcha_required || e.captcha || e.captcha_answer || e.captcha_expected,
  )
})

watch(searchCaptchaRequired, (required) => {
  if (required && !searchCaptcha.value.question) {
    generateSearchCaptcha()
  }
})

// Priority options
const priorityOptions = [
  {
    label: 'Rendah',
    value: 'Rendah',
    color: 'success',
    description: 'Tidak mengganggu operasional',
  },
  {
    label: 'Sedang',
    value: 'Sedang',
    color: 'info',
    description: 'Sedikit mengganggu operasional',
  },
  {
    label: 'Tinggi',
    value: 'Tinggi',
    color: 'warn',
    description: 'Sangat mengganggu operasional',
  },
  {
    label: 'Kritikal',
    value: 'Kritikal',
    color: 'danger',
    description: 'Menghentikan operasional',
  },
]

// Generate simple captcha
const generateCaptcha = () => {
  const operations = [
    { type: 'add', symbol: '+' },
    { type: 'subtract', symbol: '-' },
    { type: 'multiply', symbol: '×' },
  ]

  const operation = operations[Math.floor(Math.random() * operations.length)]
  let num1, num2, answer

  switch (operation.type) {
    case 'add':
      num1 = Math.floor(Math.random() * 20) + 1
      num2 = Math.floor(Math.random() * 20) + 1
      answer = num1 + num2
      break
    case 'subtract':
      num1 = Math.floor(Math.random() * 20) + 10
      num2 = Math.floor(Math.random() * 10) + 1
      answer = num1 - num2
      break
    case 'multiply':
      num1 = Math.floor(Math.random() * 10) + 1
      num2 = Math.floor(Math.random() * 10) + 1
      answer = num1 * num2
      break
  }

  captcha.value = {
    question: `${num1} ${operation.symbol} ${num2} = ?`,
    answer: answer.toString(),
    userAnswer: '',
  }

  form.captcha_expected = answer.toString()
}

// Watch for captcha user input
watch(
  () => captcha.value.userAnswer,
  (newValue) => {
    form.captcha_answer = newValue
  },
)

// Methods
const selectService = (service) => {
  selectedService.value = service
  if (service === 'create') {
    activeStep.value = 1
  } else {
    activeStep.value = 1
  }
}

const nextStep = () => {
  if (activeStep.value < steps.length - 1) {
    activeStep.value++
    // Generate captcha when moving to confirmation step
    if (activeStep.value === 2 && selectedService.value === 'create') {
      generateCaptcha()
    }
  }
}

const prevStep = () => {
  if (activeStep.value > 0) {
    activeStep.value--
  }
}

const goToChoice = () => {
  activeStep.value = 0
  selectedService.value = null
  form.reset()
  searchForm.reset()
  searchForm.clearErrors()
  clearAttachment()
  captcha.value = { question: '', answer: '', userAnswer: '' }

  // Clear any flash messages
  if (page.props.flash?.success) {
    delete page.props.flash.success
  }
  if (page.props.flash?.incident_found) {
    delete page.props.flash.incident_found
  }
}

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
  if (uploader.value) uploader.value.clear()
  form.attachment = null
  attachmentPreview.value = null
}

const submitForm = () => {
  if (selectedService.value === 'create') {
    form.post(route('incident.store'), {
      onSuccess: () => {
        form.reset()
        clearAttachment()
        attachmentMode.value = 'file'
        activeStep.value = 0
        selectedService.value = null
        captcha.value = { question: '', answer: '', userAnswer: '' }
      },
      onError: () => {
        if (activeStep.value === 2) {
          generateCaptcha()
        }
      },
    })
  } else {
    if (searchCaptchaRequired.value) {
      searchForm.captcha_answer = (
        searchCaptcha.value.userAnswer ?? ''
      ).toString()
      if (searchCaptcha.value.answer) {
        searchForm.captcha_expected = searchCaptcha.value.answer.toString()
      }
    }
    searchForm.post(route('incident.search'), {
      onSuccess: () => {
        searchCaptcha.value = { question: '', answer: '', userAnswer: '' }
        searchForm.captcha_answer = ''
        searchForm.captcha_expected = ''
        searchForm.clearErrors()
        if (page.props.flash && 'captcha_required' in page.props.flash) {
          delete page.props.flash.captcha_required
        }
      },
      onError: () => {
        if (searchCaptchaRequired.value && !searchCaptcha.value.question)
          generateSearchCaptcha()
      },
    })
  }
}

const maxDate = new Date()

// Format functions
const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  return new Date(dateTime).toLocaleString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getStatusSeverity = (status) => {
  const severityMap = {
    Baru: 'info',
    Diverifikasi: 'primary',
    'Dalam Penyelidikan': 'warn',
    Selesai: 'success',
    Ditutup: 'secondary',
  }
  return severityMap[status] || 'info'
}

const getPrioritySeverity = (priority) => {
  const severityMap = {
    Rendah: 'success',
    Sedang: 'info',
    Tinggi: 'warn',
    Kritikal: 'danger',
  }
  return severityMap[priority] || 'info'
}

// Helper functions for the search results
const getFileIcon = (filename) => {
  if (!filename) return 'pi-file'

  const extension = filename.split('.').pop().toLowerCase()
  const iconMap = {
    pdf: [IconFileTypePdf, 'bg-red-100', 'text-red-600'],
    doc: [IconFileTypeDoc, 'bg-blue-100', 'text-blue-600'],
    docx: [IconFileTypeDocx, 'bg-blue-100', 'text-blue-600'],
    zip: [IconFileTypeZip, 'bg-yellow-100', 'text-yellow-600'],
    jpg: [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    jpeg: [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    png: [IconFileTypePng, 'bg-green-100', 'text-green-600'],
  }

  return iconMap[extension] || [IconFile, 'bg-slate-100', 'text-slate-600']
}

const downloadAttachment = (attachment) => {
  const link = document.createElement('a')
  const url =
    typeof attachment === 'string' ? attachment : attachment?.download_url
  if (!url) return
  link.href = url
  link.target = '_blank'
  link.rel = 'noopener'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// Initialize
onMounted(() => {
  // Scroll animations
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

  // If server requires captcha for search (after repeated failures), generate it immediately
  if (searchCaptchaRequired.value && !searchCaptcha.value.question)
    generateSearchCaptcha()
})
</script>

<template>
  <AppLayout title="Lapor Insiden Siber">
    <!-- Hero Section -->
    <section
      ref="heroRef"
      class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
    >
      <div class="absolute inset-0 z-0">
        <vue-particles
          id="tsparticles"
          :options="minimalParticlesOptions"
          class="h-full w-full"
        />
      </div>

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
              <!-- <i-lucide-triangle-alert class="w-10 h-10 text-red-400 mb-1" /> -->
              <IconAlertHexagon size="36" class="text-red-400" />
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
              Buat tiket baru untuk melaporkan insiden keamanan siber atau lacak
              status tiket yang sudah ada
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
                  &lt; 24 Jam
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
        <div class="mx-auto max-w-6xl">
          <!-- Flash Messages -->
          <div v-if="page.props.flash?.success" class="mb-8">
            <div class="rounded-xl border border-green-200 bg-green-50 p-6">
              <div class="flex items-start">
                <IconCheck class="mr-3 hidden text-green-600 sm:flex" />
                <div>
                  <h3 class="text-lg font-bold text-green-800">
                    {{ page.props.flash.success?.title }}
                  </h3>
                  <p class="mt-1 text-green-700">
                    {{ page.props.flash.success?.message }}
                  </p>
                  <div v-if="page.props.flash.success?.case_id" class="mt-3">
                    <button
                      @click="goToChoice"
                      class="rounded-lg bg-green-600 px-4 py-2 font-medium text-white transition-colors hover:bg-green-700"
                    >
                      Buat Tiket Lain
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stepper -->
          <div class="mb-8">
            <div class="flex items-center justify-center">
              <div class="flex items-center space-x-4 lg:space-x-8">
                <div
                  v-for="(step, index) in steps"
                  :key="step.key"
                  class="flex items-center"
                >
                  <!-- Step Circle -->
                  <div class="flex flex-col items-center">
                    <div
                      class="flex h-12 w-12 items-center justify-center rounded-full font-semibold transition-all duration-300"
                      :class="
                        activeStep >= index
                          ? 'bg-blue-600 text-white shadow-lg'
                          : 'bg-slate-200 text-slate-500'
                      "
                    >
                      <i :class="step.icon" class="!text-lg" />
                    </div>
                    <div class="mt-2 text-center">
                      <p
                        class="text-sm font-medium sm:text-base"
                        :class="
                          activeStep >= index
                            ? 'text-blue-600'
                            : 'text-slate-500'
                        "
                      >
                        {{ step.title }}
                      </p>
                      <p class="hidden text-sm text-slate-400 sm:block">
                        {{ step.description }}
                      </p>
                    </div>
                  </div>

                  <!-- Connector Line -->
                  <div
                    v-if="index < steps.length - 1"
                    class="mx-2 h-1 w-16 transition-all duration-300 lg:mx-4 lg:w-24"
                    :class="activeStep > index ? 'bg-blue-600' : 'bg-slate-200'"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
          >
            <!-- Step 1: Service Choice -->
            <div v-if="activeStep === 0" class="p-6 sm:p-8">
              <div class="mb-6 text-center sm:mb-8">
                <h2 class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl">
                  Pilih Layanan
                </h2>
                <p class="text-slate-600">
                  Apa yang ingin Anda lakukan hari ini?
                </p>
              </div>

              <div
                class="mx-auto grid max-w-4xl grid-cols-1 gap-6 md:grid-cols-2"
              >
                <!-- Create New Ticket -->
                <div
                  @click="selectService('create')"
                  class="group relative cursor-pointer"
                >
                  <div
                    class="rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 transition-all duration-300 hover:border-blue-400 hover:shadow-lg group-hover:-translate-y-1 sm:p-8"
                  >
                    <div class="text-center">
                      <div
                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 transition-colors group-hover:bg-blue-200 sm:h-16 sm:w-16"
                      >
                        <IconPlus
                          size="18"
                          stroke-width="1.5"
                          class="text-blue-600"
                        />
                      </div>
                      <h3 class="mb-1 text-xl font-bold text-slate-900">
                        Buat Tiket Baru
                      </h3>
                      <p class="text-slate-600">
                        Laporkan insiden keamanan siber yang baru terjadi
                      </p>
                    </div>
                    <div
                      class="absolute inset-0 rounded-xl bg-blue-600 opacity-0 transition-opacity group-hover:opacity-5"
                    ></div>
                  </div>
                </div>

                <!-- Check Existing Ticket -->
                <div
                  @click="selectService('search')"
                  class="group relative cursor-pointer"
                >
                  <div
                    class="rounded-xl border-2 border-green-200 bg-gradient-to-br from-green-50 to-emerald-50 p-6 transition-all duration-300 hover:border-green-400 hover:shadow-lg group-hover:-translate-y-1 sm:p-8"
                  >
                    <div class="text-center">
                      <div
                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 transition-colors group-hover:bg-green-200 sm:h-16 sm:w-16"
                      >
                        <IconSearch
                          size="18"
                          stroke-width="1.5"
                          class="text-green-600"
                        />
                      </div>
                      <h3 class="mb-1 text-xl font-bold text-slate-900">
                        Cek Status Tiket
                      </h3>
                      <p class="text-slate-600">
                        Lacak perkembangan tiket yang sudah dibuat
                      </p>
                    </div>
                    <div
                      class="absolute inset-0 rounded-xl bg-green-600 opacity-0 transition-opacity group-hover:opacity-5"
                    ></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 2: Form or Search -->
            <div v-else-if="activeStep === 1">
              <!-- Create Ticket Form -->
              <div v-if="selectedService === 'create'" class="p-0 py-6 sm:p-8">
                <div class="mx-auto max-w-4xl">
                  <div class="mb-6 text-center sm:mb-8">
                    <h2
                      class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl"
                    >
                      Buat Tiket Baru
                    </h2>
                    <p class="text-slate-600">
                      Isi formulir di bawah dengan lengkap dan akurat
                    </p>
                  </div>

                  <hr class="mx-6 !mt-0 sm:hidden" />

                  <form
                    @submit.prevent="nextStep"
                    class="space-y-6 sm:space-y-8"
                  >
                    <!-- Reporter Information -->
                    <div
                      class="rounded-xl border-slate-200 p-6 sm:border sm:bg-slate-50"
                    >
                      <h3
                        class="mb-4 flex items-center text-lg font-semibold text-slate-900"
                      >
                        <IconUserExclamation
                          size="18"
                          class="mr-2 text-blue-600"
                        />
                        Informasi Pelapor
                      </h3>

                      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                          <label class="mb-2 block font-medium text-slate-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            v-model="form.reporter_name"
                            placeholder="Masukkan nama lengkap"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.reporter_name }"
                            required
                          />
                          <small
                            v-if="form.errors.reporter_name"
                            class="p-error"
                            >{{ form.errors.reporter_name }}</small
                          >
                        </div>

                        <div>
                          <label class="mb-2 block font-medium text-slate-700">
                            Email <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            v-model="form.reporter_email"
                            type="email"
                            placeholder="nama@email.com"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.reporter_email }"
                            required
                          />
                          <small
                            v-if="form.errors.reporter_email"
                            class="p-error"
                            >{{ form.errors.reporter_email }}</small
                          >
                        </div>

                        <div class="md:col-span-2">
                          <label class="mb-2 block font-medium text-slate-700">
                            Nomor Telepon
                            <span class="text-slate-500">(Opsional)</span>
                          </label>
                          <InputText
                            v-model="form.reporter_phone"
                            placeholder="08123456789"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.reporter_phone }"
                          />
                          <small
                            v-if="form.errors.reporter_phone"
                            class="p-error"
                            >{{ form.errors.reporter_phone }}</small
                          >
                        </div>
                      </div>
                    </div>

                    <hr class="mx-6 !mt-0 sm:hidden" />

                    <!-- Incident Information -->
                    <div
                      class="rounded-xl border-slate-200 px-6 py-0 sm:border sm:bg-slate-50 sm:p-6"
                    >
                      <h3
                        class="mb-4 flex items-center text-lg font-semibold text-slate-900"
                      >
                        <IconUrgent size="18" class="mr-2 text-red-600" />
                        Detail Tiket
                      </h3>

                      <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                          <div>
                            <label
                              class="mb-2 block font-medium text-slate-700"
                            >
                              Kategori <span class="text-red-500">*</span>
                            </label>
                            <Select
                              v-model="form.incident_type_id"
                              :options="incidentTypes"
                              optionLabel="name"
                              optionValue="id"
                              placeholder="Pilih kategori"
                              class="w-full"
                              :class="{
                                'p-invalid': form.errors.incident_type_id,
                              }"
                              required
                            />
                            <small
                              v-if="form.errors.incident_type_id"
                              class="p-error"
                              >{{ form.errors.incident_type_id }}</small
                            >
                          </div>

                          <div>
                            <label
                              class="mb-2 block font-medium text-slate-700"
                            >
                              Waktu Kejadian <span class="text-red-500">*</span>
                            </label>
                            <DatePicker
                              v-model="form.incident_at"
                              showTime
                              showIcon
                              hourFormat="24"
                              iconDisplay="input"
                              :maxDate="maxDate"
                              placeholder="Pilih tanggal & waktu"
                              class="w-full"
                              :class="{ 'p-invalid': form.errors.incident_at }"
                              required
                            />
                            <small
                              v-if="form.errors.incident_at"
                              class="p-error"
                              >{{ form.errors.incident_at }}</small
                            >
                          </div>
                        </div>

                        <div>
                          <label class="mb-2 block font-medium text-slate-700">
                            Prioritas Tiket <span class="text-red-500">*</span>
                          </label>
                          <div
                            class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4"
                          >
                            <div
                              v-for="priority in priorityOptions"
                              :key="priority.value"
                              @click="form.priority = priority.value"
                              class="cursor-pointer"
                            >
                              <div
                                class="rounded-lg border p-4 text-center transition-all duration-200 hover:shadow-md"
                                :class="
                                  form.priority === priority.value
                                    ? 'border-blue-500 bg-blue-50'
                                    : 'border-slate-200 hover:border-slate-300'
                                "
                              >
                                <Tag
                                  :value="priority.label"
                                  :severity="priority.color"
                                  size="small"
                                />
                              </div>
                            </div>
                          </div>
                          <small v-if="form.errors.priority" class="p-error">{{
                            form.errors.priority
                          }}</small>
                        </div>

                        <div>
                          <label class="mb-2 block font-medium text-slate-700">
                            Deskripsi <span class="text-red-500">*</span>
                          </label>
                          <Textarea
                            v-model="form.description"
                            rows="5"
                            placeholder="Jelaskan tiket Anda secara detail..."
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.description }"
                            required
                          />
                          <small
                            v-if="form.errors.description"
                            class="p-error"
                            >{{ form.errors.description }}</small
                          >
                        </div>

                        <!-- Selected Type Info Panel -->
                        <Transition
                          enter-active-class="transition-all duration-300 ease-out"
                          enter-from-class="opacity-0 -translate-y-2"
                          enter-to-class="opacity-100 translate-y-0"
                          leave-active-class="transition-all duration-200 ease-in"
                          leave-from-class="opacity-100 translate-y-0"
                          leave-to-class="opacity-0 -translate-y-2"
                        >
                          <div
                            v-if="
                              selectedType &&
                              (selectedType.description || selectedType.guide)
                            "
                          >
                            <div
                              class="overflow-hidden rounded-xl border border-indigo-200 bg-indigo-50"
                            >
                              <!-- Header -->
                              <div
                                class="flex items-center gap-3 border-b border-indigo-200 bg-indigo-100 px-5 py-3"
                              >
                                <div
                                  class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-600"
                                >
                                  <i
                                    class="pi pi-info-circle !text-sm text-white"
                                  ></i>
                                </div>
                                <div>
                                  <p class="font-semibold text-indigo-900">
                                    {{ selectedType.name }}
                                  </p>
                                  <p
                                    v-if="selectedType.description"
                                    class="text-sm text-indigo-700"
                                  >
                                    {{ selectedType.description }}
                                  </p>
                                </div>
                              </div>
                              <!-- Guide Content -->
                              <div v-if="selectedType.guide" class="p-5">
                                <p
                                  class="mb-3 text-xs font-semibold uppercase tracking-wider text-indigo-600"
                                >
                                  Panduan Pelaporan
                                </p>
                                <div
                                  class="prose prose-sm max-w-none text-slate-700 [&>h3]:mb-2 [&>h3]:text-base [&>h3]:font-semibold [&>h3]:text-indigo-900 [&>li]:mb-1 [&>ol]:mb-2 [&>ol]:pl-5 [&>p]:mb-2 [&>ul]:mb-2 [&>ul]:pl-5"
                                  v-html="selectedType.guide"
                                />
                              </div>
                            </div>
                          </div>
                        </Transition>

                        <!-- Attachment -->
                        <div>
                          <label class="mb-2 block font-medium text-slate-700">
                            Lampiran Bukti
                            <span class="text-slate-500">(Opsional)</span>
                          </label>

                          <!-- Mode Toggle -->
                          <div
                            class="mb-4 flex w-fit overflow-hidden rounded-xl border border-slate-300"
                          >
                            <button
                              type="button"
                              @click="setAttachmentMode('file')"
                              class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors"
                              :class="
                                attachmentMode === 'file'
                                  ? 'bg-indigo-600 text-white'
                                  : 'bg-white text-slate-600 hover:bg-slate-50'
                              "
                            >
                              <i class="pi pi-upload text-xs"></i>
                              Upload Dokumen
                            </button>
                            <button
                              type="button"
                              @click="setAttachmentMode('link')"
                              class="flex items-center gap-2 border-l border-slate-300 px-4 py-2 text-sm font-medium transition-colors"
                              :class="
                                attachmentMode === 'link'
                                  ? 'bg-indigo-600 text-white'
                                  : 'bg-white text-slate-600 hover:bg-slate-50'
                              "
                            >
                              <i class="pi pi-link text-xs"></i>
                              Kirim Link
                            </button>
                          </div>

                          <!-- File Upload -->
                          <div v-if="attachmentMode === 'file'">
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
                                  class="rounded-lg border border-slate-200 bg-slate-50 p-4"
                                >
                                  <div
                                    class="flex items-start justify-between gap-4"
                                  >
                                    <div class="flex items-start">
                                      <div class="mt-1">
                                        <component
                                          :is="getFileIcon(files[0].name)[0]"
                                          :class="getFileIcon(files[0].name)[2]"
                                          class="mr-3"
                                          size="18"
                                        />
                                      </div>
                                      <div>
                                        <p
                                          class="break-all font-medium text-slate-900"
                                        >
                                          {{ files[0].name }}
                                        </p>
                                        <p class="text-sm text-slate-500">
                                          {{
                                            (
                                              files[0].size /
                                              1024 /
                                              1024
                                            ).toFixed(2)
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
                                  class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 px-4 py-6 transition-colors hover:border-blue-400"
                                  @click="triggerFileInput"
                                >
                                  <IconFileSearch class="mb-2 text-gray-400" />
                                  <p class="font-medium text-slate-600">
                                    Klik atau drag file ke sini
                                  </p>
                                  <p class="text-sm text-slate-400">
                                    JPG, PNG, PDF, ZIP, DOC (Maks. 2MB)
                                  </p>
                                </div>
                              </template>
                            </FileUpload>
                          </div>

                          <!-- Link Input -->
                          <div v-else class="space-y-2">
                            <Textarea
                              v-model="form.attachment_links"
                              rows="3"
                              class="w-full rounded-xl"
                              placeholder="Masukkan URL bukti, pisahkan dengan koma jika lebih dari satu.&#10;Contoh: https://drive.google.com/file/xxx, https://example.com/screenshot.png"
                            />
                            <p class="text-sm text-slate-500">
                              <i class="pi pi-info-circle mr-1"></i>
                              Untuk beberapa link, pisahkan dengan koma (,)
                            </p>
                          </div>

                          <small
                            v-if="
                              form.errors.attachment ||
                              form.errors.attachment_links
                            "
                            class="p-error"
                          >
                            {{
                              form.errors.attachment ||
                              form.errors.attachment_links
                            }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <hr class="mx-6 sm:hidden" />

                    <!-- Navigation -->
                    <div class="flex justify-between px-6 sm:px-0">
                      <button
                        type="button"
                        @click="goToChoice"
                        class="py-2 text-slate-600 transition-colors hover:text-slate-800"
                      >
                        ← Kembali
                      </button>
                      <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                      >
                        Selanjutnya →
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Search Ticket -->
              <div
                v-else-if="selectedService === 'search'"
                class="p-0 py-6 sm:p-8"
              >
                <div class="mx-auto max-w-2xl">
                  <div class="mb-6 text-center sm:mb-8">
                    <h2
                      class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl"
                    >
                      Cek Status Tiket
                    </h2>
                    <p class="text-slate-600">
                      Masukkan ID tiket dan email untuk melihat status
                    </p>
                  </div>

                  <hr
                    v-if="!page.props.flash?.incident_found"
                    class="mx-6 !mt-0 sm:hidden"
                  />

                  <!-- Search Form -->
                  <form @submit.prevent="submitForm" class="space-y-6">
                    <div
                      v-if="!page.props.flash?.incident_found"
                      class="rounded-xl border-slate-200 p-6 sm:border sm:bg-slate-50"
                    >
                      <div class="space-y-4">
                        <div>
                          <label class="mb-2 block font-medium text-slate-700">
                            ID Tiket <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            v-model="searchForm.case_id"
                            placeholder="CSIRT-XXXX-XX-XXX"
                            class="w-full font-mono"
                            :class="{ 'p-invalid': searchForm.errors.case_id }"
                            required
                          />
                          <small
                            v-if="searchForm.errors.case_id"
                            class="p-error"
                            >{{ searchForm.errors.case_id }}</small
                          >
                        </div>

                        <div>
                          <label class="mb-2 block font-medium text-slate-700">
                            Email Pelapor <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            v-model="searchForm.email"
                            type="email"
                            placeholder="email@domain.com"
                            class="w-full"
                            :class="{ 'p-invalid': searchForm.errors.email }"
                            required
                          />
                          <small
                            v-if="searchForm.errors.email"
                            class="p-error"
                            >{{ searchForm.errors.email }}</small
                          >
                        </div>

                        <!-- Conditional Captcha after repeated failures -->
                        <div v-if="searchCaptchaRequired" class="pt-2">
                          <label class="mb-2 block font-medium text-slate-700">
                            Verifikasi Keamanan
                          </label>
                          <div class="flex items-center space-x-4">
                            <div
                              class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-mono"
                            >
                              {{ searchCaptcha.question }}
                            </div>
                            <div>
                              <InputNumber
                                v-model="searchCaptcha.userAnswer"
                                placeholder="Jawaban"
                                class="w-auto"
                                :inputClass="{
                                  'p-invalid':
                                    searchForm.errors.captcha ||
                                    searchForm.errors.captcha_answer,
                                }"
                                required
                              />
                            </div>
                          </div>
                          <small
                            v-if="
                              searchForm.errors.captcha ||
                              searchForm.errors.captcha_answer
                            "
                            class="p-error mt-2 block text-red-600"
                          >
                            {{
                              searchForm.errors.captcha ||
                              searchForm.errors.captcha_answer
                            }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <!-- Search Error -->
                    <div
                      v-if="searchForm.errors.search"
                      class="mx-6 !mt-0 rounded-lg border border-red-200 bg-red-50 p-4 sm:mx-0 sm:!mt-6"
                    >
                      <p class="text-red-700">{{ searchForm.errors.search }}</p>
                    </div>

                    <hr
                      v-if="page.props.flash?.incident_found"
                      class="mx-6 !mt-0 sm:hidden"
                    />

                    <!-- Search Result -->
                    <div
                      v-if="page.props.flash?.incident_found"
                      class="rounded-xl border-slate-200 bg-white p-6 pt-0 sm:border sm:pt-6"
                    >
                      <h3
                        class="mb-6 flex items-center text-lg font-semibold text-slate-900"
                      >
                        <IconCircleCheck
                          size="18"
                          class="mr-2 text-green-600"
                        />
                        Tiket Ditemukan
                      </h3>

                      <div class="space-y-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                          <div class="grid grid-cols-1 gap-4">
                            <div>
                              <p class="text-slate-600">ID Tiket</p>
                              <p class="font-mono font-semibold">
                                {{ page.props.flash.incident_found.case_id }}
                              </p>
                            </div>
                          </div>
                          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                              <p class="text-slate-600">Status</p>
                              <Tag
                                :value="page.props.flash.incident_found.status"
                                :severity="
                                  getStatusSeverity(
                                    page.props.flash.incident_found.status,
                                  )
                                "
                              />
                            </div>
                            <div>
                              <p class="text-slate-600">Prioritas</p>
                              <Tag
                                :value="
                                  page.props.flash.incident_found.priority
                                "
                                :severity="
                                  getPrioritySeverity(
                                    page.props.flash.incident_found.priority,
                                  )
                                "
                              />
                            </div>
                            <div>
                              <p class="text-slate-600">Kategori</p>
                              <p class="font-medium">
                                {{
                                  page.props.flash.incident_found.incident_type
                                    ?.name
                                }}
                              </p>
                            </div>
                            <div>
                              <p class="text-slate-600">Dilaporkan</p>
                              <p class="font-medium">
                                {{
                                  formatDateTime(
                                    page.props.flash.incident_found.reported_at,
                                  )
                                }}
                              </p>
                            </div>
                            <div
                              v-if="
                                page.props.flash.incident_found.assigned_user
                              "
                            >
                              <p class="text-slate-600">Ditangani Oleh</p>
                              <p class="font-medium">
                                {{
                                  page.props.flash.incident_found.assigned_user
                                    .name
                                }}
                              </p>
                            </div>
                          </div>
                        </div>

                        <!-- Attachment Section -->
                        <div
                          v-if="page.props.flash.incident_found.attachment"
                          class="border-t border-slate-200 pt-4"
                        >
                          <h4
                            class="mb-3 flex items-center text-lg font-semibold text-slate-900"
                          >
                            <IconPaperclip
                              size="18"
                              class="mr-2 text-blue-600"
                            />
                            Lampiran
                          </h4>
                          <div
                            class="rounded-lg border border-slate-200 bg-slate-50 p-4"
                          >
                            <div class="flex items-center justify-between">
                              <div class="flex items-center">
                                <div
                                  class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg"
                                  :class="
                                    getFileIcon(
                                      page.props.flash.incident_found.attachment
                                        .filename,
                                    )[1]
                                  "
                                >
                                  <component
                                    :is="
                                      getFileIcon(
                                        page.props.flash.incident_found
                                          .attachment.filename,
                                      )[0]
                                    "
                                    :class="
                                      getFileIcon(
                                        page.props.flash.incident_found
                                          .attachment.filename,
                                      )[2]
                                    "
                                    size="18"
                                  />
                                </div>
                                <div>
                                  <p class="font-medium text-slate-900">
                                    Lampiran bukti insiden
                                  </p>
                                  <p class="text-sm text-slate-500">
                                    <strong>{{
                                      page.props.flash.incident_found.attachment
                                        .extension
                                    }}</strong>
                                    {{
                                      page.props.flash.incident_found.attachment
                                        .file_size || 'N/A'
                                    }}
                                  </p>
                                </div>
                              </div>
                              <Button
                                variant="text"
                                @click="
                                  downloadAttachment(
                                    page.props.flash.incident_found.attachment,
                                  )
                                "
                              >
                                <IconDownload size="16" />
                              </Button>
                            </div>
                          </div>
                        </div>

                        <!-- Logs are intentionally not shown in public search to protect privacy -->
                      </div>
                    </div>

                    <div
                      v-if="page.props.flash?.incident_found"
                      class="mx-6 !mt-0 rounded-xl border border-yellow-200 bg-yellow-50 p-6 sm:mx-0 sm:!mt-6"
                    >
                      <p class="text-slate-600">
                        Untuk melihat detail lengkap tiket, gunakan tautan yang
                        telah dikirimkan ke email Anda saat tiket dibuat. Jika
                        Anda belum menerima email, periksa folder spam atau
                        hubungi tim CSIRT Bojonegoro.
                      </p>
                    </div>

                    <hr
                      v-if="
                        !page.props.flash?.incident_found &&
                        !searchForm.errors.search
                      "
                      class="mx-6 !mt-0 sm:hidden"
                    />

                    <!-- Navigation -->
                    <div class="flex justify-between px-6 sm:px-0">
                      <button
                        type="button"
                        @click="goToChoice"
                        class="py-2 text-slate-600 transition-colors hover:text-slate-800"
                      >
                        ← Kembali
                      </button>
                      <button
                        v-if="!page.props.flash?.incident_found"
                        type="submit"
                        :disabled="searchForm.processing"
                        :class="
                          searchForm.processing
                            ? 'bg-slate-400'
                            : 'bg-green-600 hover:bg-green-700'
                        "
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-white transition-colors disabled:cursor-not-allowed"
                      >
                        <IconLoader3
                          v-if="searchForm.processing"
                          class="animate-spin"
                          size="16"
                        />
                        <IconSearch v-else size="14" />
                        {{
                          searchForm.processing ? 'Mencari...' : 'Cari Tiket'
                        }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Step 3: Confirmation -->
            <div
              v-else-if="activeStep === 2 && selectedService === 'create'"
              class="p-6 sm:p-8"
            >
              <div class="mx-auto max-w-3xl">
                <div class="mb-6 text-center sm:mb-8">
                  <h2
                    class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl"
                  >
                    Konfirmasi Tiket
                  </h2>
                  <p class="text-slate-600">
                    Pastikan semua informasi sudah benar sebelum mengirim
                  </p>
                </div>

                <hr class="my-6 sm:hidden" />

                <!-- Confirmation Details -->
                <div
                  class="mb-6 rounded-xl border-slate-200 sm:border sm:bg-slate-50 sm:p-6"
                >
                  <h3
                    class="mb-4 flex items-center text-lg font-semibold text-slate-900"
                  >
                    <IconTicket size="18" class="mr-2 text-blue-600" />
                    Ringkasan Tiket
                  </h3>

                  <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                      <div>
                        <p class="text-slate-600">Pelapor</p>
                        <p class="font-medium">{{ form.reporter_name }}</p>
                      </div>
                      <div>
                        <p class="text-slate-600">Email</p>
                        <p class="font-medium">{{ form.reporter_email }}</p>
                      </div>
                      <div>
                        <p class="text-slate-600">Kategori</p>
                        <p class="font-medium">
                          {{
                            incidentTypes.find(
                              (t) => t.id == form.incident_type_id,
                            )?.name
                          }}
                        </p>
                      </div>
                      <div>
                        <p class="text-slate-600">Prioritas</p>
                        <Tag
                          :value="form.priority"
                          :severity="getPrioritySeverity(form.priority)"
                        />
                      </div>
                      <div>
                        <p class="text-slate-600">Waktu Kejadian</p>
                        <p class="font-medium">
                          {{ formatDateTime(form.incident_at) }}
                        </p>
                      </div>
                      <div v-if="form.attachment">
                        <p class="text-slate-600">Lampiran</p>
                        <p class="font-medium text-green-600">
                          ✓ File terlampir
                        </p>
                      </div>
                    </div>

                    <div>
                      <p class="mb-1 text-slate-600">Deskripsi</p>
                      <div class="rounded-lg border bg-white p-3">
                        <p class="whitespace-pre-wrap text-slate-700">
                          {{ form.description }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <hr class="my-6 sm:hidden" />

                <!-- Captcha -->
                <div
                  class="mb-6 rounded-xl border-yellow-200 sm:border sm:bg-yellow-50 sm:p-6"
                >
                  <h3
                    class="mb-4 flex items-center text-lg font-semibold text-slate-900"
                  >
                    <IconShieldHalfFilled
                      size="18"
                      class="mr-2 text-yellow-600"
                    />
                    Verifikasi Keamanan
                  </h3>

                  <div class="flex items-center space-x-4">
                    <div
                      class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-mono"
                    >
                      {{ captcha.question }}
                    </div>
                    <div>
                      <InputNumber
                        v-model="captcha.userAnswer"
                        placeholder="Jawaban"
                        class="w-auto"
                        :inputClass="{
                          'p-invalid': form.errors.captcha_answer,
                        }"
                        required
                      />
                    </div>
                    <!-- <button
                      type="button"
                      @click="generateCaptcha"
                      class="text-slate-500 hover:text-blue-600 transition-colors"
                      title="Generate ulang"
                    >
                      <i class="pi pi-refresh text-lg" />
                    </button> -->
                  </div>
                  <small
                    v-if="form.errors.captcha_answer"
                    class="p-error mt-2 block text-red-600"
                    >{{ form.errors.captcha_answer }}</small
                  >
                </div>

                <hr class="my-6 sm:hidden" />

                <!-- Terms Notice -->
                <div
                  class="mb-6 rounded-xl border-blue-200 sm:border sm:bg-blue-50 sm:p-6"
                >
                  <h4 class="mb-3 font-semibold text-blue-900">
                    Ketentuan Layanan:
                  </h4>
                  <ul class="space-y-1 text-sm text-blue-800">
                    <li>• Pastikan informasi yang diberikan akurat</li>
                    <li>• Informasi tiket akan dijaga kerahasiaannya</li>
                    <li>
                      • Anda akan dihubungi jika diperlukan informasi tambahan
                    </li>
                    <li>
                      • Lacak perkembangan tiket melalui halaman ini secara
                      berkala
                    </li>
                  </ul>
                </div>

                <hr class="my-6 sm:hidden" />

                <!-- Navigation -->
                <div class="flex justify-between">
                  <button
                    type="button"
                    @click="prevStep"
                    class="py-2 text-slate-600 transition-colors hover:text-slate-800"
                  >
                    ← Edit Formulir
                  </button>
                  <button
                    @click="submitForm"
                    :disabled="form.processing"
                    :class="
                      form.processing
                        ? 'bg-slate-400'
                        : 'bg-blue-600 hover:bg-blue-700'
                    "
                    class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-semibold text-white transition-colors disabled:cursor-not-allowed"
                  >
                    <IconLoader3
                      v-if="form.processing"
                      class="animate-spin"
                      size="16"
                    />
                    <IconSend v-else size="14" />
                    {{ form.processing ? 'Mengirim Tiket...' : 'Kirim Tiket' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
