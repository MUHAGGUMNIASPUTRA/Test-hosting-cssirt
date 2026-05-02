<!-- Tujuan: Kartu preview/ringkasan laporan insiden sebelum disimpan -->
<!-- Caller: Admin/Incidents/Create.vue -->
<!-- Side Effects: none -->
<script setup>
import { computed } from 'vue'
import { getSeverity } from '@/utils/status'

const props = defineProps({
  isEditing: { type: Boolean, default: false },
  incident: { type: Object, default: null },
  reporterName: { type: String, default: '' },
  status: { type: String, default: '' },
  priority: { type: String, default: '' },
  incidentTypeId: { default: null },
  incidentAt: { default: null },
  incidentTypeOptions: { type: Array, default: () => [] },
  selectedAssetsCount: { type: Number, default: 0 },
})

const categoryLabel = computed(
  () =>
    props.incidentTypeOptions.find((t) => t.value === props.incidentTypeId)
      ?.label || 'Belum diisi',
)

const formatDateTime = (date) =>
  date ? new Date(date).toLocaleString('id-ID') : ''
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:p-6">
    <h3 class="mb-4 text-xl/6 font-semibold text-slate-700">
      {{ isEditing ? 'Ringkasan Perubahan' : 'Preview Laporan' }}
    </h3>
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <span class="text-slate-500">ID Insiden:</span>
        <span
          class="rounded bg-slate-200 px-2 py-1 font-mono text-xs text-slate-700"
        >
          {{ isEditing ? incident.case_id : 'Auto Generated' }}
        </span>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-slate-500">Pelapor:</span>
        <span class="ml-2 truncate text-slate-700">{{
          reporterName || 'Belum diisi'
        }}</span>
      </div>
      <div class="flex items-center justify-between">
        <span class="text-slate-500">Status:</span>
        <Tag
          :value="status"
          :severity="getSeverity('incident-status', status)"
          size="small"
        />
      </div>
      <div class="flex items-center justify-between">
        <span class="text-slate-500">Prioritas:</span>
        <Tag
          :value="priority"
          :severity="getSeverity('priority', priority)"
          size="small"
        />
      </div>
      <div class="flex items-center justify-between">
        <span class="text-slate-500">Kategori:</span>
        <span class="ml-2 truncate text-slate-700">{{
          incidentTypeId ? categoryLabel : 'Belum diisi'
        }}</span>
      </div>
      <div v-if="incidentAt" class="flex items-start justify-between">
        <span class="text-slate-500">Waktu:</span>
        <span class="text-right text-slate-700">{{
          formatDateTime(incidentAt)
        }}</span>
      </div>
      <div v-if="selectedAssetsCount" class="flex items-center justify-between">
        <span class="text-slate-500">Aset:</span>
        <span class="text-indigo-600">{{ selectedAssetsCount }} terpilih</span>
      </div>
    </div>
  </div>
</template>
