<!-- Tujuan: Tab riwayat penanganan insiden -->
<!-- Caller: Admin/Incidents/Create.vue -->
<!-- Side Effects: none -->
<script setup>
defineProps({
  isEditing: { type: Boolean, default: false },
  logs: { type: Array, default: () => [] },
})
</script>

<template>
  <div v-if="isEditing && logs.length" class="space-y-3">
    <div
      v-for="log in logs"
      :key="log.id"
      class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
    >
      <div class="mb-2 flex items-center justify-between">
        <span class="font-medium text-slate-700">{{
          log.user?.name ?? '—'
        }}</span>
        <Tag
          v-if="log.is_public"
          value="Publik"
          severity="info"
          class="!text-xs"
        />
      </div>
      <p class="whitespace-pre-line text-sm text-slate-600">
        {{ log.log_message }}
      </p>
    </div>
  </div>
  <div v-else class="py-16 text-center">
    <IconHistory class="mx-auto mb-3 text-slate-300" size="40" />
    <p class="text-slate-500">
      {{
        isEditing
          ? 'Belum ada riwayat penanganan.'
          : 'Riwayat tersedia setelah insiden disimpan.'
      }}
    </p>
  </div>
</template>
