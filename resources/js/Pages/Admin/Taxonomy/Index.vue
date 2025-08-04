<script setup>
// filepath: resources/js/Pages/Admin/Taxonomy/Index.vue

import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useConfirm } from "primevue/useconfirm"
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  categories: Array,
  tags: Array,
})

const { isMobile, isDesktop } = useResponsive()
const confirm = useConfirm()

// State for Dialog
const dialogVisible = ref(false)
const isEditing = ref(false)
const currentItem = ref(null)
const currentType = ref('')

const form = useForm({
  name: '',
})

const openDialog = (type, item = null) => {
  currentType.value = type
  if (item) {
    isEditing.value = true
    currentItem.value = item
    form.name = item.name
  } else {
    isEditing.value = false
    form.reset()
  }
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  form.reset()
}

const submitForm = () => {
  const isCategory = currentType.value === 'Kategori'
  const routeName = isEditing.value
    ? (isCategory ? 'admin.categories.update' : 'admin.tags.update')
    : (isCategory ? 'admin.categories.store' : 'admin.tags.store')

  const params = isEditing.value ? [currentItem.value.id] : []

  form.submit(isEditing.value ? 'put' : 'post', route(routeName, ...params), {
    onSuccess: () => {
      closeDialog()
    },
  })
}

const deleteItem = (type, item) => {
  const routeName = type === 'Kategori' ? 'admin.categories.destroy' : 'admin.tags.destroy'

  confirm.require({
    message: `Apakah Anda yakin ingin menghapus "${item.name}"?`,
    header: `Hapus ${type}`,
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
      useForm({}).delete(route(routeName, item.id))
    }
  })
}

