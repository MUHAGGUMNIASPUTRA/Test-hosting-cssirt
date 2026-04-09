<script setup>
// filepath: resources/js/Pages/Incidents/Create.vue

import { router, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch } from 'vue';
import { useParticles } from '@/Composables/useParticles';
import { IconFile, IconFileTypeDoc, IconFileTypeDocx, IconFileTypeJpg, IconFileTypePdf, IconFileTypePng, IconFileTypeZip } from '@tabler/icons-vue';

const page = usePage();

const props = defineProps({
  incidentTypes: Array,
});

// Animation refs
const heroRef = ref(null);
const formRef = ref(null);
const { minimalParticlesOptions } = useParticles();

// Step management
const activeStep = ref(0);
const steps = [
  {
    key: 'choice',
    title: 'Pilih Layanan',
    description: 'Buat tiket baru atau cek tiket yang ada',
    icon: 'pi pi-directions'
  },
  {
    key: 'form',
    title: 'Isi Formulir',
    description: 'Lengkapi informasi tiket Anda',
    icon: 'pi pi-file-edit'
  },
  {
    key: 'confirmation',
    title: 'Konfirmasi',
    description: 'Tinjau dan kirim tiket Anda',
    icon: 'pi pi-check-circle'
  }
];

// Service choice
const selectedService = ref(null); // Start with null, not 'create'

// Captcha
const captcha = ref({
  question: '',
  answer: '',
  userAnswer: ''
});

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
});

const searchForm = useForm({
  case_id: '',
  email: '',
  captcha_answer: '',
  captcha_expected: '',
});

// Selected incident type info
const selectedType = computed(() => {
  if (!form.incident_type_id) return null;
  return props.incidentTypes.find(t => t.id === form.incident_type_id) || null;
});

// Attachment mode toggle
const attachmentMode = ref('file'); // 'file' | 'link'

const setAttachmentMode = (mode) => {
  attachmentMode.value = mode;
  form.attachment_type = mode;
  form.attachment = null;
  form.attachment_links = '';
  if (mode === 'file') {
    attachmentPreview.value = null;
    if (uploader.value) uploader.value.clear();
  }
};

const uploader = ref(null);
const attachmentPreview = ref(null);

// Search captcha reuses the same generator
const searchCaptcha = ref({ question: '', answer: '', userAnswer: '' });
const generateSearchCaptcha = () => {
  generateCaptcha();
  // Mirror the generated captcha into the search form UI/state
  searchCaptcha.value = { ...captcha.value };
  searchForm.captcha_expected = captcha.value.answer.toString();
};

watch(() => searchCaptcha.value.userAnswer, (newValue) => {
  searchForm.captcha_answer = newValue;
});

const searchCaptchaRequired = computed(() => {
  const f = page.props.flash || {};
  // If an incident is found, do not require captcha on this view
  if (f.incident_found) return false;
  const e = searchForm.errors || {};
  return Boolean(f.captcha_required || e.captcha || e.captcha_answer || e.captcha_expected);
});

watch(searchCaptchaRequired, (required) => {
  if (required && !searchCaptcha.value.question) {
    generateSearchCaptcha();
  }
});

// Priority options
const priorityOptions = [
  { label: 'Rendah', value: 'Rendah', color: 'success', description: 'Tidak mengganggu operasional' },
  { label: 'Sedang', value: 'Sedang', color: 'info', description: 'Sedikit mengganggu operasional' },
  { label: 'Tinggi', value: 'Tinggi', color: 'warn', description: 'Sangat mengganggu operasional' },
  { label: 'Kritikal', value: 'Kritikal', color: 'danger', description: 'Menghentikan operasional' }
];

// Generate simple captcha
const generateCaptcha = () => {
  const operations = [
    { type: 'add', symbol: '+' },
    { type: 'subtract', symbol: '-' },
    { type: 'multiply', symbol: '×' }
  ];

  const operation = operations[Math.floor(Math.random() * operations.length)];
  let num1, num2, answer;

  switch (operation.type) {
    case 'add':
      num1 = Math.floor(Math.random() * 20) + 1;
      num2 = Math.floor(Math.random() * 20) + 1;
      answer = num1 + num2;
      break;
    case 'subtract':
      num1 = Math.floor(Math.random() * 20) + 10;
      num2 = Math.floor(Math.random() * 10) + 1;
      answer = num1 - num2;
      break;
    case 'multiply':
      num1 = Math.floor(Math.random() * 10) + 1;
      num2 = Math.floor(Math.random() * 10) + 1;
      answer = num1 * num2;
      break;
  }

  captcha.value = {
    question: `${num1} ${operation.symbol} ${num2} = ?`,
    answer: answer.toString(),
    userAnswer: ''
  };

  form.captcha_expected = answer.toString();
};

