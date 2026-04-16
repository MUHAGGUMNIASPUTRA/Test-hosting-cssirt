<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] }, // [{ tech_stack_id, version }]
  techStacks: { type: Array, default: () => [] }, // all available stacks
  categories: { type: Array, default: () => [] }, // for TechStackFormDialog
})

const emit = defineEmits(['update:modelValue', 'techstack-saved', 'refresh'])

const selectedToAdd = ref(null)
const showAddDialog = ref(false)

const selectedIds = computed(() => props.modelValue.map((s) => s.tech_stack_id))

// PrimeVue grouped Select requires [{ label, items: [{ label, value }] }]
const groupedOptions = computed(() => {
  const groups = {}
  props.techStacks
    .filter((ts) => !selectedIds.value.includes(ts.id))
    .forEach((ts) => {
      const cat = ts.category?.name ?? 'Lainnya'
      if (!groups[cat]) groups[cat] = []
      groups[cat].push({ label: ts.name, value: ts.id })
    })
  return Object.entries(groups).map(([label, items]) => ({ label, items }))
})

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

const onTechStackSaved = () => {
  showAddDialog.value = false
  emit('techstack-saved')
}
</script>

<template>
  <AdminFormSection
    title="Tech Stack"
    description="Framework dan teknologi yang digunakan"
    color="indigo"
  >
    <template #icon="{ iconClass }">
      <IconCode :class="iconClass" />
    </template>

    <div class="mb-3 flex gap-2">
      <Select
        v-model="selectedToAdd"
        :options="groupedOptions"
        option-label="label"
        option-value="value"
        option-group-label="label"
        option-group-children="items"
        placeholder="Pilih tech stack..."
        class="flex-1"
        filter
        show-clear
      />
      <Button
        type="button"
        severity="secondary"
        variant="outlined"
        v-tooltip="'Refresh daftar'"
        @click="$emit('refresh')"
      >
        <IconRefresh size="15" />
      </Button>
      <Button type="button" :disabled="!selectedToAdd" @click="addStack">
        <IconPlus size="15" class="mr-1" />Tambah
      </Button>
    </div>

    <p class="mb-3 text-xs text-slate-500">
      Belum ada tech stack yang diinginkan?
      <button
        type="button"
        class="text-blue-600 hover:underline"
        @click="showAddDialog = true"
      >
        Tambahkan
      </button>
    </p>

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

    <TechStackFormDialog
      :visible="showAddDialog"
      :categories="categories"
      @update:visible="showAddDialog = $event"
      @saved="onTechStackSaved"
    />
  </AdminFormSection>
</template>
