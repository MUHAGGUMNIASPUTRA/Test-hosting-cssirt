<!-- Tujuan: Halaman detail Aplikasi Mobile dengan navigasi tab -->
<!-- Caller: MobileApplicationController@show -->
<!-- Side Effects: none -->
<script setup>
import { formatDatetime } from '@/utils/date'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  mobileApplication: Object,
  guides: { type: Array, default: () => [] },
})

const ma = props.mobileApplication

const securityData = computed(() => ({
  confidentiality: ma.security_classification?.confidentiality ?? null,
  integrity: ma.security_classification?.integrity ?? null,
  availability: ma.security_classification?.availability ?? null,
}))

const stageSeverity = (v) =>
  ({
    draft: 'secondary',
    pengajuan: 'info',
    pengujian: 'warn',
    revisi: 'danger',
    persiapan: 'warn',
    diterima: 'success',
  })[v] ?? 'secondary'
const appStatusSeverity = (v) =>
  ({ aktif: 'success', idle: 'warn', nonaktif: 'secondary' })[v] ?? 'secondary'
const dangerLevelSeverity = (v) =>
  ({ bahaya: 'danger', peringatan: 'warn', aman: 'success' })[v] ?? 'secondary'

const showDeleteDialog = ref(false)
const handleDelete = () => {
  router.delete(route('admin.mobile-applications.destroy', ma.id), {
    onSuccess: () => router.visit(route('admin.mobile-applications.index')),
  })
}
</script>

