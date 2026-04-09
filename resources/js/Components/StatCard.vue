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
  red:    { bg: 'bg-red-50',    border: 'border-red-200',    icon: 'text-red-600' },
  orange: { bg: 'bg-orange-50', border: 'border-orange-200', icon: 'text-orange-600' },
  blue:   { bg: 'bg-blue-50',   border: 'border-blue-200',   icon: 'text-blue-600' },
  green:  { bg: 'bg-green-50',  border: 'border-green-200',  icon: 'text-green-600' },
  yellow: { bg: 'bg-yellow-50', border: 'border-yellow-200', icon: 'text-yellow-600' },
  purple: { bg: 'bg-purple-50', border: 'border-purple-200', icon: 'text-purple-600' },
}

const colors = computed(() => colorMap[props.color] ?? colorMap.blue)

// Ukuran ikon dikirim ke slot agar bisa dipakai dengan Tabler Icons (:size)
const iconSize = computed(() => isMobile.value ? 18 : undefined)
</script>

<template>
  <!-- Horizontal: ikon kiri, label + angka kanan (Admin Index pages) -->
  <div v-if="layout === 'horizontal'" class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
    <div class="flex items-center">
      <div
        class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-lg border flex items-center justify-center"
        :class="[colors.bg, colors.border]"
      >
        <slot :iconClass="colors.icon" :iconSize="iconSize" />
      </div>
      <div class="ml-3">
        <p class="text-sm sm:text-base font-medium text-slate-600">{{ label }}</p>
        <p class="text-lg/5 sm:text-xl font-bold text-slate-900">{{ value }}</p>
      </div>
    </div>
  </div>

  <!-- Vertical: label + angka besar kiri, ikon kanan + subtext di bawah (Dashboard) -->
  <div v-else class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-slate-200">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm sm:text-base font-medium text-slate-600">{{ label }}</p>
        <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ value }}</p>
        <p v-if="subtext || $slots.subtext" class="text-xs sm:text-sm" :class="subtextClass">
          <slot name="subtext">{{ subtext }}</slot>
        </p>
      </div>
      <div
        class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg border flex items-center justify-center"
        :class="[colors.bg, colors.border]"
      >
        <slot :iconClass="colors.icon" :iconSize="iconSize" />
      </div>
    </div>
  </div>
</template>
