<!-- Tujuan: Menampilkan link aset virtual terdampak di kolom tabel insiden -->
<!-- Caller: Admin/Incidents/Index.vue -->
<!-- Side Effects: none -->
<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  webApplications: { type: Array, default: () => [] },
  mobileApplications: { type: Array, default: () => [] },
})
</script>

<template>
  <div class="flex flex-wrap items-center gap-1">
    <template v-if="!webApplications.length && !mobileApplications.length">
      <span class="text-sm text-slate-400">—</span>
    </template>
    <template v-else>
      <span class="flex flex-wrap gap-1">
        <template
          v-if="webApplications.length + mobileApplications.length === 1"
        >
          <Link
            :href="
              webApplications.length
                ? route('admin.web-applications.show', webApplications[0].id)
                : route(
                    'admin.mobile-applications.show',
                    mobileApplications[0].id,
                  )
            "
            class="text-sm text-blue-600 hover:underline"
          >
            {{ webApplications[0]?.name ?? mobileApplications[0]?.name }}
          </Link>
        </template>
        <template v-else>
          <Link
            v-if="webApplications.length"
            :href="route('admin.web-applications.show', webApplications[0].id)"
            class="text-sm text-blue-600 hover:underline"
            >{{ webApplications[0].name }}</Link
          >
          <Link
            v-else-if="mobileApplications.length"
            :href="
              route('admin.mobile-applications.show', mobileApplications[0].id)
            "
            class="text-sm text-blue-600 hover:underline"
            >{{ mobileApplications[0].name }}</Link
          >
          <Tag
            :value="`+${webApplications.length + mobileApplications.length - 1} lainnya`"
            severity="secondary"
            class="!text-xs"
          />
        </template>
      </span>
    </template>
  </div>
</template>