// Watch for captcha user input
watch(() => captcha.value.userAnswer, (newValue) => {
  form.captcha_answer = newValue;
});

// Methods
const selectService = (service) => {
  selectedService.value = service;
  if (service === 'create') {
    activeStep.value = 1;
  } else {
    activeStep.value = 1;
  }
};

const nextStep = () => {
  if (activeStep.value < steps.length - 1) {
    activeStep.value++;
    // Generate captcha when moving to confirmation step
    if (activeStep.value === 2 && selectedService.value === 'create') {
      generateCaptcha();
    }
  }
};

const prevStep = () => {
  if (activeStep.value > 0) {
    activeStep.value--;
  }
};

const goToChoice = () => {
  activeStep.value = 0;
  selectedService.value = null;
  form.reset();
  searchForm.reset();
  searchForm.clearErrors();
  clearAttachment();
  captcha.value = { question: '', answer: '', userAnswer: '' };

  // Clear any flash messages
  if (page.props.flash?.success) {
    delete page.props.flash.success;
  }
  if (page.props.flash?.incident_found) {
    delete page.props.flash.incident_found;
  }
};

const triggerFileInput = () => {
  const input = uploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
};

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
  if (uploader.value) uploader.value.clear();
  form.attachment = null;
  attachmentPreview.value = null;
};

const submitForm = () => {
  if (selectedService.value === 'create') {
    form.post(route('incident.store'), {
      onSuccess: () => {
        form.reset();
        clearAttachment();
        attachmentMode.value = 'file';
        activeStep.value = 0;
        selectedService.value = null;
        captcha.value = { question: '', answer: '', userAnswer: '' };
      },
      onError: () => {
        if (activeStep.value === 2) {
          generateCaptcha();
        }
      }
    });
  } else {
    if (searchCaptchaRequired.value) {
      searchForm.captcha_answer = (searchCaptcha.value.userAnswer ?? '').toString();
      if (searchCaptcha.value.answer) {
        searchForm.captcha_expected = searchCaptcha.value.answer.toString();
      }
    }
    searchForm.post(route('incident.search'), {
      onSuccess: () => {
        searchCaptcha.value = { question: '', answer: '', userAnswer: '' };
        searchForm.captcha_answer = '';
        searchForm.captcha_expected = '';
        searchForm.clearErrors();
        if (page.props.flash && 'captcha_required' in page.props.flash) {
          delete page.props.flash.captcha_required;
        }
      },
      onError: () => {
        if (searchCaptchaRequired.value && !searchCaptcha.value.question)
          generateSearchCaptcha();
      }
    });
  }
};

const maxDate = new Date()

// Format functions
const formatDateTime = (dateTime) => {
  if (!dateTime) return '';
  return new Date(dateTime).toLocaleString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getStatusSeverity = (status) => {
  const severityMap = {
    'Baru': 'info',
    'Diverifikasi': 'primary',
    'Dalam Penyelidikan': 'warn',
    'Selesai': 'success',
    'Ditutup': 'secondary'
  };
  return severityMap[status] || 'info';
};

const getPrioritySeverity = (priority) => {
  const severityMap = {
    'Rendah': 'success',
    'Sedang': 'info',
    'Tinggi': 'warn',
    'Kritikal': 'danger'
  };
  return severityMap[priority] || 'info';
};

// Helper functions for the search results
const getFileIcon = (filename) => {
  if (!filename) return 'pi-file';

  const extension = filename.split('.').pop().toLowerCase();
  const iconMap = {
    'pdf': [IconFileTypePdf, 'bg-red-100', 'text-red-600'],
    'doc': [IconFileTypeDoc, 'bg-blue-100', 'text-blue-600'],
    'docx': [IconFileTypeDocx, 'bg-blue-100', 'text-blue-600'],
    'zip': [IconFileTypeZip, 'bg-yellow-100', 'text-yellow-600'],
    'jpg': [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    'jpeg': [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    'png': [IconFileTypePng, 'bg-green-100', 'text-green-600'],
  };

  return iconMap[extension] || [IconFile, 'bg-slate-100', 'text-slate-600'];
};

const downloadAttachment = (attachment) => {
  const link = document.createElement('a');
  const url = typeof attachment === 'string' ? attachment : attachment?.download_url;
  if (!url) return;
  link.href = url;
  link.target = '_blank';
  link.rel = 'noopener';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

// Initialize
onMounted(() => {
  // Scroll animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up')
      }
    })
  }, observerOptions);

  if (formRef.value) observer.observe(formRef.value);

  // If server requires captcha for search (after repeated failures), generate it immediately
  if (searchCaptchaRequired.value && !searchCaptcha.value.question) generateSearchCaptcha();
});
</script>

