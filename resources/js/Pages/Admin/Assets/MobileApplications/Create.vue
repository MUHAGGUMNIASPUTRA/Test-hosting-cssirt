<!-- Tujuan: Form tambah/edit Aplikasi Mobile dengan navigasi tab -->
<!-- Caller: MobileApplicationController@create / edit -->
<!-- Side Effects: Inertia POST/PUT ke admin.mobile-applications.store/update -->
<script setup>
import { router, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  mobileApplication: { type: Object, default: null },
  organizations: Array,
  locations: Array,
  vendors: Array,
  techStacks: Array,
  techStackCategories: { type: Array, default: () => [] },
  guides: { type: Array, default: () => [] },
  stageOptions: Array,
  appStatusOptions: Array,
  employees: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.mobileApplication)
const ma = props.mobileApplication
const activeTab = ref('0')

const tabFieldMap = {
  0: [
    'name',
    'description',
    'stage',
    'app_status',
    'current_version',
    'app_link',
  ],
  1: ['tech_stacks'],
  2: [
    'location_id',
    'provider_org_id',
    'owner_org_id',
    'owner_employee_id',
    'vendor_id',
  ],
  3: [],
  4: ['security'],
}

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
  if (isEdit.value) form.put(route('admin.mobile-applications.update', ma.id))
  else form.post(route('admin.mobile-applications.store'))
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
            ? 'Perbarui data aplikasi mobile.'
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

      <Tabs v-model:value="activeTab">
        <TabList>
          <Tab value="0">Utama</Tab>
          <Tab value="1">Spesifikasi</Tab>
          <Tab value="2">Kepemilikan</Tab>
          <Tab value="3">Referensi</Tab>
          <Tab value="4">Keamanan</Tab>
        </TabList>
        <TabPanels>
          <!-- Tab 0: Utama -->
          <TabPanel value="0" class="space-y-4">
            <AdminFormSection
              title="Informasi Utama"
              description="Nama dan deskripsi aplikasi mobile"
              color="blue"
            >
              <template #icon="{ iconClass }"
                ><IconDeviceMobile :class="iconClass"
              /></template>
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
            <AppStatusSection
              :model-value="statusData"
              :stage-options="stageOptions"
              :app-status-options="appStatusOptions"
              :show-https="false"
              :errors="form.errors"
              @update:model-value="updateStatus"
            />
            <AdminFormSection
              title="Detail Aplikasi"
              description="Versi dan tautan unduhan"
              color="green"
            >
              <template #icon="{ iconClass }"
                ><IconInfoCircle :class="iconClass"
              /></template>
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
          </TabPanel>

          <!-- Tab 1: Spesifikasi -->
          <TabPanel value="1">
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
          </TabPanel>

          <!-- Tab 2: Kepemilikan -->
          <TabPanel value="2">
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
          </TabPanel>

          <!-- Tab 3: Referensi -->
          <TabPanel value="3">
            <GuideReferenceSection
              :guides="guides"
              asset-type="mobile-application"
              :asset-id="ma?.id ?? null"
            />
          </TabPanel>

          <!-- Tab 4: Keamanan -->
          <TabPanel value="4" class="space-y-4">
            <SecurityClassificationForm
              :model-value="form.security"
              asset-type="mobile-application"
              :asset-id="ma?.id ?? null"
              :security-notes="mobileApplication?.security_notes ?? []"
              @update:model-value="(v) => (form.security = v)"
            />
            <AdminFormSection
              v-if="ma?.id && mobileApplication?.incidents?.length"
              title="Insiden Terkait"
              description="Insiden keamanan yang berkaitan"
              color="red"
            >
              <template #icon="{ iconClass }"
                ><IconUrgent :class="iconClass"
              /></template>
              <div class="space-y-2">
                <div
                  v-for="inc in mobileApplication.incidents"
                  :key="inc.id"
                  class="flex flex-wrap items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 p-3"
                >
                  <a
                    :href="route('admin.incidents.show', inc.id)"
                    class="font-mono text-sm font-medium text-blue-600 hover:underline"
                    >{{ inc.case_id }}</a
                  >
                  <Tag
                    :value="inc.status"
                    severity="secondary"
                    class="!text-xs"
                  />
                  <Tag :value="inc.priority" severity="warn" class="!text-xs" />
                  <span
                    v-if="inc.incident_type"
                    class="text-xs text-slate-500"
                    >{{ inc.incident_type.name }}</span
                  >
                </div>
              </div>
            </AdminFormSection>
            <AdminFormSection
              v-else-if="ma?.id"
              title="Insiden Terkait"
              description="Insiden keamanan yang berkaitan"
              color="red"
            >
              <template #icon="{ iconClass }"
                ><IconUrgent :class="iconClass"
              /></template>
              <p class="text-sm text-slate-400">Tidak ada insiden terkait.</p>
            </AdminFormSection>
          </TabPanel>
        </TabPanels>
      </Tabs>
    </form>
  </AdminLayout>
</template>
