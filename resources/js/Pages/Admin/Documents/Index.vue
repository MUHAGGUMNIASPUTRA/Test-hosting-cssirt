<script setup>
import { useResponsive } from '@/Composables/useResponsive'
import { formatDate } from '@/utils/date'
import { getSeverity } from '@/utils/status'
import { Link, router } from '@inertiajs/vue3'
import { IconFileTypePdf } from '@tabler/icons-vue'
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  documentAreas: Array,
})

const { isMobile, dtConfig } = useResponsive()

const NO_AREA_OPTION = { id: 0, name: 'Tanpa Area' }
const documentAreasOptions = computed(() => [
  NO_AREA_OPTION,
  ...(props.documentAreas ?? []),
])

const stageOptions = [
  'Perlu Dibuat',
  'Telah Dibuat',
  'Perlu Review',
  'Telah Direview',
  'Perlu TTD',
  'Final',
]

const visibilityOptions = [
  { label: 'Publik', value: '1' },
  { label: 'Privat', value: '0' },
]

// Inisialisasi dengan struktur default agar template tidak error sebelum data tiba
const documents = ref({ data: [], current_page: 1, per_page: 10, total: 0 })
const loading = ref(false)

const searchQuery = ref('')
const selectedAreas = ref([])
const selectedStage = ref('')
const selectedVisibility = ref('')

const paginatedData = computed(() => documents.value)

