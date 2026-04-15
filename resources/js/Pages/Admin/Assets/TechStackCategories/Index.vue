<script setup>
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useAdminTable } from '@/Composables/useAdminTable'

const props = defineProps({
  categories: Object,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const paginatedData = computed(() => props.categories)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.tech-stack-categories.index', {
  search: searchQuery,
})

// Dialog
const dialogVisible = ref(false)
const isEditing = ref(false)
const current = ref(null)

const form = useForm({ name: '', description: '' })

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
  form.description = item.description ?? ''
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
    form.put(
      route('admin.tech-stack-categories.update', current.value.id),
      opts,
    )
  } else {
    form.post(route('admin.tech-stack-categories.store'), opts)
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
  router.delete(
    route('admin.tech-stack-categories.destroy', toDelete.value.id),
    {
      onSuccess: () => {
        showDeleteDialog.value = false
        toDelete.value = null
      },
    },
  )
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
  <AdminLayout title="Kategori Tech Stack">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Kategori"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminPageHeader
        title="Kategori Tech Stack"
        description="Kelola kategori untuk tech stack."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Kategori</Button
          >
        </template>
      </AdminPageHeader>

      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <IconField class="w-full sm:w-64">
          <InputIcon><i class="pi pi-search" /></InputIcon>
          <InputText
            v-model="searchQuery"
            placeholder="Cari kategori..."
            class="w-full"
            @keyup.enter="applyFilters"
          />
        </IconField>
      </AdminFilterBar>

      <AdminDataTable
        :value="categories?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada kategori.
          </div>
        </template>
        <Column field="name" header="Nama Kategori" />
        <Column
          field="description"
          header="Deskripsi"
          class="hidden md:table-cell"
        >
          <template #body="{ data }">
            <span class="text-sm text-slate-500">{{
              data.description ?? '—'
            }}</span>
          </template>
        </Column>
        <Column
          field="tech_stacks_count"
          header="Jumlah Stack"
          class="hidden lg:table-cell"
        >
          <template #body="{ data }">
            <Tag
              :value="`${data.tech_stacks_count} stack`"
              severity="secondary"
            />
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
      class="w-full max-w-md"
    >
      <template #container="{ closeCallback }">
        <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
          <div class="border-b border-slate-200 p-5">
            <h3 class="text-lg font-semibold text-slate-900">
              {{ isEditing ? 'Edit Kategori' : 'Tambah Kategori' }}
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
                placeholder="Nama kategori"
                required
              />
              <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                {{ form.errors.name }}
              </p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Deskripsi</label
              >
              <Textarea
                v-model="form.description"
                class="w-full"
                rows="2"
                placeholder="Deskripsi singkat (opsional)"
              />
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
                isEditing ? 'Simpan' : 'Tambah'
              }}</Button>
            </div>
          </form>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
