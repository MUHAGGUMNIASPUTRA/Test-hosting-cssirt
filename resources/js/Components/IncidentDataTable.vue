<!-- Tujuan: Tabel data insiden dengan kolom lengkap dan dialog hapus -->
<!-- Caller: Admin/Incidents/Index.vue -->
<!-- Side Effects: router.delete admin.incidents.destroy -->
<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { formatDatetime } from '@/utils/date'

defineProps({
  data: { type: Array, default: () => [] },
  serverConfig: { type: Object, required: true },
  hasActiveFilters: { type: Boolean, default: false },
})

defineEmits(['page'])

const showDeleteDialog = ref(false)
const incidentToDelete = ref(null)

const confirmDelete = (incident) => {
  incidentToDelete.value = incident
  showDeleteDialog.value = true
}

const handleDelete = () => {
  if (!incidentToDelete.value) return
  router.delete(route('admin.incidents.destroy', incidentToDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      incidentToDelete.value = null
    },
  })
}
</script>

<template>
  <div>
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="insiden berikut"
      delete-label="Ya, Hapus Insiden"
      @confirm="handleDelete"
    >
      <template #item-info>
        <div class="mb-1 flex items-center justify-between">
          <span class="font-medium text-slate-600">ID Insiden:</span>
          <span
            class="rounded bg-slate-200 px-2 py-1 font-mono text-xs text-slate-900"
          >
            {{ incidentToDelete?.case_id }}
          </span>
        </div>
        <div class="flex items-center justify-between">
          <span class="font-medium text-slate-600">Pelapor:</span>
          <span class="text-slate-900">{{
            incidentToDelete?.reporter_name
          }}</span>
        </div>
      </template>
    </DeleteConfirmDialog>

    <AdminDataTable
      :value="data"
      :server-config="serverConfig"
      @page="$emit('page', $event)"
    >
      <template #empty>
        <div class="py-12 text-center">
          <IconMailExclamation size="30" class="mx-auto mb-4 text-slate-300" />
          <p class="text-lg font-medium text-slate-500">
            {{
              hasActiveFilters
                ? 'Tidak ada insiden yang sesuai filter'
                : 'Belum ada insiden yang dilaporkan'
            }}
          </p>
          <p class="mt-1 text-sm text-slate-400">
            {{
              hasActiveFilters
                ? 'Coba ubah kriteria pencarian'
                : 'Insiden yang dilaporkan akan muncul di sini'
            }}
          </p>
        </div>
      </template>

      <Column field="case_id" header="ID Insiden">
        <template #body="{ data: row }">
          <Link :href="route('admin.incidents.show', row.id)">
            <Tag
              :value="row.case_id"
              severity="secondary"
              size="small"
              class="font-mono !text-slate-500"
            />
          </Link>
          <div class="mt-1 space-x-1 text-xs text-slate-500 lg:hidden">
            <span>{{ row.reporter_name }}</span>
            <span>•</span>
            <span class="text-slate-400">{{
              row.incident_type?.name || 'N/A'
            }}</span>
          </div>
        </template>
      </Column>

      <Column
        field="reporter_name"
        header="Pelapor"
        class="hidden lg:table-cell"
      >
        <template #body="{ data: row }">
          <div class="text-sm font-medium text-slate-700">
            {{ row.reporter_name }}
          </div>
          <div class="text-sm text-slate-500">{{ row.reporter_email }}</div>
        </template>
      </Column>

      <Column
        field="incident_type"
        header="Kategori"
        class="hidden lg:table-cell"
      >
        <template #body="{ data: row }">
          <span class="text-sm text-slate-700">{{
            row.incident_type?.name || 'N/A'
          }}</span>
        </template>
      </Column>

      <Column field="status" header="Status" class="hidden lg:table-cell">
        <template #body="{ data: row }">
          <StatusBadge type="incident-status" :value="row.status" />
        </template>
      </Column>

      <Column field="priority" header="Prioritas" class="hidden lg:table-cell">
        <template #body="{ data: row }">
          <StatusBadge type="priority" :value="row.priority" />
        </template>
      </Column>

      <Column header="Aset Virtual" class="hidden xl:table-cell">
        <template #body="{ data: row }">
          <IncidentAssetLinks
            :web-applications="row.web_applications ?? []"
            :mobile-applications="row.mobile_applications ?? []"
          />
        </template>
      </Column>

      <Column header="Ditugaskan ke" class="hidden xl:table-cell">
        <template #body="{ data: row }">
          <span
            v-if="row.assigned_user"
            class="text-sm font-medium text-slate-700"
          >
            {{ row.assigned_user.name }}
          </span>
          <span v-else class="text-sm text-slate-400">—</span>
        </template>
      </Column>

      <Column
        field="reported_at"
        header="Dilaporkan"
        class="hidden lg:table-cell"
      >
        <template #body="{ data: row }">
          <span class="text-sm text-slate-500">{{
            formatDatetime(row.reported_at)
          }}</span>
        </template>
      </Column>

      <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
        <template #body="{ data: row }">
          <IncidentRowActions :item="row" @delete="confirmDelete" />
        </template>
      </Column>
    </AdminDataTable>
  </div>
</template>
