<!-- Tujuan: Form tambah/edit Aset Informasi -->
<!-- Caller: InformationAssetController@create / edit -->
<!-- Side Effects: Inertia POST/PUT ke admin.information-assets.store/update -->
<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  informationAsset: { type: Object, default: null },
  documents: { type: Array, default: () => [] },
  organizations: { type: Array, default: () => [] },
  locations: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.informationAsset)
const asset = props.informationAsset

const form = useForm({
  document_id: asset?.document_id ?? null,
  storage_format: asset?.storage_format ?? '',
  location_id: asset?.location_id ?? '',
  owner_org_id: asset?.owner_org_id ?? '',
  security: {
    confidentiality: asset?.security_classification?.confidentiality ?? null,
    integrity: asset?.security_classification?.integrity ?? null,
    availability: asset?.security_classification?.availability ?? null,
  },
})

const documentOptions = computed(() =>
  props.documents.map((d) => ({ label: d.title, value: d.id })),
)
const locationOptions = computed(() =>
  props.locations.map((l) => ({ label: l.name, value: l.id })),
)
const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)

const storageFormats = [
  {
    label: 'File Dokumen',
    value: 'file_dokumen',
    description: 'Tersimpan dalam format digital',
  },
  {
    label: 'Cetak',
    value: 'cetak',
    description: 'Tersimpan dalam bentuk fisik/cetak',
  },
  {
    label: 'Keduanya',
    value: 'keduanya',
    description: 'Tersimpan dalam format digital dan fisik',
  },
]

const submit = () => {
  if (isEdit.value) {
    form.put(route('admin.information-assets.update', asset.id))
  } else {
    form.post(route('admin.information-assets.store'))
  }
}
</script>

<template>
  <AdminLayout
    :title="isEdit ? 'Edit Aset Informasi' : 'Tambah Aset Informasi'"
  >
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="isEdit ? 'Edit Aset Informasi' : 'Tambah Aset Informasi'"
        :description="
          isEdit
            ? 'Perbarui data aset informasi.'
            : 'Tambahkan data aset informasi baru.'
        "
        back-route="admin.information-assets.index"
        :processing="form.processing"
      >
        <template #actions>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <IconLoader3
              v-if="form.processing"
              class="animate-spin"
              size="16"
            />
            <IconDeviceFloppy v-else size="16" />
            {{
              form.processing ? 'Menyimpan...' : isEdit ? 'Update' : 'Simpan'
            }}
          </button>
        </template>
      </AdminFormHeader>

      <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="space-y-4 xl:col-span-2">
          <!-- Informasi Aset -->
          <AdminFormSection
            title="Informasi Aset"
            description="Dokumen referensi dan format penyimpanan"
            color="purple"
          >
            <template #icon="{ iconClass }">
              <IconDatabase :class="iconClass" />
            </template>
            <div class="space-y-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Dokumen Referensi</label
                >
                <Select
                  v-model="form.document_id"
                  :options="documentOptions"
                  option-label="label"
                  option-value="value"
                  placeholder="Pilih dokumen (opsional)"
                  class="w-full"
                  show-clear
                  filter
                />
                <p
                  v-if="form.errors.document_id"
                  class="mt-1 text-xs text-red-600"
                >
                  {{ form.errors.document_id }}
                </p>
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-slate-700"
                  >Format Penyimpanan <span class="text-red-500">*</span></label
                >
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                  <button
                    v-for="fmt in storageFormats"
                    :key="fmt.value"
                    type="button"
                    class="rounded-lg border-2 p-3 text-left transition"
                    :class="
                      form.storage_format === fmt.value
                        ? 'border-purple-500 bg-purple-50'
                        : 'border-slate-200 hover:border-slate-300'
                    "
                    @click="form.storage_format = fmt.value"
                  >
                    <p class="text-sm font-medium text-slate-800">
                      {{ fmt.label }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-500">
                      {{ fmt.description }}
                    </p>
                  </button>
                </div>
                <p
                  v-if="form.errors.storage_format"
                  class="mt-1 text-xs text-red-600"
                >
                  {{ form.errors.storage_format }}
                </p>
              </div>
            </div>
          </AdminFormSection>

          <!-- Penempatan & Kepemilikan -->
          <AdminFormSection
            title="Penempatan & Kepemilikan"
            description="Lokasi dan pemilik aset informasi"
            color="green"
          >
            <template #icon="{ iconClass }">
              <IconBuilding :class="iconClass" />
            </template>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Lokasi</label
                >
                <Select
                  v-model="form.location_id"
                  :options="locationOptions"
                  option-label="label"
                  option-value="value"
                  placeholder="Pilih lokasi"
                  class="w-full"
                  show-clear
                  filter
                />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Pemilik Aset</label
                >
                <Select
                  v-model="form.owner_org_id"
                  :options="orgOptions"
                  option-label="label"
                  option-value="value"
                  placeholder="Pilih organisasi"
                  class="w-full"
                  show-clear
                  filter
                />
              </div>
            </div>
          </AdminFormSection>
        </div>

        <div class="flex flex-col gap-4">
          <!-- Aksi mobile -->
          <div class="flex gap-3 xl:hidden">
            <a :href="route('admin.information-assets.index')" class="flex-1">
              <Button
                severity="secondary"
                variant="outlined"
                class="w-full"
                :disabled="form.processing"
                >Batal</Button
              >
            </a>
            <Button type="submit" class="flex-1" :loading="form.processing">
              {{ isEdit ? 'Simpan' : 'Tambah' }}
            </Button>
          </div>

          <!-- Klasifikasi Keamanan -->
          <SecurityClassificationForm
            :model-value="form.security"
            asset-type="information-asset"
            :asset-id="asset?.id ?? null"
            :security-notes="informationAsset?.security_notes ?? []"
            @update:model-value="(v) => (form.security = v)"
          />
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
