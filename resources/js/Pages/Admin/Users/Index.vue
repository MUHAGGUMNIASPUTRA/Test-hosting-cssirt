<script setup>
// filepath: resources/js/Pages/Admin/Users/Index.vue

import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  users: Object,
  roleOptions: Object,
  filters: Object,
  auth: Object,
})

const { isMobile, isDesktop, dtConfig } = useResponsive()
const confirm = useConfirm()

// Dialog states
const dialogVisible = ref(false)
const isEditing = ref(false)
const currentUser = ref(null)

// Search and filters
const searchQuery = ref(props.filters?.search || '')
const selectedRole = ref(props.filters?.role || '')

// Add pagination state
const lazyParams = ref({
  first: 0,
  rows: props.users.per_page || 10,
  page: props.users.current_page || 1,
})

// Form for user operations
const form = useForm({
  name: '',
  email: '',
  current_password: '',
  password: '',
  password_confirmation: '',
  role: 'user',
})

const roleOptionsArray = computed(() => {
  return Object.entries(props.roleOptions).map(([value, label]) => ({
    label,
    value,
  }))
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedRole.value) params.set('role', selectedRole.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url =
    route('admin.users.index') + (queryString ? '?' + queryString : '')

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
  if (selectedRole.value) params.set('role', selectedRole.value)

  params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url =
    route('admin.users.index') + (queryString ? '?' + queryString : '')

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
  selectedRole.value = ''
  lazyParams.value.page = 1
  lazyParams.value.first = 0
  applyFilters()
}

const openCreateDialog = () => {
  isEditing.value = false
  currentUser.value = null
  form.reset()
  form.role = 'user'
  dialogVisible.value = true
}

const openEditDialog = (user) => {
  isEditing.value = true
  currentUser.value = user
  form.name = user.name
  form.email = user.email
  form.current_password = ''
  form.password = ''
  form.password_confirmation = ''
  form.role = user.role
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  form.reset()
  form.clearErrors()
}

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('admin.users.update', currentUser.value.id), {
      onSuccess: () => {
        closeDialog()
      },
    })
  } else {
    form.post(route('admin.users.store'), {
      onSuccess: () => {
        closeDialog()
      },
    })
  }
}

const deleteUser = (user) => {
  confirm.require({
    message: `Apakah Anda yakin ingin menghapus pengguna "${user.name}"?`,
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
      router.delete(route('admin.users.destroy', user.id))
    },
  })
}

const getRoleSeverity = (role) => {
  const severityMap = {
    admin: 'danger',
    staff: 'primary',
    user: 'success',
  }
  return severityMap[role] || 'secondary'
}

const getRoleIcon = (role) => {
  const iconMap = {
    admin: 'admin_panel_settings',
    staff: 'badge',
    user: 'person',
  }
  return iconMap[role] || 'person'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

// Stats computed
const stats = computed(() => {
  const allData = props.users.data || []
  const admin = allData.filter((user) => user.role === 'admin').length
  const staff = allData.filter((user) => user.role === 'staff').length
  const users = allData.filter((user) => user.role === 'user').length

  return { admin, staff, users }
})

// Server-side DataTable configuration
const serverSideConfig = computed(() => {
  return {
    ...dtConfig(),
    lazy: true,
    totalRecords: props.users.total,
    first: (props.users.current_page - 1) * props.users.per_page,
    rows: props.users.per_page,
  }
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
        deleteUser(item)
      },
      visible: item.id !== props.auth.user.id,
    },
  ]
})
</script>

