<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    // { stage, app_status, https_status }
  },
  stageOptions: { type: Array, default: () => [] },
  appStatusOptions: { type: Array, default: () => [] },
  httpsStatusOptions: { type: Array, default: () => [] },
  showHttps: { type: Boolean, default: false },
  errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['update:modelValue'])

const update = (field, val) =>
  emit('update:modelValue', { ...props.modelValue, [field]: val })
</script>

<template>
  <AdminFormSection
    title="Status & Kondisi"
    description="Tahap pengembangan dan status operasional"
    color="amber"
  >
    <template #icon="{ iconClass }">
      <IconCircleCheck :class="iconClass" />
    </template>

    <div
      :class="
        showHttps
          ? 'grid grid-cols-1 gap-4 sm:grid-cols-3'
          : 'grid grid-cols-1 gap-4 sm:grid-cols-2'
      "
    >
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700"
          >Tahap <span class="text-red-500">*</span></label
        >
        <Select
          :model-value="modelValue.stage"
          :options="stageOptions"
          option-label="name"
          option-value="value"
          placeholder="Pilih tahap"
          class="w-full"
          @update:model-value="(v) => update('stage', v)"
        />
        <p v-if="errors.stage" class="mt-1 text-xs text-red-600">
          {{ errors.stage }}
        </p>
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700"
          >Status Aplikasi <span class="text-red-500">*</span></label
        >
        <Select
          :model-value="modelValue.app_status"
          :options="appStatusOptions"
          option-label="name"
          option-value="value"
          placeholder="Status"
          class="w-full"
          @update:model-value="(v) => update('app_status', v)"
        />
        <p v-if="errors.app_status" class="mt-1 text-xs text-red-600">
          {{ errors.app_status }}
        </p>
      </div>

      <div v-if="showHttps">
        <label class="mb-1 block text-sm font-medium text-slate-700"
          >Status HTTPS <span class="text-red-500">*</span></label
        >
        <Select
          :model-value="modelValue.https_status"
          :options="httpsStatusOptions"
          option-label="name"
          option-value="value"
          placeholder="HTTPS"
          class="w-full"
          @update:model-value="(v) => update('https_status', v)"
        />
        <p v-if="errors.https_status" class="mt-1 text-xs text-red-600">
          {{ errors.https_status }}
        </p>
      </div>
    </div>
  </AdminFormSection>
</template>
