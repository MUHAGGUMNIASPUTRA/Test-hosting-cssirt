<!-- Tujuan: Input pencarian dan seleksi aset virtual terdampak insiden -->
<!-- Caller: Admin/Incidents/Create.vue -->
<!-- Side Effects: axios GET /api/virtual-assets -->
<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue'])

const assetSearch = ref('')
const assetResults = ref([])
const assetLoading = ref(false)
let debounceTimer = null

const getAssetTypeLabel = (type) =>
  type === 'web-application' ? 'Web' : 'Mobile'

const searchAssets = async () => {
  if (assetSearch.value.length < 2) {
    assetResults.value = []
    return
  }
  assetLoading.value = true
  try {
    const { data } = await axios.get('/api/virtual-assets', {
      params: { search: assetSearch.value },
    })
    assetResults.value = data.filter(
      (a) => !props.modelValue.some((s) => s.id === a.id),
    )
  } catch {
    assetResults.value = []
  } finally {
    assetLoading.value = false
  }
}

watch(assetSearch, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(searchAssets, 300)
})

const selectAsset = (asset) => {
  if (!props.modelValue.some((a) => a.id === asset.id)) {
    emit('update:modelValue', [
      ...props.modelValue,
      { id: asset.id, name: asset.name, asset_type: asset.asset_type },
    ])
  }
  assetSearch.value = ''
  assetResults.value = []
}

const removeAsset = (id) => {
  emit(
    'update:modelValue',
    props.modelValue.filter((a) => a.id !== id),
  )
}
</script>

<template>
  <AdminFormSection
    title="Aset Virtual Terdampak"
    description="Aplikasi web atau mobile yang terdampak insiden ini (opsional)"
    color="indigo"
  >
    <template #icon="{ iconClass }">
      <IconCloud :class="iconClass" />
    </template>
    <div v-if="modelValue.length" class="mb-3 flex flex-wrap gap-2">
      <div
        v-for="asset in modelValue"
        :key="asset.id"
        class="flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-sm"
      >
        <span class="text-xs font-semibold text-indigo-400">{{
          getAssetTypeLabel(asset.asset_type)
        }}</span>
        <span class="text-slate-700">{{ asset.name }}</span>
        <button
          type="button"
          class="ml-1 text-slate-400 hover:text-red-500"
          @click="removeAsset(asset.id)"
        >
          <IconX size="12" />
        </button>
      </div>
    </div>
    <div class="relative">
      <IconField class="w-full">
        <InputIcon>
          <IconLoader3
            v-if="assetLoading"
            size="14"
            class="animate-spin text-slate-400"
          />
          <i v-else class="pi pi-search" />
        </InputIcon>
        <InputText
          v-model="assetSearch"
          placeholder="Cari nama aplikasi web atau mobile..."
          class="w-full"
        />
      </IconField>
      <div
        v-if="assetResults.length"
        class="absolute z-10 mt-1 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg"
      >
        <button
          v-for="asset in assetResults"
          :key="asset.id"
          type="button"
          class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-indigo-50"
          @click="selectAsset(asset)"
        >
          <Tag
            :value="getAssetTypeLabel(asset.asset_type)"
            severity="secondary"
            size="small"
            class="!text-xs"
          />
          {{ asset.name }}
        </button>
      </div>
    </div>
    <p
      v-if="assetSearch.length >= 2 && !assetResults.length && !assetLoading"
      class="mt-2 text-xs text-slate-400"
    >
      Tidak ada hasil untuk "{{ assetSearch }}"
    </p>
  </AdminFormSection>
</template>
