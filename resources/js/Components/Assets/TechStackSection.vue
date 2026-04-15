<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] }, // [{ tech_stack_id, version }]
  techStacks: { type: Array, default: () => [] }, // all available stacks
})

const emit = defineEmits(['update:modelValue'])

const selectedToAdd = ref(null)

const selectedIds = computed(() => props.modelValue.map((s) => s.tech_stack_id))

const availableOptions = computed(() =>
  props.techStacks
    .filter((ts) => !selectedIds.value.includes(ts.id))
    .map((ts) => ({
      label: ts.name,
      value: ts.id,
      category: ts.category?.name ?? 'Lainnya',
    })),
)

const getStackName = (id) =>
  props.techStacks.find((ts) => ts.id === id)?.name ?? id

const addStack = () => {
  if (!selectedToAdd.value) return
  emit('update:modelValue', [
    ...props.modelValue,
    { tech_stack_id: selectedToAdd.value, version: '' },
  ])
  selectedToAdd.value = null
}

const removeStack = (index) =>
  emit(
    'update:modelValue',
    props.modelValue.filter((_, i) => i !== index),
  )

const updateVersion = (index, val) => {
  const updated = props.modelValue.map((s, i) =>
    i === index ? { ...s, version: val } : s,
  )
  emit('update:modelValue', updated)
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Tech Stack</h3>

    <div class="mb-4 flex gap-2">
      <Select
        v-model="selectedToAdd"
        :options="availableOptions"
        option-label="label"
        option-value="value"
        option-group-label="category"
        placeholder="Pilih tech stack..."
        class="flex-1"
        filter
        show-clear
      />
      <Button type="button" :disabled="!selectedToAdd" @click="addStack">
        <IconPlus size="15" class="mr-1" />Tambah
      </Button>
    </div>

    <div
      v-if="modelValue.length === 0"
      class="py-3 text-center text-sm text-slate-400"
    >
      Belum ada tech stack yang dipilih.
    </div>

    <div class="space-y-2">
      <div
        v-for="(stack, index) in modelValue"
        :key="stack.tech_stack_id"
        class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
      >
        <IconCode size="16" class="flex-shrink-0 text-slate-400" />
        <span class="flex-1 text-sm font-medium text-slate-700">{{
          getStackName(stack.tech_stack_id)
        }}</span>
        <InputText
          :model-value="stack.version"
          class="w-28 !text-xs"
          placeholder="Versi"
          @update:model-value="updateVersion(index, $event)"
        />
        <Button
          type="button"
          size="small"
          severity="danger"
          variant="text"
          class="!p-0"
          @click="removeStack(index)"
        >
          <IconX size="14" />
        </Button>
      </div>
    </div>
  </div>
</template>
