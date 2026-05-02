<!-- Tujuan: Form section klasifikasi dokumen (area dan stage) -->
<!-- Caller: Documents/Create.vue -->
<!-- Side Effects: emit update:modelValue -->

<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  documentAreas: {
    type: Array,
    default: () => [],
  },
  stageOptions: {
    type: Array,
    default: () => [],
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['update:modelValue'])

const updateField = (field, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}
</script>

<template>
  <!-- Area Dokumen -->
  <AdminFormSection
    title="Area Dokumen"
    description="Kategori pengelompokan"
    color="indigo"
  >
    <template #icon="{ iconClass }">
      <IconFolders :class="iconClass" />
    </template>
    <label class="mb-2 block font-medium text-gray-700">
      Pilih Area
      <span class="font-normal text-slate-400">(Opsional)</span>
    </label>
    <Select
      :model-value="modelValue.document_area_id"
      :options="documentAreas"
      optionLabel="name"
      optionValue="id"
      placeholder="— Tidak Ada Area —"
      class="w-full"
      showClear
      @update:model-value="updateField('document_area_id', $event)"
    />
    <small v-if="errors.document_area_id" class="p-error mt-1 block">
      {{ errors.document_area_id }}
    </small>
  </AdminFormSection>

  <!-- Stage -->
  <AdminFormSection
    title="Stage"
    description="Tahap pengerjaan dokumen"
    color="orange"
  >
    <template #icon="{ iconClass }">
      <IconCircleCheck :class="iconClass" />
    </template>
    <div>
      <label class="mb-2 block font-medium text-gray-700">
        Pilih Stage
        <span class="font-normal text-slate-400">(Opsional)</span>
      </label>
      <Select
        :model-value="modelValue.stage"
        :options="stageOptions"
        placeholder="— Belum Ditentukan —"
        class="w-full"
        showClear
        @update:model-value="updateField('stage', $event)"
      />
      <small v-if="errors.stage" class="p-error mt-1 block">
        {{ errors.stage }}
      </small>
    </div>
  </AdminFormSection>
</template>
