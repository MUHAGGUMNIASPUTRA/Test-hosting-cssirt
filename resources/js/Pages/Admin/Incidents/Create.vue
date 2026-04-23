<!-- Tujuan: Form tambah/edit insiden admin dengan navigasi tab (Utama dan Riwayat) -->
<!-- Caller: IncidentController@create / edit -->
<!-- Side Effects: Inertia POST/PUT ke admin.incidents.store/update -->
<script setup>
import { router, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { getSeverity } from '@/utils/status'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  incident: { type: Object, default: null },
  incidentTypes: Array,
  staffUsers: Array,
})

const { isMobile } = useResponsive()

const isEditing = computed(() => !!props.incident)
const pageTitle = computed(() =>
  isEditing.value
    ? `Edit Insiden: ${props.incident.case_id}`
    : 'Lapor Insiden Baru',
)
const submitButtonText = computed(() =>
  isEditing.value ? 'Update Laporan' : 'Simpan Laporan',
)

const activeTab = ref('0')

const detectAttachmentMode = () => props.incident?.attachment?.type ?? 'file'

const form = useForm({
  reporter_name: props.incident?.reporter_name || '',
  reporter_email: props.incident?.reporter_email || '',
  reporter_phone: props.incident?.reporter_phone || '',
  incident_type_id: props.incident?.incident_type_id || null,
  incident_at: props.incident
    ? new Date(props.incident.incident_at)
    : new Date(),
  description: props.incident?.description || '',
  status: props.incident?.status || 'Baru',
  priority: props.incident?.priority || 'Sedang',
  assigned_to: props.incident?.assigned_to || null,
  attachment_type: detectAttachmentMode(),
  attachment: null,
  attachment_links:
    (detectAttachmentMode() === 'link'
      ? props.incident?.attachment?.url
      : '') || '',
})

const selectedAssets = ref([
  ...(props.incident?.web_applications ?? []).map((a) => ({
    id: a.id,
    name: a.name,
    asset_type: 'web-application',
  })),
  ...(props.incident?.mobile_applications ?? []).map((a) => ({
    id: a.id,
    name: a.name,
    asset_type: 'mobile-application',
  })),
])

const statusOptions = [
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' },
]

const priorityOptions = [
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' },
]

const staffUserOptions = computed(() => [
  { label: 'Tidak ditugaskan', value: null },
  ...(props.staffUsers?.map((u) => ({ label: u.name, value: u.id })) || []),
])

const incidentTypeOptions = computed(
  () => props.incidentTypes?.map((t) => ({ label: t.name, value: t.id })) || [],
)

const submit = () => {
  const formData = { ...form.data() }
  if (formData.incident_at) {
    formData.incident_at = new Date(formData.incident_at)
      .toISOString()
      .slice(0, 16)
  }
  formData.virtual_assets = selectedAssets.value.map((a) => ({
    id: a.id,
    asset_type: a.asset_type,
  }))

  if (isEditing.value) {
    form
      .transform(() => ({ ...formData, _method: 'PUT' }))
      .post(route('admin.incidents.update', props.incident.id))
  } else {
    form.transform(() => formData).post(route('admin.incidents.store'))
  }
}
</script>

