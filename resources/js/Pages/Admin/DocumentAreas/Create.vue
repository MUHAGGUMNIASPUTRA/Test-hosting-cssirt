<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  documentArea: {
    type: Object,
    default: null,
  },
})

const { isMobile } = useResponsive()
const isEditMode = computed(() => !!props.documentArea)

const form = useForm({
  name: props.documentArea?.name || '',
  description: props.documentArea?.description || '',
})

const submit = () => {
  if (isEditMode.value) {
    form
      .transform((data) => ({ ...data, _method: 'PUT' }))
      .post(route('admin.document-areas.update', props.documentArea.id))
  } else {
    form.post(route('admin.document-areas.store'))
  }
}
</script>

<template>
  <AdminLayout
    :title="isEditMode ? 'Edit Area Dokumen' : 'Tambah Area Dokumen'"
  >
    <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
              {{ isEditMode ? 'Edit Area Dokumen' : 'Tambah Area Dokumen' }}
            </h2>
            <p class="text-slate-600">
              {{
                isEditMode
                  ? 'Perbarui informasi area dokumen'
                  : 'Buat area/kategori dokumen baru'
              }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <Link
              :href="route('admin.document-areas.index')"
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

      <!-- Form -->
      <div
        class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
      >
        <!-- Usage info in edit mode -->
        <div
          v-if="isEditMode && documentArea.documents_count > 0"
          class="flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3"
        >
          <IconInfoCircle size="18" class="flex-shrink-0 text-blue-500" />
          <p class="text-sm text-blue-700">
            Area ini digunakan oleh
            <strong>{{ documentArea.documents_count }} dokumen</strong>.
          </p>
        </div>

        <!-- Name -->
        <div>
          <label class="mb-2 block font-medium text-gray-700">
            Nama Area <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="form.name"
            class="w-full"
            :class="{ 'p-invalid': form.errors.name }"
            placeholder="Contoh: Tata Kelola Keamanan Informasi..."
            required
          />
          <small v-if="form.errors.name" class="p-error mt-1 block">{{
            form.errors.name
          }}</small>
        </div>

        <!-- Description -->
        <div>
          <label class="mb-2 block font-medium text-gray-700">
            Deskripsi <span class="font-normal text-slate-400">(Opsional)</span>
          </label>
          <Textarea
            v-model="form.description"
            rows="4"
            class="w-full"
            :class="{ 'p-invalid': form.errors.description }"
            placeholder="Jelaskan secara singkat area dokumen ini..."
          />
          <small v-if="form.errors.description" class="p-error mt-1 block">{{
            form.errors.description
          }}</small>
        </div>
      </div>

      <!-- Mobile Submit -->
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
