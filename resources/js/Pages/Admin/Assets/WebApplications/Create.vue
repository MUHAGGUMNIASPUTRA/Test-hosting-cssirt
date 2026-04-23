<!-- Tujuan: Form tambah/edit Aplikasi Web dengan navigasi tab -->
<!-- Caller: WebApplicationController@create / edit -->
<!-- Side Effects: Inertia POST/PUT ke admin.web-applications.store/update -->
<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

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
  ipAddresses: { type: Array, default: () => [] },
  subdomains: { type: Array, default: () => [] },
})

const isEdit = computed(() => !!props.webApplication)
const wa = props.webApplication
const activeTab = ref('0')

const tabFieldMap = {
  0: ['name', 'description', 'stage', 'app_status', 'https_status'],
  1: ['vms', 'networks', 'tech_stacks'],
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
    ip_address_id: n.ip_address_id ?? null,
    subdomain_id: n.subdomain_id ?? null,
    _ip: n.ip_address ?? null,
    _subdomain: n.subdomain ?? null,
  })) ?? [
    {
      environment: '',
      ip_address_id: null,
      subdomain_id: null,
      _ip: null,
      _subdomain: null,
    },
  ],
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
  const cleanNetworks = (data) => ({
    ...data,
    networks: data.networks.map(({ _ip, _subdomain, ...net }) => net),
  })
  if (isEdit.value)
    form
      .transform(cleanNetworks)
      .put(route('admin.web-applications.update', wa.id))
  else form.transform(cleanNetworks).post(route('admin.web-applications.store'))
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Aplikasi Web' : 'Tambah Aplikasi Web'">
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="isEdit ? 'Edit Aplikasi Web' : 'Tambah Aplikasi Web'"
        :description="
          isEdit
            ? 'Perbarui data aplikasi web.'
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
              description="Nama dan deskripsi aplikasi web"
              color="blue"
            >
              <template #icon="{ iconClass }"
                ><IconWorldWww :class="iconClass"
              /></template>
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
            <AppStatusSection
              :model-value="statusData"
              :stage-options="stageOptions"
              :app-status-options="appStatusOptions"
              :https-status-options="httpsStatusOptions"
              :show-https="true"
              :errors="form.errors"
              @update:model-value="updateStatus"
            />
          </TabPanel>

          <!-- Tab 1: Spesifikasi -->
          <TabPanel value="1" class="space-y-4">
            <VmSpecSection
              :model-value="form.vms"
              @update:model-value="(v) => (form.vms = v)"
            />
            <NetworkSpecSection
              :model-value="form.networks"
              :errors="form.errors"
              :ip-addresses="ipAddresses"
              :subdomains="subdomains"
              @update:model-value="(v) => (form.networks = v)"
              @refresh="
                (key) =>
                  router.reload({
                    only:
                      key === 'ipAddresses' ? ['ipAddresses'] : ['subdomains'],
                  })
              "
            />
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
              asset-type="web-application"
              :asset-id="wa?.id ?? null"
            />
          </TabPanel>

          <!-- Tab 4: Keamanan -->
          <TabPanel value="4" class="space-y-4">
            <SecurityClassificationForm
              :model-value="form.security"
              asset-type="web-application"
              :asset-id="wa?.id ?? null"
              :security-notes="webApplication?.security_notes ?? []"
              @update:model-value="(v) => (form.security = v)"
            />
            <AdminFormSection
              v-if="wa?.id && webApplication?.incidents?.length"
              title="Insiden Terkait"
              description="Insiden keamanan yang berkaitan"
              color="red"
            >
              <template #icon="{ iconClass }"
                ><IconUrgent :class="iconClass"
              /></template>
              <div class="space-y-2">
                <div
                  v-for="inc in webApplication.incidents"
                  :key="inc.id"
                  class="flex flex-wrap items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 p-3"
                >
                  <Link
                    :href="route('admin.incidents.show', inc.id)"
                    class="font-mono text-sm font-medium text-blue-600 hover:underline"
                    >{{ inc.case_id }}</Link
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
              v-else-if="wa?.id"
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