<template>
  <AdminLayout :title="`Detail: ${ma.name}`">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aplikasi Mobile"
      @confirm="handleDelete"
    >
      <template #item-info>{{ ma.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminFormHeader
        :title="ma.name"
        :description="ma.description"
        back-route="admin.mobile-applications.index"
        :processing="false"
      >
        <template #actions>
          <button
            class="inline-flex items-center justify-center gap-2 rounded-md border border-red-200 px-4 py-2 text-red-600 transition hover:bg-red-50"
            @click="showDeleteDialog = true"
          >
            <IconTrash size="16" />Hapus
          </button>
          <Link :href="route('admin.mobile-applications.edit', ma.id)">
            <button
              class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
            >
              <IconEdit size="16" />Edit
            </button>
          </Link>
        </template>
      </AdminFormHeader>

      <Tabs value="0">
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
              title="Status & Kondisi"
              description="Tahap dan status aplikasi"
              color="orange"
            >
              <template #icon="{ iconClass }"
                ><IconActivity :class="iconClass"
              /></template>
              <div class="flex flex-wrap gap-4">
                <div>
                  <p class="mb-1 text-xs text-slate-500">Tahap</p>
                  <Tag
                    :value="ma.stage"
                    :severity="stageSeverity(ma.stage)"
                    class="capitalize"
                  />
                </div>
                <div>
                  <p class="mb-1 text-xs text-slate-500">Status App</p>
                  <Tag
                    :value="ma.app_status"
                    :severity="appStatusSeverity(ma.app_status)"
                    class="capitalize"
                  />
                </div>
              </div>
            </AdminFormSection>
            <AdminFormSection
              v-if="ma.current_version || ma.app_link"
              title="Detail Aplikasi"
              description="Versi dan tautan unduhan"
              color="green"
            >
              <template #icon="{ iconClass }"
                ><IconInfoCircle :class="iconClass"
              /></template>
              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div v-if="ma.current_version">
                  <p
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Versi Saat Ini
                  </p>
                  <p class="mt-1 text-sm text-slate-700">
                    {{ ma.current_version }}
                  </p>
                </div>
                <div v-if="ma.app_link">
                  <p
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Link Aplikasi
                  </p>
                  <a
                    :href="ma.app_link"
                    target="_blank"
                    rel="noopener"
                    class="mt-1 flex items-center gap-1 text-sm text-blue-600 hover:underline"
                  >
                    <IconExternalLink size="14" />Buka Link
                  </a>
                </div>
              </div>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 1: Spesifikasi -->
          <TabPanel value="1">
            <AdminFormSection
              title="Tech Stack"
              description="Framework dan teknologi yang digunakan"
              color="indigo"
            >
              <template #icon="{ iconClass }"
                ><IconCode :class="iconClass"
              /></template>
              <p
                v-if="!ma.tech_stacks?.length"
                class="py-2 text-sm text-slate-400"
              >
                Belum ada tech stack terdaftar.
              </p>
              <div v-else class="flex flex-wrap gap-2">
                <div
                  v-for="ts in ma.tech_stacks"
                  :key="ts.tech_stack_id"
                  class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm"
                >
                  <span class="font-medium text-slate-700">{{
                    ts.tech_stack?.name
                  }}</span>
                  <span v-if="ts.version" class="text-xs text-slate-400"
                    >v{{ ts.version }}</span
                  >
                </div>
              </div>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 2: Kepemilikan -->
          <TabPanel value="2">
            <AdminFormSection
              title="Penempatan & Kepemilikan"
              description="Lokasi, organisasi, dan kontak"
              color="green"
            >
              <template #icon="{ iconClass }"
                ><IconBuilding :class="iconClass"
              /></template>
              <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Lokasi
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ ma.location?.name ?? '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Penyedia Aset
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ ma.provider_org?.name ?? '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Pemilik Aset
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ ma.owner_org?.name ?? '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Vendor
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ ma.vendor?.company_name ?? '—' }}
                  </dd>
                </div>
                <div class="sm:col-span-2">
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Kontak PJ
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    <span
                      v-if="
                        ma.owner_contact_type === 'manual' && ma.owner_employee
                      "
                      >{{ ma.owner_employee.name }}</span
                    >
                    <span v-else-if="ma.owner_org?.it_contact_name"
                      >{{ ma.owner_org.it_contact_name
                      }}<span v-if="ma.owner_org.it_contact_phone">
                        · {{ ma.owner_org.it_contact_phone }}</span
                      ></span
                    >
                    <span v-else>—</span>
                  </dd>
                </div>
              </dl>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 3: Referensi -->
          <TabPanel value="3">
            <GuideReferenceSection
              :guides="guides"
              asset-type="mobile-application"
              :asset-id="ma.id"
            />
          </TabPanel>

          <!-- Tab 4: Keamanan -->
          <TabPanel value="4" class="space-y-4">
            <SecurityClassificationForm
              :model-value="securityData"
              :readonly-scores="true"
              asset-type="mobile-application"
              :asset-id="ma.id"
              :security-notes="mobileApplication.security_notes ?? []"
            />
            <AdminFormSection
              title="Insiden Terkait"
              description="Insiden keamanan yang berkaitan"
              color="red"
            >
              <template #icon="{ iconClass }"
                ><IconUrgent :class="iconClass"
              /></template>
              <p v-if="!ma.incidents?.length" class="text-sm text-slate-400">
                Tidak ada insiden terkait.
              </p>
              <div v-else class="space-y-2">
                <div
                  v-for="inc in ma.incidents"
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
              v-if="ma.audit_logs?.length"
              title="Audit Log"
              description="Riwayat perubahan aset"
              color="slate"
            >
              <template #icon="{ iconClass }"
                ><IconClipboardList :class="iconClass"
              /></template>
              <div class="space-y-2">
                <div
                  v-for="log in ma.audit_logs"
                  :key="log.id"
                  class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"
                >
                  <div class="mb-1 flex items-center justify-between">
                    <span class="font-medium text-slate-700">{{
                      log.user?.name ?? '—'
                    }}</span>
                    <Tag
                      v-if="log.danger_level"
                      :value="log.danger_level"
                      :severity="dangerLevelSeverity(log.danger_level)"
                      class="!text-xs capitalize"
                    />
                  </div>
                  <p class="text-slate-600">{{ log.message }}</p>
                  <p class="mt-1 text-xs text-slate-400">
                    {{ formatDatetime(log.created_at) }}
                  </p>
                </div>
              </div>
            </AdminFormSection>
          </TabPanel>
        </TabPanels>
      </Tabs>
    </div>
  </AdminLayout>
</template>
