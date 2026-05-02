<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { computed, onMounted, ref, watch } from 'vue'

defineEmits(['back'])

const page = usePage()

const searchForm = useForm({
  case_id: '',
  email: '',
  captcha_answer: '',
  captcha_expected: '',
})

const searchCaptcha = ref({ question: '', answer: '', userAnswer: '' })

const searchCaptchaRequired = computed(() => {
  const f = page.props.flash || {}
  if (f.incident_found) return false
  const e = searchForm.errors || {}
  return Boolean(
    f.captcha_required || e.captcha || e.captcha_answer || e.captcha_expected,
  )
})

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

  searchCaptcha.value = {
    question: `${num1} ${operation.symbol} ${num2} = ?`,
    answer: answer.toString(),
    userAnswer: '',
  }
  searchForm.captcha_expected = answer.toString()
}

watch(
  () => searchCaptcha.value.userAnswer,
  (newValue) => {
    searchForm.captcha_answer = newValue
  },
)

watch(searchCaptchaRequired, (required) => {
  if (required && !searchCaptcha.value.question) generateCaptcha()
})

const submit = () => {
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
        generateCaptcha()
    },
  })
}

onMounted(() => {
  if (searchCaptchaRequired.value && !searchCaptcha.value.question)
    generateCaptcha()
})
</script>

<template>
  <div class="p-0 py-6 sm:p-8">
    <div class="mx-auto max-w-2xl">
      <div class="mb-6 text-center sm:mb-8">
        <h2 class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl">
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

      <form @submit.prevent="submit" class="space-y-6">
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
              <small v-if="searchForm.errors.case_id" class="p-error">
                {{ searchForm.errors.case_id }}
              </small>
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
              <small v-if="searchForm.errors.email" class="p-error">
                {{ searchForm.errors.email }}
              </small>
            </div>

            <!-- Conditional Captcha -->
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
              <small
                v-if="
                  searchForm.errors.captcha || searchForm.errors.captcha_answer
                "
                class="p-error mt-2 block text-red-600"
              >
                {{
                  searchForm.errors.captcha || searchForm.errors.captcha_answer
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

        <IncidentFoundTicket
          v-if="page.props.flash?.incident_found"
          :ticket="page.props.flash.incident_found"
        />

        <hr
          v-if="!page.props.flash?.incident_found && !searchForm.errors.search"
          class="mx-6 !mt-0 sm:hidden"
        />

        <!-- Navigation -->
        <div class="flex justify-between px-6 sm:px-0">
          <button
            type="button"
            @click="$emit('back')"
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
            {{ searchForm.processing ? 'Mencari...' : 'Cari Tiket' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
