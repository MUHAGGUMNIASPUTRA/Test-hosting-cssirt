<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  incidentTypes: Array,
  staffUsers: Array,
});

const form = useForm({
  reporter_name: '',
  reporter_email: '',
  incident_type_id: null,
  incident_at: new Date(),
  description: '',
  status: 'Baru',
  priority: 'Sedang',
  assigned_to: null,
});

const statusOptions = ['Baru', 'Diverifikasi', 'Dalam Penyelidikan', 'Selesai', 'Ditutup'];
const priorityOptions = ['Rendah', 'Sedang', 'Tinggi', 'Kritis'];

const submit = () => {
  form.post(route('admin.incidents.store'));
};
</script>

<template>
  <AdminLayout title="Buat Laporan Insiden Baru">
    <form @submit.prevent="submit">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Buat Laporan Insiden Baru</h1>
        <Button type="submit" label="Simpan Laporan" icon="pi pi-save" :loading="form.processing" />
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow space-y-6">
          <h3 class="text-lg font-semibold border-b pb-2">Detail Laporan</h3>
          <!-- Reporter Name & Email -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="reporter_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelapor</label>
              <InputText id="reporter_name" v-model="form.reporter_name" class="w-full" :class="{ 'p-invalid': form.errors.reporter_name }" />
              <small v-if="form.errors.reporter_name" class="p-error">{{ form.errors.reporter_name }}</small>
            </div>
            <div>
              <label for="reporter_email" class="block text-sm font-medium text-gray-700 mb-1">Email Pelapor</label>
              <InputText id="reporter_email" v-model="form.reporter_email" class="w-full" :class="{ 'p-invalid': form.errors.reporter_email }" />
              <small v-if="form.errors.reporter_email" class="p-error">{{ form.errors.reporter_email }}</small>
            </div>
          </div>
          <!-- Incident Type & Time -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="incident_type_id" class="block text-sm font-medium text-gray-700 mb-1">Jenis Insiden</label>
              <Select id="incident_type_id" v-model="form.incident_type_id" :options="props.incidentTypes" optionLabel="name" optionValue="id" placeholder="Pilih Jenis" class="w-full" :class="{ 'p-invalid': form.errors.incident_type_id }" />
              <small v-if="form.errors.incident_type_id" class="p-error">{{ form.errors.incident_type_id }}</small>
            </div>
            <div>
              <label for="incident_at" class="block text-sm font-medium text-gray-700 mb-1">Waktu Kejadian</label>
              <DatePicker id="incident_at" v-model="form.incident_at" showTime hourFormat="24" class="w-full" :class="{ 'p-invalid': form.errors.incident_at }" />
              <small v-if="form.errors.incident_at" class="p-error">{{ form.errors.incident_at }}</small>
            </div>
          </div>
          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Insiden</label>
            <Textarea id="description" v-model="form.description" rows="8" class="w-full" :class="{ 'p-invalid': form.errors.description }" />
            <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="bg-white p-6 rounded-lg shadow space-y-6 h-fit">
          <h3 class="text-lg font-semibold border-b pb-2">Manajemen</h3>
          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <Select id="status" v-model="form.status" :options="statusOptions" placeholder="Pilih Status" class="w-full" />
          </div>
          <!-- Priority -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
            <SelectButton v-model="form.priority" :options="priorityOptions" aria-labelledby="priority" />
          </div>
          <!-- Assigned To -->
          <div>
            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Tugaskan Kepada</label>
            <Select id="assigned_to" v-model="form.assigned_to" :options="props.staffUsers" optionLabel="name" optionValue="id" placeholder="Pilih Staf" class="w-full" :class="{ 'p-invalid': form.errors.assigned_to }" />
            <small v-if="form.errors.assigned_to" class="p-error">{{ form.errors.assigned_to }}</small>
          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
