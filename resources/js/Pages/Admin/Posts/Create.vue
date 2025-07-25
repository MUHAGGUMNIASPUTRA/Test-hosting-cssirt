<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  categories: Array,
  tags: Array,
});

const form = useForm({
  title: '',
  body: '',
  excerpt: '',
  image: null,
  status: 'Draft',
  categories: [],
  tags: [],
});

const statusOptions = ref(['Draft', 'Published']);

const onImageSelect = (event) => {
  form.image = event.files[0];
};

const submit = () => {
  form.post(route('admin.posts.store'), {
    onError: (errors) => {
      // Handle error display if needed
    }
  });
};
</script>

<template>
  <AdminLayout title="Tambah Artikel Baru">
    <form @submit.prevent="submit">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Tambah Artikel Baru</h1>
        <Button type="submit" label="Simpan Artikel" icon="pi pi-save" :loading="form.processing" />
      </div>

      <!-- Success Message -->
      <Message v-if="$page.props.flash?.success" severity="success" class="mb-4">{{ $page.props.flash?.success }}</Message>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Left Column) -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Judul & Konten</h3>
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Artikel</label>
              <InputText id="title" v-model="form.title" class="w-full" :class="{ 'p-invalid': form.errors.title }" />
              <small v-if="form.errors.title" class="p-error">{{ form.errors.title }}</small>
            </div>
            <!-- Body -->
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Isi Artikel</label>
              <RichTextEditor v-model="form.body" />
              <small v-if="form.errors.body" class="p-error">{{ form.errors.body }}</small>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
             <h3 class="text-lg font-semibold mb-4">Kutipan Singkat (Excerpt)</h3>
             <div>
                <Textarea v-model="form.excerpt" rows="4" class="w-full" placeholder="Tulis ringkasan singkat dari artikel ini..." :class="{ 'p-invalid': form.errors.excerpt }" />
                <small v-if="form.errors.excerpt" class="p-error">{{ form.errors.excerpt }}</small>
             </div>
          </div>
        </div>

        <!-- Sidebar (Right Column) -->
        <div class="space-y-6">
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Opsi Publikasi</h3>
            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <SelectButton v-model="form.status" :options="statusOptions" aria-labelledby="basic" />
            </div>
            <!-- Featured Image -->
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama</label>
              <FileUpload name="image" @select="onImageSelect" :showUploadButton="false" :showCancelButton="false" :multiple="false" accept="image/*">
                <template #empty>
                  <p>Drag & drop atau klik untuk memilih gambar.</p>
                </template>
              </FileUpload>
              <small v-if="form.errors.image" class="p-error">{{ form.errors.image }}</small>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Kategori & Tag</h3>
            <!-- Categories -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <MultiSelect v-model="form.categories" :options="props.categories" optionLabel="name" optionValue="id" placeholder="Pilih Kategori" class="w-full" :class="{ 'p-invalid': form.errors.categories }" />
              <small v-if="form.errors.categories" class="p-error">{{ form.errors.categories }}</small>
            </div>
            <!-- Tags -->
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Tag (Opsional)</label>
              <MultiSelect v-model="form.tags" :options="props.tags" optionLabel="name" optionValue="id" placeholder="Pilih Tag" class="w-full" />
            </div>
          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
