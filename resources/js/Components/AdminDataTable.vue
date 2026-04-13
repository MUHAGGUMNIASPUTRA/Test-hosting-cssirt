<script setup>
/**
 * AdminDataTable — wrapper standar untuk PrimeVue DataTable di halaman admin.
 *
 * Props:
 *   value        — array data baris (biasanya paginatedData.data)
 *   serverConfig — object config server-side dari useAdminTable().serverSideConfig
 *
 * Events:
 *   page — diteruskan dari PrimeVue DataTable (untuk onPage handler)
 *
 * Slots:
 *   default — Column-column PrimeVue
 *   empty   — override tampilan kosong (opsional)
 */
defineProps({
  value: { type: Array, default: () => [] },
  serverConfig: { type: Object, default: () => ({}) },
})

defineEmits(['page'])
</script>

<template>
  <div
    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
  >
    <DataTable
      v-bind="serverConfig"
      :value="value"
      @page="$emit('page', $event)"
    >
      <template #empty>
        <slot name="empty">
          <div class="py-12 text-center">
            <svg
              class="mx-auto mb-4 h-12 w-12 text-slate-300"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <p class="text-lg font-medium text-slate-500">Tidak ada data</p>
            <p class="mt-1 text-sm text-slate-400">Data akan muncul di sini</p>
          </div>
        </slot>
      </template>

      <slot />
    </DataTable>
  </div>
</template>
