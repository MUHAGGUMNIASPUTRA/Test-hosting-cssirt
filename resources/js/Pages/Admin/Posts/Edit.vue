<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';
import MultiSelect from 'primevue/multiselect';
import SelectButton from 'primevue/selectbutton';
import Button from 'primevue/button';
import Message from 'primevue/message';

const props = defineProps({
  post: Object,
  categories: Array,
  tags: Array,
});

const form = useForm({
  _method: 'PUT',
  title: props.post.title,
  body: props.post.body,
  excerpt: props.post.excerpt,
  image: null,
  status: props.post.status,
  categories: props.post.categories.map(c => c.id),
  tags: props.post.tags.map(t => t.id),
});

const statusOptions = ref(['Draft', 'Published']);

// Prefill the form with existing data when component is mounted
onMounted(() => {
  form.title = props.post.title;
  form.body = props.post.body;
  form.excerpt = props.post.excerpt;
  form.status = props.post.status;
  form.categories = props.post.categories.map(c => c.id);
  form.tags = props.post.tags.map(t => t.id);
});

const onImageSelect = (event) => {
  form.image = event.files[0];
};

const submit = () => {
  form.post(route('admin.posts.update', props.post.id));
};
</script>

<template>
  <AdminLayout title="Edit Artikel">
    <form @submit.prevent="submit">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Edit Artikel</h1>
        <Button type="submit" label="Update Artikel" icon="pi pi-save" :loading="form.processing" />
      </div>

      <Message v-if="$page.props.flash?.success" severity="success" class="mb-4">{{ $page.props.flash?.success }}</Message>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Judul & Konten</h3>
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Artikel</label>
              <InputText id="title" v-model="form.title" class="w-full" :class="{ 'p-invalid': form.errors.title }" />
              <small v-if="form.errors.title" class="p-error">{{ form.errors.title }}</small>
            </div>
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Isi Artikel</label>
              <RichTextEditor v-model="form.body" />
              <small v-if="form.errors.body" class="p-error">{{ form.errors.body }}</small>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
             <h3 class="text-lg font-semibold mb-4">Kutipan Singkat (Excerpt)</h3>
             <div>
                <Textarea v-model="form.excerpt" rows="4" class="w-full" :class="{ 'p-invalid': form.errors.excerpt }" />
                <small v-if="form.errors.excerpt" class="p-error">{{ form.errors.excerpt }}</small>
             </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Opsi Publikasi</h3>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <SelectButton v-model="form.status" :options="statusOptions" aria-labelledby="basic" />
            </div>
            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama</label>
              <p v-if="post.image" class="text-sm text-gray-500 mb-2">Gambar saat ini: <a :href="`/storage/${post.image}`" target="_blank" class="text-red-600 hover:underline">Lihat</a></p>
              <FileUpload name="image" @select="onImageSelect" :showUploadButton="false" :showCancelButton="false" :multiple="false" accept="image/*">
                <template #empty>
                  <p>Pilih gambar baru untuk mengganti.</p>
                </template>
              </FileUpload>
              <small v-if="form.errors.image" class="p-error">{{ form.errors.image }}</small>
            </div>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Kategori & Tag</h3>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <MultiSelect v-model="form.categories" :options="props.categories" optionLabel="name" optionValue="id" placeholder="Pilih Kategori" class="w-full" :class="{ 'p-invalid': form.errors.categories }" />
              <small v-if="form.errors.categories" class="p-error">{{ form.errors.categories }}</small>
            </div>
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
