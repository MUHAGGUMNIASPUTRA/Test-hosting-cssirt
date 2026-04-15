<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  license: { type: Object, default: null },
  organizations: Array,
  locations: Array,
  employees: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.license)
const lic = props.license

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
  if (isEdit.value) {
    form.put(route('admin.licenses.update', lic.id))
  } else {
    form.post(route('admin.licenses.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Lisensi' : 'Tambah Lisensi'">
    <div class="space-y-4">
      <AdminPageHeader
        :title="isEdit ? 'Edit Lisensi' : 'Tambah Lisensi'"
        description="Kelola data lisensi perangkat lunak."
      />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <div class="space-y-4 lg:col-span-2">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">
                Informasi Lisensi
              </h3>
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
            </div>

            <OwnerContactSection
              :organizations="organizations"
              :locations="locations"
              :employees="employees"
              :vendors="null"
              :model-value="ownerData"
              :errors="form.errors"
              @update:model-value="updateOwner"
            />
          </div>

          <div class="space-y-4">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">Status</h3>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-700">Lisensi Aktif</span>
                <ToggleSwitch v-model="form.is_active" />
              </div>
            </div>

            <div class="flex gap-3">
              <Link :href="route('admin.licenses.index')" class="flex-1">
                <Button
                  severity="secondary"
                  variant="outlined"
                  class="w-full"
                  :disabled="form.processing"
                  >Batal</Button
                >
              </Link>
              <Button type="submit" class="flex-1" :loading="form.processing">
                {{ isEdit ? 'Simpan' : 'Tambah' }}
              </Button>
            </div>

            <SecurityClassificationForm
              :model-value="form.security"
              @update:model-value="(v) => (form.security = v)"
            />
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
