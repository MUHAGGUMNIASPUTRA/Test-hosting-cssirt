<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  organizations: { type: Array, default: () => [] },
  locations: { type: Array, default: () => [] },
  employees: { type: Array, default: () => [] },
  vendors: { type: Array, default: null }, // null = don't show vendor field
  modelValue: {
    type: Object,
    default: () => ({
      location_id: '',
      provider_org_id: '',
      owner_org_id: '',
      owner_contact_type: 'auto',
      owner_employee_id: null,
      vendor_id: null,
    }),
  },
  errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits([
  'update:modelValue',
  'vendor-saved',
  'vendor-refresh',
])

const showVendorDialog = ref(false)

const update = (key, val) =>
  emit('update:modelValue', { ...props.modelValue, [key]: val })

const locationOptions = computed(() =>
  props.locations.map((l) => ({ label: l.name, value: l.id })),
)

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)

const employeeOptions = computed(() =>
  props.employees.map((e) => ({ label: e.name, value: e.id })),
)

const vendorOptions = computed(
  () =>
    props.vendors?.map((v) => ({ label: v.company_name, value: v.id })) ?? [],
)

const ownerOrg = computed(() =>
  props.organizations.find((o) => o.id === props.modelValue.owner_org_id),
)

const hasItContact = computed(
  () =>
    ownerOrg.value &&
    (ownerOrg.value.it_contact_name ||
      ownerOrg.value.it_contact_phone ||
      ownerOrg.value.it_contact_email),
)
</script>

<template>
  <AdminFormSection
    title="Penempatan & Kepemilikan"
    description="Lokasi, organisasi, dan kontak penanggung jawab"
    color="green"
  >
    <template #icon="{ iconClass }">
      <IconBuilding :class="iconClass" />
    </template>

    <div class="space-y-4">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700"
          >Lokasi <span class="text-red-500">*</span></label
        >
        <Select
          :model-value="modelValue.location_id"
          :options="locationOptions"
          option-label="label"
          option-value="value"
          placeholder="Pilih lokasi"
          class="w-full"
          show-clear
          filter
          @update:model-value="update('location_id', $event)"
        />
        <p v-if="errors.location_id" class="mt-1 text-xs text-red-600">
          {{ errors.location_id }}
        </p>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700"
            >Penyedia Aset <span class="text-red-500">*</span></label
          >
          <Select
            :model-value="modelValue.provider_org_id"
            :options="orgOptions"
            option-label="label"
            option-value="value"
            placeholder="Pilih organisasi"
            class="w-full"
            show-clear
            filter
            @update:model-value="update('provider_org_id', $event)"
          />
          <p v-if="errors.provider_org_id" class="mt-1 text-xs text-red-600">
            {{ errors.provider_org_id }}
          </p>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700"
            >Pemilik Aset <span class="text-red-500">*</span></label
          >
          <Select
            :model-value="modelValue.owner_org_id"
            :options="orgOptions"
            option-label="label"
            option-value="value"
            placeholder="Pilih organisasi"
            class="w-full"
            show-clear
            filter
            @update:model-value="update('owner_org_id', $event)"
          />
          <p v-if="errors.owner_org_id" class="mt-1 text-xs text-red-600">
            {{ errors.owner_org_id }}
          </p>
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
              modelValue.owner_contact_type === 'auto' ? 'primary' : 'secondary'
            "
            :variant="
              modelValue.owner_contact_type === 'auto' ? undefined : 'outlined'
            "
            @click="update('owner_contact_type', 'auto')"
            >Otomatis (IT Contact Org)</Button
          >
          <Button
            type="button"
            size="small"
            :severity="
              modelValue.owner_contact_type === 'manual'
                ? 'primary'
                : 'secondary'
            "
            :variant="
              modelValue.owner_contact_type === 'manual'
                ? undefined
                : 'outlined'
            "
            @click="update('owner_contact_type', 'manual')"
            >Manual (Pilih Pegawai)</Button
          >
        </div>
      </div>

      <div v-if="modelValue.owner_contact_type === 'auto'">
        <div
          v-if="ownerOrg && hasItContact"
          class="rounded-lg bg-slate-50 p-3 text-sm"
        >
          <p class="font-medium text-slate-700">
            {{ ownerOrg.it_contact_name }}
          </p>
          <p v-if="ownerOrg.it_contact_phone" class="text-slate-500">
            {{ ownerOrg.it_contact_phone }}
          </p>
          <p v-if="ownerOrg.it_contact_email" class="text-slate-500">
            {{ ownerOrg.it_contact_email }}
          </p>
        </div>
        <p v-else class="text-sm text-slate-400">
          {{
            ownerOrg
              ? 'Organisasi belum memiliki data IT Contact.'
              : 'Pilih pemilik aset terlebih dahulu.'
          }}
        </p>
      </div>

      <div v-if="modelValue.owner_contact_type === 'manual'">
        <label class="mb-1 block text-sm font-medium text-slate-700"
          >Pegawai Penanggung Jawab <span class="text-red-500">*</span></label
        >
        <Select
          :model-value="modelValue.owner_employee_id"
          :options="employeeOptions"
          option-label="label"
          option-value="value"
          placeholder="Pilih pegawai"
          class="w-full"
          show-clear
          filter
          @update:model-value="update('owner_employee_id', $event)"
        />
        <p v-if="errors.owner_employee_id" class="mt-1 text-xs text-red-600">
          {{ errors.owner_employee_id }}
        </p>
      </div>

      <div v-if="vendors !== null">
        <div class="mb-1 flex items-center justify-between">
          <label class="text-sm font-medium text-slate-700">Vendor</label>
          <button
            type="button"
            class="flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600"
            v-tooltip="'Refresh daftar vendor'"
            @click="$emit('vendor-refresh')"
          >
            <IconRefresh size="13" />
            Refresh
          </button>
        </div>
        <Select
          :model-value="modelValue.vendor_id"
          :options="vendorOptions"
          option-label="label"
          option-value="value"
          placeholder="Pilih vendor (opsional)"
          class="w-full"
          show-clear
          filter
          @update:model-value="update('vendor_id', $event)"
        />
        <p class="mt-2 text-xs text-slate-500">
          Belum ada vendor yang diinginkan?
          <button
            type="button"
            class="text-blue-600 hover:underline"
            @click="showVendorDialog = true"
          >
            Tambahkan
          </button>
        </p>
      </div>
    </div>

    <VendorFormDialog
      :visible="showVendorDialog"
      @update:visible="showVendorDialog = $event"
      @saved="emit('vendor-saved')"
    />
  </AdminFormSection>
</template>
