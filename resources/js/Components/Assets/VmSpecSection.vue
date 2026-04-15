<script setup>
const props = defineProps({
  modelValue: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue'])

const addVm = () =>
  emit('update:modelValue', [
    ...props.modelValue,
    { processor: '', ram: '', hdd: '' },
  ])

const removeVm = (index) =>
  emit(
    'update:modelValue',
    props.modelValue.filter((_, i) => i !== index),
  )

const updateVm = (index, key, val) => {
  const updated = props.modelValue.map((vm, i) =>
    i === index ? { ...vm, [key]: val } : vm,
  )
  emit('update:modelValue', updated)
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">Spesifikasi VM</h3>
      <Button
        type="button"
        size="small"
        severity="secondary"
        variant="outlined"
        @click="addVm"
      >
        <IconPlus size="14" class="mr-1" />Tambah VM
      </Button>
    </div>

    <div
      v-if="modelValue.length === 0"
      class="py-4 text-center text-sm text-slate-400"
    >
      Belum ada VM. Klik "Tambah VM" untuk menambahkan.
    </div>

    <div class="space-y-3">
      <div
        v-for="(vm, index) in modelValue"
        :key="index"
        class="relative rounded-lg border border-slate-200 bg-slate-50 p-4"
      >
        <div class="mb-2 flex items-center justify-between">
          <span
            class="text-xs font-semibold uppercase tracking-wider text-slate-500"
            >VM {{ index + 1 }}</span
          >
          <Button
            type="button"
            size="small"
            severity="danger"
            variant="text"
            class="!p-0"
            @click="removeVm(index)"
          >
            <IconTrash size="14" />
          </Button>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600"
              >Processor</label
            >
            <InputText
              :model-value="vm.processor"
              class="w-full"
              placeholder="Contoh: 4 vCPU"
              @update:model-value="updateVm(index, 'processor', $event)"
            />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600"
              >RAM</label
            >
            <InputText
              :model-value="vm.ram"
              class="w-full"
              placeholder="Contoh: 8 GB"
              @update:model-value="updateVm(index, 'ram', $event)"
            />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600"
              >HDD / Storage</label
            >
            <InputText
              :model-value="vm.hdd"
              class="w-full"
              placeholder="Contoh: 100 GB SSD"
              @update:model-value="updateVm(index, 'hdd', $event)"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
