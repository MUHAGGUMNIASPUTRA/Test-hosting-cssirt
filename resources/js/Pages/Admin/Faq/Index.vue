<script setup>
// filepath: resources/js/Pages/Admin/Faq/Index.vue

import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useAdminTable } from '@/Composables/useAdminTable'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  faqs: Object,
  categories: Array,
  filters: Object,
})

const { isMobile } = useResponsive()
const confirm = useConfirm()

// --- Dialog states ---
const dialogVisible = ref(false)
const isEditing = ref(false)
const currentFaq = ref(null)

// --- Filter state ---
const searchQuery      = ref(props.filters?.search   || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedStatus   = ref(props.filters?.status   || '')

// --- Server-side DataTable + pagination ---
const paginatedData = computed(() => props.faqs)

const { serverSideConfig, applyFilters, onPage, clearFilters, hasActiveFilters } = useAdminTable(
  paginatedData,
  'admin.faqs.index',
  { search: searchQuery, category: selectedCategory, status: selectedStatus }
)

// --- Form ---
const form = useForm({
  question: '',
  answer: '',
  category: '',
  is_published: true,
})

const statusOptions = [
  { label: 'Draft',       value: 'draft' },
  { label: 'Diterbitkan', value: 'published' },
]

const categoryOptions = computed(() =>
  props.categories.map(cat => ({ label: cat, value: cat }))
)

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
  form.question     = faq.question
  form.answer       = faq.answer
  form.category     = faq.category || ''
  form.is_published = faq.is_published
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  const options = { onSuccess: () => closeDialog() }
  if (isEditing.value) {
    form.put(route('admin.faqs.update', currentFaq.value.id), options)
  } else {
    form.post(route('admin.faqs.store'), options)
  }
}

const deleteFaq = (faq) => {
  confirm.require({
    message: `Hapus FAQ "${faq.question}"`,
    header: 'Konfirmasi Penghapusan',
    rejectProps: {
      icon: 'pi pi-times',
      label: 'Batal',
      severity: 'secondary',
      outlined: true,
      class: 'mr-2'
    },
    acceptProps: {
      icon: 'pi pi-trash',
      label: 'Hapus',
      severity: 'danger',
    },
    accept: () => {
      router.delete(route('admin.faqs.destroy', faq.id))
    }
  })
}

// --- Helpers ---
const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

const truncateText = (text, length = 100) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const stats = computed(() => {
  const allData = props.faqs.data || []
  return {
    published:       allData.filter(f => f.is_published).length,
    draft:           allData.filter(f => !f.is_published).length,
    categoriesCount: new Set(allData.filter(f => f.category).map(f => f.category)).size,
  }
})

const answerWordCount = computed(() => {
  if (!form.answer) return 0
  return form.answer.trim().split(/\s+/).filter(w => w.length > 0).length
})

// Action menu handling
const actionMenu = ref();
const selectedMenu = ref(null);
const toggleActionMenu = (event, item) => {
  selectedMenu.value = item;
  actionMenu.value.toggle(event);
};

const actionMenuItems = computed(() => {
  if (!selectedMenu.value) return [];
  const item = selectedMenu.value;
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => { openEditDialog(item); },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => { deleteFaq(item); },
    }
  ];
});
</script>

