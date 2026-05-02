<script setup>
import { computed } from 'vue'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  label: { type: String, required: true },
  value: { type: [String, Number], required: true },
  subtext: { type: String, default: '' },
  subtextClass: { type: String, default: 'text-slate-500' },
  color: { type: String, default: 'blue' },
  // 'horizontal': ikon kiri, teks kanan (untuk Admin Index)
  // 'vertical': teks kiri, ikon kanan + subtext (untuk Dashboard)
  layout: { type: String, default: 'horizontal' },
})

const { isMobile } = useResponsive()

const colorMap = {
  red: { bg: 'bg-red-50', border: 'border-red-200', icon: 'text-red-600' },
  orange: {
    bg: 'bg-orange-50',
    border: 'border-orange-200',
    icon: 'text-orange-600',
  },
  blue: { bg: 'bg-blue-50', border: 'border-blue-200', icon: 'text-blue-600' },
  green: {
    bg: 'bg-green-50',
    border: 'border-green-200',
    icon: 'text-green-600',
  },
  yellow: {
    bg: 'bg-yellow-50',
    border: 'border-yellow-200',
    icon: 'text-yellow-600',
  },
  purple: {
    bg: 'bg-purple-50',
    border: 'border-purple-200',
    icon: 'text-purple-600',
  },
}

const colors = computed(() => colorMap[props.color] ?? colorMap.blue)

// Ukuran ikon dikirim ke slot agar bisa dipakai dengan Tabler Icons (:size)
const iconSize = computed(() => (isMobile.value ? 18 : undefined))
</script>

<template>
  <!-- Horizontal: ikon kiri, label + angka kanan (Admin Index pages) -->
  <div
    v-if="layout === 'horizontal'"
    class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
  >
    <div class="flex items-center">
      <div
        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg border sm:h-12 sm:w-12"
        :class="[colors.bg, colors.border]"
      >
        <slot :iconClass="colors.icon" :iconSize="iconSize" />
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium text-slate-600 sm:text-base">
          {{ label }}
        </p>
        <p class="text-lg/5 font-bold text-slate-900 sm:text-xl">{{ value }}</p>
      </div>
    </div>
  </div>

  <!-- Vertical: label + angka besar kiri, ikon kanan + subtext di bawah (Dashboard) -->
  <div
    v-else
    class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6"
  >
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-slate-600 sm:text-base">
          {{ label }}
        </p>
        <p class="text-xl font-bold text-slate-900 sm:text-3xl">{{ value }}</p>
        <p
          v-if="subtext || $slots.subtext"
          class="text-xs sm:text-sm"
          :class="subtextClass"
        >
          <slot name="subtext">{{ subtext }}</slot>
        </p>
      </div>
      <div
        class="flex h-10 w-10 items-center justify-center rounded-lg border sm:h-12 sm:w-12"
        :class="[colors.bg, colors.border]"
      >
        <slot :iconClass="colors.icon" :iconSize="iconSize" />
      </div>
    </div>
  </div>
</template>