const fetchDocuments = async (page = 1) => {
  loading.value = true
  try {
    const perPage = paginatedData.value?.per_page ?? 10
    const params = new URLSearchParams()
    if (searchQuery.value) params.set('search', searchQuery.value)
    selectedAreas.value.forEach((area) => params.append('areas[]', area.id))
    if (selectedStage.value) params.set('stage', selectedStage.value)
    if (selectedVisibility.value !== '')
      params.set('is_public', selectedVisibility.value)
    params.set('per_page', perPage)
    if (page > 1) params.set('page', page)
    const qs = params.toString()
    const { data } = await axios.get(
      route('api.admin.documents.index') + (qs ? '?' + qs : ''),
    )
    if (data?.data) {
      documents.value = data.data
    }
  } catch {
    // Biarkan data tetap di state terakhir yang valid
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchDocuments())

const applyFilters = () => fetchDocuments(1)

const onPage = (event) => {
  const page = Math.floor(event.first / event.rows) + 1
  fetchDocuments(page)
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedAreas.value = []
  selectedStage.value = ''
  selectedVisibility.value = ''
  fetchDocuments(1)
}

const hasActiveFilters = computed(
  () =>
    !!searchQuery.value ||
    selectedAreas.value.length > 0 ||
    !!selectedStage.value ||
    selectedVisibility.value !== '',
)

const serverSideConfig = computed(() => ({
  ...dtConfig(),
  lazy: true,
  totalRecords: paginatedData.value?.total ?? 0,
  first: paginatedData.value?.current_page
    ? (paginatedData.value.current_page - 1) *
      (paginatedData.value.per_page ?? 10)
    : 0,
  rows: paginatedData.value?.per_page ?? 10,
}))

// Delete
const deleteVisible = ref(false)
const docToDelete = ref(null)

const confirmDelete = (doc) => {
  docToDelete.value = doc
  deleteVisible.value = true
}

const handleDelete = () => {
  router.delete(route('admin.documents.destroy', docToDelete.value.id), {
    onSuccess: () => {
      deleteVisible.value = false
      docToDelete.value = null
      fetchDocuments(paginatedData.value?.current_page ?? 1)
    },
  })
}

// Toggle visibility
const toggleVisibility = (doc) => {
  router.patch(
    route('admin.documents.toggle-visibility', doc.id),
    {},
    {
      preserveScroll: true,
      onSuccess: () => fetchDocuments(paginatedData.value?.current_page ?? 1),
    },
  )
}

const isUrl = (path) =>
  path && (path.startsWith('http://') || path.startsWith('https://'))

const stageSeverity = (stage) => getSeverity('document-stage', stage)
</script>

<template>
  <AdminLayout title="Panduan & Dokumen">
    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <AdminPageHeader
        title="Panduan & Dokumen"
        description="Kelola dokumen panduan dan file referensi"
      >
        <template #action>
          <Link
            :href="route('admin.documents.create')"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
          >
            <IconPlus size="16" />
            Tambah Dokumen
          </Link>
        </template>
      </AdminPageHeader>

      <!-- Filter -->
      <AdminFilterBar
        :has-active-filters="hasActiveFilters"
        @clear="clearFilters"
      >
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
          <IconField>
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText
              v-model="searchQuery"
              placeholder="Cari judul, deskripsi, versi..."
              class="w-full"
              @keyup.enter="applyFilters"
            />
          </IconField>
          <MultiSelect
            v-model="selectedAreas"
            :options="documentAreasOptions"
            optionLabel="name"
            dataKey="id"
            placeholder="Filter Area Dokumen"
            class="w-full"
            :maxSelectedLabels="2"
            selectedItemsLabel="{0} area dipilih"
            @change="applyFilters"
          />
          <Select
            v-model="selectedStage"
            :options="stageOptions"
            placeholder="Filter Stage"
            class="w-full"
            showClear
            @change="applyFilters"
          />
          <Select
            v-model="selectedVisibility"
            :options="visibilityOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="Filter Visibilitas"
            class="w-full"
            showClear
            @change="applyFilters"
          />
        </div>
      </AdminFilterBar>

      <!-- Table -->
      <AdminDataTable
        :value="documents?.data ?? []"
        :serverConfig="serverSideConfig"
        @page="onPage"
      >
        <Column field="title" header="Judul">
          <template #body="{ data }">
            <div class="font-medium text-slate-900">{{ data.title }}</div>
            <div
              v-if="data.description"
              class="max-w-xs truncate text-sm text-slate-500"
            >
              {{ data.description }}
            </div>
          </template>
        </Column>

        <Column header="Area Dokumen" v-if="!isMobile">
          <template #body="{ data }">
            <span v-if="data.document_area" class="text-sm text-slate-700">
              {{ data.document_area.name }}
            </span>
            <span v-else class="text-slate-400">-</span>
          </template>
        </Column>

        <Column header="File Draft" v-if="!isMobile" style="width: 90px">
          <template #body="{ data }">
            <a
              v-if="data.draft_file_path"
              :href="
                isUrl(data.draft_file_path)
                  ? data.draft_file_path
                  : `/storage/${data.draft_file_path}`
              "
              target="_blank"
              rel="noopener"
              class="inline-flex items-center text-blue-600 hover:text-blue-800"
              v-tooltip.top="'Buka File Draft'"
            >
              <IconExternalLink size="18" />
            </a>
            <span v-else class="text-slate-400">-</span>
          </template>
        </Column>

        <Column header="File Sah" v-if="!isMobile" style="width: 130px">
          <template #body="{ data }">
            <div v-if="data.official_file_path" class="space-y-1">
              <a
                :href="
                  isUrl(data.official_file_path)
                    ? data.official_file_path
                    : `/storage/${data.official_file_path}`
                "
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800"
              >
                <IconExternalLink
                  v-if="isUrl(data.official_file_path)"
                  size="16"
                />
                <IconFileTypePdf v-else size="16" />
                <Tag
                  :value="isUrl(data.official_file_path) ? 'Link' : 'PDF'"
                  :severity="
                    isUrl(data.official_file_path) ? 'info' : 'success'
                  "
                  class="text-xs"
                />
              </a>
            </div>
            <span v-else class="text-slate-400">-</span>
            <div v-if="data.reference_number" class="text-xs text-slate-500">
              {{ data.reference_number }}
            </div>
            <span v-else class="text-slate-400">-</span>
          </template>
        </Column>

        <Column header="Stage" v-if="!isMobile" style="width: 120px">
          <template #body="{ data }">
            <Tag
              v-if="data.stage"
              :value="data.stage"
              :severity="stageSeverity(data.stage)"
              class="text-xs"
            />
            <span v-else class="text-slate-400">-</span>
          </template>
        </Column>

        <Column header="Visibilitas" v-if="!isMobile" style="width: 100px">
          <template #body="{ data }">
            <Tag
              :value="data.is_public ? 'Publik' : 'Privat'"
              :severity="data.is_public ? 'success' : 'secondary'"
            />
          </template>
        </Column>

        <Column header="Terbit" v-if="!isMobile">
          <template #body="{ data }">
            <span class="text-sm text-slate-600">{{
              formatDate(data.published_at)
            }}</span>
          </template>
        </Column>

        <Column header="Aksi" style="width: 120px">
          <template #body="{ data }">
            <div class="flex items-center gap-1">
              <!-- Toggle Visibility -->
              <Button
                :icon="data.is_public ? 'pi pi-eye' : 'pi pi-eye-slash'"
                size="small"
                :severity="data.is_public ? 'success' : 'secondary'"
                text
                rounded
                :v-tooltip="
                  data.is_public ? 'Sembunyikan dari publik' : 'Publikasikan'
                "
                @click="toggleVisibility(data)"
              />

              <Link :href="route('admin.documents.edit', data.id)">
                <Button
                  icon="pi pi-pencil"
                  size="small"
                  severity="secondary"
                  text
                  rounded
                  v-tooltip="'Edit'"
                />
              </Link>
              <Button
                icon="pi pi-trash"
                size="small"
                severity="danger"
                text
                rounded
                v-tooltip="'Hapus'"
                @click="confirmDelete(data)"
              />
            </div>
          </template>
        </Column>

        <template #empty>
          <div class="py-8 text-center text-slate-500">
            <template v-if="loading">
              <IconLoader2
                size="40"
                class="mx-auto mb-3 animate-spin text-slate-300"
              />
              <p>Memuat data...</p>
            </template>
            <template v-else>
              <IconFileDescription
                size="40"
                class="mx-auto mb-3 text-slate-300"
              />
              <p>Belum ada panduan atau dokumen.</p>
            </template>
          </div>
        </template>
      </AdminDataTable>
    </div>

    <!-- Delete Dialog -->
    <DeleteConfirmDialog
      v-model:visible="deleteVisible"
      entityLabel="panduan"
      :deleteLabel="docToDelete?.title"
      @confirm="handleDelete"
    >
      <template #item-info>
        <div v-if="docToDelete" class="space-y-1 text-sm">
          <p><span class="font-medium">Judul:</span> {{ docToDelete.title }}</p>
          <p v-if="docToDelete.version">
            <span class="font-medium">Versi:</span> {{ docToDelete.version }}
          </p>
        </div>
      </template>
    </DeleteConfirmDialog>
  </AdminLayout>
</template>
