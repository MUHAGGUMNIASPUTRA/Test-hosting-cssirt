<script setup>
// filepath: resources/js/Pages/Admin/Posts/Create.vue

import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

// Detect existing image type from attachment object
const detectExistingImageMode = (post) => {
  return post?.image?.type ?? 'file'
}

const props = defineProps({
  post: {
    type: Object,
    default: null,
  },
  categories: Array,
  tags: Array,
})

const { isMobile, isDesktop } = useResponsive()

// Check if we're in edit mode
const isEditMode = computed(() => !!props.post)

// Image mode toggle
const imageMode = ref(detectExistingImageMode(props.post))

// Initialize form with post data if editing, empty values if creating
const form = useForm({
  _method: isEditMode.value ? 'PUT' : 'POST',
  title: props.post?.title || '',
  body: props.post?.body || '',
  excerpt: props.post?.excerpt || '',
  image: null,
  image_type: detectExistingImageMode(props.post),
  image_url:
    (detectExistingImageMode(props.post) === 'link'
      ? props.post?.image?.url
      : '') || '',
  status: props.post?.status || 'Draft',
  categories: props.post?.categories?.map((c) => c.id) || [],
  tags: props.post?.tags?.map((t) => t.id) || [],
})

const statusOptions = ref(['Draft', 'Published'])
const imagePreview = ref(null)
const fileUploader = ref(null)
const excerptWords = ref(0)

// AI excerpt generation
const isGeneratingExcerpt = ref(false)
const excerptGenerationMessage = ref('')

// Word count for excerpt (limit to 35 words)
const excerptWordCount = computed(() => {
  if (!form.excerpt) return 0
  return form.excerpt
    .trim()
    .split(/\s+/)
    .filter((word) => word.length > 0).length
})

const excerptWordLimit = 35
const isExcerptOverLimit = computed(
  () => excerptWordCount.value > excerptWordLimit,
)

// Check if we can generate excerpt
const canGenerateExcerpt = computed(() => {
  return (
    form.title.trim().length > 0 &&
    form.body.trim().length > 100 &&
    !isGeneratingExcerpt.value
  )
})

// Watch excerpt changes for word count
watch(
  () => form.excerpt,
  (newExcerpt) => {
    excerptWords.value = excerptWordCount.value
  },
  { immediate: true },
)

const setImageMode = (mode) => {
  imageMode.value = mode
  form.image_type = mode
  form.image = null
  form.image_url = ''
  imagePreview.value = null
  if (mode === 'file' && fileUploader.value) {
    fileUploader.value.clear?.()
  }
}

const onImageSelect = (event) => {
  const file = event.files[0]
  form.image = file

  // Create preview URL
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const clearFile = () => {
  form.file = null
  form.image = null
  imagePreview.value = null
  if (fileUploader.value) {
    fileUploader.value.clear()
  }
}

const removeImage = () => {
  // Clear the file uploader
  console.log(fileUploader.value)
  form.file = null
  if (fileUploader.value) {
    fileUploader.value.clear()
  }

  // Reset post image in edit mode
  if (!isEditMode.value) return

  // Set form image to null and add a flag to indicate removal
  form.image = null
  form.removeImage = true // Add this flag to indicate image should be removed
  imagePreview.value = null

  // Hide the current image preview by setting it to null
  props.post.image = null
}

function triggerFileInput() {
  const input = fileUploader.value?.$el.querySelector('input[type="file"]')
  if (input) input.click()
}

// Generate excerpt using AI
const generateExcerpt = async () => {
  if (!canGenerateExcerpt.value) {
    excerptGenerationMessage.value =
      'Mohon isi judul dan isi artikel terlebih dahulu'
    setTimeout(() => {
      excerptGenerationMessage.value = ''
    }, 3000)
    return
  }

  isGeneratingExcerpt.value = true
  excerptGenerationMessage.value = 'Sedang membuat ringkasan...'

  try {
    const response = await fetch(route('admin.generate-excerpt'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute('content'),
        Accept: 'application/json',
      },
      body: JSON.stringify({
        title: form.title,
        body: form.body,
      }),
    })

    const data = await response.json()

    if (data.success) {
      form.excerpt = data.excerpt
      excerptGenerationMessage.value = `✓ ${data.message} (${data.word_count} kata)`

      // Clear success message after 5 seconds
      setTimeout(() => {
        excerptGenerationMessage.value = ''
      }, 5000)
    } else {
      excerptGenerationMessage.value = `❌ ${data.message}`

      // Clear error message after 5 seconds
      setTimeout(() => {
        excerptGenerationMessage.value = ''
      }, 5000)
    }
  } catch (error) {
    console.error('Error generating excerpt:', error)
    excerptGenerationMessage.value =
      '❌ Terjadi kesalahan saat membuat ringkasan'

    // Clear error message after 5 seconds
    setTimeout(() => {
      excerptGenerationMessage.value = ''
    }, 5000)
  } finally {
    isGeneratingExcerpt.value = false
  }
}

