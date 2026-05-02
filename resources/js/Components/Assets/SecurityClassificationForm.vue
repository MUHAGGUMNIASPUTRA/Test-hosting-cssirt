<script setup>
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      confidentiality: null,
      integrity: null,
      availability: null,
    }),
  },
  errors: { type: Object, default: () => ({}) },
  assetType: { type: String, default: null },
  assetId: { type: String, default: null },
  securityNotes: { type: Array, default: () => [] },
  readonlyScores: { type: Boolean, default: false },
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

const assetPropMap = {
  license: 'license',
  'web-application': 'webApplication',
  'mobile-application': 'mobileApplication',
}
const handleRefresh = () => {
  const only = assetPropMap[props.assetType]
  router.reload(only ? { only: [only] } : undefined)
}
</script>

<template>
  <AdminFormSection
    title="Klasifikasi Keamanan"
    description="Skor CIA dan catatan keamanan aset"
    color="rose"
  >
    <template #icon="{ iconClass }">
      <IconShieldLock :class="iconClass" />
    </template>

    <template #extra>
      <Tag
        v-if="total !== null"
        :value="`Skor: ${total}/15`"
        :severity="totalSeverity"
      />
    </template>

    <div class="space-y-5">
      <!-- CIA Score buttons / read-only display -->
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
        <!-- Read-only mode -->
        <div v-if="readonlyScores" class="flex gap-2">
          <div
            v-for="score in scoreOptions"
            :key="score"
            class="flex h-9 w-full items-center justify-center rounded-lg border text-sm font-medium"
            :class="
              modelValue[dim.key] === score
                ? 'border-primary-500 bg-primary-500 !text-white'
                : 'border-slate-100 bg-slate-50 text-slate-300'
            "
          >
            {{ score }}
          </div>
        </div>
        <!-- Editable mode -->
        <div v-else class="flex gap-2">
          <button
            v-for="score in scoreOptions"
            :key="score"
            type="button"
            class="flex h-9 w-full items-center justify-center rounded-lg border text-sm font-medium transition-colors"
            :class="
              modelValue[dim.key] === score
                ? 'border-primary-500 bg-primary-500 !text-white'
                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50'
            "
            @click="update(dim.key, score)"
          >
            <span>{{ score }}</span>
          </button>
        </div>
      </div>

      <!-- Security Notes -->
      <div>
        <h4 class="mb-3 text-sm font-semibold text-slate-700">
          Catatan Keamanan
        </h4>

        <p v-if="!assetId" class="text-sm text-slate-400">
          Catatan tersedia setelah data disimpan.
        </p>

        <template v-else>
          <div v-if="securityNotes.length > 0" class="mb-4 space-y-3">
            <SecurityNoteItem
              v-for="note in securityNotes"
              :key="note.id"
              :note="note"
              @refresh="handleRefresh"
            />
          </div>

          <SecurityNoteAddForm
            :asset-type="assetType"
            :asset-id="assetId"
            @refresh="handleRefresh"
          />
        </template>
      </div>
    </div>
  </AdminFormSection>
</template>
