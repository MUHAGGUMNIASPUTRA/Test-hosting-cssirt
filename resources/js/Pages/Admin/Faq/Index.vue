<script setup>
// filepath: resources/js/Pages/Admin/Faq/Index.vue
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useConfirm } from "primevue/useconfirm"
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  faqs: Object,
  categories: Array,
  filters: Object,
})

const { dtConfig } = useResponsive()
const confirm = useConfirm()

// Dialog states
const dialogVisible = ref(false)
const isEditing = ref(false)
const currentFaq = ref(null)

// Search and filters
const searchQuery = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedStatus = ref(props.filters?.status || '')

// Add pagination state
const lazyParams = ref({
  first: 0,
  rows: props.faqs.per_page || 10,
  page: props.faqs.current_page || 1
})

// Form for FAQ operations
const form = useForm({
  question: '',
  answer: '',
  category: '',
  is_published: true,
})

const statusOptions = [
  { label: 'Dipublikasi', value: 'published' },
  { label: 'Draft', value: 'draft' },
]

const categoryOptions = computed(() => {
  return props.categories.map(cat => ({ label: cat, value: cat }))
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedCategory.value) params.set('category', selectedCategory.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.faqs.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

// Handle pagination change
const onPage = (event) => {
  lazyParams.value.first = event.first
  lazyParams.value.rows = event.rows
  lazyParams.value.page = Math.floor(event.first / event.rows) + 1

  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedCategory.value) params.set('category', selectedCategory.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.faqs.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedStatus.value = ''
  lazyParams.value.page = 1
  lazyParams.value.first = 0
  applyFilters()
}

const openCreateDialog = () => {
  isEditing.value = false
  currentFaq.value = null
  form.reset()
  form.is_published = true
  dialogVisible.value = true
}

const openEditDialog = (faq) => {
  isEditing.value = true
  currentFaq.value = faq
  form.question = faq.question
  form.answer = faq.answer
  form.category = faq.category || ''
  form.is_published = faq.is_published
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('admin.faqs.update', currentFaq.value.id), {
      onSuccess: () => {
        closeDialog()
      },
    })
  } else {
    form.post(route('admin.faqs.store'), {
      onSuccess: () => {
        closeDialog()
      },
    })
  }
}

const deleteFaq = (faq) => {
  confirm.require({
    message: `Apakah Anda yakin ingin menghapus FAQ ini?`,
    header: 'Konfirmasi Penghapusan',
    rejectProps: {
      label: 'Batal',
      severity: 'secondary',
      outlined: true,
      size: 'small',
      class: 'mr-2'
    },
    acceptProps: {
      label: 'Ya, Hapus',
      severity: 'danger',
      size: 'small'
    },
    accept: () => {
      router.delete(route('admin.faqs.destroy', faq.id))
    }
  })
}

