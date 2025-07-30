<script setup>
// filepath: resources/js/Pages/Admin/Announcements/Index.vue
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useConfirm } from "primevue/useconfirm"
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  announcements: Object,
  levelOptions: Object,
  filters: Object,
})

const { dtConfig } = useResponsive()
const confirm = useConfirm()

// Dialog states
const dialogVisible = ref(false)
const isEditing = ref(false)
const currentAnnouncement = ref(null)

// Search and filters
const searchQuery = ref(props.filters?.search || '')
const selectedLevel = ref(props.filters?.level || '')
const selectedStatus = ref(props.filters?.status || '')

// Add pagination state
const lazyParams = ref({
  first: 0,
  rows: props.announcements.per_page || 10,
  page: props.announcements.current_page || 1
})

// Form for announcement operations
const form = useForm({
  title: '',
  content: '',
  level: 'info',
  start_date: '',
  end_date: '',
  is_active: true,
})

const statusOptions = [
  { label: 'Aktif', value: 'active' },
  { label: 'Tidak Aktif', value: 'inactive' },
  { label: 'Sedang Berjalan', value: 'current' },
  { label: 'Terjadwal', value: 'scheduled' },
  { label: 'Kedaluwarsa', value: 'expired' },
]

const levelOptionsArray = computed(() => {
  return Object.entries(props.levelOptions).map(([value, label]) => ({ label, value }))
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedLevel.value) params.set('level', selectedLevel.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.announcements.index') + (queryString ? '?' + queryString : '')

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
  if (selectedLevel.value) params.set('level', selectedLevel.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.announcements.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedLevel.value = ''
  selectedStatus.value = ''
  lazyParams.value.page = 1
  lazyParams.value.first = 0
  applyFilters()
}

const openCreateDialog = () => {
  isEditing.value = false
  currentAnnouncement.value = null
  form.reset()

  // Set default dates
  const now = new Date()
  const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000)
  form.start_date = now
  form.end_date = tomorrow
  form.level = 'info'
  form.is_active = true

  dialogVisible.value = true
}

const openEditDialog = (announcement) => {
  isEditing.value = true
  currentAnnouncement.value = announcement
  form.title = announcement.title
  form.content = announcement.content
  form.level = announcement.level
  form.start_date = new Date(announcement.start_date)
  form.end_date = new Date(announcement.end_date)
  form.is_active = announcement.is_active
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('admin.announcements.update', currentAnnouncement.value.id), {
      onSuccess: () => {
        closeDialog()
      },
    })
  } else {
    form.post(route('admin.announcements.store'), {
      onSuccess: () => {
        closeDialog()
      },
    })
  }
}

const deleteAnnouncement = (announcement) => {
  confirm.require({
    message: `Hapus pengumuman "${announcement.title}"?`,
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
      router.delete(route('admin.announcements.destroy', announcement.id))
    }
  })
}

const getLevelSeverity = (level) => {
  const severityMap = {
    info: 'info',
    warning: 'warn',
    critical: 'danger'
  }
  return severityMap[level] || 'info'
}

