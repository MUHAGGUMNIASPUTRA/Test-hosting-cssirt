<!-- Tujuan: Halaman detail insiden dengan navigasi tab (Utama dan Riwayat) -->
<!-- Caller: IncidentController@show -->
<!-- Side Effects: none -->
<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  incident: Object,
  staffUsers: Array,
})

const getStatusSeverity = (status) =>
  ({
    Baru: 'info',
    Diverifikasi: 'primary',
    'Dalam Penyelidikan': 'warn',
    Selesai: 'success',
    Ditutup: 'secondary',
  })[status] || 'info'

const getPrioritySeverity = (priority) =>
  ({ Rendah: 'success', Sedang: 'info', Tinggi: 'warn', Kritikal: 'danger' })[
    priority
  ] || 'info'

// Delete log
const deleteLogId = ref(null)

const handleDeleteLog = () => {
  router.delete(
    route('admin.incidents.logs.destroy', {
      incident: props.incident.id,
      log: deleteLogId.value,
    }),
    {
      preserveScroll: true,
      onSuccess: () => {
        deleteLogId.value = null
      },
    },
  )
}
</script>

<template>
  <AdminLayout :title="`Detail Insiden: ${incident.case_id}`">
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
              Detail Insiden
            </h2>
            <div class="mt-2 flex items-center gap-3">
              <Tag
                :value="incident.case_id"
                severity="secondary"
                size="small"
                class="font-mono !text-slate-500"
              />
              <Tag
                :value="incident.status"
                :severity="getStatusSeverity(incident.status)"
                size="small"
              />
              <Tag
                :value="incident.priority"
                :severity="getPrioritySeverity(incident.priority)"
                size="small"
              />
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <Button
              severity="secondary"
              @click="() => router.get(route('admin.incidents.index'))"
              class="w-full lg:w-auto"
            >
              <IconArrowLeft size="16" />
              Kembali
            </Button>
            <Button
              v-if="incident.status !== 'Ditutup'"
              severity="primary"
              @click="
                () => router.get(route('admin.incidents.edit', incident.id))
              "
              class="w-full lg:w-auto"
            >
              <IconEdit size="16" />
              Edit Insiden
            </Button>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <Tabs value="0">
        <TabList>
          <Tab value="0">Utama</Tab>
          <Tab value="1">
            Riwayat
            <span
              v-if="incident.incident_logs?.length"
              class="ml-1.5 rounded-full bg-slate-200 px-1.5 py-0.5 text-xs text-slate-600"
            >
              {{ incident.incident_logs.length }}
            </span>
          </Tab>
        </TabList>
        <TabPanels>
          <!-- Tab 0: Utama -->
          <TabPanel value="0">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-5 lg:gap-6">
              <!-- Main Content (3 cols) -->
              <div class="space-y-4 lg:col-span-3 lg:space-y-6">
                <IncidentInfoCard :incident="incident" />
              </div>

              <!-- Sidebar (2 cols) -->
              <div class="space-y-4 lg:col-span-2 lg:space-y-6">
                <IncidentManagementPanel
                  v-if="incident.status !== 'Ditutup'"
                  :incident="incident"
                  :staff-users="staffUsers"
                />
              </div>
            </div>
          </TabPanel>

          <!-- Tab 1: Riwayat -->
          <TabPanel value="1">
            <div class="max-w-2xl space-y-4">
              <IncidentAddLogForm
                :incident-id="incident.id"
                :is-closed="incident.status === 'Ditutup'"
              />

              <div v-if="incident.incident_logs?.length" class="space-y-3">
                <IncidentLogEntry
                  v-for="log in incident.incident_logs"
                  :key="log.id"
                  :log="log"
                  :incident-id="incident.id"
                  @request-delete="deleteLogId = $event"
                />
              </div>

              <div v-else class="py-12 text-center">
                <IconHistory class="mx-auto mb-2 text-slate-300" size="36" />
                <p class="text-sm text-slate-500">
                  Belum ada riwayat penanganan
                </p>
              </div>
            </div>
          </TabPanel>
        </TabPanels>
      </Tabs>
    </div>

    <!-- Delete Log Dialog -->
    <Dialog
      v-model:visible="deleteLogId"
      modal
      header="Hapus Catatan"
      :style="{ width: '360px' }"
    >
      <p class="text-slate-600">
        Catatan ini akan dihapus permanen beserta lampirannya. Lanjutkan?
      </p>
      <template #footer>
        <Button severity="secondary" @click="deleteLogId = null">Batal</Button>
        <Button severity="danger" @click="handleDeleteLog">Ya, Hapus</Button>
      </template>
    </Dialog>
  </AdminLayout>
</template>
