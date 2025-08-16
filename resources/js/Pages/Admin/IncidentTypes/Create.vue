<script setup>
// filepath: resources/js/Pages/Admin/IncidentTypes/Create.vue

import { router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  incidentType: Object,
})

const { isMobile } = useResponsive();
const isEditing = computed(() => Boolean(props.incidentType))
const pageTitle = computed(() => isEditing.value ? 'Edit Kategori Insiden' : 'Tambah Kategori Insiden')
const submitButtonText = computed(() => isEditing.value ? 'Perbarui Kategori' : 'Simpan Kategori')

const form = useForm({
  name: props.incidentType?.name || '',
  description: props.incidentType?.description || '',
})

const submit = () => {
  if (isEditing.value) {
    form.put(route('admin.incident-types.update', props.incidentType.id))
  } else {
    form.post(route('admin.incident-types.store'))
  }
}
</script>

<template>
  <AdminLayout :title="pageTitle">
    <form @submit.prevent="submit">
      <div class="space-y-4 lg:space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-xl lg:text-2xl font-bold text-slate-900">{{ pageTitle }}</h2>
              <p class="text-slate-600">
                {{ isEditing ? 'Perbarui informasi kategori insiden' : 'Tambah kategori baru untuk klasifikasi insiden' }}
              </p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
              <Button
                severity="secondary"
                @click="() => router.get(route('admin.incident-types.index'))"
                class="w-full sm:w-auto"
              >
                <IconArrowLeft :size="16"/>
                Kembali
              </Button>
              <Button
                v-if="!isMobile"
                type="submit"
                severity="primary"
                :disabled="form.processing"
                class="w-full sm:w-auto"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </Button>
            </div>
          </div>
        </div>

        <!-- Main Form -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
          <!-- Form Fields -->
          <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Kategori</h3>

              <div class="space-y-4">
                <!-- Name Field -->
                <div>
                  <label class="block font-medium text-slate-700 mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    v-model="form.name"
                    placeholder="Contoh: Phishing, Malware, DDoS"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.name }"
                    required
                  />
                  <small v-if="form.errors.name" class="text-xs p-error">{{ form.errors.name }}</small>
                  <small class="text-xs text-slate-500">
                    Nama kategori harus unik dan akan digunakan untuk mengklasifikasikan insiden
                  </small>
                </div>

                <!-- Description Field -->
                <div>
                  <label class="block font-medium text-slate-700 mb-2">
                    Deskripsi
                  </label>
                  <Textarea
                    v-model="form.description"
                    placeholder="Jelaskan jenis insiden yang termasuk dalam kategori ini..."
                    rows="4"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.description }"
                  />
                  <p v-if="form.errors.description" class="text-xs p-error">{{ form.errors.description }}</p>
                  <p v-else class="text-xs text-slate-500">
                    Deskripsi opsional untuk memberikan penjelasan lebih detail tentang kategori
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-4 lg:space-y-6">
            <!-- Preview Card -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 lg:p-6">
              <h3 class="text-lg font-semibold text-slate-900 mb-4">Preview</h3>
              <div class="space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-slate-500">Nama:</span>
                  <span class="text-slate-700 font-medium truncate ml-2">{{ form.name || 'Belum diisi' }}</span>
                </div>
                <div class="flex justify-between items-start">
                  <span class="text-slate-500">Slug:</span>
                  <span class="text-slate-700 text-right font-mono text-sm">
                    {{ form.name ? form.name.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-').trim('-') : 'auto-generated' }}
                  </span>
                </div>
                <div v-if="form.description" class="pt-2 border-t border-slate-200">
                  <span class="text-slate-500 text-sm block mb-1">Deskripsi:</span>
                  <p class="text-slate-700 text-sm line-clamp-3">{{ form.description }}</p>
                </div>
                <div v-if="isEditing && incidentType?.incidents" class="pt-2 border-t border-slate-200">
                  <span class="text-slate-500 text-sm">Digunakan dalam:</span>
                  <Tag
                    :value="`${incidentType.incidents.length} insiden`"
                    :severity="incidentType.incidents.length > 0 ? 'success' : 'secondary'"
                    size="small"
                    class="ml-2"
                  />
                </div>
              </div>
            </div>

            <!-- Usage Info (for editing) -->
            <div v-if="isEditing && incidentType?.incidents?.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
              <div class="flex items-start">
                <IconAlertTriangle class="text-yellow-600 mt-1 mr-2" size="16" />
                <div>
                  <h4 class="font-medium text-yellow-800 mb-2">Kategori Sedang Digunakan</h4>
                  <p class="text-yellow-700 text-sm mb-3">
                    Kategori ini digunakan dalam {{ incidentType.incidents.length }} insiden.
                    Perubahan nama akan mempengaruhi semua insiden terkait.
                  </p>
                  <div class="space-y-1">
                    <p class="text-yellow-700 text-xs font-medium">Beberapa insiden:</p>
                    <div class="space-y-1">
                      <div
                        v-for="incident in incidentType.incidents.slice(0, 3)"
                        :key="incident.id"
                        class="text-xs font-mono bg-yellow-100 px-2 py-1 rounded"
                      >
                        {{ incident.case_id }}
                      </div>
                      <p v-if="incidentType.incidents.length > 3" class="text-xs text-yellow-600">
                        dan {{ incidentType.incidents.length - 3 }} lainnya...
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Guidelines -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
              <h4 class="font-medium text-blue-800 mb-2">Panduan</h4>
              <ul class="text-blue-700 text-sm space-y-1">
                <li>• Gunakan nama yang jelas dan mudah dipahami</li>
                <li>• Hindari nama yang terlalu umum atau ambigu</li>
                <li>• Deskripsi membantu tim memilih kategori yang tepat</li>
                <li>• Slug akan dibuat otomatis dari nama</li>
              </ul>
            </div>

            <!-- Button Submit (Mobile Only) -->
            <div v-if="isMobile">
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </button>
            </div>

          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
