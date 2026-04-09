<script setup>
// filepath: resources/js/Pages/Admin/Posts/Create.vue

import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useResponsive } from '@/Composables/useResponsive';

// Detect existing image type (URL or file path)
const detectExistingImageMode = (post) => {
  if (!post?.image) return 'file';
  const val = post.image;
  if (val.startsWith('http://') || val.startsWith('https://')) return 'link';
  return 'file';
};

const props = defineProps({
  post: {
    type: Object,
    default: null
  },
  categories: Array,
  tags: Array,
});

const { isMobile, isDesktop } = useResponsive();

// Check if we're in edit mode
const isEditMode = computed(() => !!props.post);

// Image mode toggle
const imageMode = ref(detectExistingImageMode(props.post));

// Initialize form with post data if editing, empty values if creating
const form = useForm({
  _method: isEditMode.value ? 'PUT' : 'POST',
  title: props.post?.title || '',
  body: props.post?.body || '',
  excerpt: props.post?.excerpt || '',
  image: null,
  image_type: detectExistingImageMode(props.post),
  image_url: (detectExistingImageMode(props.post) === 'link' ? props.post?.image : '') || '',
  status: props.post?.status || 'Draft',
  categories: props.post?.categories?.map(c => c.id) || [],
  tags: props.post?.tags?.map(t => t.id) || [],
});

const statusOptions = ref(['Draft', 'Published']);
const imagePreview = ref(null);
const fileUploader = ref(null)
const excerptWords = ref(0);

// AI excerpt generation
const isGeneratingExcerpt = ref(false);
const excerptGenerationMessage = ref('');

// Word count for excerpt (limit to 35 words)
const excerptWordCount = computed(() => {
  if (!form.excerpt) return 0;
  return form.excerpt.trim().split(/\s+/).filter(word => word.length > 0).length;
});

const excerptWordLimit = 35;
const isExcerptOverLimit = computed(() => excerptWordCount.value > excerptWordLimit);

// Check if we can generate excerpt
const canGenerateExcerpt = computed(() => {
  return form.title.trim().length > 0 && form.body.trim().length > 100 && !isGeneratingExcerpt.value;
});

// Watch excerpt changes for word count
watch(() => form.excerpt, (newExcerpt) => {
  excerptWords.value = excerptWordCount.value;
}, { immediate: true });

const setImageMode = (mode) => {
  imageMode.value = mode;
  form.image_type = mode;
  form.image = null;
  form.image_url = '';
  imagePreview.value = null;
  if (mode === 'file' && fileUploader.value) {
    fileUploader.value.clear?.();
  }
};

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

const clearFile = () => {
  form.file = null
  form.image = null;
  imagePreview.value = null;
  if (fileUploader.value) {
    fileUploader.value.clear()
  }
}

const removeImage = () => {
  // Clear the file uploader
  console.log(fileUploader.value);
  form.file = null
  if (fileUploader.value) {
    fileUploader.value.clear();
  }

  // Reset post image in edit mode
  if (!isEditMode.value) return;

  // Set form image to null and add a flag to indicate removal
  form.image = null;
  form.removeImage = true; // Add this flag to indicate image should be removed
  imagePreview.value = null;

  // Hide the current image preview by setting it to null
  props.post.image = null;
};


function triggerFileInput() {
  const input = fileUploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
}

// Generate excerpt using AI
const generateExcerpt = async () => {
  if (!canGenerateExcerpt.value) {
    excerptGenerationMessage.value = 'Mohon isi judul dan isi artikel terlebih dahulu';
    setTimeout(() => {
      excerptGenerationMessage.value = '';
    }, 3000);
    return;
  }

  isGeneratingExcerpt.value = true;
  excerptGenerationMessage.value = 'Sedang membuat ringkasan...';

  try {
    const response = await fetch(route('admin.generate-excerpt'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        title: form.title,
        body: form.body
      })
    });

    const data = await response.json();

    if (data.success) {
      form.excerpt = data.excerpt;
      excerptGenerationMessage.value = `✓ ${data.message} (${data.word_count} kata)`;

      // Clear success message after 5 seconds
      setTimeout(() => {
        excerptGenerationMessage.value = '';
      }, 5000);
    } else {
      excerptGenerationMessage.value = `❌ ${data.message}`;

      // Clear error message after 5 seconds
      setTimeout(() => {
        excerptGenerationMessage.value = '';
      }, 5000);
    }
  } catch (error) {
    console.error('Error generating excerpt:', error);
    excerptGenerationMessage.value = '❌ Terjadi kesalahan saat membuat ringkasan';

    // Clear error message after 5 seconds
    setTimeout(() => {
      excerptGenerationMessage.value = '';
    }, 5000);
  } finally {
    isGeneratingExcerpt.value = false;
  }
};