<template>
  <AdminLayout :title="pageTitle">
    <form @submit.prevent="submit">
      <div class="space-y-4 lg:space-y-6">
        <!-- Header -->
        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
          >
            <div>
              <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
                {{ isEditing ? 'Edit Insiden' : 'Lapor Insiden Baru' }}
              </h2>
              <p v-if="!isEditing" class="text-slate-600">
                Buat laporan insiden keamanan siber baru untuk ditindaklanjuti
              </p>
              <div v-if="isEditing" class="mt-2 flex items-center gap-3">
                <Tag
                  :value="incident.case_id"
                  severity="secondary"
                  size="small"
                  class="font-mono !text-slate-500"
                />
                <Tag
                  :value="incident.status"
                  :severity="getSeverity('incident-status', incident.status)"
                  size="small"
                />
                <Tag
                  :value="incident.priority"
                  :severity="getSeverity('priority', incident.priority)"
                  size="small"
                />
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Button
                severity="secondary"
                class="w-full lg:w-auto"
                @click="() => router.get(route('admin.incidents.index'))"
              >
                <IconArrowLeft size="16" />Kembali
              </Button>
              <Button
                v-if="!isMobile"
                type="submit"
                severity="primary"
                :disabled="form.processing"
                class="w-full lg:w-auto"
              >
                <IconLoader3
                  v-if="form.processing"
                  class="animate-spin"
                  size="16"
                />
                <IconDeviceFloppy v-else size="16" />
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </Button>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <Tabs v-model:value="activeTab">
          <TabList>
            <Tab value="0">Utama</Tab>
            <Tab value="1">Riwayat</Tab>
          </TabList>
          <TabPanels>
            <TabPanel value="0">
              <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
                <div class="space-y-4 lg:col-span-2 lg:space-y-6">
                  <IncidentReporterSection
                    :name="form.reporter_name"
                    :email="form.reporter_email"
                    :phone="form.reporter_phone"
                    :errors="form.errors"
                    @update:name="form.reporter_name = $event"
                    @update:email="form.reporter_email = $event"
                    @update:phone="form.reporter_phone = $event"
                  />
                  <IncidentDetailSection
                    :incident-type-id="form.incident_type_id"
                    :incident-at="form.incident_at"
                    :description="form.description"
                    :incident-types="incidentTypes"
                    :errors="form.errors"
                    @update:incident-type-id="form.incident_type_id = $event"
                    @update:incident-at="form.incident_at = $event"
                    @update:description="form.description = $event"
                  >
                    <template #attachment>
                      <IncidentAttachmentSection
                        :is-editing="isEditing"
                        :existing-attachment="incident?.attachment"
                        :attachment-type="form.attachment_type"
                        :attachment-links="form.attachment_links"
                        @update:attachment-type="form.attachment_type = $event"
                        @update:attachment="form.attachment = $event"
                        @update:attachment-links="
                          form.attachment_links = $event
                        "
                      />
                    </template>
                  </IncidentDetailSection>
                  <IncidentVirtualAssetsSection v-model="selectedAssets" />
                </div>
                <div class="space-y-4 lg:space-y-6">
                  <IncidentManagementSection
                    :status="form.status"
                    :priority="form.priority"
                    :assigned-to="form.assigned_to"
                    :status-options="statusOptions"
                    :priority-options="priorityOptions"
                    :staff-user-options="staffUserOptions"
                    @update:status="form.status = $event"
                    @update:priority="form.priority = $event"
                    @update:assigned-to="form.assigned_to = $event"
                  />
                  <IncidentPreviewSection
                    :is-editing="isEditing"
                    :incident="incident"
                    :reporter-name="form.reporter_name"
                    :status="form.status"
                    :priority="form.priority"
                    :incident-type-id="form.incident_type_id"
                    :incident-at="form.incident_at"
                    :incident-type-options="incidentTypeOptions"
                    :selected-assets-count="selectedAssets.length"
                  />
                  <div v-if="isMobile">
                    <button
                      type="submit"
                      :disabled="form.processing"
                      class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-800 disabled:opacity-50"
                    >
                      <IconLoader3
                        v-if="form.processing"
                        class="animate-spin"
                        size="16"
                      />
                      <IconDeviceFloppy v-else size="16" />
                      {{ form.processing ? 'Menyimpan...' : submitButtonText }}
                    </button>
                  </div>
                </div>
              </div>
            </TabPanel>

            <TabPanel value="1">
              <IncidentHistoryTab
                :is-editing="isEditing"
                :logs="incident?.incident_logs ?? []"
              />
            </TabPanel>
          </TabPanels>
        </Tabs>
      </div>
    </form>
  </AdminLayout>
</template>
