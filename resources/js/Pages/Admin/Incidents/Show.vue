<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  incident: Object,
});

const getStatusSeverity = (status) => {
  const map = { 'Baru': 'info', 'Diverifikasi': 'info', 'Dalam Penyelidikan': 'warning', 'Selesai': 'success', 'Ditutup': 'secondary' };
  return map[status] || 'info';
};

const getPrioritySeverity = (priority) => {
  const map = { 'Rendah': 'success', 'Sedang': 'info', 'Tinggi': 'warning', 'Kritis': 'danger' };
  return map[priority] || 'info';
};
</script>

<template>
  <AdminLayout :title="`Detail Insiden: ${incident.case_id}`">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-700">Detail Insiden</h1>
        <p class="text-gray-500">{{ incident.case_id }}</p>
      </div>
      <Link :href="route('admin.incidents.index')">
        <Button label="Kembali ke Daftar" icon="pi pi-arrow-left" severity="secondary" outlined />
      </Link>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Details & Management -->
      <div class="lg:col-span-2 space-y-6">
        <Card>
          <template #title>Informasi Laporan</template>
          <template #content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div><strong>Pelapor:</strong> {{ incident.reporter_name }}</div>
              <div><strong>Email:</strong> {{ incident.reporter_email }}</div>
              <div><strong>Telepon:</strong> {{ incident.reporter_phone || '-' }}</div>
              <div><strong>Jenis Insiden:</strong> {{ incident.incident_type.name }}</div>
              <div><strong>Waktu Kejadian:</strong> {{ new Date(incident.incident_at).toLocaleString('id-ID') }}</div>
              <div><strong>Waktu Lapor:</strong> {{ new Date(incident.reported_at).toLocaleString('id-ID') }}</div>
            </div>
            <div class="mt-4">
              <strong>Deskripsi:</strong>
              <p class="mt-1 text-gray-700 whitespace-pre-wrap">{{ incident.description }}</p>
            </div>
            <div v-if="incident.attachment" class="mt-4">
              <strong>Lampiran:</strong>
              <a :href="`/storage/${incident.attachment}`" target="_blank" class="text-red-600 hover:underline ml-2">
                Lihat Lampiran
              </a>
            </div>
          </template>
        </Card>
        <Card>
          <template #title>Manajemen Insiden</template>
          <template #content>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <Tag :value="incident.status" :severity="getStatusSeverity(incident.status)" class="text-lg" />
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                    <Tag :value="incident.priority" :severity="getPrioritySeverity(incident.priority)" class="text-lg" />
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ditugaskan Kepada</label>
                    <p class="font-semibold">{{ incident.assigned_user ? incident.assigned_user.name : 'Belum Ditugaskan' }}</p>
                </div>
             </div>
             <!-- Form untuk update status/prioritas bisa ditambahkan di sini -->
          </template>
        </Card>
      </div>

      <!-- Right Column: Logs/Timeline -->
      <div class="lg:col-span-1">
        <Card>
          <template #title>Riwayat Penanganan</template>
          <template #content>
            <Timeline :value="incident.incident_logs" align="left" class="w-full md:w-20rem">
              <template #marker="slotProps">
                  <span class="flex w-8 h-8 items-center justify-center text-white rounded-full z-10 shadow-md" :class="{ 'bg-blue-500': !slotProps.item.user, 'bg-green-500': slotProps.item.user }">
                      <i class="pi pi-user"></i>
                  </span>
              </template>
              <template #content="slotProps">
                <div class="ml-4">
                  <p class="font-semibold">{{ slotProps.item.user.name }}</p>
                  <p class="text-sm text-gray-600">{{ slotProps.item.log_message }}</p>
                  <small class="text-gray-400">{{ new Date(slotProps.item.created_at).toLocaleString('id-ID') }}</small>
                </div>
              </template>
            </Timeline>
            <div class="mt-6">
                <Textarea placeholder="Tambah catatan baru..." rows="3" class="w-full" />
                <Button label="Tambah Catatan" icon="pi pi-plus" class="mt-2 w-full" />
            </div>
          </template>
        </Card>
      </div>
    </div>
  </AdminLayout>
</template>
