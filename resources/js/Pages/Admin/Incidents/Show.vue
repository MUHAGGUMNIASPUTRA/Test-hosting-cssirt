<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { ref } from 'vue';

const props = defineProps({
  incident: Object,
  staffUsers: Array,
});

const toast = useToast();

const logForm = useForm({
  log_message: '',
});

const managementForm = useForm({
  status: props.incident.status,
  priority: props.incident.priority,
  assigned_to: props.incident.assigned_to,
});

const statusOptions = ref(['Baru', 'Diverifikasi', 'Dalam Penyelidikan', 'Selesai', 'Ditutup']);
const priorityOptions = ref(['Rendah', 'Sedang', 'Tinggi', 'Kritis']);

const submitLog = () => {
  logForm.post(route('admin.incidents.logs.store', props.incident.id), {
    preserveScroll: true,
    onSuccess: () => {
      logForm.reset();
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Catatan berhasil ditambahkan.', life: 3000 });
    },
  });
};

const submitManagement = () => {
  managementForm.put(route('admin.incidents.management.update', props.incident.id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Status insiden berhasil diperbarui.', life: 3000 });
    }
  });
};

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
    <Toast />
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
              <a :href="`/storage/${incident.attachment}`" target="_blank" class="text-blue-600 hover:underline ml-2">
                Lihat Lampiran
              </a>
            </div>
          </template>
        </Card>
        <Card>
          <template #title>Manajemen Insiden</template>
          <template #content>
             <form @submit.prevent="submitManagement">
               <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                  <div>
                      <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                      <Select id="status" v-model="managementForm.status" :options="statusOptions" class="w-full" />
                  </div>
                   <div>
                      <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                      <Select id="priority" v-model="managementForm.priority" :options="priorityOptions" class="w-full" />
                  </div>
                   <div>
                      <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Ditugaskan Kepada</label>
                      <Select id="assigned_to" v-model="managementForm.assigned_to" :options="props.staffUsers" optionLabel="name" optionValue="id" placeholder="Pilih Staf" class="w-full" />
                  </div>
               </div>
               <div class="mt-4 flex justify-end">
                  <Button type="submit" label="Update Status" :loading="managementForm.processing" />
               </div>
             </form>
          </template>
        </Card>
      </div>

      <!-- Right Column: Logs/Timeline -->
      <div class="lg:col-span-1">
        <Card>
          <template #title>Riwayat Penanganan</template>
          <template #content>
            <!-- Timeline Section -->
            <div v-if="incident.incident_logs.length > 0" class="mt-3">
              <div v-for="(log, index) in incident.incident_logs" :key="log.id" class="relative flex items-start gap-4 pb-4">
                <!-- Vertical line connector -->
                <div v-if="index < incident.incident_logs.length - 1" class="absolute left-4 top-5 h-full w-px bg-gray-200"></div>
                <!-- Marker -->
                <div class="relative z-10 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-blue-500 text-white">
                  <i class="pi pi-user"></i>
                </div>
                <!-- Content -->
                <div class="-mt-1">
                  <p class="font-semibold text-gray-800">{{ log.user.name }}</p>
                  <p class="text-sm text-gray-600">{{ log.log_message }}</p>
                  <small class="text-gray-400">{{ new Date(log.created_at).toLocaleString('id-ID') }}</small>
                </div>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500">Belum ada riwayat penanganan.</p>

            <form @submit.prevent="submitLog" class="mt-3">
                <Textarea v-model="logForm.log_message" placeholder="Tambah catatan baru..." rows="3" class="w-full" :class="{ 'p-invalid': logForm.errors.log_message }" />
                <small v-if="logForm.errors.log_message" class="p-error">{{ logForm.errors.log_message }}</small>
                <Button type="submit" label="Tambah Catatan" icon="pi pi-plus" class="mt-2 w-full" :loading="logForm.processing" />
            </form>
          </template>
        </Card>
      </div>
    </div>
  </AdminLayout>
</template>
