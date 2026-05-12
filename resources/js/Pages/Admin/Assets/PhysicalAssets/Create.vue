<!-- Tujuan: Form tambah/edit Aset Fisik dengan navigasi tab dan klasifikasi keamanan -->
<!-- Caller: PhysicalAssetController@create / edit -->
<!-- Side Effects: Inertia POST/PUT ke admin.physical-assets.store/update -->
<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  physicalAsset: { type: Object, default: null },
  organizations: Array,
  locations: Array,
  employees: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.physicalAsset)
const asset = props.physicalAsset
const activeTab = ref('0')

const tabFieldMap = {
  0: [
    'asset_code',
    'name',
    'description',
    'specifications',
    'year',
    'attachment',
  ],
  1: ['location_id', 'owner_org_id', 'owner_employee_id'],
  2: ['security'],
}

const attachmentMode = ref(asset?.attachment?.type ?? 'file')

const form = useForm({
  asset_code: asset?.asset_code ?? '',
  name: asset?.name ?? '',
  description: asset?.description ?? '',
  specifications: asset?.specifications ?? '',
  year: asset?.year ?? '',
  attachment_type: asset?.attachment?.type ?? null,
  attachment: null,
  attachment_link:
    asset?.attachment?.type === 'link' ? (asset.attachment.url ?? '') : '',
  location_id: asset?.location_id ?? '',
  owner_org_id: asset?.owner_org_id ?? '',
  owner_contact_type: asset?.owner_contact_type ?? 'auto',
  owner_employee_id: asset?.owner_employee_id ?? null,
  security: {
    confidentiality: asset?.security_classification?.confidentiality ?? null,
    integrity: asset?.security_classification?.integrity ?? null,
    availability: asset?.security_classification?.availability ?? null,
  },
})

watch(
  () => form.errors,
  (errors) => {
    const errorKeys = Object.keys(errors)
    if (!errorKeys.length) return
    for (const [tabIndex, fields] of Object.entries(tabFieldMap)) {
      if (fields.some((f) => errorKeys.some((k) => k.startsWith(f)))) {
        activeTab.value = String(tabIndex)
        break
      }
    }
  },
)

const locationOptions = computed(() =>
  (props.locations ?? []).map((l) => ({ label: l.name, value: l.id })),
)
const orgOptions = computed(() =>
  (props.organizations ?? []).map((o) => ({
    label: o.name,
    value: o.id,
    it_contact_name: o.it_contact_name,
    it_contact_phone: o.it_contact_phone,
  })),
)
const employeeOptions = computed(() =>
  (props.employees ?? []).map((e) => ({ label: e.name, value: e.id })),
)

const ownerOrg = computed(() =>
  props.organizations?.find((o) => o.id === form.owner_org_id),
)

const setAttachmentMode = (mode) => {
  attachmentMode.value = mode
  form.attachment_type = mode
  form.attachment = null
  form.attachment_link = ''
}

const fileInput = ref(null)
const handleFileChange = (e) => {
  form.attachment = e.target.files[0] ?? null
}