const getStatusSeverity = (isPublished) => {
  return isPublished ? 'success' : 'secondary'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const truncateText = (text, length = 100) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Stats computed
const stats = computed(() => {
  const allData = props.faqs.data || []
  const published = allData.filter(faq => faq.is_published).length
  const draft = allData.filter(faq => !faq.is_published).length
  const categoriesCount = new Set(allData.filter(faq => faq.category).map(faq => faq.category)).size

  return { published, draft, categoriesCount }
})

// Server-side DataTable configuration
const serverSideConfig = computed(() => {
  return {
    ...dtConfig(),
    lazy: true,
    totalRecords: props.faqs.total,
    first: (props.faqs.current_page - 1) * props.faqs.per_page,
    rows: props.faqs.per_page,
  }
})

// Word count for answer
const answerWordCount = computed(() => {
  if (!form.answer) return 0
  return form.answer.trim().split(/\s+/).filter(word => word.length > 0).length
})
</script>

<template>
  <AdminLayout title="Kelola FAQ">
    <ConfirmDialog />

    <div class="space-y-4 sm:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Kelola FAQ</h2>
            <p class="text-slate-600">Kelola pertanyaan yang sering diajukan</p>
          </div>
          <Button
            @click="openCreateDialog"
            severity="primary"
            class="px-4 py-2"
          >
            <template #default>
              <span class="material-symbols-outlined !text-xl">add</span>
              Tambah FAQ
            </template>
          </Button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-blue-600">help</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Total FAQ</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ faqs.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-green-600">check_circle</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Dipublikasi</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ stats.published }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-orange-50 border border-orange-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-orange-600">draft</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Draft</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ stats.draft }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-purple-600">category</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Kategori</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ stats.categoriesCount }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="searchQuery || selectedCategory || selectedStatus"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari FAQ</label>
            <div class="relative">
              <IconField class="w-full">
                <InputIcon>
                  <i class="pi pi-search" />
                </InputIcon>
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari pertanyaan, jawaban, atau kategori..."
                  class="w-full pl-10"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </div>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Kategori</label>
            <Select
              v-model="selectedCategory"
              :options="categoryOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Kategori"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Status</label>
            <Select
              v-model="selectedStatus"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Status"
              class="w-full"
              showClear
              @change="applyFilters"
            />
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <DataTable
          v-bind="serverSideConfig"
          :value="faqs.data"
          @page="onPage"
        >
          <template #empty>
            <div class="text-center py-12">
              <span class="material-symbols-outlined text-slate-300 mb-4 !text-5xl">help</span>
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedCategory || selectedStatus ? 'Tidak ada FAQ yang sesuai filter' : 'Belum ada FAQ yang dibuat' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedCategory || selectedStatus ? 'Coba ubah kriteria pencarian' : 'FAQ yang dibuat akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column header="FAQ" class="min-w-80">
            <template #body="{ data }">
              <div class="flex items-start gap-3">
                <div class="flex-1 min-w-0">
                  <h3 class="font-medium text-slate-700 mb-1 line-clamp-2">
                    {{ data.question }}
                  </h3>

                  <p class="text-slate-600 text-sm line-clamp-2">
                    {{ truncateText(data.answer, 120) }}
                  </p>
                </div>
              </div>
            </template>
          </Column>

          <Column field="category" header="Kategori" class="hidden sm:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">
                {{ data.category || 'Tidak ada' }}
              </span>
            </template>
          </Column>

          <Column field="is_published" header="Status" class="hidden sm:table-cell">
            <template #body="{ data }">
              <Tag
                :value="data.is_published ? 'Dipublikasi' : 'Draft'"
                :severity="getStatusSeverity(data.is_published)"
                size="small"
              />
            </template>
          </Column>

          <Column field="updated_at" header="Diperbarui" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.updated_at) }}</span>
            </template>
          </Column>

          <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
            <template #body="{ data }">
              <div class="flex items-center justify-end space-x-2">
                <button
                  @click="openEditDialog(data)"
                  class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button
                  @click="deleteFaq(data)"
                  class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Hapus"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>

    <!-- Create/Edit Dialog -->
    <Dialog
      v-model:visible="dialogVisible"
      :modal="true"
      :closable="false"
      class="w-full max-w-[95vw] sm:max-w-3xl"
    >
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200">
          <!-- Header -->
          <div class="p-4 sm:p-6 border-b border-slate-200">
            <div class="flex items-center">
              <div>
                <h3 class="text-lg font-semibold text-slate-900">{{ isEditing ? 'Edit FAQ' : 'Tambah FAQ Baru' }}</h3>
                <p class="text-sm text-slate-500">{{ isEditing ? 'Perbarui informasi FAQ' : 'Buat FAQ baru untuk membantu pengguna' }}</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 sm:p-6">
            <div class="space-y-6">
              <!-- Main Content -->
              <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 sm:p-6 space-y-4">
                <div>
                  <label for="question" class="block font-medium text-slate-700 mb-2">
                    Pertanyaan <span class="text-red-500">*</span>
                  </label>
                  <Textarea
                    id="question"
                    v-model="form.question"
                    rows="2"
                    placeholder="Masukkan pertanyaan yang sering diajukan..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.question }"
                  />
                  <p v-if="form.errors.question" class="mt-1 text-red-600 text-sm">
                    {{ form.errors.question }}
                  </p>
                </div>

                <div>
                  <label for="answer" class="block font-medium text-slate-700 mb-2">
                    Jawaban <span class="text-red-500">*</span>
                  </label>
                  <Textarea
                    id="answer"
                    v-model="form.answer"
                    rows="5"
                    placeholder="Masukkan jawaban yang lengkap dan informatif..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.answer }"
                  />
                  <div class="flex justify-between items-center mt-1">
                    <p v-if="form.errors.answer" class="text-red-600 text-sm">
                      {{ form.errors.answer }}
                    </p>
                    <p class="text-xs text-slate-400 ml-auto">
                      {{ answerWordCount }} kata
                    </p>
                  </div>
                </div>
              </div>

              <!-- Category and Status -->
              <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 sm:p-6">
                <div class="space-y-4">
                  <div>
                    <label for="category" class="block font-medium text-slate-700 mb-2">
                      Kategori <span class="text-slate-500 font-normal">(Opsional)</span>
                    </label>
                    <InputText
                      id="category"
                      v-model="form.category"
                      placeholder="Masukkan kategori"
                      class="w-full"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.category }"
                    />
                    <p v-if="form.errors.category" class="mt-1 text-red-600 text-sm">
                      {{ form.errors.category }}
                    </p>
                    <p class="mt-1 text-slate-500 text-xs">
                      Contoh: Keamanan, Teknis, Umum
                    </p>
                  </div>

                  <div>
                    <div class="flex items-center justify-between">
                      <label for="is_published" class="font-medium text-slate-700">
                        Status Publikasi
                      </label>
                      <ToggleSwitch
                        id="is_published"
                        v-model="form.is_published"
                      />
                    </div>
                    <p class="text-sm text-slate-500 mt-1">
                      {{ form.is_published ? 'FAQ akan ditampilkan di website publik' : 'FAQ disimpan sebagai draft' }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-slate-200">
              <Button
                type="button"
                @click="closeCallback"
                severity="secondary"
                size="small"
                :disabled="form.processing"
              >
                Batal
              </Button>
              <Button
                type="submit"
                severity="primary"
                size="small"
                :loading="form.processing"
              >
                <template #default>
                  <span class="material-symbols-outlined !text-xl/4" :class="{ 'animate-spin': form.processing }">
                    {{ form.processing ? 'progress_activity' : 'save' }}
                  </span>
                  {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Update FAQ' : 'Simpan FAQ') }}
                </template>
              </Button>
            </div>
          </form>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
