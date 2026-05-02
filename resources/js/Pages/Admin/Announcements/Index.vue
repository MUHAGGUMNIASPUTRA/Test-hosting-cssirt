<script setup>
// filepath: resources/js/Pages/Admin/Announcements/Index.vue

import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  announcements: Object,
  levelOptions: Object,
  filters: Object,
})

const { isMobile, isDesktop, dtConfig } = useResponsive()
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
  page: props.announcements.current_page || 1,
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
  return Object.entries(props.levelOptions).map(([value, label]) => ({
    label,
    value,
  }))
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedLevel.value) params.set('level', selectedLevel.value)
  if (selectedStatus.value) params.set('status', selectedStatus.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url =
    route('admin.announcements.index') + (queryString ? '?' + queryString : '')

  router.get(
    url,
    {},
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
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
  const url =
    route('admin.announcements.index') + (queryString ? '?' + queryString : '')

  router.get(
    url,
    {},
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
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
    form.put(
      route('admin.announcements.update', currentAnnouncement.value.id),
      {
        onSuccess: () => {
          closeDialog()
        },
      },
    )
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
      icon: 'pi pi-times',
      label: 'Batal',
      severity: 'secondary',
      outlined: true,
      class: 'mr-2',
    },
    acceptProps: {
      icon: 'pi pi-trash',
      label: 'Hapus',
      severity: 'danger',
    },
    accept: () => {
      router.delete(route('admin.announcements.destroy', announcement.id))
    },
  })
}

const getLevelSeverity = (level) => {
  const severityMap = {
    info: 'info',
    warning: 'warn',
    critical: 'danger',
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
    day: 'numeric',
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

  const active = allData.filter((ann) => ann.is_active).length
  const current = allData.filter((ann) => {
    const start = new Date(ann.start_date)
    const end = new Date(ann.end_date)
    return ann.is_active && start <= now && end >= now
  }).length
  const scheduled = allData.filter((ann) => {
    const start = new Date(ann.start_date)
    return ann.is_active && start > now
  }).length
  const expired = allData.filter((ann) => {
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
    first:
      (props.announcements.current_page - 1) * props.announcements.per_page,
    rows: props.announcements.per_page,
  }
})

// Word count for content
const contentWordCount = computed(() => {
  if (!form.content) return 0
  return form.content
    .trim()
    .split(/\s+/)
    .filter((word) => word.length > 0).length
})

// Action menu handling
const actionMenu = ref()
const selectedMenu = ref(null)
const toggleActionMenu = (event, item) => {
  selectedMenu.value = item
  actionMenu.value.toggle(event)
}

const actionMenuItems = computed(() => {
  if (!selectedMenu.value) return []
  const item = selectedMenu.value
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => {
        openEditDialog(item)
      },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => {
        deleteAnnouncement(item)
      },
    },
  ]
})
</script>

