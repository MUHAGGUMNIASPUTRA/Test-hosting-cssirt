<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      confidentiality: null,
      integrity: null,
      availability: null,
      notes: '',
    }),
  },
  errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['update:modelValue'])

const update = (key, val) =>
  emit('update:modelValue', { ...props.modelValue, [key]: val })

const dims = [
  { key: 'confidentiality', label: 'Kerahasiaan (C)' },
  { key: 'integrity', label: 'Integritas (I)' },
  { key: 'availability', label: 'Ketersediaan (A)' },
]

const scoreOptions = [1, 2, 3, 4, 5]

const total = computed(() => {
  const { confidentiality, integrity, availability } = props.modelValue
  if (!confidentiality && !integrity && !availability) return null
  return (confidentiality || 0) + (integrity || 0) + (availability || 0)
})

const totalSeverity = computed(() => {
  const t = total.value
  if (!t) return 'secondary'
  if (t <= 5) return 'success'
  if (t <= 10) return 'warn'
  return 'danger'
})

const scoreSeverity = (score) => {
  if (!score) return 'secondary'
  if (score <= 2) return 'success'
  if (score === 3) return 'warn'
  return 'danger'
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">Klasifikasi Keamanan</h3>
      <Tag
        v-if="total !== null"
        :value="`Skor: ${total}/15`"
        :severity="totalSeverity"
      />
    </div>
    <div class="space-y-4">
      <div v-for="dim in dims" :key="dim.key">
        <div class="mb-2 flex items-center justify-between">
          <label class="text-sm font-medium text-slate-700">{{
            dim.label
          }}</label>
          <Tag
            v-if="modelValue[dim.key]"
            :value="String(modelValue[dim.key])"
            :severity="scoreSeverity(modelValue[dim.key])"
            class="!text-xs"
          />
        </div>
        <div class="flex gap-2">
          <button
            v-for="score in scoreOptions"
            :key="score"
            type="button"
            class="flex h-9 w-full items-center justify-center rounded-lg border text-sm font-medium transition-colors"
            :class="
              modelValue[dim.key] === score
                ? 'border-primary-500 bg-primary-500 text-white'
                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50'
            "
            @click="update(dim.key, score)"
          >
            {{ score }}
          </button>
        </div>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700"
          >Catatan Keamanan</label
        >
        <Textarea
          :model-value="modelValue.notes"
          class="w-full"
          rows="2"
          placeholder="Catatan tambahan tentang keamanan aset..."
          @update:model-value="update('notes', $event)"
        />
      </div>
    </div>
  </div>
</template>
