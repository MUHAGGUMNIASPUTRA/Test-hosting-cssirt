<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

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
const selectedCategory = ref(null)

const openCreate = () => {
  selectedCategory.value = null
  dialogVisible.value = true
}

const openEdit = (item) => {
  selectedCategory.value = item
  dialogVisible.value = true
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

    <TechStackCategoryFormDialog
      v-model:visible="dialogVisible"
      :category="selectedCategory"
    />

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
  </AdminLayout>
</template>
