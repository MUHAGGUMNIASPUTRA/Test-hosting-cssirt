<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  incident: Object,
  staffUsers: Array,
})

const getStatusSeverity = (status) => {
  const map = {
    Baru: 'info',
    Diverifikasi: 'primary',
    'Dalam Penyelidikan': 'warn',
    Selesai: 'success',
    Ditutup: 'secondary',
  }
  return map[status] || 'info'
}

const getPrioritySeverity = (priority) => {
  const map = {
    Rendah: 'success',
    Sedang: 'info',
    Tinggi: 'warn',
    Kritikal: 'danger',
  }
  return map[priority] || 'info'
}

// --- Delete log ---
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
      <!-- Header Section -->
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

      <!-- Body: 3+2 grid on large screens -->
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
          <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
          >
            <div class="mb-4 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-purple-200 bg-purple-50"
              >
                <IconTimeline
                  class="text-purple-600"
                  :size="!isDesktop ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="text-xl/6 font-semibold text-slate-900">
                  Riwayat Penanganan
                </h3>
                <p class="text-xs text-slate-600 lg:text-sm">
                  {{ incident.incident_logs.length }} catatan
                </p>
              </div>
            </div>

            <IncidentAddLogForm
              :incident-id="incident.id"
              :is-closed="incident.status === 'Ditutup'"
            />

            <div v-if="incident.incident_logs.length > 0" class="space-y-3">
              <IncidentLogEntry
                v-for="log in incident.incident_logs"
                :key="log.id"
                :log="log"
                :incident-id="incident.id"
                @request-delete="deleteLogId = $event"
              />
            </div>

            <div v-else class="mb-5 py-6 text-center">
              <IconHistory class="mx-auto mb-2 text-slate-300" size="32" />
              <p class="text-sm text-slate-500">Belum ada riwayat penanganan</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Log Confirmation Dialog -->
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
