<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  incidentType: {
    type: Object,
    default: null,
  },
})

const { isMobile } = useResponsive()

const isEditMode = computed(() => !!props.incidentType)

const form = useForm({
  name: props.incidentType?.name || '',
  description: props.incidentType?.description || '',
  guide: props.incidentType?.guide || '',
})

const submit = () => {
  if (isEditMode.value) {
    form
      .transform((data) => ({ ...data, _method: 'PUT' }))
      .post(route('admin.incident-types.update', props.incidentType.id))
  } else {
    form.post(route('admin.incident-types.store'))
  }
}
</script>

<template>
  <AdminLayout
    :title="isEditMode ? 'Edit Jenis Insiden' : 'Tambah Jenis Insiden'"
  >
    <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
              {{ isEditMode ? 'Edit Jenis Insiden' : 'Tambah Jenis Insiden' }}
            </h2>
            <p class="text-slate-600">
              {{
                isEditMode
                  ? 'Perbarui informasi jenis insiden'
                  : 'Buat jenis insiden baru'
              }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <Link
              :href="route('admin.incident-types.index')"
              class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-100 px-4 py-2 text-slate-600 transition hover:bg-slate-200"
            >
              <IconArrowLeft size="16" />
              Kembali
            </Link>
            <button
              v-if="!isMobile"
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
            >
              <IconLoader3
                v-if="form.processing"
                class="animate-spin"
                size="16"
              />
              <IconDeviceFloppy v-else size="16" />
              {{
                form.processing
                  ? 'Menyimpan...'
                  : isEditMode
                    ? 'Update'
                    : 'Simpan'
              }}
            </button>
          </div>
        </div>
      </div>

      <!-- Form Content -->
      <div
        class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
      >
        <!-- Name -->
        <div>
          <label class="mb-2 block font-medium text-gray-700">
            Nama Jenis Insiden <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="form.name"
            class="w-full"
            :class="{ 'p-invalid': form.errors.name }"
            placeholder="Contoh: Phishing, Malware, DDoS..."
            required
          />
          <small v-if="form.errors.name" class="p-error mt-1 block">{{
            form.errors.name
          }}</small>
        </div>

        <!-- Description -->
        <div>
          <label class="mb-2 block font-medium text-gray-700">
            Deskripsi Singkat
            <span class="font-normal text-slate-400">(Opsional)</span>
          </label>
          <Textarea
            v-model="form.description"
            rows="3"
            class="w-full"
            :class="{ 'p-invalid': form.errors.description }"
            placeholder="Jelaskan secara singkat tentang jenis insiden ini. Deskripsi ini akan ditampilkan kepada pelapor sebagai referensi..."
          />
          <small v-if="form.errors.description" class="p-error mt-1 block">{{
            form.errors.description
          }}</small>
        </div>

        <!-- Guide (HTML Editor) -->
        <div>
          <div class="mb-2 flex items-center justify-between">
            <label class="block font-medium text-gray-700">
              Panduan Pelaporan
              <span class="font-normal text-slate-400"
                >(Opsional, format HTML)</span
              >
            </label>
          </div>
          <p class="mb-3 text-sm text-slate-500">
            Panduan ini akan ditampilkan kepada pelapor saat mereka memilih
            jenis insiden ini pada formulir pelaporan publik. Gunakan tag HTML
            sederhana seperti
            <code class="rounded bg-slate-100 px-1">&lt;h3&gt;</code>,
            <code class="rounded bg-slate-100 px-1">&lt;p&gt;</code>,
            <code class="rounded bg-slate-100 px-1">&lt;ul&gt;</code>,
            <code class="rounded bg-slate-100 px-1">&lt;ol&gt;</code>,
            <code class="rounded bg-slate-100 px-1">&lt;li&gt;</code>,
            <code class="rounded bg-slate-100 px-1">&lt;strong&gt;</code>.
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
          <small v-if="form.errors.guide" class="p-error mt-1 block">{{
            form.errors.guide
          }}</small>

          <!-- Guide Preview -->
          <div v-if="form.guide" class="mt-4">
            <p class="mb-2 text-sm font-medium text-slate-600">
              Preview Panduan:
            </p>
            <div
              class="prose prose-sm max-w-none rounded-lg border border-blue-200 bg-blue-50 p-4 text-slate-700"
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
          class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
          <IconLoader3 v-if="form.processing" class="animate-spin" size="16" />
          <IconDeviceFloppy v-else size="16" />
          {{
            form.processing ? 'Menyimpan...' : isEditMode ? 'Update' : 'Simpan'
          }}
        </button>
      </div>
    </form>
  </AdminLayout>
</template>
