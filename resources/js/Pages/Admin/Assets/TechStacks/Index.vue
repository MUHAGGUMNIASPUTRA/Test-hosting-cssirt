<script setup>
import { useAdminTable } from '@/Composables/useAdminTable'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  techStacks: Object,
  categories: Array,
  filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category_id || '')
const paginatedData = computed(() => props.techStacks)

const {
  serverSideConfig,
  applyFilters,
  onPage,
  clearFilters,
  hasActiveFilters,
} = useAdminTable(paginatedData, 'admin.tech-stacks.index', {
  search: searchQuery,
  category_id: selectedCategory,
})

const categoryOptions = computed(() =>
  props.categories.map((c) => ({ label: c.name, value: c.id })),
)

// Dialog
const dialogVisible = ref(false)
const selectedTechStack = ref(null)

const openCreate = () => {
  selectedTechStack.value = null
  dialogVisible.value = true
}

const openEdit = (item) => {
  selectedTechStack.value = item
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
  router.delete(route('admin.tech-stacks.destroy', toDelete.value.id), {
    onSuccess: () => {
      showDeleteDialog.value = false
      toDelete.value = null
    },
  })
}
</script>

<template>
  <AdminLayout title="Tech Stack">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Tech Stack"
      @confirm="handleDelete"
    >
      <template #item-info>{{ toDelete?.name }}</template>
    </DeleteConfirmDialog>

    <TechStackFormDialog
      v-model:visible="dialogVisible"
      :tech-stack="selectedTechStack"
      :categories="categories"
    />

    <div class="space-y-4">
      <AdminPageHeader
        title="Daftar Tech Stack"
        description="Kelola teknologi yang digunakan dalam pengembangan aset."
      >
        <template #action>
          <Button @click="openCreate"
            ><IconPlus size="16" class="mr-1" />Tambah Tech Stack</Button
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
              placeholder="Cari tech stack..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <Select
            v-model="selectedCategory"
            :options="categoryOptions"
            option-label="label"
            option-value="value"
            placeholder="Semua Kategori"
            class="w-full sm:w-52"
            show-clear
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <AdminDataTable
        :value="techStacks?.data ?? []"
        :server-config="serverSideConfig"
        @page="onPage"
      >
        <template #empty>
          <div class="py-10 text-center text-slate-400">
            Belum ada tech stack.
          </div>
        </template>
        <Column header="Logo" style="width: 4rem">
          <template #body="{ data }">
            <img
              v-if="data.logo_attachment?.url"
              :src="data.logo_attachment.url"
              :alt="data.name"
              class="h-8 w-8 rounded object-contain"
            />
            <div
              v-else
              class="flex h-8 w-8 items-center justify-center rounded bg-slate-100 text-slate-400"
            >
              <IconCode size="16" />
            </div>
          </template>
        </Column>
        <Column header="Nama" class="min-w-40">
          <template #body="{ data }">
            <p class="font-medium text-slate-800">{{ data.name }}</p>
            <p v-if="data.description" class="text-sm text-slate-500">
              {{
                data.description.length > 60
                  ? data.description.substring(0, 60) + '...'
                  : data.description
              }}
            </p>
          </template>
        </Column>
        <Column header="Kategori" class="hidden md:table-cell">
          <template #body="{ data }">
            <Tag
              v-if="data.category"
              :value="data.category.name"
              severity="secondary"
            />
            <span v-else class="text-slate-400">—</span>
          </template>
        </Column>
        <Column header="Aksi" :pt="{ columnHeaderContent: 'justify-end' }">
          <template #body="{ data }">
            <div class="flex justify-end gap-1">
              <Button
                size="small"
                severity="secondary"
                variant="outlined"
                @click="openEdit(data)"
                ><IconEdit size="15"
              /></Button>
              <Button
                size="small"
                severity="danger"
                variant="outlined"
                @click="confirmDelete(data)"
                ><IconTrash size="15"
              /></Button>
            </div>
          </template>
        </Column>
      </AdminDataTable>
    </div>
  </AdminLayout>
</template>
