<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
  turnstileSiteKey: String,
})

defineEmits(['back'])

const page = usePage()

const searchForm = useForm({
  case_id: '',
  email: '',
  'cf-turnstile-response': '',
})

const turnstileWidgetRef = ref(null)

const submit = () => {
  searchForm.post(route('incident.search'), {
    onSuccess: () => {
      searchForm.clearErrors()
    },
    onError: () => {
      if (turnstileWidgetRef.value) {
        turnstileWidgetRef.value.reset()
      }
      searchForm['cf-turnstile-response'] = ''
    },
  })
}
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

            <!-- Turnstile Security Verification -->
            <div class="pt-4">
              <label class="mb-2 block font-medium text-slate-700">
                Verifikasi Keamanan
              </label>
              <TurnstileWidget
                ref="turnstileWidgetRef"
                :site-key="turnstileSiteKey"
                v-model="searchForm['cf-turnstile-response']"
              />
              <small
                v-if="searchForm.errors['cf-turnstile-response']"
                class="p-error mt-2 block text-red-600"
              >
                {{ searchForm.errors['cf-turnstile-response'] }}
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
