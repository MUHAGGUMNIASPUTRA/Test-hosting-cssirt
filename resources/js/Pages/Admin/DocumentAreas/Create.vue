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
    form.put(route('admin.document-areas.update', props.documentArea.id))
  } else {
    form.post(route('admin.document-areas.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Area Dokumen' : 'Tambah Area Dokumen'">
    <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
              {{ isEditMode ? 'Edit Area Dokumen' : 'Tambah Area Dokumen' }}
            </h2>
            <p class="text-slate-600">
              {{ isEditMode ? 'Perbarui informasi area dokumen' : 'Buat area/kategori dokumen baru' }}
            </p>
          </div>
          <div class="flex items-center gap-3">
            <Link
              :href="route('admin.document-areas.index')"
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

      <!-- Form -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        <!-- Usage info in edit mode -->
        <div v-if="isEditMode && documentArea.documents_count > 0" class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
          <IconInfoCircle size="18" class="text-blue-500 flex-shrink-0" />
          <p class="text-sm text-blue-700">
            Area ini digunakan oleh <strong>{{ documentArea.documents_count }} dokumen</strong>.
          </p>
        </div>

        <!-- Name -->
        <div>
          <label class="block font-medium text-gray-700 mb-2">
            Nama Area <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="form.name"
            class="w-full"
            :class="{ 'p-invalid': form.errors.name }"
            placeholder="Contoh: Tata Kelola Keamanan Informasi..."
            required
          />
          <small v-if="form.errors.name" class="p-error block mt-1">{{ form.errors.name }}</small>
        </div>

        <!-- Description -->
        <div>
          <label class="block font-medium text-gray-700 mb-2">
            Deskripsi <span class="text-slate-400 font-normal">(Opsional)</span>
          </label>
          <Textarea
            v-model="form.description"
            rows="4"
            class="w-full"
            :class="{ 'p-invalid': form.errors.description }"
            placeholder="Jelaskan secara singkat area dokumen ini..."
          />
          <small v-if="form.errors.description" class="p-error block mt-1">{{ form.errors.description }}</small>
        </div>
      </div>

      <!-- Mobile Submit -->
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