<template>
  <AdminLayout title="Kelola Pengguna">
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
              Kelola Pengguna
            </h2>
            <p class="text-slate-600">Kelola akun pengguna sistem</p>
          </div>
          <Button
            @click="openCreateDialog"
            severity="primary"
            class="w-full sm:w-auto"
          >
            <template #default>
              <IconUserPlus size="16" />
              Tambah Pengguna
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
              <IconUsers
                class="text-blue-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Total
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ users.total || 0 }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div class="flex items-center">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 lg:h-12 lg:w-12"
            >
              <IconUserShield
                class="text-red-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Administrator
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.admin }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div class="flex items-center">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
            >
              <IconIdBadge2
                class="text-blue-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Staff
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.staff }}
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
              <IconUser
                class="text-green-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Pengguna
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.users }}
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
            v-if="searchQuery || selectedRole"
            @click="clearFilters"
            class="font-medium text-blue-600 hover:text-blue-800"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="mb-2 block font-medium text-slate-700"
              >Cari Pengguna</label
            >
            <div class="relative">
              <IconField class="w-full">
                <InputIcon>
                  <i class="pi pi-search" />
                </InputIcon>
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari nama atau email..."
                  class="w-full pl-10"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </div>
          </div>

          <div>
            <label class="mb-2 block font-medium text-slate-700"
              >Filter Role</label
            >
            <Select
              v-model="selectedRole"
              :options="roleOptionsArray"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Role"
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
        <DataTable v-bind="serverSideConfig" :value="users.data" @page="onPage">
          <template #empty>
            <div class="py-12 text-center">
              <IconUsers class="mx-auto mb-4 text-slate-300" size="30" />
              <p class="text-lg font-medium text-slate-500">
                {{
                  searchQuery || selectedRole
                    ? 'Tidak ada pengguna ditemukan'
                    : 'Belum ada pengguna yang terdaftar'
                }}
              </p>
              <p class="mt-1 text-sm text-slate-400">
                {{
                  searchQuery || selectedRole
                    ? 'Coba ubah kriteria pencarian'
                    : 'Pengguna yang ditambahkan akan muncul di sini'
                }}
              </p>
            </div>
          </template>

          <Column header="Pengguna" class="min-w-80">
            <template #body="{ data }">
              <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                  <div
                    class="flex h-10 w-10 items-center justify-center rounded-full"
                    :class="{
                      'bg-red-100 text-red-600': data.role === 'admin',
                      'bg-blue-100 text-blue-600': data.role === 'staff',
                      'bg-green-100 text-green-600': data.role === 'user',
                    }"
                  >
                    <span class="text-lg font-semibold">
                      {{ data.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </div>

                <div class="min-w-0 flex-1">
                  <h3 class="truncate font-medium text-slate-900">
                    {{ data.name }}
                  </h3>
                  <p class="truncate text-sm text-slate-500">
                    {{ data.email }}
                  </p>
                </div>
              </div>
            </template>
          </Column>

          <Column field="role" header="Role" class="hidden lg:table-cell">
            <template #body="{ data }">
              <Tag
                :value="roleOptions[data.role]"
                :severity="getRoleSeverity(data.role)"
                size="small"
              />
            </template>
          </Column>

          <Column
            field="created_at"
            header="Terdaftar"
            class="hidden lg:table-cell"
          >
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{
                formatDate(data.created_at)
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
      class="w-full max-w-[95vw] lg:max-w-2xl"
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
              <IconUser
                class="text-blue-600"
                :size="!isDesktop ? 18 : undefined"
                v-else
              />
            </div>
            <div>
              <h3 class="text-xl/6 font-semibold text-slate-900">
                {{ isEditing ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
              </h3>
              <p class="text-xs text-slate-500 lg:text-sm">
                {{
                  isEditing
                    ? `Perbarui informasi ${currentUser?.name}`
                    : 'Buat akun pengguna baru'
                }}
              </p>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 lg:p-6">
            <div class="space-y-4 lg:space-y-6">
              <!-- Authentication Section (Only for Edit) -->
              <div
                v-if="isEditing"
                class="rounded-xl border border-amber-200 bg-amber-50 p-4 lg:p-6"
              >
                <div class="mb-2 flex items-start gap-3">
                  <IconShieldLock class="text-amber-600" />
                  <div>
                    <h4 class="font-medium text-amber-800">
                      Verifikasi Keamanan
                    </h4>
                  </div>
                </div>
                <p class="mb-4 text-sm text-amber-700">
                  Masukkan password dari akun
                  <strong>{{ currentUser?.name }}</strong> untuk melanjutkan
                </p>

                <div>
                  <label
                    for="current_password"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Password Saat Ini <span class="text-red-500">*</span>
                  </label>
                  <Password
                    id="current_password"
                    v-model="form.current_password"
                    :placeholder="'Masukkan password ' + currentUser?.name"
                    toggleMask
                    required
                    class="w-full"
                    inputClass="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.current_password,
                    }"
                    :feedback="false"
                  />
                  <p
                    v-if="form.errors.current_password"
                    class="mt-1 text-xs text-red-600"
                  >
                    {{ form.errors.current_password }}
                  </p>
                </div>
              </div>

              <!-- User Information -->
              <div
                class="space-y-4 rounded-xl border border-slate-200 bg-slate-50 p-4 lg:p-6"
              >
                <div>
                  <label
                    for="name"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Nama Lengkap <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="name"
                    v-model="form.name"
                    placeholder="Masukkan nama lengkap..."
                    required
                    class="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.name,
                    }"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>

                <div>
                  <label
                    for="email"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Alamat Email <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="Masukkan alamat email..."
                    required
                    class="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.email,
                    }"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">
                    {{ form.errors.email }}
                  </p>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                  <div>
                    <label
                      for="password"
                      class="mb-2 block font-medium text-slate-700"
                    >
                      {{ isEditing ? 'Password Baru' : 'Password' }}
                      <span v-if="!isEditing" class="text-red-500">*</span>
                    </label>
                    <Password
                      id="password"
                      v-model="form.password"
                      :placeholder="isEditing ? '' : 'Masukkan password...'"
                      toggleMask
                      :required="!isEditing"
                      class="w-full"
                      inputClass="w-full"
                      :class="{
                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                          form.errors.password,
                      }"
                      :feedback="true"
                    />
                    <p
                      v-if="form.errors.password"
                      class="mt-1 text-xs text-red-600"
                    >
                      {{ form.errors.password }}
                    </p>
                    <p
                      v-else-if="isEditing"
                      class="mt-1 text-xs text-slate-500"
                    >
                      Kosongkan jika tidak ingin mengubah
                    </p>
                  </div>

                  <div>
                    <label
                      for="password_confirmation"
                      class="mb-2 block font-medium text-slate-700"
                    >
                      Konfirmasi Password
                      <span v-if="!isEditing" class="text-red-500">*</span>
                    </label>
                    <Password
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      placeholder=""
                      toggleMask
                      :required="!isEditing"
                      class="w-full"
                      inputClass="w-full"
                      :class="{
                        'border-red-300 focus:border-red-500 focus:ring-red-500':
                          form.errors.password_confirmation,
                      }"
                      :feedback="false"
                    />
                    <p
                      v-if="form.errors.password_confirmation"
                      class="mt-1 text-xs text-red-600"
                    >
                      {{ form.errors.password_confirmation }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Settings -->
              <div
                class="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:p-6"
              >
                <div>
                  <label
                    for="role"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Role Pengguna <span class="text-red-500">*</span>
                  </label>
                  <Select
                    id="role"
                    v-model="form.role"
                    :options="roleOptionsArray"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Pilih Role"
                    class="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.role,
                    }"
                  />
                  <p v-if="form.errors.role" class="mt-1 text-xs text-red-600">
                    {{ form.errors.role }}
                  </p>
                  <p class="mt-1 text-xs text-slate-500">
                    Pilih role sesuai dengan tingkat akses yang diperlukan
                  </p>
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
                        ? 'Update Pengguna'
                        : 'Simpan Pengguna'
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
