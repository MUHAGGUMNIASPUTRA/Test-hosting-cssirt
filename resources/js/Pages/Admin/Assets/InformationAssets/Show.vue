<!-- Tujuan: Halaman detail Aset Informasi dengan navigasi tab -->
<!-- Caller: InformationAssetController@show -->
<!-- Side Effects: none -->
<script setup>
import { formatDatetime } from '@/utils/date'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  informationAsset: { type: Object, required: true },
})

const asset = props.informationAsset

const securityData = computed(() => ({
  confidentiality: asset.security_classification?.confidentiality ?? null,
  integrity: asset.security_classification?.integrity ?? null,
  availability: asset.security_classification?.availability ?? null,
}))

const storageFormatLabel = (val) =>
  ({ file_dokumen: 'File Dokumen', cetak: 'Cetak', keduanya: 'Keduanya' })[
    val
  ] ?? val

const storageFormatSeverity = (val) =>
  ({ file_dokumen: 'info', cetak: 'warn', keduanya: 'success' })[val] ??
  'secondary'

const dangerLevelSeverity = (val) =>
  ({ bahaya: 'danger', peringatan: 'warn', aman: 'success' })[val] ??
  'secondary'

const showDeleteDialog = ref(false)
const handleDelete = () => {
  router.delete(route('admin.information-assets.destroy', asset.id), {
    onSuccess: () => router.visit(route('admin.information-assets.index')),
  })
}
</script>

<template>
  <AdminLayout :title="`Detail Aset Informasi`">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aset Informasi"
      @confirm="handleDelete"
    >
      <template #item-info>{{
        asset.document?.title ?? 'Aset Informasi'
      }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminFormHeader
        :title="asset.document?.title ?? 'Aset Informasi'"
        :description="asset.document?.description"
        back-route="admin.information-assets.index"
        :processing="false"
      >
        <template #actions>
          <button
            class="inline-flex items-center justify-center gap-2 rounded-md border border-red-200 px-4 py-2 text-red-600 transition hover:bg-red-50"
            @click="showDeleteDialog = true"
          >
            <IconTrash size="16" />
            Hapus
          </button>
          <a :href="route('admin.information-assets.edit', asset.id)">
            <button
              class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
            >
              <IconEdit size="16" />
              Edit
            </button>
          </a>
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
              title="Informasi Aset"
              description="Dokumen dan format penyimpanan"
              color="purple"
            >
              <template #icon="{ iconClass }">
                <IconDatabase :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="sm:col-span-2">
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Dokumen Referensi
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ asset.document?.title ?? '(Tanpa Dokumen)' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Format Penyimpanan
                  </dt>
                  <dd class="mt-1">
                    <Tag
                      :value="storageFormatLabel(asset.storage_format)"
                      :severity="storageFormatSeverity(asset.storage_format)"
                    />
                  </dd>
                </div>
              </dl>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab 1: Kepemilikan -->
          <TabPanel value="1" class="space-y-4">
            <AdminFormSection
              title="Penempatan & Kepemilikan"
              description="Lokasi dan pemilik aset"
              color="green"
            >
              <template #icon="{ iconClass }">
                <IconBuilding :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Lokasi
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ asset.location?.name ?? '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-medium uppercase tracking-wider text-slate-400"
                  >
                    Pemilik Aset
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ asset.owner_org?.name ?? '—' }}
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
              asset-type="information-asset"
              :asset-id="asset.id"
              :security-notes="informationAsset.security_notes ?? []"
            />

            <AdminFormSection
              v-if="asset.audit_logs?.length"
              title="Audit Log"
              description="Riwayat perubahan aset"
              color="slate"
            >
              <template #icon="{ iconClass }">
                <IconClipboardList :class="iconClass" />
              </template>
              <div class="space-y-2">
                <div
                  v-for="log in asset.audit_logs"
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
