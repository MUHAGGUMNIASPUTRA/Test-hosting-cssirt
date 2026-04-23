<!-- Tujuan: Section spesifikasi jaringan dengan picker IP Address dan Subdomain dari master data -->
<!-- Caller: WebApplications/Create.vue -->
<!-- Side Effects: emit update:modelValue, emit refresh -->
<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) },
  ipAddresses: { type: Array, default: () => [] },
  subdomains: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue', 'refresh'])

const ipPickerIndex = ref(null)
const subdomainPickerIndex = ref(null)

const addNetwork = () =>
  emit('update:modelValue', [
    ...props.modelValue,
    {
      environment: '',
      ip_address_id: null,
      subdomain_id: null,
      _ip: null,
      _subdomain: null,
    },
  ])

const removeNetwork = (index) =>
  emit(
    'update:modelValue',
    props.modelValue.filter((_, i) => i !== index),
  )

const updateField = (index, key, val) => {
  const updated = props.modelValue.map((net, i) =>
    i === index ? { ...net, [key]: val } : net,
  )
  emit('update:modelValue', updated)
}

const onIpSelect = (index, ipObj) => {
  const updated = props.modelValue.map((net, i) =>
    i === index ? { ...net, ip_address_id: ipObj.id, _ip: ipObj } : net,
  )
  emit('update:modelValue', updated)
  ipPickerIndex.value = null
}

const clearIp = (index) => {
  const updated = props.modelValue.map((net, i) =>
    i === index ? { ...net, ip_address_id: null, _ip: null } : net,
  )
  emit('update:modelValue', updated)
}

const onSubdomainSelect = (index, subObj) => {
  const updated = props.modelValue.map((net, i) =>
    i === index ? { ...net, subdomain_id: subObj.id, _subdomain: subObj } : net,
  )
  emit('update:modelValue', updated)
  subdomainPickerIndex.value = null
}

const clearSubdomain = (index) => {
  const updated = props.modelValue.map((net, i) =>
    i === index ? { ...net, subdomain_id: null, _subdomain: null } : net,
  )
  emit('update:modelValue', updated)
}

const resolveIp = (net) =>
  net._ip ?? props.ipAddresses.find((ip) => ip.id === net.ip_address_id) ?? null

const resolveSubdomain = (net) =>
  net._subdomain ??
  props.subdomains.find((s) => s.id === net.subdomain_id) ??
  null
</script>

<template>
  <AdminFormSection
    title="Spesifikasi Jaringan"
    description="IP Address dan subdomain per environment"
    color="cyan"
  >
    <template #icon="{ iconClass }"
      ><IconNetwork :class="iconClass"
    /></template>
    <div class="mb-3 flex justify-end">
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
      Belum ada jaringan. Klik "Tambah Jaringan" untuk menambahkan.
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
            >
              Jaringan {{ index + 1 }}
            </span>
            <Tag
              v-if="index === 0"
              value="Utama"
              severity="info"
              class="!text-xs"
            />
          </div>
          <Button
            v-if="index !== 0"
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

        <div class="space-y-3">
          <!-- Environment -->
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">
              Environment
              <span v-if="index === 0" class="text-red-500">*</span>
            </label>
            <InputText
              :model-value="net.environment"
              class="w-full"
              placeholder="Contoh: Production, Staging"
              @update:model-value="updateField(index, 'environment', $event)"
            />
            <p
              v-if="index === 0 && errors['networks.0.environment']"
              class="mt-1 text-xs text-red-600"
            >
              {{ errors['networks.0.environment'] }}
            </p>
          </div>

          <!-- IP Address picker -->
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">
              IP Address
              <span v-if="index === 0" class="text-red-500">*</span>
            </label>
            <div
              v-if="resolveIp(net)"
              class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2"
            >
              <IconServer size="14" class="shrink-0 text-slate-400" />
              <div class="flex-1 text-sm">
                <span class="font-mono font-medium text-slate-800">{{
                  resolveIp(net).private_ip
                }}</span>
                <span
                  v-if="resolveIp(net).public_ip"
                  class="ml-2 text-xs text-slate-500"
                >
                  (Publik: {{ resolveIp(net).public_ip }})
                </span>
              </div>
              <button
                type="button"
                class="text-xs text-blue-600 hover:underline"
                @click="ipPickerIndex = index"
              >
                Ganti
              </button>
              <button
                type="button"
                class="text-xs text-red-500 hover:underline"
                @click="clearIp(index)"
              >
                Hapus
              </button>
            </div>
            <Button
              v-else
              type="button"
              size="small"
              severity="secondary"
              variant="outlined"
              class="w-full"
              @click="ipPickerIndex = index"
            >
              <IconServer size="14" class="mr-1" />Pilih IP Address
            </Button>
            <p
              v-if="index === 0 && errors['networks.0.ip_address_id']"
              class="mt-1 text-xs text-red-600"
            >
              {{ errors['networks.0.ip_address_id'] }}
            </p>
          </div>

          <!-- Subdomain picker -->
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600"
              >Subdomain / Domain</label
            >
            <div
              v-if="resolveSubdomain(net)"
              class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2"
            >
              <IconWorldWww size="14" class="shrink-0 text-slate-400" />
              <span class="flex-1 text-sm font-medium text-slate-800">{{
                resolveSubdomain(net).subdomain
              }}</span>
              <button
                type="button"
                class="text-xs text-blue-600 hover:underline"
                @click="subdomainPickerIndex = index"
              >
                Ganti
              </button>
              <button
                type="button"
                class="text-xs text-red-500 hover:underline"
                @click="clearSubdomain(index)"
              >
                Hapus
              </button>
            </div>
            <Button
              v-else
              type="button"
              size="small"
              severity="secondary"
              variant="outlined"
              class="w-full"
              @click="subdomainPickerIndex = index"
            >
              <IconWorldWww size="14" class="mr-1" />Pilih Subdomain
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AdminFormSection>

  <!-- IP Picker Dialog -->
  <NetworkIpPickerDialog
    :visible="ipPickerIndex !== null"
    :ip-addresses="ipAddresses"
    @update:visible="ipPickerIndex = null"
    @select="onIpSelect(ipPickerIndex, $event)"
    @refresh="$emit('refresh', 'ipAddresses')"
  />

  <!-- Subdomain Picker Dialog -->
  <NetworkSubdomainPickerDialog
    :visible="subdomainPickerIndex !== null"
    :subdomains="subdomains"
    @update:visible="subdomainPickerIndex = null"
    @select="onSubdomainSelect(subdomainPickerIndex, $event)"
    @refresh="$emit('refresh', 'subdomains')"
  />
</template>
