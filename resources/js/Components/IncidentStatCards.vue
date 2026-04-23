<!-- Tujuan: Grid statistik insiden untuk halaman index -->
<!-- Caller: Admin/Incidents/Index.vue -->
<!-- Side Effects: none -->
<script setup>
defineProps({
  stats: { type: Object, default: () => ({}) },
})
</script>

<template>
  <div class="grid grid-cols-2 gap-4 lg:grid-cols-2 lg:gap-6 xl:grid-cols-4">
    <StatCard color="blue" label="Total Insiden" :value="stats?.total || 0">
      <template #default="{ iconClass, iconSize }">
        <IconMailExclamation :class="iconClass" :size="iconSize" />
      </template>
    </StatCard>
    <StatCard
      color="yellow"
      label="Dalam Proses"
      :value="stats?.in_progress || 0"
    >
      <template #default="{ iconClass, iconSize }">
        <IconRefresh :class="iconClass" :size="iconSize" />
      </template>
    </StatCard>
    <StatCard color="red" label="Kritikal" :value="stats?.critical || 0">
      <template #default="{ iconClass, iconSize }">
        <IconAlertHexagon :class="iconClass" :size="iconSize" />
      </template>
    </StatCard>
    <StatCard color="green" label="Selesai" :value="stats?.completed || 0">
      <template #default="{ iconClass, iconSize }">
        <IconCircleCheck :class="iconClass" :size="iconSize" />
      </template>
    </StatCard>
  </div>
</template>
