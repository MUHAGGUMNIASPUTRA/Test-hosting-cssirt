<script setup>
const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['update:modelValue'])

const addNetwork = () =>
  emit('update:modelValue', [
    ...props.modelValue,
    { environment: '', dns: '', local_ip: '', public_ip: '' },
  ])

const removeNetwork = (index) =>
  emit(
    'update:modelValue',
    props.modelValue.filter((_, i) => i !== index),
  )

const updateNetwork = (index, key, val) => {
  const updated = props.modelValue.map((net, i) =>
    i === index ? { ...net, [key]: val } : net,
  )
  emit('update:modelValue', updated)
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">Spesifikasi Jaringan</h3>
      <Button
        type="button"
        size="small"
        severity="secondary"
        variant="outlined"
        @click="addNetwork"
      >
        <IconPlus size="14" class="mr-1" />Tambah Jaringan
      </Button>
    </div>

    <div
      v-if="modelValue.length === 0"
      class="py-4 text-center text-sm text-slate-400"
    >
      Belum ada data jaringan. Klik "Tambah Jaringan" untuk menambahkan.
    </div>

    <div class="space-y-3">
      <div
        v-for="(net, index) in modelValue"
        :key="index"
        class="rounded-lg border p-4"
        :class="
          index === 0
            ? 'border-primary-200 bg-primary-50/30'
            : 'border-slate-200 bg-slate-50'
        "
      >
        <div class="mb-3 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span
              class="text-xs font-semibold uppercase tracking-wider text-slate-500"
              >Jaringan {{ index + 1 }}</span
            >
            <Tag
              v-if="index === 0"
              value="Utama"
              severity="info"
              class="!text-xs"
            />
          </div>
          <Button
            v-if="index != 0"
            type="button"
            size="small"
            severity="danger"
            variant="text"
            class="!p-0"
            @click="removeNetwork(index)"
          >
            <IconTrash size="14" />
          </Button>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">
              Environment
              <span v-if="index === 0" class="text-red-500">*</span>
            </label>
            <InputText
              :model-value="net.environment"
              class="w-full"
              placeholder="Contoh: Production, Staging"
              @update:model-value="updateNetwork(index, 'environment', $event)"
            />
            <p
              v-if="index === 0 && errors['networks.0.environment']"
              class="mt-1 text-xs text-red-600"
            >
              {{ errors['networks.0.environment'] }}
            </p>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600"
              >DNS / Domain</label
            >
            <InputText
              :model-value="net.dns"
              class="w-full"
              placeholder="Contoh: app.example.go.id"
              @update:model-value="updateNetwork(index, 'dns', $event)"
            />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">
              IP Lokal
              <span v-if="index === 0" class="text-red-500">*</span>
            </label>
            <InputText
              :model-value="net.local_ip"
              class="w-full"
              placeholder="Contoh: 192.168.1.10"
              @update:model-value="updateNetwork(index, 'local_ip', $event)"
            />
            <p
              v-if="index === 0 && errors['networks.0.local_ip']"
              class="mt-1 text-xs text-red-600"
            >
              {{ errors['networks.0.local_ip'] }}
            </p>
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600"
              >IP Publik</label
            >
            <InputText
              :model-value="net.public_ip"
              class="w-full"
              placeholder="Contoh: 103.x.x.x"
              @update:model-value="updateNetwork(index, 'public_ip', $event)"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