<template>
  <AdminLayout title="Kelola Pengumuman">
    <ConfirmDialog :style="{ width: isMobile ? '95vw' : undefined }" />

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div
          class="flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
              Kelola Pengumuman
            </h2>
            <p class="text-slate-600">
              Kelola pengumuman penting untuk pengguna
            </p>
          </div>
          <Button
            @click="openCreateDialog"
            severity="primary"
            class="w-full sm:w-auto"
          >
            <template #default>
              <IconCirclePlus size="16" />
              Tambah Pengumuman
            </template>
          </Button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div
        class="grid grid-cols-2 gap-4 lg:grid-cols-2 lg:gap-6 xl:grid-cols-4"
      >
        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div class="flex items-center">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
            >
              <IconSpeakerphone
                class="text-blue-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Total
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ announcements.total || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div class="flex items-center">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-green-200 bg-green-50 lg:h-12 lg:w-12"
            >
              <IconCircleCheck
                class="text-green-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Aktif
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.current }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div class="flex items-center">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-200 bg-orange-50 lg:h-12 lg:w-12"
            >
              <IconClock
                class="text-orange-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Terjadwal
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.scheduled }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div class="flex items-center">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 lg:h-12 lg:w-12"
            >
              <IconHistory
                class="text-slate-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Kedaluwarsa
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.expired }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-xl font-semibold text-slate-900">
            Filter & Pencarian
          </h3>
          <button
            v-if="searchQuery || selectedLevel || selectedStatus"
            @click="clearFilters"
            class="font-medium text-blue-600 hover:text-blue-800"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div>
            <label class="mb-2 block font-medium text-slate-700"
              >Cari Pengumuman</label
            >
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
            <label class="mb-2 block font-medium text-slate-700"
              >Filter Level</label
            >
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
            <label class="mb-2 block font-medium text-slate-700"
              >Filter Status</label
            >
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
      <div
        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
      >
        <DataTable
          v-bind="serverSideConfig"
          :value="announcements.data"
          @page="onPage"
        >
          <template #empty>
            <div class="py-12 text-center">
              <IconSpeakerphone class="mx-auto mb-4 text-slate-300" size="30" />
              <p class="text-lg font-medium text-slate-500">
                {{
                  searchQuery || selectedLevel || selectedStatus
                    ? 'Tidak ada pengumuman ditemukan'
                    : 'Belum ada pengumuman yang dibuat'
                }}
              </p>
              <p class="mt-1 text-sm text-slate-400">
                {{
                  searchQuery || selectedLevel || selectedStatus
                    ? 'Coba ubah kriteria pencarian'
                    : 'Pengumuman yang dibuat akan muncul di sini'
                }}
              </p>
            </div>
          </template>

          <Column header="Pengumuman" class="min-w-80">
            <template #body="{ data }">
              <div class="flex items-start gap-3">
                <div class="min-w-0 flex-1">
                  <h3 class="line-clamp-2 font-medium text-slate-700">
                    {{ data.title }}
                  </h3>
                  <p class="line-clamp-2 text-sm text-slate-600">
                    {{ truncateText(data.content, 120) }}
                  </p>
                  <div
                    class="mt-1 flex items-center gap-1 text-xs text-slate-500"
                  >
                    <IconClock size="14" stroke-width="1.5" />
                    <span
                      >{{ formatDate(data.start_date) }} -
                      {{ formatDate(data.end_date) }}</span
                    >
                  </div>
                </div>
              </div>
            </template>
          </Column>

          <Column field="level" header="Level" class="hidden lg:table-cell">
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

          <Column
            field="updated_at"
            header="Diperbarui"
            class="hidden lg:table-cell"
          >
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{
                formatDate(data.updated_at)
              }}</span>
            </template>
          </Column>

          <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
            <template #body="{ data }">
              <div class="flex items-center justify-end">
                <Button
                  variant="text"
                  class="!p-0"
                  @click="toggleActionMenu($event, data)"
                >
                  <template #default>
                    <div
                      class="flex items-center text-slate-400 hover:text-blue-600"
                    >
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
                    itemLabel: { class: 'text-sm' },
                  }"
                />
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
      class="w-full max-w-[95vw] lg:max-w-3xl"
    >
      <template #container="{ closeCallback }">
        <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
          <!-- Header -->
          <div
            class="flex items-center gap-3 border-b border-slate-200 p-4 lg:gap-4 lg:p-6"
          >
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
            >
              <IconEdit
                class="text-blue-600"
                :size="!isDesktop ? 18 : undefined"
                v-if="isEditing"
              />
              <IconSpeakerphone
                class="text-blue-600"
                :size="!isDesktop ? 18 : undefined"
                v-else
              />
            </div>
            <div>
              <h3 class="text-xl/6 font-semibold text-slate-900">
                {{ isEditing ? 'Edit Pengumuman' : 'Tambah Pengumuman Baru' }}
              </h3>
              <p class="text-xs text-slate-500 lg:text-sm">
                {{
                  isEditing
                    ? 'Perbarui informasi pengumuman'
                    : 'Buat pengumuman baru untuk pengguna'
                }}
              </p>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 lg:p-6">
            <div class="space-y-4 lg:space-y-6">
              <!-- Main Content -->
              <div
                class="space-y-4 rounded-xl border border-slate-200 bg-slate-50 p-4 lg:p-6"
              >
                <div>
                  <label
                    for="title"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Judul Pengumuman <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="title"
                    v-model="form.title"
                    placeholder="Masukkan judul pengumuman..."
                    required
                    class="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.title,
                    }"
                  />
                  <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                    {{ form.errors.title }}
                  </p>
                </div>

                <div>
                  <label
                    for="content"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Konten Pengumuman <span class="text-red-500">*</span>
                  </label>
                  <Textarea
                    id="content"
                    v-model="form.content"
                    rows="3"
                    placeholder="Masukkan konten pengumuman..."
                    required
                    class="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.content,
                    }"
                  />
                  <div class="mt-1 flex items-center justify-between">
                    <p v-if="form.errors.content" class="text-sm text-red-600">
                      {{ form.errors.content }}
                    </p>
                    <p class="ml-auto text-xs text-slate-400">
                      {{ contentWordCount }} kata
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label
                      for="start_date"
                      class="mb-2 block font-medium text-slate-700"
                    >
                      Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <DatePicker
                      id="start_date"
                      v-model="form.start_date"
                      required
                      class="w-full"
                      dateFormat="dd M yy"
                      placeholder="Pilih tanggal mulai"
                      :class="{
                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                          form.errors.start_date,
                      }"
                    />
                    <p
                      v-if="form.errors.start_date"
                      class="mt-1 text-sm text-red-600"
                    >
                      {{ form.errors.start_date }}
                    </p>
                  </div>

                  <div>
                    <label
                      for="end_date"
                      class="mb-2 block font-medium text-slate-700"
                    >
                      Tanggal Berakhir <span class="text-red-500">*</span>
                    </label>
                    <DatePicker
                      id="end_date"
                      v-model="form.end_date"
                      required
                      class="w-full"
                      dateFormat="dd M yy"
                      placeholder="Pilih tanggal berakhir"
                      :class="{
                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                          form.errors.end_date,
                      }"
                    />
                    <p
                      v-if="form.errors.end_date"
                      class="mt-1 text-sm text-red-600"
                    >
                      {{ form.errors.end_date }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Settings -->
              <div
                class="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:p-6"
              >
                <div class="space-y-4">
                  <div>
                    <label
                      for="level"
                      class="mb-2 block font-medium text-slate-700"
                    >
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
                      :class="{
                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                          form.errors.level,
                      }"
                    />
                    <p
                      v-if="form.errors.level"
                      class="mt-1 text-sm text-red-600"
                    >
                      {{ form.errors.level }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                      Level menentukan prioritas dan warna pengumuman
                    </p>
                  </div>

                  <div>
                    <div class="flex items-center justify-between">
                      <label for="is_active" class="font-medium text-slate-700">
                        Status Aktif
                      </label>
                      <ToggleSwitch id="is_active" v-model="form.is_active" />
                    </div>
                    <p class="mt-1 text-sm text-slate-500">
                      {{
                        form.is_active
                          ? 'Pengumuman akan ditampilkan sesuai jadwal'
                          : 'Pengumuman disimpan sebagai draft'
                      }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div
              class="mt-6 flex items-center justify-end space-x-3 border-t border-slate-200 pt-6"
            >
              <Button
                @click="closeCallback"
                severity="secondary"
                variant="outlined"
                :disabled="form.processing"
              >
                <template #default> <IconX size="16" />Batal </template>
              </Button>
              <Button
                type="submit"
                severity="primary"
                :loading="form.processing"
              >
                <template #default>
                  <IconLoader3
                    v-if="form.processing"
                    class="animate-spin"
                    size="16"
                  />
                  <IconDeviceFloppy v-else size="16" />
                  {{
                    form.processing
                      ? 'Menyimpan...'
                      : isEditing
                        ? 'Update Pengumuman'
                        : 'Simpan Pengumuman'
                  }}
                </template>
              </Button>
            </div>
          </form>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
