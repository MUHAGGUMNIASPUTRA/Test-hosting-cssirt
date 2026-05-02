<script setup>
import { formatDatetime } from '@/utils/date'

const props = defineProps({
  form: Object,
  incidentTypes: Array,
  captcha: Object,
})

defineEmits(['submit', 'back'])

const getPrioritySeverity = (priority) =>
  ({ Rendah: 'success', Sedang: 'info', Tinggi: 'warn', Kritikal: 'danger' })[
    priority
  ] || 'info'

const selectedTypeName = () =>
  props.incidentTypes.find((t) => t.id == props.form.incident_type_id)?.name
</script>

<template>
  <div class="p-6 sm:p-8">
    <div class="mx-auto max-w-3xl">
      <div class="mb-6 text-center sm:mb-8">
        <h2 class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl">
          Konfirmasi Tiket
        </h2>
        <p class="text-slate-600">
          Pastikan semua informasi sudah benar sebelum mengirim
        </p>
      </div>

      <hr class="my-6 sm:hidden" />

      <!-- Summary -->
      <div
        class="mb-6 rounded-xl border-slate-200 sm:border sm:bg-slate-50 sm:p-6"
      >
        <h3 class="mb-4 flex items-center text-lg font-semibold text-slate-900">
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
              <p class="font-medium">{{ selectedTypeName() }}</p>
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
              <p class="font-medium">{{ formatDatetime(form.incident_at) }}</p>
            </div>
            <div v-if="form.attachment || form.attachment_links">
              <p class="text-slate-600">Lampiran</p>
              <p class="font-medium text-green-600">✓ Terlampir</p>
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
        <h3 class="mb-4 flex items-center text-lg font-semibold text-slate-900">
          <IconShieldHalfFilled size="18" class="mr-2 text-yellow-600" />
          Verifikasi Keamanan
        </h3>
        <div class="flex items-center space-x-4">
          <div
            class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-mono"
          >
            {{ captcha.question }}
          </div>
          <InputNumber
            v-model="captcha.userAnswer"
            placeholder="Jawaban"
            class="w-auto"
            :inputClass="{ 'p-invalid': form.errors.captcha_answer }"
            required
          />
        </div>
        <small
          v-if="form.errors.captcha_answer"
          class="p-error mt-2 block text-red-600"
        >
          {{ form.errors.captcha_answer }}
        </small>
      </div>

      <hr class="my-6 sm:hidden" />

      <!-- Terms -->
      <div
        class="mb-6 rounded-xl border-blue-200 sm:border sm:bg-blue-50 sm:p-6"
      >
        <h4 class="mb-3 font-semibold text-blue-900">Ketentuan Layanan:</h4>
        <ul class="space-y-1 text-sm text-blue-800">
          <li>• Pastikan informasi yang diberikan akurat</li>
          <li>• Informasi tiket akan dijaga kerahasiaannya</li>
          <li>• Anda akan dihubungi jika diperlukan informasi tambahan</li>
          <li>• Lacak perkembangan tiket melalui halaman ini secara berkala</li>
        </ul>
      </div>

      <hr class="my-6 sm:hidden" />

      <!-- Navigation -->
      <div class="flex justify-between">
        <button
          type="button"
          @click="$emit('back')"
          class="py-2 text-slate-600 transition-colors hover:text-slate-800"
        >
          ← Edit Formulir
        </button>
        <button
          @click="$emit('submit')"
          :disabled="form.processing"
          :class="
            form.processing ? 'bg-slate-400' : 'bg-blue-600 hover:bg-blue-700'
          "
          class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-semibold text-white transition-colors disabled:cursor-not-allowed"
        >
          <IconLoader3 v-if="form.processing" class="animate-spin" size="16" />
          <IconSend v-else size="14" />
          {{ form.processing ? 'Mengirim Tiket...' : 'Kirim Tiket' }}
        </button>
      </div>
    </div>
  </div>
</template>
