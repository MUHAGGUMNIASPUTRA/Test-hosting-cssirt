<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  description: { type: String, default: null },
  color: { type: String, default: 'blue' },
})

const colorMap = {
  blue: {
    wrapper: 'border-blue-200 bg-blue-50',
    icon: 'text-blue-600',
  },
  green: {
    wrapper: 'border-green-200 bg-green-50',
    icon: 'text-green-600',
  },
  amber: {
    wrapper: 'border-amber-200 bg-amber-50',
    icon: 'text-amber-600',
  },
  orange: {
    wrapper: 'border-orange-200 bg-orange-50',
    icon: 'text-orange-600',
  },
  purple: {
    wrapper: 'border-purple-200 bg-purple-50',
    icon: 'text-purple-600',
  },
  indigo: {
    wrapper: 'border-indigo-200 bg-indigo-50',
    icon: 'text-indigo-600',
  },
  teal: {
    wrapper: 'border-teal-200 bg-teal-50',
    icon: 'text-teal-600',
  },
  rose: {
    wrapper: 'border-rose-200 bg-rose-50',
    icon: 'text-rose-600',
  },
  slate: {
    wrapper: 'border-slate-200 bg-slate-50',
    icon: 'text-slate-600',
  },
}

const colors = computed(() => colorMap[props.color] ?? colorMap.blue)
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5 flex items-center">
      <div
        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg border sm:h-12 sm:w-12"
        :class="colors.wrapper"
      >
        <slot name="icon" :icon-class="colors.icon" />
      </div>
      <div class="ml-3 min-w-0 flex-1">
        <h3 class="font-semibold text-slate-900">{{ title }}</h3>
        <p v-if="description" class="text-xs text-slate-600 sm:text-sm">
          {{ description }}
        </p>
      </div>
      <div v-if="$slots.extra" class="ml-3 flex-shrink-0">
        <slot name="extra" />
      </div>
    </div>
    <slot />
  </div>
</template>