const submit = () => {
  if (isEdit.value) {
    form
      .transform((data) => ({ ...data, _method: 'PUT' }))
      .post(route('admin.physical-assets.update', asset.id), {
        forceFormData: true,
      })
  } else {
    form.post(route('admin.physical-assets.store'), {
      forceFormData: true,
    })
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Aset Fisik' : 'Tambah Aset Fisik'">
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="isEdit ? 'Edit Aset Fisik' : 'Tambah Aset Fisik'"
        :description="
          isEdit
            ? 'Perbarui data aset fisik.'
            : 'Tambahkan data aset fisik baru.'
        "
        back-route="admin.physical-assets.index"
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

      <Tabs v-model:value="activeTab">
        <TabList>
          <Tab value="0">Utama</Tab>
          <Tab value="1">Kepemilikan</Tab>
          <Tab value="2">Keamanan</Tab>
        </TabList>
        <TabPanels>
          <!-- Tab 0: Utama -->
          <TabPanel value="0" class="space-y-4">
            <!-- Informasi Aset Fisik -->
            <AdminFormSection
              title="Informasi Aset Fisik"
              description="Kode, nama, dan spesifikasi perangkat"
              color="slate"
            >
              <template #icon="{ iconClass }"
                ><IconServer :class="iconClass"
              /></template>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Kode Aset <span class="text-red-500">*</span></label
                  >
                  <InputText
                    v-model="form.asset_code"
                    class="w-full"
                    placeholder="Contoh: FISIK-001"
                    required
                  />
                  <p
                    v-if="form.errors.asset_code"
                    class="mt-1 text-xs text-red-600"
                  >
                    {{ form.errors.asset_code }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Nama Aset <span class="text-red-500">*</span></label
                  >
                  <InputText
                    v-model="form.name"
                    class="w-full"
                    placeholder="Nama perangkat"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Tahun Pengadaan</label
                  >
                  <InputText
                    v-model="form.year"
                    class="w-full"
                    type="number"
                    placeholder="Contoh: 2023"
                    min="1900"
                    max="2100"
                  />
                  <p v-if="form.errors.year" class="mt-1 text-xs text-red-600">
                    {{ form.errors.year }}
                  </p>
                </div>
                <div class="sm:col-span-2">
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Deskripsi</label
                  >
                  <Textarea
                    v-model="form.description"
                    class="w-full"
                    rows="2"
                    placeholder="Deskripsi singkat..."
                  />
                </div>
                <div class="sm:col-span-2">
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Spesifikasi</label
                  >
                  <Textarea
                    v-model="form.specifications"
                    class="w-full"
                    rows="3"
                    placeholder="Contoh: Intel Core i5, 8GB RAM, 256GB SSD..."
                  />
                </div>
              </div>
            </AdminFormSection>

            <!-- Lampiran -->
            <AdminFormSection
              title="Lampiran"
              description="Dokumen atau tautan terkait aset"
              color="blue"
            >
              <template #icon="{ iconClass }"
                ><IconPaperclip :class="iconClass"
              /></template>
              <div
                class="mb-3 flex w-fit overflow-hidden rounded-lg border border-slate-300"
              >
                <button
                  type="button"
                  @click="setAttachmentMode('file')"
                  class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium transition-colors"
                  :class="
                    attachmentMode === 'file'
                      ? 'bg-blue-600 text-white'
                      : 'bg-white text-slate-600 hover:bg-slate-50'
                  "
                >
                  <IconUpload size="14" /> Upload File
                </button>
                <button
                  type="button"
                  @click="setAttachmentMode('link')"
                  class="flex items-center gap-1.5 border-l border-slate-300 px-3 py-1.5 text-sm font-medium transition-colors"
                  :class="
                    attachmentMode === 'link'
                      ? 'bg-blue-600 text-white'
                      : 'bg-white text-slate-600 hover:bg-slate-50'
                  "
                >
                  <IconLink size="14" /> Link
                </button>
              </div>
              <div
                v-if="
                  isEdit &&
                  asset.attachment &&
                  attachmentMode === 'file' &&
                  !form.attachment
                "
                class="mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"
              >
                <IconPaperclip size="16" class="flex-shrink-0 text-slate-400" />
                <span class="text-slate-600">{{
                  asset.attachment.filename || asset.attachment.url
                }}</span>
                <span class="ml-auto text-xs text-slate-400"
                  >Upload baru untuk mengganti</span
                >
              </div>
              <div v-if="attachmentMode === 'file'">
                <input
                  ref="fileInput"
                  type="file"
                  class="hidden"
                  @change="handleFileChange"
                />
                <div
                  class="cursor-pointer rounded-lg border-2 border-dashed border-slate-300 p-6 text-center transition hover:border-blue-400"
                  @click="fileInput?.click()"
                >
                  <p
                    v-if="form.attachment"
                    class="text-sm font-medium text-slate-700"
                  >
                    {{ form.attachment.name }}
                  </p>
                  <template v-else>
                    <IconCloudUpload
                      size="28"
                      class="mx-auto mb-2 text-slate-400"
                    />
                    <p class="text-sm text-slate-500">
                      Klik untuk memilih file
                    </p>
                  </template>
                </div>
              </div>
              <div v-else>
                <InputText
                  v-model="form.attachment_link"
                  class="w-full"
                  placeholder="https://..."
                  type="url"
                />
                <p
                  v-if="form.errors.attachment_link"
                  class="mt-1 text-xs text-red-600"
                >
                  {{ form.errors.attachment_link }}
                </p>
              </div>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 1: Kepemilikan -->
          <TabPanel value="1" class="space-y-4">
            <AdminFormSection
              title="Penempatan & Kepemilikan"
              description="Lokasi dan pemilik aset"
              color="green"
            >
              <template #icon="{ iconClass }"
                ><IconBuilding :class="iconClass"
              /></template>
              <div class="space-y-4">
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
                <div>
                  <label class="mb-2 block text-sm font-medium text-slate-700"
                    >Kontak Penanggung Jawab</label
                  >
                  <div class="flex flex-wrap gap-2">
                    <Button
                      type="button"
                      size="small"
                      :severity="
                        form.owner_contact_type === 'auto'
                          ? 'primary'
                          : 'secondary'
                      "
                      :variant="
                        form.owner_contact_type === 'auto'
                          ? undefined
                          : 'outlined'
                      "
                      @click="form.owner_contact_type = 'auto'"
                      >Otomatis (IT Contact Org)</Button
                    >
                    <Button
                      type="button"
                      size="small"
                      :severity="
                        form.owner_contact_type === 'manual'
                          ? 'primary'
                          : 'secondary'
                      "
                      :variant="
                        form.owner_contact_type === 'manual'
                          ? undefined
                          : 'outlined'
                      "
                      @click="form.owner_contact_type = 'manual'"
                      >Manual (Pilih Pegawai)</Button
                    >
                  </div>
                </div>
                <div v-if="form.owner_contact_type === 'auto'">
                  <div
                    v-if="ownerOrg?.it_contact_name"
                    class="rounded-lg bg-slate-50 p-3 text-sm"
                  >
                    <p class="font-medium text-slate-700">
                      {{ ownerOrg.it_contact_name }}
                    </p>
                    <p v-if="ownerOrg.it_contact_phone" class="text-slate-500">
                      {{ ownerOrg.it_contact_phone }}
                    </p>
                  </div>
                  <p v-else class="text-sm text-slate-400">
                    {{
                      ownerOrg
                        ? 'Organisasi belum memiliki IT Contact.'
                        : 'Pilih pemilik aset terlebih dahulu.'
                    }}
                  </p>
                </div>
                <div v-if="form.owner_contact_type === 'manual'">
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Pegawai Penanggung Jawab
                    <span class="text-red-500">*</span></label
                  >
                  <Select
                    v-model="form.owner_employee_id"
                    :options="employeeOptions"
                    option-label="label"
                    option-value="value"
                    placeholder="Pilih pegawai"
                    class="w-full"
                    show-clear
                    filter
                  />
                  <p
                    v-if="form.errors.owner_employee_id"
                    class="mt-1 text-xs text-red-600"
                  >
                    {{ form.errors.owner_employee_id }}
                  </p>
                </div>
              </div>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 2: Keamanan -->
          <TabPanel value="2" class="space-y-4">
            <SecurityClassificationForm
              :model-value="form.security"
              asset-type="physical-asset"
              :asset-id="asset?.id ?? null"
              :security-notes="physicalAsset?.security_notes ?? []"
              @update:model-value="(v) => (form.security = v)"
            />
          </TabPanel>
        </TabPanels>
      </Tabs>
    </form>
  </AdminLayout>
</template>
