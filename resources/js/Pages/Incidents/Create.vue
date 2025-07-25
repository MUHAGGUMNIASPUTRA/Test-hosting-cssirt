<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";

const props = defineProps({
  incidentTypes: Array,
});

const toast = useToast();

const form = useForm({
  reporter_name: '',
  reporter_email: '',
  reporter_phone: '',
  incident_type_id: null,
  incident_at: null,
  description: '',
  attachment: null,
});

const uploader = ref(null);
const attachmentPreview = ref(null);

const handleFileSelect = (event) => {
  const file = event.files[0];
  form.attachment = file;

  if (file && file.type.startsWith('image/')) {
    attachmentPreview.value = URL.createObjectURL(file);
  } else {
    attachmentPreview.value = null;
  }
};

const clearAttachment = () => {
    if (uploader.value) {
        uploader.value.clear();
    }
    form.attachment = null;
    attachmentPreview.value = null;
};

const submit = () => {
  form.post(route('incident.store'), {
    onSuccess: () => {
        toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Laporan Anda telah berhasil dikirim. Terima kasih.', life: 3000 });
        form.reset();
        clearAttachment();
    },
  });
};
</script>

<template>
  <AppLayout title="Lapor Insiden Siber">
    <Toast />
    <div class="bg-gray-50">
      <div class="mx-auto max-w-7xl py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
        <div class="relative bg-white shadow-xl rounded-lg">
          <div class="grid grid-cols-1 lg:grid-cols-2">
            <!-- Contact Information -->
            <div class="relative px-6 py-10 sm:px-10 lg:py-12">
              <div class="mx-auto max-w-lg">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">Formulir Pelaporan Insiden</h2>
                <p class="mt-4 text-lg text-gray-500">
                  Gunakan formulir ini untuk melaporkan insiden keamanan siber. Mohon isi data dengan sejelas mungkin untuk mempercepat proses penanganan.
                </p>
                <div class="mt-8 text-base text-gray-500">
                  <h3 class="font-semibold text-gray-900">Sebelum Melapor:</h3>
                  <ul class="mt-2 list-disc list-inside space-y-1">
                    <li>Siapkan kronologi kejadian secara rinci.</li>
                    <li>Sertakan bukti seperti screenshot atau log jika ada.</li>
                    <li>Pastikan informasi kontak Anda aktif dan dapat dihubungi.</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Form -->
            <div class="px-6 py-10 sm:px-10 lg:py-12">
              <div class="mx-auto max-w-lg">
                <form @submit.prevent="submit">
                  <div class="space-y-6">
                    <Message v-if="$page.props.flash && $page.props.flash.success" severity="success" :closable="false">{{ $page.props.flash.success }}</Message>

                    <!-- Reporter Name -->
                    <div>
                      <label for="reporter_name" class="block text-sm font-medium text-gray-700">Nama Pelapor</label>
                      <div class="mt-1">
                        <InputText id="reporter_name" v-model="form.reporter_name" class="w-full" :class="{ 'p-invalid': form.errors.reporter_name }" required />
                        <small v-if="form.errors.reporter_name" class="p-error">{{ form.errors.reporter_name }}</small>
                      </div>
                    </div>

                    <!-- Reporter Email -->
                    <div>
                      <label for="reporter_email" class="block text-sm font-medium text-gray-700">Email</label>
                      <div class="mt-1">
                        <InputText id="reporter_email" v-model="form.reporter_email" type="email" class="w-full" :class="{ 'p-invalid': form.errors.reporter_email }" required />
                        <small v-if="form.errors.reporter_email" class="p-error">{{ form.errors.reporter_email }}</small>
                      </div>
                    </div>

                    <!-- Reporter Phone -->
                    <div>
                      <label for="reporter_phone" class="block text-sm font-medium text-gray-700">No. Telepon (Opsional)</label>
                      <div class="mt-1">
                        <InputText id="reporter_phone" v-model="form.reporter_phone" class="w-full" :class="{ 'p-invalid': form.errors.reporter_phone }" />
                        <small v-if="form.errors.reporter_phone" class="p-error">{{ form.errors.reporter_phone }}</small>
                      </div>
                    </div>

                    <!-- Incident Type -->
                    <div>
                      <label for="incident_type_id" class="block text-sm font-medium text-gray-700">Jenis Insiden</label>
                      <div class="mt-1">
                        <Select id="incident_type_id" v-model="form.incident_type_id" :options="props.incidentTypes" optionLabel="name" optionValue="id" placeholder="Pilih Jenis Insiden" class="w-full" :class="{ 'p-invalid': form.errors.incident_type_id }" required />
                        <small v-if="form.errors.incident_type_id" class="p-error">{{ form.errors.incident_type_id }}</small>
                      </div>
                    </div>

                    <!-- Incident At -->
                    <div>
                      <label for="incident_at" class="block text-sm font-medium text-gray-700">Waktu Kejadian</label>
                      <div class="mt-1">
                        <DatePicker id="incident_at" v-model="form.incident_at" showTime hourFormat="24" class="w-full" :class="{ 'p-invalid': form.errors.incident_at }" required />
                        <small v-if="form.errors.incident_at" class="p-error">{{ form.errors.incident_at }}</small>
                      </div>
                    </div>

                    <!-- Description -->
                    <div>
                      <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Insiden</label>
                      <div class="mt-1">
                        <Textarea id="description" v-model="form.description" rows="5" class="w-full" :class="{ 'p-invalid': form.errors.description }" required />
                        <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
                      </div>
                    </div>

                    <!-- Attachment -->
                    <div>
                      <label for="attachment" class="block text-sm font-medium text-gray-700">Lampiran (Opsional, maks: 5MB)</label>
                       <div class="mt-1">
                        <FileUpload ref="uploader" name="attachment" @select="handleFileSelect" :showUploadButton="false" :showCancelButton="false" :multiple="false" accept=".jpg,.jpeg,.png,.pdf,.zip">
                          <template #content="{ files }">
                            <div v-if="files[0]" class="p-4 border-t border-gray-200">
                              <div class="flex justify-between items-center">
                                <div class="flex items-center gap-4">
                                  <img v-if="attachmentPreview" :src="attachmentPreview" :alt="files[0].name" class="w-16 h-16 object-cover rounded" />
                                  <i v-else class="pi pi-file text-4xl text-gray-500"></i>
                                  <div>
                                    <p class="font-semibold">{{ files[0].name }}</p>
                                    <small class="text-gray-500">{{ (files[0].size / 1024).toFixed(2) }} KB</small>
                                  </div>
                                </div>
                                <Button @click="clearAttachment" icon="pi pi-times" severity="danger" text rounded />
                              </div>
                            </div>
                          </template>
                          <template #empty>
                            <div class="p-8 text-center text-gray-500">
                              <i class="pi pi-upload text-4xl mb-2"></i>
                              <p>Drag & drop file ke sini atau klik untuk memilih.</p>
                            </div>
                          </template>
                        </FileUpload>
                        <small v-if="form.errors.attachment" class="p-error">{{ form.errors.attachment }}</small>
                      </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                      <Button type="submit" label="Kirim Laporan" icon="pi pi-send" :loading="form.processing" />
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
