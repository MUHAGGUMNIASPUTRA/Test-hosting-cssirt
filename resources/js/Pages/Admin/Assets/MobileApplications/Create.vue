<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  mobileApplication: { type: Object, default: null },
  organizations: Array,
  locations: Array,
  vendors: Array,
  techStacks: Array,
  guides: Array,
  stageOptions: Array,
  appStatusOptions: Array,
  employees: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.mobileApplication)
const ma = props.mobileApplication

const form = useForm({
  name: ma?.name ?? '',
  description: ma?.description ?? '',
  stage: ma?.stage ?? 'draft',
  app_status: ma?.app_status ?? 'aktif',
  current_version: ma?.current_version ?? '',
  app_link: ma?.app_link ?? '',
  location_id: ma?.location_id ?? '',
  provider_org_id: ma?.provider_org_id ?? '',
  owner_org_id: ma?.owner_org_id ?? '',
  owner_contact_type: ma?.owner_contact_type ?? 'auto',
  owner_employee_id: ma?.owner_employee_id ?? null,
  vendor_id: ma?.vendor_id ?? null,
  tech_stacks:
    ma?.tech_stacks?.map((t) => ({
      tech_stack_id: t.tech_stack_id,
      version: t.version ?? '',
    })) ?? [],
  security: {
    confidentiality: ma?.security_classification?.confidentiality ?? null,
    integrity: ma?.security_classification?.integrity ?? null,
    availability: ma?.security_classification?.availability ?? null,
    notes: ma?.security_classification?.notes ?? '',
  },
})

const statusData = computed(() => ({
  stage: form.stage,
  app_status: form.app_status,
}))

const updateStatus = (val) => {
  form.stage = val.stage
  form.app_status = val.app_status
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
    form.put(route('admin.mobile-applications.update', ma.id))
  } else {
    form.post(route('admin.mobile-applications.store'))
  }
}
</script>

<template>
  <AdminLayout
    :title="isEdit ? 'Edit Aplikasi Mobile' : 'Tambah Aplikasi Mobile'"
  >
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="isEdit ? 'Edit Aplikasi Mobile' : 'Tambah Aplikasi Mobile'"
        :description="
          isEdit
            ? 'Perbarui data dan spesifikasi aplikasi mobile.'
            : 'Tambahkan data aplikasi mobile baru.'
        "
        back-route="admin.mobile-applications.index"
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
            description="Nama dan deskripsi aplikasi mobile"
            color="blue"
          >
            <template #icon="{ iconClass }">
              <IconDeviceMobile :class="iconClass" />
            </template>
            <div class="space-y-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Nama Aplikasi <span class="text-red-500">*</span></label
                >
                <InputText
                  v-model="form.name"
                  class="w-full"
                  placeholder="Nama aplikasi mobile"
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
            :show-https="false"
            :errors="form.errors"
            @update:model-value="updateStatus"
          />

          <!-- Versi & Link -->
          <AdminFormSection
            title="Detail Aplikasi"
            description="Versi dan tautan unduhan"
            color="green"
          >
            <template #icon="{ iconClass }">
              <IconInfoCircle :class="iconClass" />
            </template>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Versi Saat Ini</label
                >
                <InputText
                  v-model="form.current_version"
                  class="w-full"
                  placeholder="Contoh: 1.2.0"
                />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Link Aplikasi</label
                >
                <InputText
                  v-model="form.app_link"
                  class="w-full"
                  placeholder="https://play.google.com/..."
                  type="url"
                />
                <p
                  v-if="form.errors.app_link"
                  class="mt-1 text-xs text-red-600"
                >
                  {{ form.errors.app_link }}
                </p>
              </div>
            </div>
          </AdminFormSection>

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

          <!-- Tech Stack -->
          <TechStackSection
            :model-value="form.tech_stacks"
            :tech-stacks="techStacks"
            @update:model-value="(v) => (form.tech_stacks = v)"
          />
        </div>

        <div class="space-y-4">
          <!-- Aksi mobile -->
          <div class="flex gap-3 xl:hidden">
            <Link
              :href="route('admin.mobile-applications.index')"
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

          <SecurityClassificationForm
            :model-value="form.security"
            @update:model-value="(v) => (form.security = v)"
          />

          <div
            v-if="guides?.length"
            class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
          >
            <h3 class="mb-3 font-semibold text-slate-800">Panduan Referensi</h3>
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
  </AdminLayout>
</template>
