<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  webApplication: { type: Object, default: null },
  organizations: Array,
  locations: Array,
  vendors: Array,
  techStacks: Array,
  guides: Array,
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
    notes: wa?.security_classification?.notes ?? '',
  },
})

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
    <div class="space-y-4">
      <AdminPageHeader
        :title="isEdit ? 'Edit Aplikasi Web' : 'Tambah Aplikasi Web'"
        description="Kelola data dan spesifikasi aplikasi web."
      />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
          <div class="space-y-4 xl:col-span-2">
            <!-- Informasi Utama -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">Informasi Utama</h3>
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
            </div>

            <!-- Status & Tahap -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">
                Status & Kondisi
              </h3>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Tahap <span class="text-red-500">*</span></label
                  >
                  <Select
                    v-model="form.stage"
                    :options="stageOptions"
                    option-label="name"
                    option-value="value"
                    placeholder="Pilih tahap"
                    class="w-full"
                  />
                  <p v-if="form.errors.stage" class="mt-1 text-xs text-red-600">
                    {{ form.errors.stage }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Status Aplikasi <span class="text-red-500">*</span></label
                  >
                  <Select
                    v-model="form.app_status"
                    :options="appStatusOptions"
                    option-label="name"
                    option-value="value"
                    placeholder="Status"
                    class="w-full"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Status HTTPS <span class="text-red-500">*</span></label
                  >
                  <Select
                    v-model="form.https_status"
                    :options="httpsStatusOptions"
                    option-label="name"
                    option-value="value"
                    placeholder="HTTPS"
                    class="w-full"
                  />
                </div>
              </div>
            </div>

            <!-- Penempatan & Kepemilikan -->
            <OwnerContactSection
              :organizations="organizations"
              :locations="locations"
              :employees="employees"
              :vendors="vendors"
              :model-value="ownerData"
              :errors="form.errors"
              @update:model-value="updateOwner"
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
              @update:model-value="(v) => (form.tech_stacks = v)"
            />
          </div>

          <div class="space-y-4">
            <!-- Aksi -->
            <div class="flex gap-3">
              <Link
                :href="route('admin.web-applications.index')"
                class="flex-1"
              >
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
              @update:model-value="(v) => (form.security = v)"
            />

            <!-- Panduan Referensi -->
            <div
              v-if="guides?.length"
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-3 font-semibold text-slate-800">
                Panduan Referensi
              </h3>
              <ul class="space-y-2">
                <li
                  v-for="guide in guides"
                  :key="guide.id"
                  class="flex items-center gap-2 text-sm text-slate-600"
                >
                  <IconBook size="14" class="flex-shrink-0 text-slate-400" />
                  {{ guide.name }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
