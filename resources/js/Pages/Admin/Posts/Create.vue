<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  post: {
    type: Object,
    default: null
  },
  categories: Array,
  tags: Array,
});

// Check if we're in edit mode
const isEditMode = computed(() => !!props.post);

// Initialize form with post data if editing, empty values if creating
const form = useForm({
  _method: isEditMode.value ? 'PUT' : 'POST',
  title: props.post?.title || '',
  body: props.post?.body || '',
  excerpt: props.post?.excerpt || '',
  image: null,
  status: props.post?.status || 'Draft',
  categories: props.post?.categories?.map(c => c.id) || [],
  tags: props.post?.tags?.map(t => t.id) || [],
});

const statusOptions = ref(['Draft', 'Published']);
const imagePreview = ref(null);
const excerptWords = ref(0);

// Word count for excerpt (limit to 35 words)
const excerptWordCount = computed(() => {
  if (!form.excerpt) return 0;
  return form.excerpt.trim().split(/\s+/).filter(word => word.length > 0).length;
});

const excerptWordLimit = 35;
const isExcerptOverLimit = computed(() => excerptWordCount.value > excerptWordLimit);

// Watch excerpt changes for word count
watch(() => form.excerpt, (newExcerpt) => {
  excerptWords.value = excerptWordCount.value;
}, { immediate: true });

const onImageSelect = (event) => {
  const file = event.files[0];
  form.image = file;

  // Create preview URL
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  form.image = null;
  imagePreview.value = null;
};

const submit = () => {
  if (isEditMode.value) {
    form.post(route('admin.posts.update', props.post.id));
  } else {
    form.post(route('admin.posts.store'));
  }
};

const fileUploader = ref(null)

function triggerFileInput() {
  const input = fileUploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
}