const getAnnouncementStatus = (announcement) => {
  const now = new Date()
  const startDate = new Date(announcement.start_date)
  const endDate = new Date(announcement.end_date)

  if (!announcement.is_active) {
    return { label: 'Tidak Aktif', severity: 'secondary' }
  }

  if (startDate > now) {
    return { label: 'Terjadwal', severity: 'info' }
  }

  if (endDate < now) {
    return { label: 'Kedaluwarsa', severity: 'secondary' }
  }

  return { label: 'Aktif', severity: 'success' }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const truncateText = (text, length = 100) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

// Stats computed
const stats = computed(() => {
  const allData = props.announcements.data || []
  const now = new Date()

  const active = allData.filter(ann => ann.is_active).length
  const current = allData.filter(ann => {
    const start = new Date(ann.start_date)
    const end = new Date(ann.end_date)
    return ann.is_active && start <= now && end >= now
  }).length
  const scheduled = allData.filter(ann => {
    const start = new Date(ann.start_date)
    return ann.is_active && start > now
  }).length
  const expired = allData.filter(ann => {
    const end = new Date(ann.end_date)
    return end < now
  }).length

  return { active, current, scheduled, expired }
})

// Server-side DataTable configuration
const serverSideConfig = computed(() => {
  return {
    ...dtConfig(),
    lazy: true,
    totalRecords: props.announcements.total,
    first: (props.announcements.current_page - 1) * props.announcements.per_page,
    rows: props.announcements.per_page,
  }
})

// Word count for content
const contentWordCount = computed(() => {
  if (!form.content) return 0
  return form.content.trim().split(/\s+/).filter(word => word.length > 0).length
})
</script>

<template>
  <AdminLayout title="Kelola Pengumuman">
    <ConfirmDialog />

    <div class="space-y-4 sm:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Kelola Pengumuman</h2>
            <p class="text-slate-600">Kelola pengumuman penting untuk pengguna</p>
          </div>
          <Button
            @click="openCreateDialog"
            severity="primary"
            class="px-4 py-2"
          >
            <template #default>
              <span class="material-symbols-outlined !text-xl">add</span>
              Tambah Pengumuman
            </template>
          </Button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-blue-600">campaign</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Total</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ announcements.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-green-600">check_circle</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Aktif</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ stats.current }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-orange-50 border border-orange-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-orange-600">schedule</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Terjadwal</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ stats.scheduled }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-slate-600">history</span>
            </div>
            <div class="ml-3">
              <p class="font-medium text-slate-600">Kedaluwarsa</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ stats.expired }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="searchQuery || selectedLevel || selectedStatus"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Pengumuman</label>
            <div class="relative">
              <IconField class="w-full">
                <InputIcon>
                  <i class="pi pi-search" />
                </InputIcon>
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari judul atau konten..."
                  class="w-full pl-10"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </div>
          </div>

          <div>
            <label class="block font-medium text-slate-700 mb-2">Filter Level</label>
            <Select
              v-model="selectedLevel"
              :options="levelOptionsArray"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Level"
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
          :value="announcements.data"
          @page="onPage"
        >
          <template #empty>
            <div class="text-center py-12">
              <span class="material-symbols-outlined text-slate-300 mb-4 !text-5xl">campaign</span>
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedLevel || selectedStatus ? 'Tidak ada pengumuman yang sesuai filter' : 'Belum ada pengumuman yang dibuat' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedLevel || selectedStatus ? 'Coba ubah kriteria pencarian' : 'Pengumuman yang dibuat akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column header="Pengumuman" class="min-w-80">
            <template #body="{ data }">
              <div class="flex items-start gap-3">
                <div class="flex-1 min-w-0">
                  <h3 class="font-medium text-slate-700 line-clamp-2">
                    {{ data.title }}
                  </h3>

                  <p class="text-slate-600 text-sm line-clamp-2">
                    {{ truncateText(data.content, 120) }}
                  </p>

                  <div class="text-xs text-slate-500">
                    <span class="material-symbols-outlined text-slate-400 !text-base mb-0.5 mr-1">schedule</span>
                    <span>{{ formatDate(data.start_date) }} - {{ formatDate(data.end_date) }}</span>
                  </div>
                </div>
              </div>
            </template>
          </Column>

          <Column field="level" header="Level" class="hidden sm:table-cell">
            <template #body="{ data }">
              <Tag
                :value="levelOptions[data.level]"
                :severity="getLevelSeverity(data.level)"
                size="small"
              />
            </template>
          </Column>

          <Column field="status" header="Status" class="hidden lg:table-cell">
            <template #body="{ data }">
              <Tag
                :value="getAnnouncementStatus(data).label"
                :severity="getAnnouncementStatus(data).severity"
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
                  @click="deleteAnnouncement(data)"
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
          <div class="p-4 sm:p-6 border-b border-slate-200 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-blue-600">{{ isEditing ? 'edit' : 'campaign' }}</span>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-slate-900">{{ isEditing ? 'Edit Pengumuman' : 'Tambah Pengumuman Baru' }}</h3>
              <p class="text-sm text-slate-500">{{ isEditing ? 'Perbarui informasi pengumuman' : 'Buat pengumuman baru untuk pengguna' }}</p>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 sm:p-6">
            <div class="space-y-4 sm:space-y-6">
              <!-- Main Content -->
              <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 sm:p-6 space-y-4">
                <div>
                  <label for="title" class="block font-medium text-slate-700 mb-2">
                    Judul Pengumuman <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="title"
                    v-model="form.title"
                    placeholder="Masukkan judul pengumuman..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.title }"
                  />
                  <p v-if="form.errors.title" class="mt-1 text-red-600 text-sm">
                    {{ form.errors.title }}
                  </p>
                </div>

                <div>
                  <label for="content" class="block font-medium text-slate-700 mb-2">
                    Konten Pengumuman <span class="text-red-500">*</span>
                  </label>
                  <Textarea
                    id="content"
                    v-model="form.content"
                    rows="3"
                    placeholder="Masukkan konten pengumuman..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.content }"
                  />
                  <div class="flex justify-between items-center mt-1">
                    <p v-if="form.errors.content" class="text-red-600 text-sm">
                      {{ form.errors.content }}
                    </p>
                    <p class="text-xs text-slate-400 ml-auto">
                      {{ contentWordCount }} kata
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="start_date" class="block font-medium text-slate-700 mb-2">
                      Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <DatePicker
                      id="start_date"
                      v-model="form.start_date"
                      required
                      class="w-full"
                      dateFormat="dd M yy"
                      placeholder="Pilih tanggal mulai"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.start_date }"
                    />
                    <p v-if="form.errors.start_date" class="mt-1 text-red-600 text-sm">
                      {{ form.errors.start_date }}
                    </p>
                  </div>

                  <div>
                    <label for="end_date" class="block font-medium text-slate-700 mb-2">
                      Tanggal Berakhir <span class="text-red-500">*</span>
                    </label>
                    <DatePicker
                      id="end_date"
                      v-model="form.end_date"
                      required
                      class="w-full"
                      dateFormat="dd M yy"
                      placeholder="Pilih tanggal berakhir"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.end_date }"
                    />
                    <p v-if="form.errors.end_date" class="mt-1 text-red-600 text-sm">
                      {{ form.errors.end_date }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Settings -->
              <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 sm:p-6">
                <div class="space-y-4">
                  <div>
                    <label for="level" class="block font-medium text-slate-700 mb-2">
                      Level Pengumuman <span class="text-red-500">*</span>
                    </label>
                    <Select
                      id="level"
                      v-model="form.level"
                      :options="levelOptionsArray"
                      optionLabel="label"
                      optionValue="value"
                      placeholder="Pilih Level"
                      class="w-full"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.level }"
                    />
                    <p v-if="form.errors.level" class="mt-1 text-red-600 text-sm">
                      {{ form.errors.level }}
                    </p>
                    <p class="mt-1 text-slate-500 text-xs">
                      Level menentukan prioritas dan warna pengumuman
                    </p>
                  </div>

                  <div>
                    <div class="flex items-center justify-between">
                      <label for="is_active" class="font-medium text-slate-700">
                        Status Aktif
                      </label>
                      <ToggleSwitch
                        id="is_active"
                        v-model="form.is_active"
                      />
                    </div>
                    <p class="text-sm text-slate-500 mt-1">
                      {{ form.is_active ? 'Pengumuman akan ditampilkan sesuai jadwal' : 'Pengumuman disimpan sebagai draft' }}
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
                  {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Update Pengumuman' : 'Simpan Pengumuman') }}
                </template>
              </Button>
            </div>
          </form>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
