<script setup>
// filepath: resources/js/Pages/Admin/Users/Index.vue

import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useConfirm } from "primevue/useconfirm"
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
  page: props.users.current_page || 1
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
  return Object.entries(props.roleOptions).map(([value, label]) => ({ label, value }))
})

const applyFilters = () => {
  const params = new URLSearchParams()

  if (searchQuery.value) params.set('search', searchQuery.value)
  if (selectedRole.value) params.set('role', selectedRole.value)

  // Add pagination params
  if (lazyParams.value.page > 1) params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.users.index') + (queryString ? '?' + queryString : '')

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
  if (selectedRole.value) params.set('role', selectedRole.value)

  params.set('page', lazyParams.value.page)

  const queryString = params.toString()
  const url = route('admin.users.index') + (queryString ? '?' + queryString : '')

  router.get(url, {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
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
      class: 'mr-2'
    },
    acceptProps: {
      icon: 'pi pi-trash',
      label: 'Hapus',
      severity: 'danger',
    },
    accept: () => {
      router.delete(route('admin.users.destroy', user.id))
    }
  })
}

const getRoleSeverity = (role) => {
  const severityMap = {
    admin: 'danger',
    staff: 'primary',
    user: 'success'
  }
  return severityMap[role] || 'secondary'
}

const getRoleIcon = (role) => {
  const iconMap = {
    admin: 'admin_panel_settings',
    staff: 'badge',
    user: 'person'
  }
  return iconMap[role] || 'person'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Stats computed
const stats = computed(() => {
  const allData = props.users.data || []
  const admin = allData.filter(user => user.role === 'admin').length
  const staff = allData.filter(user => user.role === 'staff').length
  const users = allData.filter(user => user.role === 'user').length

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
      command: () => { deleteUser(item); },
      visible: item.id !== props.auth.user.id,
    }
  ];
});
</script>

