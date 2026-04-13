<script setup>
// filepath: resources/js/Pages/Admin/Taxonomy/Index.vue

import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useConfirm } from 'primevue/useconfirm'
import { useResponsive } from '@/Composables/useResponsive'

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
    ? isCategory
      ? 'admin.categories.update'
      : 'admin.tags.update'
    : isCategory
      ? 'admin.categories.store'
      : 'admin.tags.store'

  const params = isEditing.value ? [currentItem.value.id] : []

  form.submit(isEditing.value ? 'put' : 'post', route(routeName, ...params), {
    onSuccess: () => {
      closeDialog()
    },
  })
}

const deleteItem = (type, item) => {
  const routeName =
    type === 'Kategori' ? 'admin.categories.destroy' : 'admin.tags.destroy'

  confirm.require({
    message: `Apakah Anda yakin ingin menghapus "${item.name}"?`,
    header: `Hapus ${type}`,
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
      useForm({}).delete(route(routeName, item.id))
    },
  })
}

// Stats computed
const stats = computed(() => {
  const totalCategories = props.categories?.length || 0
  const totalTags = props.tags?.length || 0
  const totalCategoryPosts =
    props.categories?.reduce((sum, cat) => sum + (cat.posts_count || 0), 0) || 0
  const totalTagPosts =
    props.tags?.reduce((sum, tag) => sum + (tag.posts_count || 0), 0) || 0

  return {
    totalCategories,
    totalTags,
    totalCategoryPosts,
    totalTagPosts,
  }
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

// Action menu handling
const catActionMenu = ref()
const catSelectedMenu = ref(null)
const toggleCatActionMenu = (event, item) => {
  catSelectedMenu.value = item
  catActionMenu.value.toggle(event)
}

const catActionMenuItems = computed(() => {
  if (!catSelectedMenu.value) return []
  const item = catSelectedMenu.value
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => {
        openDialog('Kategori', item)
      },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => {
        deleteItem('Kategori', item)
      },
    },
  ]
})

const tagActionMenu = ref()
const tagSelectedMenu = ref(null)
const toggleTagActionMenu = (event, item) => {
  tagSelectedMenu.value = item
  tagActionMenu.value.toggle(event)
}

const tagActionMenuItems = computed(() => {
  if (!tagSelectedMenu.value) return []
  const item = tagSelectedMenu.value
  return [
    {
      label: 'Edit',
      icon: 'pi pi-pen-to-square',
      command: () => {
        openDialog('Tag', item)
      },
    },
    {
      label: 'Hapus',
      icon: 'pi pi-trash',
      command: () => {
        deleteItem('Tag', item)
      },
    },
  ]
})
</script>

