<!-- Tujuan: Halaman detail Lisensi dengan navigasi tab -->
<!-- Caller: LicenseController@show -->
<!-- Side Effects: none -->
<script setup>
import { formatDate } from '@/utils/date'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  license: { type: Object, required: true },
})

const lic = props.license

const securityData = computed(() => ({
  confidentiality: lic.security_classification?.confidentiality ?? null,
  integrity: lic.security_classification?.integrity ?? null,
  availability: lic.security_classification?.availability ?? null,
}))

const showDeleteDialog = ref(false)
const handleDelete = () => {
  router.delete(route('admin.licenses.destroy', lic.id), {
    onSuccess: () => router.visit(route('admin.licenses.index')),
  })
}
</script>

<template>
  <AdminLayout :title="`Detail: ${lic.name}`">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Lisensi"
      @confirm="handleDelete"
    >
      <template #item-info>{{ lic.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminFormHeader
        :title="lic.name"
        :description="lic.description"
        back-route="admin.licenses.index"
        :processing="false"
      >
        <template #actions>
          <button
            class="inline-flex items-center justify-center gap-2 rounded-md border border-red-200 px-4 py-2 text-red-600 transition hover:bg-red-50"
            @click="showDeleteDialog = true"
          >
            <IconTrash size="16" />Hapus
          </button>
          <Link :href="route('admin.licenses.edit', lic.id)">
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
              <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="sm:col-span-2">
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Nama Lisensi
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">{{ lic.name }}</dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Versi
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ lic.version || '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Tanggal Kedaluwarsa
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ lic.expired_at ? formatDate(lic.expired_at) : '—' }}
                  </dd>
                </div>
                <div class="sm:col-span-2">
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Deskripsi
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ lic.description || '—' }}
                  </dd>
                </div>
              </dl>
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
                <Tag
                  :value="lic.is_active ? 'Aktif' : 'Tidak Aktif'"
                  :severity="lic.is_active ? 'success' : 'secondary'"
                />
              </div>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 1: Kepemilikan -->
          <TabPanel value="1">
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
                    {{ lic.location?.name ?? '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Penyedia Aset
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ lic.provider_org?.name ?? '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Pemilik Aset
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ lic.owner_org?.name ?? '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Kontak PJ
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    <span
                      v-if="
                        lic.owner_contact_type === 'manual' &&
                        lic.owner_employee
                      "
                      >{{ lic.owner_employee.name }}</span
                    >
                    <span v-else-if="lic.owner_org?.it_contact_name"
                      >{{ lic.owner_org.it_contact_name
                      }}<span v-if="lic.owner_org.it_contact_phone">
                        · {{ lic.owner_org.it_contact_phone }}</span
                      ></span
                    >
                    <span v-else>—</span>
                  </dd>
                </div>
              </dl>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 2: Keamanan -->
          <TabPanel value="2" class="space-y-4">
            <SecurityClassificationForm
              :model-value="securityData"
              :readonly-scores="true"
              asset-type="license"
              :asset-id="lic.id"
              :security-notes="license.security_notes ?? []"
            />
            <AdminFormSection
              v-if="lic.audit_logs?.length"
              title="Audit Log"
              description="Riwayat perubahan aset"
              color="slate"
            >
              <template #icon="{ iconClass }"
                ><IconClipboardList :class="iconClass"
              /></template>
              <div class="space-y-2">
                <div
                  v-for="log in lic.audit_logs"
                  :key="log.id"
                  class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"
                >
                  <div class="mb-1 flex items-center justify-between">
                    <span class="font-medium text-slate-700">{{
                      log.user?.name ?? '—'
                    }}</span>
                  </div>
                  <p class="text-slate-600">{{ log.message }}</p>
                </div>
              </div>
            </AdminFormSection>
          </TabPanel>
        </TabPanels>
      </Tabs>
    </div>
  </AdminLayout>
</template>
