<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: { type: String, required: true },
  description: { type: String, default: null },
  backRoute: { type: String, default: null },
  processing: { type: Boolean, default: false },
})
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div
      class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <div>
        <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
          {{ title }}
        </h2>
        <p v-if="description" class="text-slate-600">{{ description }}</p>
      </div>
      <div class="flex items-center gap-3">
        <slot name="back">
          <Link
            v-if="backRoute"
            :href="route(backRoute)"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-100 px-4 py-2 text-slate-600 transition hover:bg-slate-200"
          >
            <IconArrowLeft size="16" />
            Kembali
          </Link>
        </slot>
        <slot name="actions">
          <button
            type="submit"
            :disabled="processing"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <IconLoader3 v-if="processing" class="animate-spin" size="16" />
            <IconDeviceFloppy v-else size="16" />
            {{ processing ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </slot>
      </div>
    </div>
  </div>
</template>
