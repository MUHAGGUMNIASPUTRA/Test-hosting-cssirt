<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  incidentType: {
    type: Object,
    default: null,
  },
});

const { isMobile } = useResponsive();

const isEditMode = computed(() => !!props.incidentType);

const form = useForm({
  name: props.incidentType?.name || '',
  description: props.incidentType?.description || '',
  guide: props.incidentType?.guide || '',
});

const submit = () => {
  if (isEditMode.value) {
    form.put(route('admin.incident-types.update', props.incidentType.id));
  } else {
    form.post(route('admin.incident-types.store'));
  }
};
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Jenis Insiden' : 'Tambah Jenis Insiden'">
    <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
              {{ isEditMode ? 'Edit Jenis Insiden' : 'Tambah Jenis Insiden' }}
            </h2>
            <p class="text-slate-600">
              {{ isEditMode ? 'Perbarui informasi jenis insiden' : 'Buat jenis insiden baru' }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <Link
              :href="route('admin.incident-types.index')"
              class="bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
            >
              <IconArrowLeft size="16" />
              Kembali
            </Link>
            <button
              v-if="!isMobile"
              type="submit"
              :disabled="form.processing"
              class="bg-blue-600 hover:bg-blue-700 text-white inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
            >
              <IconLoader3 v-if="form.processing" class="animate-spin" size="16" />
              <IconDeviceFloppy v-else size="16" />
              {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Form Content -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">

        <!-- Name -->
        <div>
          <label class="block font-medium text-gray-700 mb-2">
            Nama Jenis Insiden <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="form.name"
            class="w-full"
            :class="{ 'p-invalid': form.errors.name }"
            placeholder="Contoh: Phishing, Malware, DDoS..."
            required
          />
          <small v-if="form.errors.name" class="p-error block mt-1">{{ form.errors.name }}</small>
        </div>

        <!-- Description -->
        <div>
          <label class="block font-medium text-gray-700 mb-2">
            Deskripsi Singkat <span class="text-slate-400 font-normal">(Opsional)</span>
          </label>
          <Textarea
            v-model="form.description"
            rows="3"
            class="w-full"
            :class="{ 'p-invalid': form.errors.description }"
            placeholder="Jelaskan secara singkat tentang jenis insiden ini. Deskripsi ini akan ditampilkan kepada pelapor sebagai referensi..."
          />
          <small v-if="form.errors.description" class="p-error block mt-1">{{ form.errors.description }}</small>
        </div>

        <!-- Guide (HTML Editor) -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block font-medium text-gray-700">
              Panduan Pelaporan <span class="text-slate-400 font-normal">(Opsional, format HTML)</span>
            </label>
          </div>
          <p class="text-sm text-slate-500 mb-3">
            Panduan ini akan ditampilkan kepada pelapor saat mereka memilih jenis insiden ini pada formulir pelaporan publik.
            Gunakan tag HTML sederhana seperti <code class="bg-slate-100 px-1 rounded">&lt;h3&gt;</code>,
            <code class="bg-slate-100 px-1 rounded">&lt;p&gt;</code>,
            <code class="bg-slate-100 px-1 rounded">&lt;ul&gt;</code>,
            <code class="bg-slate-100 px-1 rounded">&lt;ol&gt;</code>,
            <code class="bg-slate-100 px-1 rounded">&lt;li&gt;</code>,
            <code class="bg-slate-100 px-1 rounded">&lt;strong&gt;</code>.
          </p>
          <Textarea
            v-model="form.guide"
            rows="16"
            class="w-full font-mono text-sm"
            :class="{ 'p-invalid': form.errors.guide }"
            placeholder="<h3>Panduan Pelaporan</h3>
<p>Informasi yang perlu disertakan:</p>
<ul>
  <li><strong>Poin pertama:</strong> Deskripsi</li>
  <li><strong>Poin kedua:</strong> Deskripsi</li>
</ul>
<p><strong>Tindakan segera:</strong></p>
<ol>
  <li>Langkah pertama</li>
  <li>Langkah kedua</li>
</ol>"
          />
          <small v-if="form.errors.guide" class="p-error block mt-1">{{ form.errors.guide }}</small>

          <!-- Guide Preview -->
          <div v-if="form.guide" class="mt-4">
            <p class="text-sm font-medium text-slate-600 mb-2">Preview Panduan:</p>
            <div
              class="prose prose-sm max-w-none p-4 bg-blue-50 border border-blue-200 rounded-lg text-slate-700"
              v-html="form.guide"
            />
          </div>
        </div>
      </div>

      <!-- Mobile Submit Button -->
      <div class="block sm:hidden">
        <button
          type="submit"
          :disabled="form.processing"
          class="bg-blue-600 hover:bg-blue-700 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
        >
          <IconLoader3 v-if="form.processing" class="animate-spin" size="16" />
          <IconDeviceFloppy v-else size="16" />
          {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update' : 'Simpan' }}
        </button>
      </div>
    </form>
  </AdminLayout>
</template>