</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Artikel' : 'Tambah Artikel Baru'">
    <div class="min-h-screen bg-gray-50">
      <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ isEditMode ? 'Edit Artikel' : 'Tambah Artikel Baru' }}</h2>
              <p class="text-slate-600">{{ isEditMode ? 'Perbarui informasi artikel' : 'Buat artikel baru untuk dipublikasikan' }}</p>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('admin.posts.index')"
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
              >
                <span class="material-symbols-outlined !text-xl">west</span>
                  Kembali
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <span class="material-symbols-outlined !text-xl" :class="{ 'animate-spin': form.processing }">
                  {{ form.processing ? 'progress_activity' : 'save' }}
                </span>
                {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update Artikel' : 'Simpan Artikel' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <Message v-if="$page.props.flash?.success" severity="success" class="shadow-sm">
          {{ $page.props.flash?.success }}
        </Message>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
          <!-- Main Content (Left Column) -->
          <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Title & Content Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
              <div class="flex items-center mb-4 sm:mb-6">
                <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-blue-600">title</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Judul & Konten</h3>
                  <p class="text-xs sm:text-base text-slate-600">Masukkan judul dan isi artikel yang ingin Anda buat atau edit</p>
                </div>
              </div>

              <!-- Title -->
              <div class="space-y-4">
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Artikel <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="title"
                    v-model="form.title"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.title }"
                    placeholder="Masukkan judul artikel..."
                    required />
                  <small v-if="form.errors.title" class="p-error block mt-1">{{ form.errors.title }}</small>
                </div>

                <!-- Body -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Isi Artikel <span class="text-red-500">*</span>
                  </label>
                  <RichTextEditor
                    v-model="form.body"
                    required
                    :class="{ 'p-invalid': form.errors.body }" />
                  <small v-if="form.errors.body" class="p-error block mt-1">{{ form.errors.body }}</small>
                </div>
              </div>
            </div>

            <!-- Excerpt Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
              <div class="flex items-center mb-4 sm:mb-6">
                <div class="w-12 h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-purple-600">text_snippet</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Kutipan Singkat (Excerpt)</h3>
                  <p class="text-xs sm:text-base text-slate-600">Ringkasan singkat dari artikel ini</p>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Ringkasan Artikel <span class="text-red-500">*</span>
                  <span class="text-xs text-gray-500 ml-1">(maksimal {{ excerptWordLimit }} kata)</span>
                </label>
                <Textarea
                  v-model="form.excerpt"
                  rows="4"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.excerpt || isExcerptOverLimit }"
                  placeholder="Tulis ringkasan singkat dari artikel ini..."
                  maxlength="500" />

                <div class="flex justify-between items-center mt-2">
                  <small v-if="form.errors.excerpt" class="p-error">{{ form.errors.excerpt }}</small>
                  <small :class="{ 'text-red-500': isExcerptOverLimit, 'text-gray-500': !isExcerptOverLimit }"
                          class="text-xs">
                    {{ excerptWordCount }}/{{ excerptWordLimit }} kata
                    <span v-if="isExcerptOverLimit" class="ml-1">- Melebihi batas!</span>
                  </small>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar (Right Column) -->
          <div class="space-y-4 sm:space-y-6">
            <!-- Publication Options Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
              <div class="flex items-center mb-4 sm:mb-6">
                <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-green-600">upload_2</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Opsi Publikasi</h3>
                  <p class="text-xs sm:text-base text-slate-600">Atur status publikasi artikel ini</p>
                </div>
              </div>

              <!-- Status -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">Status Publikasi</label>
                  <SelectButton
                    v-model="form.status"
                    :options="statusOptions"
                    class="w-full"
                  />
                </div>

                <!-- Featured Image -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Utama</label>

                  <!-- Current Image Preview (Edit Mode) -->
                  <div v-if="isEditMode && props.post?.image && !imagePreview" class="mb-3">
                    <div class="relative inline-block rounded-lg w-full h-32 overflow-hidden">
                      <Image
                        :src="`/storage/${props.post.image}`"
                        :alt="props.post.title"
                        class="w-full h-full object-cover"
                        :pt="{ image: { class: 'w-full h-full object-cover' } }"
                        preview
                      />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                  </div>

                  <!-- New Image Preview -->
                  <div v-if="imagePreview" class="mb-3">
                    <div class="relative inline-block rounded-lg w-full h-32 overflow-hidden">
                      <Image
                        :src="imagePreview"
                        alt="Preview"
                        class="w-full h-full object-cover"
                        :pt="{ image: { class: 'w-full h-full object-cover' } }"
                        preview
                      />
                      <button
                        type="button"
                        @click="removeImage"
                        class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-500 transition-colors">
                        <span class="material-symbols-outlined !text-sm">close</span>
                      </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ isEditMode ? 'Gambar baru (akan mengganti gambar lama)' : 'Preview gambar' }}
                    </p>
                  </div>

                  <!-- File Upload -->
                  <FileUpload
                    ref="fileUploader"
                    name="image"
                    @select="onImageSelect"
                    :auto="true"
                    :customUpload="true"
                    :showUploadButton="false"
                    :showCancelButton="false"
                    :multiple="false"
                    accept="image/*"
                    class="w-full"
                  >
                    <template #empty>
                      <div
                        class="flex flex-col items-center justify-center py-6 px-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-600 transition-colors cursor-pointer"
                        @click="triggerFileInput"
                      >
                        <span class="material-symbols-outlined text-gray-400 text-2xl mb-2">add_photo_alternate</span>
                        <p class="text-sm text-gray-600 text-center">
                          {{ isEditMode ? 'Pilih gambar baru untuk mengganti' : 'Drag & drop atau klik untuk memilih gambar' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (max 2MB)</p>
                      </div>
                    </template>
                  </FileUpload>
                </div>
              </div>
            </div>

            <!-- Categories & Tags Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
              <div class="flex items-center mb-4 sm:mb-6">
                <div class="w-12 h-12 bg-orange-50 border border-orange-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-orange-600">bookmarks</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Kategori & Tag</h3>
                  <p class="text-xs sm:text-base text-slate-600">Tentukan kategori dan tag untuk artikel ini</p>
                </div>
              </div>

              <div class="space-y-4">
                <!-- Categories -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                  </label>
                  <MultiSelect
                    v-model="form.categories"
                    :options="props.categories"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Pilih Kategori"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.categories }"
                    display="chip"
                    :maxSelectedLabels="3"
                    :showToggleAll="false"
                    required
                  />
                  <small v-if="form.errors.categories" class="p-error block mt-1">{{ form.errors.categories }}</small>
                </div>

                <!-- Tags -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tag <span class="text-gray-400">(Opsional)</span>
                  </label>
                  <MultiSelect
                    v-model="form.tags"
                    :options="props.tags"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Pilih Tag"
                    class="w-full"
                    display="chip"
                    :maxSelectedLabels="3"
                    :showToggleAll="false"
                  />
                </div>
              </div>
            </div>

            <!-- Button Submit (Mobile Only) -->
            <div class="block sm:hidden">
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <span class="material-symbols-outlined !text-xl" :class="{ 'animate-spin': form.processing }">
                  {{ form.processing ? 'progress_activity' : 'save' }}
                </span>
                {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update Artikel' : 'Simpan Artikel' }}
              </button>
            </div>

          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<style>
.p-fileupload-header {
  display: none !important;
}
.p-fileupload-content {
  padding: 1.125rem !important;
}
</style>
