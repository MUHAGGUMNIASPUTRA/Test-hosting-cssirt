<!-- Tujuan: Form tambah/edit Lisensi dengan navigasi tab -->
<!-- Caller: LicenseController@create / edit -->
<!-- Side Effects: Inertia POST/PUT ke admin.licenses.store/update -->
<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  license: { type: Object, default: null },
  organizations: Array,
  locations: Array,
  employees: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.license)
const lic = props.license
const activeTab = ref('0')

const tabFieldMap = {
  0: ['name', 'description', 'version', 'expired_at', 'is_active'],
  1: ['location_id', 'provider_org_id', 'owner_org_id', 'owner_employee_id'],
  2: ['security'],
}

const form = useForm({
  name: lic?.name ?? '',
  description: lic?.description ?? '',
  is_active: lic?.is_active ?? true,
  version: lic?.version ?? '',
  expired_at: lic?.expired_at ?? '',
  location_id: lic?.location_id ?? '',
  provider_org_id: lic?.provider_org_id ?? '',
  owner_org_id: lic?.owner_org_id ?? '',
  owner_contact_type: lic?.owner_contact_type ?? 'auto',
  owner_employee_id: lic?.owner_employee_id ?? null,
  security: {
    confidentiality: lic?.security_classification?.confidentiality ?? null,
    integrity: lic?.security_classification?.integrity ?? null,
    availability: lic?.security_classification?.availability ?? null,
    notes: lic?.security_classification?.notes ?? '',
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

const ownerData = computed(() => ({
  location_id: form.location_id,
  provider_org_id: form.provider_org_id,
  owner_org_id: form.owner_org_id,
  owner_contact_type: form.owner_contact_type,
  owner_employee_id: form.owner_employee_id,
  vendor_id: null,
}))

const updateOwner = (val) => {
  form.location_id = val.location_id
  form.provider_org_id = val.provider_org_id
  form.owner_org_id = val.owner_org_id
  form.owner_contact_type = val.owner_contact_type
  form.owner_employee_id = val.owner_employee_id
}

const submit = () => {
  if (isEdit.value) form.put(route('admin.licenses.update', lic.id))
  else form.post(route('admin.licenses.store'))
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Lisensi' : 'Tambah Lisensi'">
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="isEdit ? 'Edit Lisensi' : 'Tambah Lisensi'"
        :description="
          isEdit
            ? 'Perbarui data lisensi perangkat lunak.'
            : 'Tambahkan data lisensi perangkat lunak baru.'
        "
        back-route="admin.licenses.index"
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
            <AdminFormSection
              title="Informasi Lisensi"
              description="Nama, versi, dan tanggal kedaluwarsa"
              color="purple"
            >
              <template #icon="{ iconClass }"
                ><IconKey :class="iconClass"
              /></template>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Nama Lisensi <span class="text-red-500">*</span></label
                  >
                  <InputText
                    v-model="form.name"
                    class="w-full"
                    placeholder="Nama lisensi"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Versi</label
                  >
                  <InputText
                    v-model="form.version"
                    class="w-full"
                    placeholder="Contoh: 2024.1"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Tanggal Kedaluwarsa</label
                  >
                  <InputText
                    v-model="form.expired_at"
                    class="w-full"
                    type="date"
                  />
                  <p
                    v-if="form.errors.expired_at"
                    class="mt-1 text-xs text-red-600"
                  >
                    {{ form.errors.expired_at }}
                  </p>
                </div>
                <div class="sm:col-span-2">
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Deskripsi</label
                  >
                  <Textarea
                    v-model="form.description"
                    class="w-full"
                    rows="3"
                    placeholder="Deskripsi lisensi..."
                  />
                </div>
              </div>
            </AdminFormSection>
            <AdminFormSection
              title="Status"
              description="Aktif / tidak aktif"
              color="teal"
            >
              <template #icon="{ iconClass }"
                ><IconCircleCheck :class="iconClass"
              /></template>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-700">Lisensi Aktif</span>
                <ToggleSwitch v-model="form.is_active" />
              </div>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 1: Kepemilikan -->
          <TabPanel value="1">
            <OwnerContactSection
              :organizations="organizations"
              :locations="locations"
              :employees="employees"
              :vendors="null"
              :model-value="ownerData"
              :errors="form.errors"
              @update:model-value="updateOwner"
            />
          </TabPanel>

          <!-- Tab 2: Keamanan -->
          <TabPanel value="2">
            <SecurityClassificationForm
              :model-value="form.security"
              asset-type="license"
              :asset-id="lic?.id ?? null"
              :security-notes="license?.security_notes ?? []"
              @update:model-value="(v) => (form.security = v)"
            />
          </TabPanel>
        </TabPanels>
      </Tabs>
    </form>
  </AdminLayout>
</template>
