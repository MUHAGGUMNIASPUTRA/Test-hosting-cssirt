<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  successData: Object, // { title, message, case_id }
})

const copied = ref(false)

const copyCaseId = async () => {
  try {
    await navigator.clipboard.writeText(props.successData.case_id)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch {
    // fallback for older browsers
    const el = document.createElement('textarea')
    el.value = props.successData.case_id
    document.body.appendChild(el)
    el.select()
    document.execCommand('copy')
    document.body.removeChild(el)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  }
}
</script>

<template>
  <div
    class="rounded-xl border border-green-200 bg-white p-6 text-center shadow-sm sm:p-8"
  >
    <!-- Success icon -->
    <div
      class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100"
    >
      <IconCircleCheck size="32" class="text-green-600" />
    </div>

    <h3 class="mb-2 text-xl font-bold text-slate-900">
      {{ successData.title || 'Tiket Berhasil Dibuat!' }}
    </h3>
    <p class="mb-6 text-slate-600">
      Konfirmasi telah dikirim ke email Anda. Gunakan ID tiket di bawah untuk
      melacak perkembangan laporan Anda.
    </p>

    <!-- Case ID box -->
    <div class="mb-3 rounded-xl border border-green-200 bg-green-50 p-4">
      <p
        class="mb-1 text-xs font-medium uppercase tracking-wider text-green-700"
      >
        ID Tiket Anda
      </p>
      <div class="flex items-center justify-center gap-3">
        <span
          class="font-mono text-2xl font-bold tracking-widest text-green-800"
        >
          {{ successData.case_id }}
        </span>
        <button
          @click="copyCaseId"
          class="flex items-center gap-1.5 rounded-lg border border-green-300 bg-white px-3 py-1.5 text-xs font-medium text-green-700 transition hover:bg-green-100"
          :class="{ 'border-green-500 bg-green-100 text-green-800': copied }"
        >
          <IconCopy v-if="!copied" size="14" />
          <IconCheck v-else size="14" />
          {{ copied ? 'Tersalin!' : 'Salin' }}
        </button>
      </div>
    </div>

    <p class="mb-6 text-xs text-slate-500">
      <IconBookmark size="12" class="mr-1 inline text-orange-500" />
      Simpan ID ini sebagai referensi untuk melacak dan mengidentifikasi tiket
      Anda di masa depan.
    </p>

    <Button severity="secondary" class="mr-2" @click="router.visit('/')">
      <IconHome size="16" />
      Kembali ke Beranda
    </Button>

    <Button severity="success" @click="router.visit('/incident')">
      <IconSearch size="16" />
      Lacak Tiket Saya
    </Button>
  </div>
</template>