const submit = () => {
  if (isEditMode.value) {
    form.post(route('admin.posts.update', props.post.id))
  } else {
    form.post(route('admin.posts.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Artikel' : 'Tambah Artikel Baru'">
    <div>
      <form @submit.prevent="submit" class="space-y-4 lg:space-y-6">
        <!-- Header Section -->
        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
          >
            <div>
              <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
                {{ isEditMode ? 'Edit Artikel' : 'Tambah Artikel Baru' }}
              </h2>
              <p class="text-slate-600">
                {{
                  isEditMode
                    ? 'Perbarui informasi artikel'
                    : 'Buat artikel baru untuk diterbitkankan'
                }}
              </p>
            </div>
            <div class="flex items-center space-x-3">
              <Button
                severity="secondary"
                @click="() => router.get(route('admin.posts.index'))"
                class="w-full lg:w-auto"
              >
                <IconArrowLeft size="16" />
                Kembali
              </Button>
              <Button
                v-if="!isMobile"
                type="submit"
                severity="primary"
                :disabled="form.processing"
                class="w-full lg:w-auto"
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
                      ? 'Update Artikel'
                      : 'Simpan Artikel'
                }}
              </Button>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        <Message
          v-if="$page.props.flash?.success"
          severity="success"
          class="shadow-sm"
        >
          {{ $page.props.flash?.success }}
        </Message>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
          <!-- Main Content (Left Column) -->
          <div class="space-y-4 lg:col-span-2 lg:space-y-6">
            <!-- Title & Content Card -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
                >
                  <IconLetterT
                    class="text-blue-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Judul & Konten
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Masukkan judul dan isi artikel yang ingin Anda buat atau
                    edit
                  </p>
                </div>
              </div>

              <!-- Title -->
              <div class="space-y-4">
                <div>
                  <label
                    for="title"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Judul Artikel <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="title"
                    v-model="form.title"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.title }"
                    placeholder="Masukkan judul artikel..."
                    required
                  />
                  <small v-if="form.errors.title" class="p-error mt-1 block">{{
                    form.errors.title
                  }}</small>
                </div>

                <!-- Body -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
                    Isi Artikel <span class="text-red-500">*</span>
                  </label>
                  <RichTextEditor
                    v-model="form.body"
                    required
                    :class="{ 'p-invalid': form.errors.body }"
                  />
                  <small v-if="form.errors.body" class="p-error mt-1 block">{{
                    form.errors.body
                  }}</small>
                </div>
              </div>
            </div>

            <!-- Excerpt Card -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-purple-200 bg-purple-50 lg:h-12 lg:w-12"
                >
                  <IconReceipt
                    class="text-purple-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Kutipan Singkat (Excerpt)
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Ringkasan singkat dari artikel ini
                  </p>
                </div>
              </div>

              <div>
                <div class="mb-2 flex items-center justify-between">
                  <label class="block font-medium text-slate-700">
                    Ringkasan Artikel <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs text-slate-500"
                      >(maksimal {{ excerptWordLimit }} kata)</span
                    >
                  </label>

                  <!-- AI Generate Button -->
                  <button
                    type="button"
                    @click="generateExcerpt"
                    :disabled="!canGenerateExcerpt"
                    class="inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium transition-colors"
                    :class="
                      canGenerateExcerpt
                        ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-sm hover:from-purple-700 hover:to-blue-700'
                        : 'cursor-not-allowed bg-slate-100 text-slate-400'
                    "
                  >
                    <IconLoader3
                      v-if="isGeneratingExcerpt"
                      class="animate-spin"
                      size="14"
                    />
                    <IconSparkles v-else size="14" />
                    {{
                      isGeneratingExcerpt
                        ? 'Membuat Ringkasan...'
                        : 'Buat Ringkasan'
                    }}
                  </button>
                </div>

                <!-- AI Generation Message -->
                <div v-if="excerptGenerationMessage" class="mb-3">
                  <small
                    :class="
                      excerptGenerationMessage.includes('✓')
                        ? 'text-green-600'
                        : excerptGenerationMessage.includes('❌')
                          ? 'text-red-600'
                          : 'text-blue-600'
                    "
                    class="block rounded-lg border border-slate-300 bg-slate-50 p-2 text-xs"
                  >
                    {{ excerptGenerationMessage }}
                  </small>
                </div>

                <Textarea
                  v-model="form.excerpt"
                  rows="4"
                  class="w-full"
                  :class="{
                    'p-invalid': form.errors.excerpt || isExcerptOverLimit,
                  }"
                  placeholder="Tulis ringkasan singkat dari artikel ini atau gunakan tombol 'Buat Ringkasan' untuk generate otomatis..."
                  maxlength="500"
                />

                <div class="flex items-center justify-between">
                  <small v-if="form.errors.excerpt" class="p-error">{{
                    form.errors.excerpt
                  }}</small>
                  <small
                    :class="{
                      'text-red-500': isExcerptOverLimit,
                      'text-slate-500': !isExcerptOverLimit,
                    }"
                    class="text-xs"
                  >
                    {{ excerptWordCount }}/{{ excerptWordLimit }} kata
                    <span v-if="isExcerptOverLimit" class="ml-1"
                      >- Melebihi batas!</span
                    >
                  </small>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar (Right Column) -->
          <div class="space-y-4 lg:space-y-6">
            <!-- Publication Options Card -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-green-200 bg-green-50 lg:h-12 lg:w-12"
                >
                  <IconBrowserShare
                    class="text-green-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Opsi Publikasi
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Atur status publikasi artikel ini
                  </p>
                </div>
              </div>

              <!-- Status -->
              <div class="space-y-4">
                <div>
                  <label class="mb-3 block font-medium text-slate-700"
                    >Status Publikasi</label
                  >
                  <SelectButton
                    v-model="form.status"
                    :options="statusOptions"
                    :invalid="form.status === null"
                    required
                  />
                </div>

                <!-- Featured Image -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700"
                    >Gambar Utama</label
                  >

                  <!-- Mode Toggle -->
                  <div
                    class="mb-3 flex w-fit overflow-hidden rounded-lg border border-slate-300"
                  >
                    <button
                      type="button"
                      @click="setImageMode('file')"
                      class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium transition-colors"
                      :class="
                        imageMode === 'file'
                          ? 'bg-blue-600 text-white'
                          : 'bg-white text-slate-600 hover:bg-slate-50'
                      "
                    >
                      <IconUpload size="12" />
                      Upload Gambar
                    </button>
                    <button
                      type="button"
                      @click="setImageMode('link')"
                      class="flex items-center gap-1.5 border-l border-slate-300 px-3 py-1.5 text-xs font-medium transition-colors"
                      :class="
                        imageMode === 'link'
                          ? 'bg-blue-600 text-white'
                          : 'bg-white text-slate-600 hover:bg-slate-50'
                      "
                    >
                      <IconLink size="12" />
                      Kirim Link
                    </button>
                  </div>

                  <!-- File Upload Mode -->
                  <template v-if="imageMode === 'file'">
                    <!-- Current Image Preview (Edit Mode) -->
                    <div
                      v-if="
                        isEditMode && props.post?.image?.url && !imagePreview
                      "
                      class="mb-3"
                    >
                      <div
                        class="relative inline-block h-32 w-full overflow-hidden rounded-lg"
                      >
                        <Image
                          :src="props.post.image.url"
                          :alt="props.post.title"
                          class="h-full w-full object-cover"
                          :pt="{
                            image: { class: 'w-full h-full object-cover' },
                          }"
                          preview
                        />
                      </div>
                      <p class="mt-1 text-xs text-gray-500">Gambar saat ini</p>
                    </div>

                    <!-- New Image Preview -->
                    <div v-if="imagePreview" class="mb-3">
                      <div
                        class="relative inline-block h-32 w-full overflow-hidden rounded-lg"
                      >
                        <Image
                          :src="imagePreview"
                          alt="Preview"
                          class="h-full w-full object-cover"
                          :pt="{
                            image: { class: 'w-full h-full object-cover' },
                          }"
                          preview
                        />
                        <button
                          type="button"
                          @click="removeImage"
                          class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white transition-colors hover:bg-red-500"
                        >
                          <IconX class="text-white" size="10" />
                        </button>
                      </div>
                      <p class="mt-1 text-xs text-gray-500">
                        {{
                          isEditMode
                            ? 'Gambar baru (akan mengganti gambar lama)'
                            : 'Preview gambar'
                        }}
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
                          class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 px-4 py-6 transition-colors hover:border-blue-600"
                          @click="triggerFileInput"
                        >
                          <IconPhotoSearch
                            class="mb-2 text-gray-400"
                            :size="isMobile ? 18 : undefined"
                          />
                          <p class="text-center text-sm text-gray-600">
                            {{
                              isEditMode
                                ? 'Pilih gambar baru untuk mengganti'
                                : 'Drag & drop atau klik untuk memilih gambar'
                            }}
                          </p>
                          <p class="mt-1 text-xs text-gray-400">
                            JPG, PNG, WEBP (max 2MB)
                          </p>
                        </div>
                      </template>
                    </FileUpload>
                  </template>

                  <!-- Link Mode -->
                  <template v-else>
                    <!-- Preview current image URL (edit mode) -->
                    <div
                      v-if="
                        isEditMode && props.post?.image?.url && !form.image_url
                      "
                      class="mb-3"
                    >
                      <div
                        class="relative h-32 w-full overflow-hidden rounded-lg"
                      >
                        <img
                          :src="props.post.image.url"
                          :alt="props.post.title"
                          class="h-full w-full object-cover"
                        />
                      </div>
                      <p class="mt-1 text-xs text-gray-500">
                        Gambar saat ini (URL)
                      </p>
                    </div>
                    <!-- Preview new URL -->
                    <div v-if="form.image_url" class="mb-3">
                      <div
                        class="relative h-32 w-full overflow-hidden rounded-lg bg-slate-100"
                      >
                        <img
                          :src="form.image_url"
                          alt="Preview URL"
                          class="h-full w-full object-cover"
                          @error="$event.target.style.display = 'none'"
                        />
                      </div>
                    </div>
                    <InputText
                      v-model="form.image_url"
                      class="w-full"
                      placeholder="https://example.com/gambar.jpg"
                    />
                    <p class="mt-1 text-xs text-gray-400">
                      Masukkan URL gambar langsung
                    </p>
                  </template>
                </div>
              </div>
            </div>

            <!-- Categories & Tags Card -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-200 bg-orange-50 lg:h-12 lg:w-12"
                >
                  <IconBookmarks
                    class="text-orange-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Kategori & Tag
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Tentukan kategori dan tag untuk artikel ini
                  </p>
                </div>
              </div>

              <div class="space-y-4">
                <!-- Categories -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
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
                  <small
                    v-if="form.errors.categories"
                    class="p-error mt-1 block"
                    >{{ form.errors.categories }}</small
                  >
                </div>

                <!-- Tags -->
                <div>
                  <label class="mb-2 block font-medium text-slate-700">
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
                class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-800 disabled:opacity-50"
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
                      ? 'Update Artikel'
                      : 'Simpan Artikel'
                }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