<template>
  <AdminLayout title="Kelola Pengguna">
    <ConfirmDialog :style="{ width: isMobile ? '95vw' : undefined }" />

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Kelola Pengguna</h2>
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
      <div class="grid grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <IconUsers class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Total</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ users.total || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center">
              <IconUserShield class="text-red-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Administrator</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.admin }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <IconIdBadge2 class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Staff</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.staff }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <IconUser class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Pengguna</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.users }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-slate-900">Filter & Pencarian</h3>
          <button
            v-if="searchQuery || selectedRole"
            @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div>
            <label class="block font-medium text-slate-700 mb-2">Cari Pengguna</label>
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
            <label class="block font-medium text-slate-700 mb-2">Filter Role</label>
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
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <DataTable
          v-bind="serverSideConfig"
          :value="users.data"
          @page="onPage"
        >
          <template #empty>
            <div class="text-center py-12">
              <IconUsers class="text-slate-300 mx-auto mb-4" size="30"/>
              <p class="text-slate-500 text-lg font-medium">
                {{ searchQuery || selectedRole ? 'Tidak ada pengguna ditemukan' : 'Belum ada pengguna yang terdaftar' }}
              </p>
              <p class="text-slate-400 mt-1 text-sm">
                {{ searchQuery || selectedRole ? 'Coba ubah kriteria pencarian' : 'Pengguna yang ditambahkan akan muncul di sini' }}
              </p>
            </div>
          </template>

          <Column header="Pengguna" class="min-w-80">
            <template #body="{ data }">
              <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center"
                       :class="{
                         'bg-red-100 text-red-600': data.role === 'admin',
                         'bg-blue-100 text-blue-600': data.role === 'staff',
                         'bg-green-100 text-green-600': data.role === 'user'
                       }">
                    <span class="text-lg font-semibold">
                      {{ data.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </div>

                <div class="flex-1 min-w-0">
                  <h3 class="font-medium text-slate-900 truncate">
                    {{ data.name }}
                  </h3>
                  <p class="text-sm text-slate-500 truncate">
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

          <Column field="created_at" header="Terdaftar" class="hidden lg:table-cell">
            <template #body="{ data }">
              <span class="text-sm text-slate-500">{{ formatDate(data.created_at) }}</span>
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
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200">
          <!-- Header -->
          <div class="p-4 lg:p-6 border-b border-slate-200 flex items-center gap-3 lg:gap-4">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <IconEdit class="text-blue-600" :size="!isDesktop ? 18 : undefined" v-if="isEditing"/>
              <IconUser class="text-blue-600" :size="!isDesktop ? 18 : undefined" v-else/>
            </div>
            <div>
              <h3 class="text-xl/6 font-semibold text-slate-900">{{ isEditing ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
              <p class="text-xs lg:text-sm text-slate-500">{{ isEditing ? `Perbarui informasi ${currentUser?.name}` : 'Buat akun pengguna baru' }}</p>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 lg:p-6">
            <div class="space-y-4 lg:space-y-6">
              <!-- Authentication Section (Only for Edit) -->
              <div v-if="isEditing" class="bg-amber-50 border border-amber-200 rounded-xl p-4 lg:p-6">
                <div class="flex items-start gap-3 mb-2">
                  <IconShieldLock class="text-amber-600"/>
                  <div><h4 class="font-medium text-amber-800">Verifikasi Keamanan</h4></div>
                </div>
                <p class="text-sm text-amber-700 mb-4">Masukkan password dari akun <strong>{{ currentUser?.name }}</strong> untuk melanjutkan</p>

                <div>
                  <label for="current_password" class="block font-medium text-slate-700 mb-2">
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
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.current_password }"
                    :feedback="false"
                  />
                  <p v-if="form.errors.current_password" class="mt-1 text-red-600 text-xs">
                    {{ form.errors.current_password }}
                  </p>
                </div>
              </div>

              <!-- User Information -->
              <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 lg:p-6 space-y-4">
                <div>
                  <label for="name" class="block font-medium text-slate-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="name"
                    v-model="form.name"
                    placeholder="Masukkan nama lengkap..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.name }"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-red-600 text-xs">
                    {{ form.errors.name }}
                  </p>
                </div>

                <div>
                  <label for="email" class="block font-medium text-slate-700 mb-2">
                    Alamat Email <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="Masukkan alamat email..."
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.email }"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-red-600 text-xs">
                    {{ form.errors.email }}
                  </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                  <div>
                    <label for="password" class="block font-medium text-slate-700 mb-2">
                      {{ isEditing ? 'Password Baru' : 'Password' }} <span v-if="!isEditing" class="text-red-500">*</span>
                    </label>
                    <Password
                      id="password"
                      v-model="form.password"
                      :placeholder="isEditing ? '' : 'Masukkan password...'"
                      toggleMask
                      :required="!isEditing"
                      class="w-full"
                      inputClass="w-full"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.password }"
                      :feedback="true"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-red-600 text-xs">
                      {{ form.errors.password }}
                    </p>
                    <p v-else-if="isEditing" class="mt-1 text-slate-500 text-xs">
                      Kosongkan jika tidak ingin mengubah
                    </p>
                  </div>

                  <div>
                    <label for="password_confirmation" class="block font-medium text-slate-700 mb-2">
                      Konfirmasi Password <span v-if="!isEditing" class="text-red-500">*</span>
                    </label>
                    <Password
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      placeholder=""
                      toggleMask
                      :required="!isEditing"
                      class="w-full"
                      inputClass="w-full"
                      :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.password_confirmation }"
                      :feedback="false"
                    />
                    <p v-if="form.errors.password_confirmation" class="mt-1 text-red-600 text-xs">
                      {{ form.errors.password_confirmation }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Settings -->
              <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 lg:p-6">
                <div>
                  <label for="role" class="block font-medium text-slate-700 mb-2">
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
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.role }"
                  />
                  <p v-if="form.errors.role" class="mt-1 text-red-600 text-xs">
                    {{ form.errors.role }}
                  </p>
                  <p class="mt-1 text-slate-500 text-xs">
                    Pilih role sesuai dengan tingkat akses yang diperlukan
                  </p>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-slate-200">
              <Button
                @click="closeCallback"
                severity="secondary"
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
                  <IconLoader3 v-if="form.processing" class="animate-spin" size="16"/>
                  <IconDeviceFloppy v-else size="16"/>
                  {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Update Pengguna' : 'Simpan Pengguna') }}
                </template>
              </Button>
            </div>
          </form>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
