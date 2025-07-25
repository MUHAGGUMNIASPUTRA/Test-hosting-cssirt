<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  incidents: Object,
});

const getStatusSeverity = (status) => {
  const map = {
    'Baru': 'info',
    'Diverifikasi': 'info',
    'Dalam Penyelidikan': 'warning',
    'Selesai': 'success',
    'Ditutup': 'secondary',
  };
  return map[status] || 'info';
};

const getPrioritySeverity = (priority) => {
  const map = {
    'Rendah': 'success',
    'Sedang': 'info',
    'Tinggi': 'warning',
    'Kritis': 'danger',
  };
  return map[priority] || 'info';
};
</script>

<template>
  <AdminLayout title="Daftar Laporan Insiden">
    <div class="bg-white p-6 rounded-lg shadow">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Daftar Laporan Insiden</h1>
        <Link :href="route('admin.incidents.create')">
          <Button label="Lapor Insiden Baru" icon="pi pi-plus" />
        </Link>
      </div>

      <DataTable :value="incidents.data" paginator :rows="10" tableStyle="min-width: 50rem" stripedRows>
        <Column field="case_id" header="ID Kasus" style="width: 15%"></Column>
        <Column field="reporter_name" header="Pelapor"></Column>
        <Column header="Jenis Insiden">
          <template #body="slotProps">
            {{ slotProps.data.incident_type.name }}
          </template>
        </Column>
        <Column header="Prioritas">
          <template #body="slotProps">
            <Tag :value="slotProps.data.priority" :severity="getPrioritySeverity(slotProps.data.priority)" />
          </template>
        </Column>
        <Column header="Status">
          <template #body="slotProps">
            <Tag :value="slotProps.data.status" :severity="getStatusSeverity(slotProps.data.status)" />
          </template>
        </Column>
        <Column header="Dilaporkan Pada">
           <template #body="slotProps">
            {{ new Date(slotProps.data.reported_at).toLocaleString('id-ID') }}
          </template>
        </Column>
        <Column header="Aksi" style="width: 10%">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Link :href="route('admin.incidents.show', slotProps.data.id)">
                <Button icon="pi pi-eye" severity="secondary" text rounded tooltip="Lihat Detail" />
              </Link>
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AdminLayout>
</template>