<template>
  <AdminLayout title="Kategori & Tag">
    <ConfirmDialog :style="{ width: isMobile ? '95vw' : undefined }" />

    <div class="space-y-4 lg:space-y-6">
      <!-- Header Section -->
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div
          class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
              Kategori & Tag
            </h2>
            <p class="text-slate-600">Kelola kategori dan tag untuk artikel</p>
          </div>
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
              <IconCategory
                class="text-blue-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Total Kategori
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.totalCategories }}
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
              <IconTags
                class="text-green-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Total Tag
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.totalTags }}
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
              <IconBookmark
                class="text-orange-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Artikel Kategori
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.totalCategoryPosts }}
              </p>
            </div>
          </div>
        </div>

        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div class="flex items-center">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-lg border border-purple-200 bg-purple-50 lg:h-12 lg:w-12"
            >
              <IconTag
                class="text-purple-600"
                :size="!isDesktop ? 18 : undefined"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-slate-600 lg:text-base">
                Artikel Tag
              </p>
              <p class="text-lg/5 font-bold text-slate-900 lg:text-xl">
                {{ stats.totalTagPosts }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
        <!-- Categories Section -->
        <div
          class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
        >
          <div class="border-b border-slate-200 p-4 lg:p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3 lg:gap-4">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
                >
                  <IconCategory
                    class="text-blue-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <h3 class="text-lg font-bold text-slate-900 lg:text-xl">
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
                  <IconCategoryPlus size="16" />
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
            >
              <Column field="name" header="Nama Kategori" class="min-w-48">
                <template #body="{ data }">
                  <div class="flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                      <h4 class="truncate font-medium">{{ data.name }}</h4>
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

              <Column
                field="created_at"
                header="Dibuat"
                class="hidden lg:table-cell"
              >
                <template #body="{ data }">
                  <span class="text-sm text-slate-500">{{
                    formatDate(data.created_at)
                  }}</span>
                </template>
              </Column>

              <Column
                header="Aksi"
                :pt="{ columnHeaderContent: 'justify-end' }"
              >
                <template #body="{ data }">
                  <div class="flex items-center justify-end">
                    <Button
                      variant="text"
                      class="!p-0"
                      @click="toggleCatActionMenu($event, data)"
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
                      ref="catActionMenu"
                      :model="catActionMenuItems"
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

          <!-- Empty State for Categories -->
          <div v-else class="py-12 text-center">
            <IconCategory size="30" class="mx-auto mb-4 text-slate-300" />
            <p class="text-lg font-medium text-slate-500">Belum Ada Kategori</p>
            <p class="mt-1 text-sm text-slate-400">
              Tambah kategori untuk mengorganisir artikel
            </p>
          </div>
        </div>

        <!-- Tags Section -->
        <div
          class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
        >
          <div class="border-b border-slate-200 p-4 lg:p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3 lg:gap-4">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-green-200 bg-green-50"
                >
                  <IconTags
                    class="text-green-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <h3 class="text-lg font-bold text-slate-900 lg:text-xl">
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
                  <IconTagPlus size="16" />
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
            >
              <Column field="name" header="Nama Tag" class="min-w-48">
                <template #body="{ data }">
                  <div class="flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                      <h4 class="truncate font-medium">{{ data.name }}</h4>
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

              <Column
                field="created_at"
                header="Dibuat"
                class="hidden lg:table-cell"
              >
                <template #body="{ data }">
                  <span class="text-sm text-slate-500">{{
                    formatDate(data.created_at)
                  }}</span>
                </template>
              </Column>

              <Column
                header="Aksi"
                :pt="{ columnHeaderContent: 'justify-end' }"
              >
                <template #body="{ data }">
                  <div class="flex items-center justify-end">
                    <Button
                      variant="text"
                      class="!p-0"
                      @click="toggleTagActionMenu($event, data)"
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
                      ref="tagActionMenu"
                      :model="tagActionMenuItems"
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

          <!-- Empty State for Tags -->
          <div v-else class="py-12 text-center">
            <IconTags size="30" class="mx-auto mb-4 text-slate-300" />
            <p class="text-lg font-medium text-slate-500">Belum Ada Tag</p>
            <p class="mt-1 text-sm text-slate-400">
              Tambah tag untuk mengelompokkan artikel
            </p>
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
        <div
          class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl"
        >
          <!-- Header -->
          <div class="border-b border-slate-200 p-4 lg:p-6">
            <div class="flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg lg:h-12 lg:w-12"
                :class="
                  currentType === 'Kategori'
                    ? 'border border-blue-200 bg-blue-50'
                    : 'border border-green-200 bg-green-50'
                "
              >
                <span
                  :class="
                    currentType === 'Kategori'
                      ? 'text-blue-600'
                      : 'text-green-600'
                  "
                >
                  <IconCategory
                    v-if="currentType === 'Kategori'"
                    :size="!isDesktop ? 18 : undefined"
                  />
                  <IconTags v-else :size="!isDesktop ? 18 : undefined" />
                </span>
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900 lg:text-lg">
                  {{ isEditing ? 'Edit' : 'Tambah' }} {{ currentType }}
                </h3>
                <p class="text-sm text-slate-500">
                  {{ isEditing ? 'Perbarui informasi' : 'Buat' }}
                  {{ currentType.toLowerCase() }} baru
                </p>
              </div>
            </div>
          </div>

          <!-- Content -->
          <form @submit.prevent="submitForm" class="p-4 lg:p-6">
            <div class="space-y-4">
              <div>
                <label for="name" class="mb-2 block font-medium text-slate-700">
                  Nama {{ currentType }} <span class="text-red-500">*</span>
                </label>
                <InputText
                  id="name"
                  v-model="form.name"
                  :placeholder="`Masukkan nama ${currentType.toLowerCase()}`"
                  class="w-full"
                  :class="{
                    'border-red-300 focus:border-red-500 focus:ring-red-500':
                      form.errors.name,
                  }"
                  autofocus
                  required
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.name }}
                </p>
              </div>
            </div>

            <!-- Actions -->
            <div
              class="mt-6 flex items-center justify-between space-x-3 border-t border-slate-200 pt-6"
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
                :severity="currentType === 'Kategori' ? 'primary' : 'success'"
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
                        ? 'Update'
                        : 'Simpan'
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
