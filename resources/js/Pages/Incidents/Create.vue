<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Calendar from 'primevue/calendar'
import Textarea from 'primevue/textarea'
import FileUpload from 'primevue/fileupload'
import Button from 'primevue/button'
import Message from 'primevue/message'

const props = defineProps({
  incidentTypes: Array,
})

const form = useForm({
  reporter_name: '',
  reporter_email: '',
  reporter_phone: '',
  incident_type_id: null,
  incident_at: null,
  description: '',
  attachment: null,
})

const handleFileUpload = (event) => {
  form.attachment = event.files[0]
}

const submit = () => {
  form.post(route('incident.store'), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <AppLayout title="Lapor Insiden Siber">
    <div class="bg-gray-50">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="relative rounded-lg bg-white shadow-xl">
          <div class="grid grid-cols-1 lg:grid-cols-2">
            <!-- Contact Information -->
            <div class="relative px-6 py-10 sm:px-10 lg:py-12">
              <div class="mx-auto max-w-lg">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                  Formulir Pelaporan Insiden
                </h2>
                <p class="mt-4 text-lg text-gray-500">
                  Gunakan formulir ini untuk melaporkan insiden keamanan siber.
                  Mohon isi data dengan sejelas mungkin untuk mempercepat proses
                  penanganan.
                </p>
                <div class="mt-8 text-base text-gray-500">
                  <h3 class="font-semibold text-gray-900">Sebelum Melapor:</h3>
                  <ul class="mt-2 list-inside list-disc space-y-1">
                    <li>Siapkan kronologi kejadian secara rinci.</li>
                    <li>
                      Sertakan bukti seperti screenshot atau log jika ada.
                    </li>
                    <li>
                      Pastikan informasi kontak Anda aktif dan dapat dihubungi.
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Form -->
            <div class="px-6 py-10 sm:px-10 lg:py-12">
              <div class="mx-auto max-w-lg">
                <form @submit.prevent="submit">
                  <div class="space-y-6">
                    <!-- Success Message (FIXED) -->
                    <Message
                      v-if="$page.props.flash?.success"
                      severity="success"
                      :closable="false"
                      >{{ $page.props.flash?.success }}</Message
                    >

                    <!-- Reporter Name -->
                    <div>
                      <label
                        for="reporter_name"
                        class="block text-sm font-medium text-gray-700"
                        >Nama Pelapor</label
                      >
                      <div class="mt-1">
                        <InputText
                          id="reporter_name"
                          v-model="form.reporter_name"
                          class="w-full"
                          :class="{ 'p-invalid': form.errors.reporter_name }"
                        />
                        <small
                          v-if="form.errors.reporter_name"
                          class="p-error"
                          >{{ form.errors.reporter_name }}</small
                        >
                      </div>
                    </div>

                    <!-- Reporter Email -->
                    <div>
                      <label
                        for="reporter_email"
                        class="block text-sm font-medium text-gray-700"
                        >Email</label
                      >
                      <div class="mt-1">
                        <InputText
                          id="reporter_email"
                          v-model="form.reporter_email"
                          class="w-full"
                          :class="{ 'p-invalid': form.errors.reporter_email }"
                        />
                        <small
                          v-if="form.errors.reporter_email"
                          class="p-error"
                          >{{ form.errors.reporter_email }}</small
                        >
                      </div>
                    </div>

                    <!-- Reporter Phone -->
                    <div>
                      <label
                        for="reporter_phone"
                        class="block text-sm font-medium text-gray-700"
                        >No. Telepon (Opsional)</label
                      >
                      <div class="mt-1">
                        <InputText
                          id="reporter_phone"
                          v-model="form.reporter_phone"
                          class="w-full"
                          :class="{ 'p-invalid': form.errors.reporter_phone }"
                        />
                        <small
                          v-if="form.errors.reporter_phone"
                          class="p-error"
                          >{{ form.errors.reporter_phone }}</small
                        >
                      </div>
                    </div>

                    <!-- Incident Type -->
                    <div>
                      <label
                        for="incident_type_id"
                        class="block text-sm font-medium text-gray-700"
                        >Jenis Insiden</label
                      >
                      <div class="mt-1">
                        <Dropdown
                          id="incident_type_id"
                          v-model="form.incident_type_id"
                          :options="props.incidentTypes"
                          optionLabel="name"
                          optionValue="id"
                          placeholder="Pilih Jenis Insiden"
                          class="w-full"
                          :class="{ 'p-invalid': form.errors.incident_type_id }"
                        />
                        <small
                          v-if="form.errors.incident_type_id"
                          class="p-error"
                          >{{ form.errors.incident_type_id }}</small
                        >
                      </div>
                    </div>

                    <!-- Incident At -->
                    <div>
                      <label
                        for="incident_at"
                        class="block text-sm font-medium text-gray-700"
                        >Waktu Kejadian</label
                      >
                      <div class="mt-1">
                        <Calendar
                          id="incident_at"
                          v-model="form.incident_at"
                          showTime
                          hourFormat="24"
                          class="w-full"
                          :class="{ 'p-invalid': form.errors.incident_at }"
                        />
                        <small v-if="form.errors.incident_at" class="p-error">{{
                          form.errors.incident_at
                        }}</small>
                      </div>
                    </div>

                    <!-- Description -->
                    <div>
                      <label
                        for="description"
                        class="block text-sm font-medium text-gray-700"
                        >Deskripsi Insiden</label
                      >
                      <div class="mt-1">
                        <Textarea
                          id="description"
                          v-model="form.description"
                          rows="5"
                          class="w-full"
                          :class="{ 'p-invalid': form.errors.description }"
                        />
                        <small v-if="form.errors.description" class="p-error">{{
                          form.errors.description
                        }}</small>
                      </div>
                    </div>

                    <!-- Attachment -->
                    <div>
                      <label
                        for="attachment"
                        class="block text-sm font-medium text-gray-700"
                        >Lampiran (Opsional, maks: 5MB)</label
                      >
                      <div class="mt-1">
                        <FileUpload
                          name="attachment"
                          @select="handleFileUpload"
                          :showUploadButton="false"
                          :showCancelButton="false"
                          :multiple="false"
                          accept=".jpg,.jpeg,.png,.pdf,.zip"
                        >
                          <template #empty>
                            <p>
                              Drag and drop file ke sini atau klik untuk
                              memilih.
                            </p>
                          </template>
                        </FileUpload>
                        <small v-if="form.errors.attachment" class="p-error">{{
                          form.errors.attachment
                        }}</small>
                      </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                      <Button
                        type="submit"
                        label="Kirim Laporan"
                        icon="pi pi-send"
                        :loading="form.processing"
                      />
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