<template>
  <AdminLayout title="Kelola FAQ">
    <ConfirmDialog :style="{ width: isMobile ? '95vw' : undefined }" />

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Kelola FAQ</h2>
            <p class="text-slate-600">Kelola pertanyaan yang sering diajukan</p>
          </div>
          <Button
            @click="openCreateDialog"
            severity="primary"
            class="w-full sm:w-auto"
          >
            <template #default>
              <IconCirclePlus size="16" />
              Tambah FAQ
            </template>
          </Button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">
        <StatCard color="blue" label="Total FAQ" :value="faqs.total || 0">
          <template #default="{ iconClass, iconSize }">
            <IconHelp :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard color="green" label="Diterbitkan" :value="stats.published">
          <template #default="{ iconClass, iconSize }">
            <IconCircleCheck :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard color="orange" label="Draft" :value="stats.draft">
          <template #default="{ iconClass, iconSize }">
            <IconFile :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard color="purple" label="Kategori" :value="stats.categoriesCount">
          <template #default="{ iconClass, iconSize }">
            <IconCategory :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari FAQ</label>
            <IconField class="w-full">
              <InputIcon><i class="pi pi-search" /></InputIcon>
              <InputText
                v-model="searchQuery"
                placeholder="Cari pertanyaan, jawaban, atau kategori..."
                class="w-full"
                @keyup.enter="applyFilters"
              />
            </IconField>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Kategori</label>
            <Select v-model="selectedCategory" :options="categoryOptions"
              optionLabel="label" optionValue="value"
              placeholder="Pilih Kategori" class="w-full" showClear @change="applyFilters" />
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Status</label>
            <Select v-model="selectedStatus" :options="statusOptions"
              optionLabel="label" optionValue="value"
              placeholder="Pilih Status" class="w-full" showClear @change="applyFilters" />
          </div>
        </div>
      </div>

      <!-- DataTable -->
      <AdminDataTable :value="faqs.data" :server-config="serverSideConfig" @page="onPage">
        <template #empty>
          <div class="text-center py-12">
            <IconHelp class="text-slate-300 mx-auto mb-4" size="30" />
            <p class="text-slate-500 text-lg font-medium">
              {{ hasActiveFilters ? 'Tidak ada FAQ yang sesuai filter' : 'Belum ada FAQ yang dibuat' }}
            </p>
            <p class="text-slate-400 mt-1 text-sm">
              {{ hasActiveFilters ? 'Coba ubah kriteria pencarian' : 'FAQ yang dibuat akan muncul di sini' }}
            </p>
          </div>
        </template>

        <Column header="FAQ" class="min-w-80">
          <template #body="{ data }">
            <div class="flex-1 min-w-0">
              <h3 class="font-medium text-slate-700 mb-1 line-clamp-2">{{ data.question }}</h3>
              <p class="text-slate-600 text-sm line-clamp-2">{{ truncateText(data.answer, 120) }}</p>
              <div class="lg:hidden text-xs text-slate-500 flex items-center gap-1 mt-1">
                <IconTag size="14" stroke-width="1.5"/>
                <span>{{ data.category }}</span>
              </div>
            </div>
          </template>
        </Column>

        <Column field="category" header="Kategori" class="hidden lg:table-cell">
          <template #body="{ data }">
            <span class="text-sm text-slate-500">
              {{ data.category || 'Tidak ada' }}
            </span>
          </template>
        </Column>

        <Column field="is_published" header="Status" class="hidden lg:table-cell">
          <template #body="{ data }">
            <StatusBadge type="published" :value="data.is_published" />
          </template>
        </Column>

        <Column field="updated_at" header="Diperbarui" class="hidden lg:table-cell">
          <template #body="{ data }">
            <span class="text-sm text-slate-500">{{ formatDate(data.updated_at) }}</span>
          </template>
        </Column>

        <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex items-center justify-end">
              <Button
                variant="text"
                class="!p-0"
                @click="toggleActionMenu($event, data)"
              >
                <template #default>
                  <div class="flex items-center text-slate-400 hover:text-blue-600">
                    <IconChevronDown size="22" stroke-width="1.5" />
                  </div>
                </template>
              </Button>

              <Menu
                ref="actionMenu"
                :model="actionMenuItems"
                :popup="true"
                class="!min-w-28"
                :pt="{
                  itemIcon: { class: '!text-sm mr-1' },
                  itemLabel: { class: 'text-sm' }
                }"
              />
            </div>
          </template>
        </Column>
      </AdminDataTable>
    </div>

    <!-- Create/Edit Dialog -->
    <Dialog
      v-model:visible="dialogVisible"
      :modal="true"
      :closable="false"
      class="w-full max-w-[95vw] lg:max-w-3xl"
    >
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200">
          <!-- Header -->
          <div class="p-4 lg:p-6 border-b border-slate-200">
            <div class="flex items-center gap-3 lg:gap-4">
              <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                <IconEdit class="text-blue-600" :size="isMobile ? 18 : undefined" v-if="isEditing"/>
                <IconHelp class="text-blue-600" :size="isMobile ? 18 : undefined" v-else/>
              </div>
              <div>
                <h3 class="text-xl/6 font-semibold text-slate-900">{{ isEditing ? 'Edit FAQ' : 'Tambah FAQ' }}</h3>
                <p class="text-xs lg:text-sm text-slate-500">{{ isEditing ? 'Perbarui informasi FAQ' : 'Buat FAQ baru untuk membantu pengguna' }}</p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 lg:p-6">
            <div class="space-y-4 lg:space-y-6">
              <!-- Main Content -->
              <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 lg:p-6 space-y-4">
                <div>
                  <label for="question" class="block font-medium text-slate-700 mb-2">
                    Pertanyaan <span class="text-red-500">*</span>
                  </label>
                  <Textarea id="question" v-model="form.question" rows="2"
                    placeholder="Masukkan pertanyaan yang sering diajukan..."
                    required class="w-full"
                    :class="{ 'border-red-300': form.errors.question }" />
                  <p v-if="form.errors.question" class="mt-1 text-red-600 text-sm">{{ form.errors.question }}</p>
                </div>

                <div>
                  <label for="answer" class="block font-medium text-slate-700 mb-2">
                    Jawaban <span class="text-red-500">*</span>
                  </label>
                  <Textarea id="answer" v-model="form.answer" rows="5"
                    placeholder="Masukkan jawaban yang lengkap dan informatif..."
                    required class="w-full"
                    :class="{ 'border-red-300': form.errors.answer }" />
                  <div class="flex justify-between items-center mt-1">
                    <p v-if="form.errors.answer" class="text-red-600 text-sm">{{ form.errors.answer }}</p>
                    <p class="text-xs text-slate-400 ml-auto">{{ answerWordCount }} kata</p>
                  </div>
                </div>
              </div>

              <!-- Category and Status -->
              <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 lg:p-6">
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
                      <label for="is_published" class="font-medium text-slate-700">Status Publikasi</label>
                      <ToggleSwitch id="is_published" v-model="form.is_published" />
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
                @click="closeCallback"
                severity="secondary"
                variant="outlined"
                :disabled="form.processing"
              >
                <template #default>
                  <IconX size="16"/>Batal
                </template>
              </Button>
              <Button
                type="submit"
                severity="primary"
                :loading="form.processing"
              >
                <template #default>
                  <IconLoader3 v-if="form.processing" class="animate-spin" size="16" />
                  <IconDeviceFloppy v-else size="16" />
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
