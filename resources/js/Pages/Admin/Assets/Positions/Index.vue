<script setup>
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'

const props = defineProps({
  positions: Object,
  departments: Array,
  organizations: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedDept = ref(props.filters?.department_id || '')
const paginatedData = computed(() => props.positions)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.positions.index', {
  search: searchQuery,
  department_id: selectedDept,
})

const deptOptions = computed(() =>
  props.departments.map((d) => ({
    label: `${d.name} — ${d.organization?.name ?? ''}`,
    value: d.id,
  })),
)

// Dialog
const dialogVisible = ref(false)
const isEditing = ref(false)
const current = ref(null)

const form = useForm({ name: '', department_id: '' })

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
  form.department_id = item.department_id
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
    form.put(route('admin.positions.update', current.value.id), opts)
  } else {
    form.post(route('admin.positions.store'), opts)
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
  router.delete(route('admin.positions.destroy', toDelete.value.id), {
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
  <AdminLayout title="Jabatan">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Jabatan"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Jabatan"
        description="Kelola data jabatan pegawai."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Jabatan</Button
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
              placeholder="Cari jabatan..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <Select
            v-model="selectedDept"
            :options="deptOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Bidang"
            class="w-full sm:w-64"
            show-clear
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="positions?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">Belum ada jabatan.</div>
        </template>
        <Column field="name" header="Nama Jabatan" />
        <Column header="Bidang">
          <template #body="{ data }">{{
            data.department?.name ?? '—'
          }}</template>
        </Column>
        <Column header="Organisasi">
          <template #body="{ data }">{{
            data.department?.organization?.name ?? '—'
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
              {{ isEditing ? 'Edit Jabatan' : 'Tambah Jabatan' }}
            </h3>
          </div>
          <form @submit.prevent="submit" class="space-y-4 p-5">
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Nama Jabatan <span class="text-red-500">*</span></label
              >
              <InputText
                v-model="form.name"
                class="w-full"
                placeholder="Nama jabatan"
                required
              />
              <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                {{ form.errors.name }}
              </p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Bidang <span class="text-red-500">*</span></label
              >
              <Select
                v-model="form.department_id"
                :options="deptOptions"
                option-label="label"
                option-value="value"
                placeholder="Pilih bidang"
                class="w-full"
                required
              />
              <p
                v-if="form.errors.department_id"
                class="mt-1 text-xs text-red-600"
              >
                {{ form.errors.department_id }}
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