<template>
  <AppLayout title="Lapor Insiden Siber">

    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <div class="absolute inset-0 z-0">
        <vue-particles id="tsparticles" :options="minimalParticlesOptions" class="w-full h-full"/>
      </div>

      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container max-w-4xl text-center">
          <div class="animate-fade-in-up">
            <!-- Alert Icon -->
            <div class="w-20 h-20 bg-red-100/20 rounded-full flex items-center justify-center mx-auto mb-8 backdrop-blur-sm">
              <!-- <i-lucide-triangle-alert class="w-10 h-10 text-red-400 mb-1" /> -->
              <IconAlertHexagon size="36" class="text-red-400" />
            </div>

            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl mb-6 leading-tight">
              Lapor <span class="bg-gradient-to-r from-red-400 to-orange-400 bg-clip-text text-transparent">Insiden Siber</span>
            </h1>

            <p class="text-xl sm:text-2xl text-slate-300 mb-8 max-w-3xl mx-auto">
              Buat tiket baru untuk melaporkan insiden keamanan siber atau lacak status tiket yang sudah ada
            </p>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-3 sm:gap-6 mt-8 sm:mt-12">
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-2 sm:p-6 border border-white/20">
                <div class="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-0 sm:mb-1 lg:mb-2">24/7</div>
                <div class="text-sm sm:text-base text-slate-300">Layanan Siaga</div>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-2 sm:p-6 border border-white/20">
                <div class="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-0 sm:mb-1 lg:mb-2">&lt; 24 Jam</div>
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
        <div class="max-w-6xl mx-auto">

          <!-- Flash Messages -->
          <div v-if="page.props.flash?.success" class="mb-8">
            <div class="bg-green-50 border border-green-200 rounded-xl p-6">
              <div class="flex items-start">
                <IconCheck class="hidden sm:flex text-green-600 mr-3" />
                <div>
                  <h3 class="text-green-800 font-bold text-lg">{{ page.props.flash.success?.title }}</h3>
                  <p class="text-green-700 mt-1">{{ page.props.flash.success?.message }}</p>
                  <div v-if="page.props.flash.success?.case_id" class="mt-3">
                    <button
                      @click="goToChoice"
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
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
                      class="w-12 h-12 rounded-full flex items-center justify-center font-semibold transition-all duration-300"
                      :class="activeStep >= index
                        ? 'bg-blue-600 text-white shadow-lg'
                        : 'bg-slate-200 text-slate-500'"
                    >
                      <i :class="step.icon" class="!text-lg" />
                    </div>
                    <div class="mt-2 text-center">
                      <p
                        class="text-sm sm:text-base font-medium"
                        :class="activeStep >= index ? 'text-blue-600' : 'text-slate-500'"
                      >
                        {{ step.title }}
                      </p>
                      <p class="text-sm text-slate-400 hidden sm:block">{{ step.description }}</p>
                    </div>
                  </div>

                  <!-- Connector Line -->
                  <div
                    v-if="index < steps.length - 1"
                    class="w-16 lg:w-24 h-1 mx-2 lg:mx-4 transition-all duration-300"
                    :class="activeStep > index ? 'bg-blue-600' : 'bg-slate-200'"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

            <!-- Step 1: Service Choice -->
            <div v-if="activeStep === 0" class="p-6 sm:p-8">
              <div class="text-center mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Pilih Layanan</h2>
                <p class="text-slate-600">Apa yang ingin Anda lakukan hari ini?</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <!-- Create New Ticket -->
                <div
                  @click="selectService('create')"
                  class="relative group cursor-pointer"
                >
                  <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 sm:p-8 transition-all duration-300 hover:border-blue-400 hover:shadow-lg group-hover:-translate-y-1">
                    <div class="text-center">
                      <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                        <IconPlus size="18" stroke-width="1.5" class="text-blue-600" />
                      </div>
                      <h3 class="text-xl font-bold text-slate-900 mb-1">Buat Tiket Baru</h3>
                      <p class="text-slate-600">Laporkan insiden keamanan siber yang baru terjadi</p>
                    </div>
                    <div class="absolute inset-0 bg-blue-600 opacity-0 group-hover:opacity-5 transition-opacity rounded-xl"></div>
                  </div>
                </div>

                <!-- Check Existing Ticket -->
                <div
                  @click="selectService('search')"
                  class="relative group cursor-pointer"
                >
                  <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-6 sm:p-8 transition-all duration-300 hover:border-green-400 hover:shadow-lg group-hover:-translate-y-1">
                    <div class="text-center">
                      <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-green-200 transition-colors">
                        <IconSearch size="18" stroke-width="1.5" class="text-green-600" />
                      </div>
                      <h3 class="text-xl font-bold text-slate-900 mb-1">Cek Status Tiket</h3>
                      <p class="text-slate-600">Lacak perkembangan tiket yang sudah dibuat</p>
                    </div>
                    <div class="absolute inset-0 bg-green-600 opacity-0 group-hover:opacity-5 transition-opacity rounded-xl"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 2: Form or Search -->
            <div v-else-if="activeStep === 1">

              <!-- Create Ticket Form -->
              <div v-if="selectedService === 'create'" class="p-0 py-6 sm:p-8">
                <div class="max-w-4xl mx-auto">
                  <div class="text-center mb-6 sm:mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Buat Tiket Baru</h2>
                    <p class="text-slate-600">Isi formulir di bawah dengan lengkap dan akurat</p>
                  </div>

                  <hr class="sm:hidden !mt-0 mx-6"/>

                  <form @submit.prevent="nextStep" class="space-y-6 sm:space-y-8">

                    <!-- Reporter Information -->
                    <div class="sm:bg-slate-50 sm:border border-slate-200 rounded-xl p-6">
                      <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                        <IconUserExclamation size="18" class="text-blue-600 mr-2" />
                        Informasi Pelapor
                      </h3>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block font-medium text-slate-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            v-model="form.reporter_name"
                            placeholder="Masukkan nama lengkap"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.reporter_name }"
                            required
                          />
                          <small v-if="form.errors.reporter_name" class="p-error">{{ form.errors.reporter_name }}</small>
                        </div>

                        <div>
                          <label class="block font-medium text-slate-700 mb-2">
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
                          <small v-if="form.errors.reporter_email" class="p-error">{{ form.errors.reporter_email }}</small>
                        </div>

                        <div class="md:col-span-2">
                          <label class="block font-medium text-slate-700 mb-2">
                            Nomor Telepon <span class="text-slate-500">(Opsional)</span>
                          </label>
                          <InputText
                            v-model="form.reporter_phone"
                            placeholder="08123456789"
                            class="w-full"
                            :class="{ 'p-invalid': form.errors.reporter_phone }"
                          />
                          <small v-if="form.errors.reporter_phone" class="p-error">{{ form.errors.reporter_phone }}</small>
                        </div>
                      </div>
                    </div>

                    <hr class="sm:hidden !mt-0 mx-6"/>

                    <!-- Incident Information -->
                    <div class="sm:bg-slate-50 sm:border border-slate-200 rounded-xl py-0 px-6 sm:p-6">
                      <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                        <IconUrgent size="18" class="text-red-600 mr-2" />
                        Detail Tiket
                      </h3>

                      <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div>
                            <label class="block font-medium text-slate-700 mb-2">
                              Kategori <span class="text-red-500">*</span>
                            </label>
                            <Select
                              v-model="form.incident_type_id"
                              :options="incidentTypes"
                              optionLabel="name"
                              optionValue="id"
                              placeholder="Pilih kategori"
                              class="w-full"
                              :class="{ 'p-invalid': form.errors.incident_type_id }"
                              required
                            />
                            <small v-if="form.errors.incident_type_id" class="p-error">{{ form.errors.incident_type_id }}</small>
                          </div>

                          <div>
                            <label class="block font-medium text-slate-700 mb-2">
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
                            <small v-if="form.errors.incident_at" class="p-error">{{ form.errors.incident_at }}</small>
                          </div>
                        </div>

                        <div>
                          <label class="block font-medium text-slate-700 mb-2">
                            Prioritas Tiket <span class="text-red-500">*</span>
                          </label>
                          <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div
                              v-for="priority in priorityOptions"
                              :key="priority.value"
                              @click="form.priority = priority.value"
                              class="cursor-pointer"
                            >
                              <div
                                class="text-center p-4 border rounded-lg transition-all duration-200 hover:shadow-md"
                                :class="form.priority === priority.value
                                  ? 'border-blue-500 bg-blue-50'
                                  : 'border-slate-200 hover:border-slate-300'"
                              >
                                <Tag
                                  :value="priority.label"
                                  :severity="priority.color"
                                  size="small"
                                />
                              </div>
                            </div>
                          </div>
                          <small v-if="form.errors.priority" class="p-error">{{ form.errors.priority }}</small>
                        </div>

                        <div>
                          <label class="block font-medium text-slate-700 mb-2">
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
                          <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
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
                          <div v-if="selectedType && (selectedType.description || selectedType.guide)">
                            <div class="bg-indigo-50 border border-indigo-200 rounded-xl overflow-hidden">
                              <!-- Header -->
                              <div class="bg-indigo-100 border-b border-indigo-200 px-5 py-3 flex items-center gap-3">
                                <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                  <i class="pi pi-info-circle !text-sm text-white"></i>
                                </div>
                                <div>
                                  <p class="font-semibold text-indigo-900">{{ selectedType.name }}</p>
                                  <p v-if="selectedType.description" class="text-sm text-indigo-700">{{ selectedType.description }}</p>
                                </div>
                              </div>
                              <!-- Guide Content -->
                              <div v-if="selectedType.guide" class="p-5">
                                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-3">Panduan Pelaporan</p>
                                <div
                                  class="prose prose-sm max-w-none text-slate-700 [&>h3]:text-base [&>h3]:font-semibold [&>h3]:text-indigo-900 [&>h3]:mb-2 [&>p]:mb-2 [&>ul]:mb-2 [&>ol]:mb-2 [&>ul]:pl-5 [&>ol]:pl-5 [&>li]:mb-1"
                                  v-html="selectedType.guide"
                                />
                              </div>
                            </div>
                          </div>
                        </Transition>

                        <!-- Attachment -->
                        <div>
                          <label class="block font-medium text-slate-700 mb-2">
                            Lampiran Bukti <span class="text-slate-500">(Opsional)</span>
                          </label>

                          <!-- Mode Toggle -->
                          <div class="flex rounded-xl overflow-hidden border border-slate-300 mb-4 w-fit">
                            <button
                              type="button"
                              @click="setAttachmentMode('file')"
                              class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors"
                              :class="attachmentMode === 'file'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-white text-slate-600 hover:bg-slate-50'"
                            >
                              <i class="pi pi-upload text-xs"></i>
                              Upload Dokumen
                            </button>
                            <button
                              type="button"
                              @click="setAttachmentMode('link')"
                              class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors border-l border-slate-300"
                              :class="attachmentMode === 'link'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-white text-slate-600 hover:bg-slate-50'"
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
                              <div v-if="files[0]" class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                                <div class="flex items-start justify-between gap-4">
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
                                      <p class="font-medium text-slate-900 break-all">{{ files[0].name }}</p>
                                      <p class="text-sm text-slate-500">{{ (files[0].size / 1024 / 1024).toFixed(2) }} MB</p>
                                    </div>
                                  </div>
                                  <button
                                    type="button"
                                    @click="clearAttachment"
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                  >
                                    <IconX size="16" />
                                  </button>
                                </div>
                              </div>
                            </template>

                            <template #empty>
                              <div
                                class="flex flex-col items-center justify-center py-6 px-4 border-2 border-dashed border-slate-300 rounded-lg hover:border-blue-400 transition-colors cursor-pointer"
                                @click="triggerFileInput"
                              >
                                <IconFileSearch class="text-gray-400 mb-2"/>
                                <p class="text-slate-600 font-medium">Klik atau drag file ke sini</p>
                                <p class="text-sm text-slate-400">JPG, PNG, PDF, ZIP, DOC (Maks. 2MB)</p>
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

                          <small v-if="form.errors.attachment || form.errors.attachment_links" class="p-error">
                            {{ form.errors.attachment || form.errors.attachment_links }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <hr class="sm:hidden mx-6"/>

                    <!-- Navigation -->
                    <div class="flex justify-between px-6 sm:px-0">
                      <button
                        type="button"
                        @click="goToChoice"
                        class="py-2 text-slate-600 hover:text-slate-800 transition-colors"
                      >
                        ← Kembali
                      </button>
                      <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                      >
                        Selanjutnya →
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Search Ticket -->
              <div v-else-if="selectedService === 'search'" class="p-0 py-6 sm:p-8">
                <div class="max-w-2xl mx-auto">
                  <div class="text-center mb-6 sm:mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Cek Status Tiket</h2>
                    <p class="text-slate-600">Masukkan ID tiket dan email untuk melihat status</p>
                  </div>

                  <hr v-if="!page.props.flash?.incident_found" class="sm:hidden !mt-0 mx-6"/>

                  <!-- Search Form -->
                  <form @submit.prevent="submitForm" class="space-y-6">
                    <div v-if="!page.props.flash?.incident_found" class="sm:bg-slate-50 sm:border border-slate-200 rounded-xl p-6">
                      <div class="space-y-4">
                        <div>
                          <label class="block font-medium text-slate-700 mb-2">
                            ID Tiket <span class="text-red-500">*</span>
                          </label>
                          <InputText
                            v-model="searchForm.case_id"
                            placeholder="CSIRT-XXXX-XX-XXX"
                            class="w-full font-mono"
                            :class="{ 'p-invalid': searchForm.errors.case_id }"
                            required
                          />
                          <small v-if="searchForm.errors.case_id" class="p-error">{{ searchForm.errors.case_id }}</small>
                        </div>

                        <div>
                          <label class="block font-medium text-slate-700 mb-2">
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
                          <small v-if="searchForm.errors.email" class="p-error">{{ searchForm.errors.email }}</small>
                        </div>

                        <!-- Conditional Captcha after repeated failures -->
                        <div v-if="searchCaptchaRequired" class="pt-2">
                          <label class="block font-medium text-slate-700 mb-2">
                            Verifikasi Keamanan
                          </label>
                          <div class="flex items-center space-x-4">
                            <div class="bg-white border border-slate-300 rounded-lg px-4 py-2 font-mono">
                              {{ searchCaptcha.question }}
                            </div>
                            <div>
                              <InputNumber
                                v-model="searchCaptcha.userAnswer"
                                placeholder="Jawaban"
                                class="w-auto"
                                :inputClass="{ 'p-invalid': searchForm.errors.captcha || searchForm.errors.captcha_answer }"
                                required
                              />
                            </div>
                          </div>
                          <small v-if="searchForm.errors.captcha || searchForm.errors.captcha_answer" class="p-error block text-red-600 mt-2">
                            {{ searchForm.errors.captcha || searchForm.errors.captcha_answer }}
                          </small>
                        </div>
                      </div>
                    </div>

                    <!-- Search Error -->
                    <div v-if="searchForm.errors.search" class="bg-red-50 border border-red-200 rounded-lg p-4 mx-6 !mt-0 sm:mx-0 sm:!mt-6">
                      <p class="text-red-700">{{ searchForm.errors.search }}</p>
                    </div>

                    <hr v-if="page.props.flash?.incident_found" class="sm:hidden !mt-0 mx-6"/>

                    <!-- Search Result -->
                    <div v-if="page.props.flash?.incident_found" class="bg-white sm:border border-slate-200 rounded-xl p-6 pt-0 sm:pt-6">
                      <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
                        <IconCircleCheck size="18" class="text-green-600 mr-2" />
                        Tiket Ditemukan
                      </h3>

                      <div class="space-y-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                          <div class="grid grid-cols-1 gap-4">
                            <div>
                              <p class="text-slate-600">ID Tiket</p>
                              <p class="font-mono font-semibold">{{ page.props.flash.incident_found.case_id }}</p>
                            </div>
                          </div>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                              <p class="text-slate-600">Status</p>
                              <Tag
                                :value="page.props.flash.incident_found.status"
                                :severity="getStatusSeverity(page.props.flash.incident_found.status)"
                              />
                            </div>
                            <div>
                              <p class="text-slate-600">Prioritas</p>
                              <Tag
                                :value="page.props.flash.incident_found.priority"
                                :severity="getPrioritySeverity(page.props.flash.incident_found.priority)"
                              />
                            </div>
                            <div>
                              <p class="text-slate-600">Kategori</p>
                              <p class="font-medium">{{ page.props.flash.incident_found.incident_type?.name }}</p>
                            </div>
                            <div>
                              <p class="text-slate-600">Dilaporkan</p>
                              <p class="font-medium">{{ formatDateTime(page.props.flash.incident_found.reported_at) }}</p>
                            </div>
                            <div v-if="page.props.flash.incident_found.assigned_user">
                              <p class="text-slate-600">Ditangani Oleh</p>
                              <p class="font-medium">{{ page.props.flash.incident_found.assigned_user.name }}</p>
                            </div>
                          </div>
                        </div>

                        <!-- Attachment Section -->
                        <div v-if="page.props.flash.incident_found.attachment" class="border-t border-slate-200 pt-4">
                          <h4 class="text-lg font-semibold text-slate-900 mb-3 flex items-center">
                            <IconPaperclip size="18" class="text-blue-600 mr-2"/>
                            Lampiran
                          </h4>
                          <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                              <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" :class="getFileIcon(page.props.flash.incident_found.attachment.filename)[1]">
                                  <component :is="getFileIcon(page.props.flash.incident_found.attachment.filename)[0]" :class="getFileIcon(page.props.flash.incident_found.attachment.filename)[2]" size="18" />
                                </div>
                                <div>
                                  <p class="font-medium text-slate-900">
                                    Lampiran bukti insiden
                                  </p>
                                  <p class="text-sm text-slate-500">
                                    <strong>{{ page.props.flash.incident_found.attachment.extension }}</strong>
                                    {{ page.props.flash.incident_found.attachment.file_size || 'N/A' }}
                                  </p>
                                </div>
                              </div>
                              <Button
                                variant="text"
                                @click="downloadAttachment(page.props.flash.incident_found.attachment)"
                              >
                                <IconDownload size="16" />
                              </Button>
                            </div>
                          </div>
                        </div>

                        <!-- Logs are intentionally not shown in public search to protect privacy -->
                      </div>
                    </div>

                    <div v-if="page.props.flash?.incident_found" class="bg-yellow-50 border border-yellow-200 rounded-xl mx-6 sm:mx-0 p-6 !mt-0 sm:!mt-6">
                      <p class="text-slate-600">
                        Untuk melihat detail lengkap tiket, gunakan tautan yang telah dikirimkan ke email Anda saat tiket dibuat.
                        Jika Anda belum menerima email, periksa folder spam atau hubungi tim CSIRT Bojonegoro.
                      </p>
                    </div>

                    <hr v-if="!page.props.flash?.incident_found && !searchForm.errors.search" class="sm:hidden !mt-0 mx-6"/>

                    <!-- Navigation -->
                    <div class="flex justify-between px-6 sm:px-0">
                      <button
                        type="button"
                        @click="goToChoice"
                        class="py-2 text-slate-600 hover:text-slate-800 transition-colors"
                      >
                        ← Kembali
                      </button>
                      <button
                        v-if="!page.props.flash?.incident_found"
                        type="submit"
                        :disabled="searchForm.processing"
                        :class="searchForm.processing ? 'bg-slate-400' : 'bg-green-600 hover:bg-green-700'"
                        class="text-white px-4 py-2 rounded-lg inline-flex justify-center items-center gap-2 transition-colors disabled:cursor-not-allowed"
                      >
                        <IconLoader3 v-if="searchForm.processing" class="animate-spin" size="16"/>
                        <IconSearch v-else size="14"/>
                        {{ searchForm.processing ? 'Mencari...' : 'Cari Tiket' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Step 3: Confirmation -->
            <div v-else-if="activeStep === 2 && selectedService === 'create'" class="p-6 sm:p-8">
              <div class="max-w-3xl mx-auto">
                <div class="text-center mb-6 sm:mb-8">
                  <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Konfirmasi Tiket</h2>
                  <p class="text-slate-600">Pastikan semua informasi sudah benar sebelum mengirim</p>
                </div>

                <hr class="sm:hidden my-6"/>

                <!-- Confirmation Details -->
                <div class="sm:bg-slate-50 sm:border border-slate-200 rounded-xl sm:p-6 mb-6">
                  <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <IconTicket size="18" class="text-blue-600 mr-2" />
                    Ringkasan Tiket
                  </h3>

                  <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <p class="font-medium">{{ incidentTypes.find(t => t.id == form.incident_type_id)?.name }}</p>
                      </div>
                      <div>
                        <p class="text-slate-600">Prioritas</p>
                        <Tag :value="form.priority" :severity="getPrioritySeverity(form.priority)" />
                      </div>
                      <div>
                        <p class="text-slate-600">Waktu Kejadian</p>
                        <p class="font-medium">{{ formatDateTime(form.incident_at) }}</p>
                      </div>
                      <div v-if="form.attachment">
                        <p class="text-slate-600">Lampiran</p>
                        <p class="font-medium text-green-600">✓ File terlampir</p>
                      </div>
                    </div>

                    <div>
                      <p class="text-slate-600 mb-1">Deskripsi</p>
                      <div class="bg-white rounded-lg p-3 border">
                        <p class="text-slate-700 whitespace-pre-wrap">{{ form.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <hr class="sm:hidden my-6"/>

                <!-- Captcha -->
                <div class="sm:bg-yellow-50 sm:border border-yellow-200 rounded-xl sm:p-6 mb-6">
                  <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                    <IconShieldHalfFilled size="18" class="text-yellow-600 mr-2" />
                    Verifikasi Keamanan
                  </h3>

                  <div class="flex items-center space-x-4">
                    <div class="bg-white border border-slate-300 rounded-lg px-4 py-2 font-mono">
                      {{ captcha.question }}
                    </div>
                    <div>
                      <InputNumber
                        v-model="captcha.userAnswer"
                        placeholder="Jawaban"
                        class="w-auto"
                        :inputClass="{ 'p-invalid': form.errors.captcha_answer }"
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
                  <small v-if="form.errors.captcha_answer" class="p-error block text-red-600 mt-2">{{ form.errors.captcha_answer }}</small>
                </div>

                <hr class="sm:hidden my-6"/>

                <!-- Terms Notice -->
                <div class="sm:bg-blue-50 sm:border border-blue-200 rounded-xl sm:p-6 mb-6">
                  <h4 class="font-semibold text-blue-900 mb-3">Ketentuan Layanan:</h4>
                  <ul class="text-blue-800 space-y-1 text-sm">
                    <li>• Pastikan informasi yang diberikan akurat</li>
                    <li>• Informasi tiket akan dijaga kerahasiaannya</li>
                    <li>• Anda akan dihubungi jika diperlukan informasi tambahan</li>
                    <li>• Lacak perkembangan tiket melalui halaman ini secara berkala</li>
                  </ul>
                </div>

                <hr class="sm:hidden my-6"/>

                <!-- Navigation -->
                <div class="flex justify-between">
                  <button
                    type="button"
                    @click="prevStep"
                    class="py-2 text-slate-600 hover:text-slate-800 transition-colors"
                  >
                    ← Edit Formulir
                  </button>
                  <button
                    @click="submitForm"
                    :disabled="form.processing"
                    :class="form.processing ? 'bg-slate-400' : 'bg-blue-600 hover:bg-blue-700'"
                    class="text-white px-4 py-2 rounded-lg transition-colors font-semibold inline-flex justify-center items-center gap-2 disabled:cursor-not-allowed"
                  >
                    <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                    <IconSend v-else size="14"/>
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
