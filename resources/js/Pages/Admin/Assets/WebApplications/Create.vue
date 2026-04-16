<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  webApplication: { type: Object, default: null },
  organizations: Array,
  locations: Array,
  vendors: Array,
  techStacks: Array,
  techStackCategories: { type: Array, default: () => [] },
  guides: { type: Array, default: () => [] },
  stageOptions: Array,
  appStatusOptions: Array,
  httpsStatusOptions: Array,
  employees: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.webApplication)
const wa = props.webApplication

const form = useForm({
  name: wa?.name ?? '',
  description: wa?.description ?? '',
  stage: wa?.stage ?? 'draft',
  app_status: wa?.app_status ?? 'aktif',
  https_status: wa?.https_status ?? 'nonaktif',
  location_id: wa?.location_id ?? '',
  provider_org_id: wa?.provider_org_id ?? '',
  owner_org_id: wa?.owner_org_id ?? '',
  owner_contact_type: wa?.owner_contact_type ?? 'auto',
  owner_employee_id: wa?.owner_employee_id ?? null,
  vendor_id: wa?.vendor_id ?? null,
  vms:
    wa?.vms?.map((v) => ({
      processor: v.processor ?? '',
      ram: v.ram ?? '',
      hdd: v.hdd ?? '',
    })) ?? [],
  networks: wa?.networks?.map((n) => ({
    environment: n.environment ?? '',
    dns: n.dns ?? '',
    local_ip: n.local_ip ?? '',
    public_ip: n.public_ip ?? '',
  })) ?? [{ environment: '', dns: '', local_ip: '', public_ip: '' }],
  tech_stacks:
    wa?.tech_stacks?.map((t) => ({
      tech_stack_id: t.tech_stack_id,
      version: t.version ?? '',
    })) ?? [],
  security: {
    confidentiality: wa?.security_classification?.confidentiality ?? null,
    integrity: wa?.security_classification?.integrity ?? null,
    availability: wa?.security_classification?.availability ?? null,
  },
})

const statusData = computed(() => ({
  stage: form.stage,
  app_status: form.app_status,
  https_status: form.https_status,
}))

const updateStatus = (val) => {
  form.stage = val.stage
  form.app_status = val.app_status
  form.https_status = val.https_status
}

const ownerData = computed(() => ({
  location_id: form.location_id,
  provider_org_id: form.provider_org_id,
  owner_org_id: form.owner_org_id,
  owner_contact_type: form.owner_contact_type,
  owner_employee_id: form.owner_employee_id,
  vendor_id: form.vendor_id,
}))

const updateOwner = (val) =>
  Object.entries(val).forEach(([k, v]) => (form[k] = v))

const submit = () => {
  if (isEdit.value) {
    form.put(route('admin.web-applications.update', wa.id))
  } else {
    form.post(route('admin.web-applications.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Aplikasi Web' : 'Tambah Aplikasi Web'">
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="isEdit ? 'Edit Aplikasi Web' : 'Tambah Aplikasi Web'"
        :description="
          isEdit
            ? 'Perbarui data dan spesifikasi aplikasi web.'
            : 'Tambahkan data aplikasi web baru.'
        "
        back-route="admin.web-applications.index"
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
          <!-- Informasi Utama -->
          <AdminFormSection
            title="Informasi Utama"
            description="Nama dan deskripsi aplikasi web"
            color="blue"
          >
            <template #icon="{ iconClass }">
              <IconWorldWww :class="iconClass" />
            </template>
            <div class="space-y-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Nama Aplikasi <span class="text-red-500">*</span></label
                >
                <InputText
                  v-model="form.name"
                  class="w-full"
                  placeholder="Nama aplikasi web"
                  required
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                  {{ form.errors.name }}
                </p>
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Deskripsi</label
                >
                <Textarea
                  v-model="form.description"
                  class="w-full"
                  rows="3"
                  placeholder="Deskripsi singkat aplikasi..."
                />
              </div>
            </div>
          </AdminFormSection>

          <!-- Status & Kondisi -->
          <AppStatusSection
            :model-value="statusData"
            :stage-options="stageOptions"
            :app-status-options="appStatusOptions"
            :https-status-options="httpsStatusOptions"
            :show-https="true"
            :errors="form.errors"
            @update:model-value="updateStatus"
          />

          <!-- Penempatan & Kepemilikan -->
          <OwnerContactSection
            :organizations="organizations"
            :locations="locations"
            :employees="employees"
            :vendors="vendors"
            :model-value="ownerData"
            :errors="form.errors"
            @update:model-value="updateOwner"
            @vendor-saved="router.reload({ only: ['vendors'] })"
            @vendor-refresh="router.reload({ only: ['vendors'] })"
          />

          <!-- VM Specs -->
          <VmSpecSection
            :model-value="form.vms"
            @update:model-value="(v) => (form.vms = v)"
          />

          <!-- Network Specs -->
          <NetworkSpecSection
            :model-value="form.networks"
            :errors="form.errors"
            @update:model-value="(v) => (form.networks = v)"
          />

          <!-- Tech Stack -->
          <TechStackSection
            :model-value="form.tech_stacks"
            :tech-stacks="techStacks"
            :categories="techStackCategories"
            @update:model-value="(v) => (form.tech_stacks = v)"
            @techstack-saved="router.reload({ only: ['techStacks'] })"
            @refresh="
              router.reload({ only: ['techStacks', 'techStackCategories'] })
            "
          />
        </div>

        <div class="flex flex-col gap-4">
          <!-- Aksi mobile -->
          <div class="flex gap-3 xl:hidden">
            <Link :href="route('admin.web-applications.index')" class="flex-1">
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

          <!-- Keamanan -->
          <SecurityClassificationForm
            :model-value="form.security"
            asset-type="web-application"
            :asset-id="wa?.id ?? null"
            :security-notes="webApplication?.security_notes ?? []"
            @update:model-value="(v) => (form.security = v)"
          />

          <!-- Panduan Referensi -->
          <GuideReferenceSection
            :guides="guides"
            asset-type="web-application"
            :asset-id="wa?.id ?? null"
          />
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