// Stats computed
const stats = computed(() => {
  const totalCategories = props.categories?.length || 0
  const totalTags = props.tags?.length || 0
  const totalCategoryPosts = props.categories?.reduce((sum, cat) => sum + (cat.posts_count || 0), 0) || 0
  const totalTagPosts = props.tags?.reduce((sum, tag) => sum + (tag.posts_count || 0), 0) || 0

  return {
    totalCategories,
    totalTags,
    totalCategoryPosts,
    totalTagPosts
  }
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<template>
  <AdminLayout title="Kategori & Tag">
    <ConfirmDialog :style="{ width: isMobile ? '95vw' : undefined }" />

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <h2 class="text-xl lg:text-2xl font-bold text-slate-900">Kategori & Tag</h2>
            <p class="text-slate-600">Kelola kategori dan tag untuk artikel</p>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <IconCategory class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Total Kategori</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.totalCategories }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <IconTags class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Total Tag</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.totalTags }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-orange-50 border border-orange-200 rounded-lg flex items-center justify-center">
              <IconBookmark class="text-orange-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Artikel Kategori</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.totalCategoryPosts }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl p-4 lg:p-6 shadow-sm border border-slate-200">
          <div class="flex items-center">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-50 border border-purple-200 rounded-lg flex items-center justify-center">
              <IconTag class="text-purple-600" :size="!isDesktop ? 18 : undefined"/>
            </div>
            <div class="ml-3">
              <p class="text-sm lg:text-base font-medium text-slate-600">Artikel Tag</p>
              <p class="text-lg/5 lg:text-xl font-bold text-slate-900">{{ stats.totalTagPosts }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Categories Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="p-4 lg:p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3 lg:gap-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                  <IconCategory class="text-blue-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-slate-900">
                  Kategori ({{ stats.totalCategories }})
                </h3>
              </div>
              <Button
                @click="openDialog('Kategori')"
                severity="primary"
                :size="!isDesktop ? 'small' : undefined"
                class="px-4 py-2"
              >
                <template #default>
                  <IconPlus size="16" />
                  Tambah Kategori
                </template>
              </Button>
            </div>
          </div>

          <div v-if="props.categories && props.categories.length > 0">
            <DataTable
              :value="props.categories"
              :paginator="props.categories.length > 10"
              :rows="10"
              class="p-datatable-sm"
            >
              <Column field="name" header="Nama Kategori" class="min-w-48">
                <template #body="{ data }">
                  <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                      <h4 class="font-medium truncate">{{ data.name }}</h4>
                    </div>
                  </div>
                </template>
              </Column>

              <Column field="posts_count" header="Artikel">
                <template #body="{ data }">
                  <span class="text-sm text-slate-500">
                    {{ data.posts_count || 0 }}
                  </span>
                </template>
              </Column>

              <Column field="created_at" header="Dibuat" class="hidden lg:table-cell">
                <template #body="{ data }">
                  <span class="text-sm text-slate-500">{{ formatDate(data.created_at) }}</span>
                </template>
              </Column>

              <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
                <template #body="{ data }">
                  <div class="flex items-center justify-end">
                    <button
                      @click="openDialog('Kategori', data)"
                      class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      title="Edit"
                    >
                      <IconEdit size="14" />
                    </button>
                    <button
                      @click="deleteItem('Kategori', data)"
                      class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="Hapus"
                    >
                      <IconTrash size="14" />
                    </button>
                  </div>
                </template>
              </Column>

            </DataTable>
          </div>

          <!-- Empty State for Categories -->
          <div v-else class="text-center py-12">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
              <IconCategory class="text-blue-300"/>
            </div>
            <h3 class="font-medium text-slate-900 mb-2">Belum Ada Kategori</h3>
            <p class="text-slate-500 mb-6">Tambah kategori untuk mengorganisir artikel</p>
          </div>
        </div>

        <!-- Tags Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="p-4 lg:p-6 border-b border-slate-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3 lg:gap-4">
                <div class="w-10 h-10 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                  <IconTags class="text-green-600" :size="!isDesktop ? 18 : undefined"/>
                </div>
                <h3 class="text-lg lg:text-xl font-bold text-slate-900">
                  Tag ({{ stats.totalTags }})
                </h3>
              </div>
              <Button
                @click="openDialog('Tag')"
                severity="success"
                :size="!isDesktop ? 'small' : undefined"
                class="px-4 py-2"
              >
                <template #default>
                  <IconPlus size="16" />
                  Tambah Tag
                </template>
              </Button>
            </div>
          </div>

          <div v-if="props.tags && props.tags.length > 0">
            <DataTable
              :value="props.tags"
              :paginator="props.tags.length > 10"
              :rows="10"
              class="p-datatable-sm"
            >
              <Column field="name" header="Nama Tag" class="min-w-48">
                <template #body="{ data }">
                  <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                      <h4 class="font-medium truncate">{{ data.name }}</h4>
                    </div>
                  </div>
                </template>
              </Column>

              <Column field="posts_count" header="Artikel">
                <template #body="{ data }">
                  <span class="text-sm text-slate-500">
                    {{ data.posts_count || 0 }}
                  </span>
                </template>
              </Column>

              <Column field="created_at" header="Dibuat" class="hidden lg:table-cell">
                <template #body="{ data }">
                  <span class="text-sm text-slate-500">{{ formatDate(data.created_at) }}</span>
                </template>
              </Column>

              <Column header="Aksi" :pt="{columnHeaderContent: 'justify-end' }">
                <template #body="{ data }">
                  <div class="flex items-center justify-end">
                    <button
                      @click="openDialog('Tag', data)"
                      class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      title="Edit"
                    >
                      <IconEdit size="14" />
                    </button>
                    <button
                      @click="deleteItem('Tag', data)"
                      class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="Hapus"
                    >
                      <IconTrash size="14" />
                    </button>
                  </div>
                </template>
              </Column>

            </DataTable>
          </div>

          <!-- Empty State for Tags -->
          <div v-else class="text-center py-12">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
              <IconTags class="text-green-300"/>
            </div>
            <h3 class="font-medium text-slate-900 mb-2">Belum Ada Tag</h3>
            <p class="text-slate-500 mb-6">Tambah tag untuk mengelompokkan artikel</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Dialog -->
    <Dialog
      v-model:visible="dialogVisible"
      :modal="true"
      :closable="false"
      class="w-full max-w-md"
    >
      <template #container="{ closeCallback }">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
          <!-- Header -->
          <div class="p-4 lg:p-6 border-b border-slate-200">
            <div class="flex items-center">
              <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg flex items-center justify-center"
                   :class="currentType === 'Kategori' ? 'bg-blue-50 border border-blue-200' : 'bg-green-50 border border-green-200'">
                <span :class="currentType === 'Kategori' ? 'text-blue-600' : 'text-green-600'">
                  <IconCategory v-if="currentType === 'Kategori'" :size="!isDesktop ? 18 : undefined" />
                  <IconTags v-else :size="!isDesktop ? 18 : undefined" />
                </span>
              </div>
              <div class="ml-3">
                <h3 class="lg:text-lg font-semibold text-slate-900">
                  {{ isEditing ? 'Edit' : 'Tambah' }} {{ currentType }}
                </h3>
                <p class="text-sm text-slate-500">
                  {{ isEditing ? 'Perbarui informasi' : 'Buat' }} {{ currentType.toLowerCase() }} baru
                </p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 lg:p-6">
            <div class="space-y-4">
              <div>
                <label for="name" class="block font-medium text-slate-700 mb-2">
                  Nama {{ currentType }} <span class="text-red-500">*</span>
                </label>
                <InputText
                  id="name"
                  v-model="form.name"
                  :placeholder="`Masukkan nama ${currentType.toLowerCase()}`"
                  class="w-full"
                  :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.name }"
                  autofocus
                  required
                />
                <p v-if="form.errors.name" class="mt-1 text-red-600 text-sm">
                  {{ form.errors.name }}
                </p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between space-x-3 mt-6 pt-6 border-t border-slate-200">
              <Button
                @click="closeCallback"
                icon="pi pi-times"
                label="Batal"
                severity="secondary"
                variant="outlined"
                :disabled="form.processing"
              />
              <Button
                type="submit"
                icon="pi pi-save"
                :label="form.processing ? 'Menyimpan...' : (isEditing ? 'Update' : 'Simpan')"
                :severity="currentType === 'Kategori' ? 'primary' : 'success'"
                :loading="form.processing"
              />
            </div>
          </form>
        </div>
      </template>
    </Dialog>
  </AdminLayout>
</template>
