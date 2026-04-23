<!-- Tujuan: Dialog picker untuk memilih IpAddress dari master data, dengan opsi tambah baru -->
<!-- Caller: NetworkSpecSection -->
<!-- Side Effects: emit select (pilih), emit refresh (setelah tambah baru) -->
<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  visible: { type: Boolean, default: false },
  ipAddresses: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:visible', 'select', 'refresh'])

const search = ref('')
const selectedId = ref(null)
const showAddDialog = ref(false)

const filtered = computed(() => {
  const q = search.value.toLowerCase()
  if (!q) return props.ipAddresses
  return props.ipAddresses.filter(
    (ip) =>
      ip.private_ip?.toLowerCase().includes(q) ||
      ip.public_ip?.toLowerCase().includes(q) ||
      ip.description?.toLowerCase().includes(q),
  )
})

const selected = computed(() =>
  props.ipAddresses.find((ip) => ip.id === selectedId.value),
)

const confirm = () => {
  if (!selected.value) return
  emit('select', selected.value)
  emit('update:visible', false)
  selectedId.value = null
  search.value = ''
}

const cancel = () => {
  emit('update:visible', false)
  selectedId.value = null
  search.value = ''
}

const onAddSaved = () => {
  showAddDialog.value = false
  emit('refresh')
}
</script>

<template>
  <Dialog
    :visible="visible"
    modal
    header="Pilih IP Address"
    :style="{ width: '480px' }"
    @update:visible="$emit('update:visible', $event)"
  >
    <div class="space-y-3">
      <IconField class="w-full">
        <InputIcon><i class="pi pi-search" /></InputIcon>
        <InputText
          v-model="search"
          placeholder="Cari IP atau deskripsi..."
          class="w-full"
        />
      </IconField>

      <div
        v-if="filtered.length === 0"
        class="py-6 text-center text-sm text-slate-400"
      >
        Tidak ada IP address ditemukan.
      </div>

      <div v-else class="max-h-64 space-y-1 overflow-y-auto">
        <label
          v-for="ip in filtered"
          :key="ip.id"
          class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition"
          :class="
            selectedId === ip.id
              ? 'border-blue-400 bg-blue-50'
              : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
          "
        >
          <RadioButton
            v-model="selectedId"
            :value="ip.id"
            class="mt-0.5 shrink-0"
          />
          <div class="min-w-0 flex-1 text-sm">
            <p class="font-mono font-medium text-slate-800">
              {{ ip.private_ip }}
            </p>
            <p v-if="ip.public_ip" class="text-xs text-slate-500">
              Publik: {{ ip.public_ip }}
            </p>
            <p v-if="ip.description" class="mt-0.5 text-xs text-slate-400">
              {{ ip.description }}
            </p>
          </div>
        </label>
      </div>

      <Button
        type="button"
        size="small"
        severity="secondary"
        variant="outlined"
        class="w-full"
        @click="showAddDialog = true"
      >
        <IconPlus size="14" class="mr-1" />Tambah IP Address Baru
      </Button>
    </div>

    <template #footer>
      <Button severity="secondary" @click="cancel">Batal</Button>
      <Button :disabled="!selectedId" @click="confirm">Pilih</Button>
    </template>
  </Dialog>

  <IpAddressFormDialog
    v-if="showAddDialog"
    :visible="showAddDialog"
    @update:visible="showAddDialog = $event"
    @saved="onAddSaved"
  />
</template>