const submit = () => {
  if (isEditMode.value) {
    form.post(route('admin.posts.update', props.post.id));
  } else {
    form.post(route('admin.posts.store'));
  }
};
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Artikel' : 'Tambah Artikel Baru'">
    <div>
      <form @submit.prevent="submit" class="space-y-4 lg:space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
              <h2 class="text-xl lg:text-2xl font-bold text-slate-900">{{ isEditMode ? 'Edit Artikel' : 'Tambah Artikel Baru' }}</h2>
              <p class="text-slate-600">{{ isEditMode ? 'Perbarui informasi artikel' : 'Buat artikel baru untuk diterbitkankan' }}</p>
            </div>
            <div class="flex items-center space-x-3">
              <Button
                severity="secondary"
                @click="() => router.get(route('admin.posts.index'))"
                class="w-full lg:w-auto"
              >
                <IconArrowLeft size="16"/>
                  Kembali
              </Button>
              <Button
                v-if="!isMobile"
                type="submit"
                severity="primary"
                :disabled="form.processing"
                class="w-full lg:w-auto"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update Artikel' : 'Simpan Artikel' }}
              </Button>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <Message v-if="$page.props.flash?.success" severity="success" class="shadow-sm">
          {{ $page.props.flash?.success }}
        </Message>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
          <!-- Main Content (Left Column) -->
          <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Title & Content Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                  <IconLetterT class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Judul & Konten</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Masukkan judul dan isi artikel yang ingin Anda buat atau edit</p>
                </div>
              </div>

              <!-- Title -->
              <div class="space-y-4">
                <div>
                  <label for="title" class="block font-medium text-slate-700 mb-2">
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
                  <label class="block font-medium text-slate-700 mb-2">
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
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
                  <IconReceipt class="text-purple-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Kutipan Singkat (Excerpt)</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Ringkasan singkat dari artikel ini</p>
                </div>
              </div>

              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="block font-medium text-slate-700">
                    Ringkasan Artikel <span class="text-red-500">*</span>
                    <span class="text-xs text-slate-500 ml-1">(maksimal {{ excerptWordLimit }} kata)</span>
                  </label>

                  <!-- AI Generate Button -->
                  <button
                    type="button"
                    @click="generateExcerpt"
                    :disabled="!canGenerateExcerpt"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium rounded-md transition-colors"
                    :class="canGenerateExcerpt
                      ? 'bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white shadow-sm'
                      : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                  >
                    <IconLoader3 v-if="isGeneratingExcerpt" class="animate-spin" size="14"/>
                    <IconSparkles v-else size="14"/>
                    {{ isGeneratingExcerpt ? 'Membuat Ringkasan...' : 'Buat Ringkasan' }}
                  </button>
                </div>

                <!-- AI Generation Message -->
                <div v-if="excerptGenerationMessage" class="mb-3">
                  <small
                    :class="excerptGenerationMessage.includes('✓') ? 'text-green-600' :
                            excerptGenerationMessage.includes('❌') ? 'text-red-600' : 'text-blue-600'"
                    class="text-xs block p-2 bg-slate-50 rounded-lg border border-slate-300"
                  >
                    {{ excerptGenerationMessage }}
                  </small>
                </div>

                <Textarea
                  v-model="form.excerpt"
                  rows="4"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.excerpt || isExcerptOverLimit }"
                  placeholder="Tulis ringkasan singkat dari artikel ini atau gunakan tombol 'Buat Ringkasan' untuk generate otomatis..."
                  maxlength="500" />

                <div class="flex justify-between items-center">
                  <small v-if="form.errors.excerpt" class="p-error">{{ form.errors.excerpt }}</small>
                  <small :class="{ 'text-red-500': isExcerptOverLimit, 'text-slate-500': !isExcerptOverLimit }"
                          class="text-xs">
                    {{ excerptWordCount }}/{{ excerptWordLimit }} kata
                    <span v-if="isExcerptOverLimit" class="ml-1">- Melebihi batas!</span>
                  </small>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar (Right Column) -->
          <div class="space-y-4 lg:space-y-6">
            <!-- Publication Options Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                  <IconBrowserShare class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Opsi Publikasi</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Atur status publikasi artikel ini</p>
                </div>
              </div>

              <!-- Status -->
              <div class="space-y-4">
                <div>
                  <label class="block font-medium text-slate-700 mb-3">Status Publikasi</label>
                  <SelectButton
                    v-model="form.status"
                    :options="statusOptions"
                    :invalid="form.status === null"
                    required
                  />
                </div>

                <!-- Featured Image -->
                <div>
                  <label class="block font-medium text-slate-700 mb-2">Gambar Utama</label>

                  <!-- Mode Toggle -->
                  <div class="flex rounded-lg overflow-hidden border border-slate-300 mb-3 w-fit">
                    <button
                      type="button"
                      @click="setImageMode('file')"
                      class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors"
                      :class="imageMode === 'file' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                    >
                      <IconUpload size="12" />
                      Upload Gambar
                    </button>
                    <button
                      type="button"
                      @click="setImageMode('link')"
                      class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors border-l border-slate-300"
                      :class="imageMode === 'link' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                    >
                      <IconLink size="12" />
                      Kirim Link
                    </button>
                  </div>

                  <!-- File Upload Mode -->
                  <template v-if="imageMode === 'file'">
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
                          <IconX class="text-white" size="10"/>
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
                          <IconPhotoSearch class="text-gray-400 mb-2" :size="isMobile ? 18 : undefined"/>
                          <p class="text-sm text-gray-600 text-center">
                            {{ isEditMode ? 'Pilih gambar baru untuk mengganti' : 'Drag & drop atau klik untuk memilih gambar' }}
                          </p>
                          <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP (max 2MB)</p>
                        </div>
                      </template>
                    </FileUpload>
                  </template>

                  <!-- Link Mode -->
                  <template v-else>
                    <!-- Preview current image URL (edit mode) -->
                    <div v-if="isEditMode && props.post?.image && !form.image_url" class="mb-3">
                      <div class="relative rounded-lg w-full h-32 overflow-hidden">
                        <img :src="props.post.image" :alt="props.post.title" class="w-full h-full object-cover" />
                      </div>
                      <p class="text-xs text-gray-500 mt-1">Gambar saat ini (URL)</p>
                    </div>
                    <!-- Preview new URL -->
                    <div v-if="form.image_url" class="mb-3">
                      <div class="relative rounded-lg w-full h-32 overflow-hidden bg-slate-100">
                        <img :src="form.image_url" alt="Preview URL" class="w-full h-full object-cover" @error="$event.target.style.display='none'" />
                      </div>
                    </div>
                    <InputText
                      v-model="form.image_url"
                      class="w-full"
                      placeholder="https://example.com/gambar.jpg"
                    />
                    <p class="text-xs text-gray-400 mt-1">Masukkan URL gambar langsung</p>
                  </template>
                </div>
              </div>
            </div>

            <!-- Categories & Tags Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
              <div class="flex items-center mb-4 lg:mb-6">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-orange-50 border border-orange-200 rounded-lg flex items-center justify-center">
                  <IconBookmarks class="text-orange-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">Kategori & Tag</h3>
                  <p class="text-xs lg:text-sm text-slate-600">Tentukan kategori dan tag untuk artikel ini</p>
                </div>
              </div>

              <div class="space-y-4">
                <!-- Categories -->
                <div>
                  <label class="block font-medium text-slate-700 mb-2">
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
                  <label class="block font-medium text-slate-700 mb-2">
                    Tag <span class="text-slate-400">(Opsional)</span>
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
            <div v-if="isMobile">
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                <IconDeviceFloppy v-else size="16"/>
                {{ form.processing ? 'Menyimpan...' : isEditMode ? 'Update Artikel' : 'Simpan Artikel' }}
              </button>
            </div>

          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
