<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

const page = usePage()

const props = defineProps({
  incidentTypes: Array,
  turnstileSiteKey: String,
})

// --- Step management ---
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
const selectedService = ref(null)

// --- Create form ---
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
  'cf-turnstile-response': '',
})

// --- Navigation ---
const selectService = (service) => {
  selectedService.value = service
  activeStep.value = 1
}

const nextStep = () => {
  if (activeStep.value < steps.length - 1) {
    activeStep.value++
  }
}

const prevStep = () => {
  if (activeStep.value > 0) activeStep.value--
}

const goToChoice = () => {
  activeStep.value = 0
  selectedService.value = null
  form.reset()
  if (page.props.flash?.success) delete page.props.flash.success
  if (page.props.flash?.incident_found) delete page.props.flash.incident_found
}

// --- Submit create ---
const submitForm = () => {
  form.post(route('incident.store'), {
    onSuccess: () => {
      form.reset()
      activeStep.value = 0
      selectedService.value = null
    },
  })
}

// --- Animation ---
const formRef = ref(null)

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting)
          entry.target.classList.add('animate-fade-in-up')
      })
    },
    { threshold: 0.1, rootMargin: '0px 0px -50px 0px' },
  )
  if (formRef.value) observer.observe(formRef.value)
})
</script>

<template>
  <AppLayout title="Lapor Insiden Siber">
    <IncidentHero />

    <section
      ref="formRef"
      class="translate-y-10 bg-white py-8 opacity-0 sm:py-16 lg:py-24"
    >
      <div class="container">
        <div class="mx-auto max-w-6xl">
          <!-- Success Card -->
          <div v-if="page.props.flash?.success" class="mx-auto max-w-xl py-8">
            <IncidentSuccessCard :success-data="page.props.flash.success" />
          </div>

          <template v-else>
            <!-- Stepper -->
            <div class="mb-8">
              <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4 lg:space-x-8">
                  <div
                    v-for="(step, index) in steps"
                    :key="step.key"
                    class="flex items-center"
                  >
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
                    <div
                      v-if="index < steps.length - 1"
                      class="mx-2 h-1 w-16 transition-all duration-300 lg:mx-4 lg:w-24"
                      :class="
                        activeStep > index ? 'bg-blue-600' : 'bg-slate-200'
                      "
                    ></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step Content -->
            <div
              class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
            >
              <!-- Step 0: Service Choice -->
              <IncidentStepServiceChoice
                v-if="activeStep === 0"
                @select="selectService"
              />

              <!-- Step 1: Create Form -->
              <IncidentCreateForm
                v-else-if="activeStep === 1 && selectedService === 'create'"
                :form="form"
                :incident-types="incidentTypes"
                @next="nextStep"
                @back="goToChoice"
              />

              <!-- Step 1: Search Form -->
              <IncidentSearchForm
                v-else-if="activeStep === 1 && selectedService === 'search'"
                :turnstile-site-key="turnstileSiteKey"
                @back="goToChoice"
              />

              <!-- Step 2: Confirmation -->
              <IncidentConfirmStep
                v-else-if="activeStep === 2 && selectedService === 'create'"
                :form="form"
                :incident-types="incidentTypes"
                :turnstile-site-key="turnstileSiteKey"
                @submit="submitForm"
                @back="prevStep"
              />
            </div>
          </template>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
