<script setup>
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'

const props = defineProps({
  organizations: Object,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const paginatedData = computed(() => props.organizations)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.organizations.index', {
  search: searchQuery,
})

// Dialog
const dialogVisible = ref(false)
const isEditing = ref(false)
const current = ref(null)

const form = useForm({
  name: '',
  it_contact_name: '',
  it_contact_phone: '',
  it_contact_email: '',
})

const openCreate = () => {
  isEditing.value = false
  current.value = null
  form.reset()
  dialogVisible.value = true
}

const openEdit = (item) => {
  isEditing.value = true
  current.value = item
  form.name = item.name
  form.it_contact_name = item.it_contact_name || ''
  form.it_contact_phone = item.it_contact_phone || ''
  form.it_contact_email = item.it_contact_email || ''
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  form.reset()
  form.clearErrors()
}

const submit = () => {
  const opts = { onSuccess: closeDialog }
  if (isEditing.value) {
    form.put(route('admin.organizations.update', current.value.id), opts)
  } else {
    form.post(route('admin.organizations.store'), opts)
  }
}

// Delete
const showDeleteDialog = ref(false)
const toDelete = ref(null)

const confirmDelete = (item) => {
  toDelete.value = item
  showDeleteDialog.value = true
}

const handleDelete = () => {
  router.delete(route('admin.organizations.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}

// Action menu
const actionMenu = ref()
const selectedMenu = ref(null)
const toggleMenu = (e, item) => {
  selectedMenu.value = item
  actionMenu.value.toggle(e)
}
const menuItems = computed(() => {
  if (!selectedMenu.value) return []
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => openEdit(selectedMenu.value),
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => confirmDelete(selectedMenu.value),
    },
  ]
})
</script>

<template>
  <AdminLayout title="Organisasi">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Organisasi"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Organisasi"
        description="Kelola data organisasi yang terdaftar."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Organisasi</Button
          >
        </template>
      </AdminPageHeader>

      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <IconField class="w-full sm:w-72">
          <InputIcon><i class="pi pi-search" /></InputIcon>
          <InputText
            v-model="searchQuery"
            placeholder="Cari organisasi..."
            class="w-full"
            @keyup.enter="applyFilters"
          />
        </IconField>
      </AdminFilterBar>

      <AdminDataTable
        :value="organizations?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada organisasi.
          </div>
        </template>
        <Column field="name" header="Nama Organisasi" />
        <Column header="Kontak IT">
          <template #body="{ data }">
            <div v-if="data.it_contact_name" class="space-y-0.5 text-sm">
              <p class="font-medium text-slate-700">
                {{ data.it_contact_name }}
              </p>
              <p class="text-slate-500">{{ data.it_contact_phone }}</p>
              <p class="text-slate-500">{{ data.it_contact_email }}</p>
            </div>
            <span v-else class="text-slate-400">—</span>
          </template>
        </Column>
        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex justify-end">
              <Button
                variant="text"
                class="!p-0"
                @click="toggleMenu($event, data)"
              >
                <IconDotsVertical size="18" class="text-slate-400" />
              </Button>
              <Menu
                ref="actionMenu"
                :model="menuItems"
                :popup="true"
                class="!min-w-28"
              />
            </div>
          </template>
        </Column>
      </AdminDataTable>
    </div>

    <Dialog
      v-model:visible="dialogVisible"
      :modal="true"
      :closable="false"
      class="w-full max-w-lg"
    >
      <template #container="{ closeCallback }">
        <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
          <div class="border-b border-slate-200 p-5">
            <h3 class="text-lg font-semibold text-slate-900">
              {{ isEditing ? 'Edit Organisasi' : 'Tambah Organisasi' }}
            </h3>
          </div>
          <form @submit.prevent="submit" class="space-y-4 p-5">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Nama <span class="text-red-500">*</span></label
              >
              <InputText
                v-model="form.name"
                class="w-full"
                placeholder="Nama organisasi"
                required
              />
              <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                {{ form.errors.name }}
              </p>
            </div>
            <p class="text-sm font-medium text-slate-500">
              Kontak IT (opsional)
            </p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm text-slate-600"
                  >Nama PIC IT</label
                >
                <InputText
                  v-model="form.it_contact_name"
                  class="w-full"
                  placeholder="Nama"
                />
              </div>
              <div>
                <label class="mb-1 block text-sm text-slate-600">Telepon</label>
                <InputText
                  v-model="form.it_contact_phone"
                  class="w-full"
                  placeholder="08xx"
                />
              </div>
              <div class="sm:col-span-2">
                <label class="mb-1 block text-sm text-slate-600">Email</label>
                <InputText
                  v-model="form.it_contact_email"
                  class="w-full"
                  placeholder="email@domain.com"
                  type="email"
                />
              </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
              <Button
                severity="secondary"
                variant="outlined"
                :disabled="form.processing"
                @click="closeCallback"
                >Batal</Button
              >
              <Button type="submit" :loading="form.processing">{{
                isEditing ? 'Simpan Perubahan' : 'Tambah'
              }}</Button>
            </div>
          </form>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
