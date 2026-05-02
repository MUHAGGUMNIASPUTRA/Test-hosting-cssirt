<script setup>
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'

const props = defineProps({
  departments: Object,
  organizations: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedOrg = ref(props.filters?.organization_id || '')
const paginatedData = computed(() => props.departments)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.departments.index', {
  search: searchQuery,
  organization_id: selectedOrg,
})

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)

// Dialog
const dialogVisible = ref(false)
const isEditing = ref(false)
const current = ref(null)

const form = useForm({ name: '', organization_id: '' })

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
  form.organization_id = item.organization_id
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
    form.put(route('admin.departments.update', current.value.id), opts)
  } else {
    form.post(route('admin.departments.store'), opts)
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
  router.delete(route('admin.departments.destroy', toDelete.value.id), {
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
  <AdminLayout title="Bidang">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Bidang"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Bidang"
        description="Kelola data bidang berdasarkan organisasi."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Bidang</Button
          >
        </template>
      </AdminPageHeader>

      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <div class="flex flex-wrap gap-3">
          <IconField class="w-full sm:w-64">
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText
              v-model="searchQuery"
              placeholder="Cari bidang..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <Select
            v-model="selectedOrg"
            :options="orgOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Organisasi"
            class="w-full sm:w-56"
            show-clear
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="departments?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">Belum ada bidang.</div>
        </template>
        <Column field="name" header="Nama Bidang" />
        <Column header="Organisasi">
          <template #body="{ data }">{{
            data.organization?.name ?? '—'
          }}</template>
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
      class="w-full max-w-md"
    >
      <template #container="{ closeCallback }">
        <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
          <div class="border-b border-slate-200 p-5">
            <h3 class="text-lg font-semibold text-slate-900">
              {{ isEditing ? 'Edit Bidang' : 'Tambah Bidang' }}
            </h3>
          </div>
          <form @submit.prevent="submit" class="space-y-4 p-5">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Nama Bidang <span class="text-red-500">*</span></label
              >
              <InputText
                v-model="form.name"
                class="w-full"
                placeholder="Nama bidang"
                required
              />
              <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                {{ form.errors.name }}
              </p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Organisasi <span class="text-red-500">*</span></label
              >
              <Select
                v-model="form.organization_id"
                :options="orgOptions"
                option-label="label"
                option-value="value"
                placeholder="Pilih organisasi"
                class="w-full"
                required
              />
              <p
                v-if="form.errors.organization_id"
                class="mt-1 text-xs text-red-600"
              >
                {{ form.errors.organization_id }}
              </p>
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
