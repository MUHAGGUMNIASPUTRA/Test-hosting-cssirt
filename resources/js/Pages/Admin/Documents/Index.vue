<script setup>
import { useResponsive } from '@/Composables/useResponsive';
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  documents: Object,
  documentAreas: Array,
  filters: Object,
});

const { isMobile, dtConfig } = useResponsive();

const NO_AREA_OPTION = { id: 0, name: 'Tanpa Area' };
const documentAreasOptions = computed(() => [NO_AREA_OPTION, ...(props.documentAreas ?? [])]);

const searchQuery = ref(props.filters?.search || '');
const selectedAreas = ref(
  props.filters?.areas
    ? documentAreasOptions.value.filter((a) =>
      [].concat(props.filters.areas).map(Number).includes(Number(a.id)),
    )
    : [],
);

const paginatedData = computed(() => props.documents);

const buildUrl = (page = 1) => {
  const params = new URLSearchParams();
  if (searchQuery.value) params.set('search', searchQuery.value);
  selectedAreas.value.forEach((area) => params.append('areas[]', area.id));
  if (page > 1) params.set('page', page);
  const qs = params.toString();
  return route('admin.documents.index') + (qs ? '?' + qs : '');
};

const navigate = (page) => {
  router.get(buildUrl(page), {}, { preserveState: true, preserveScroll: true, replace: true });
};

const applyFilters = () => navigate(1);

const onPage = (event) => {
  const page = Math.floor(event.first / event.rows) + 1;
  navigate(page);
};

const clearFilters = () => {
  searchQuery.value = '';
  selectedAreas.value = [];
  navigate(1);
};

const hasActiveFilters = computed(() => !!searchQuery.value || selectedAreas.value.length > 0);

const serverSideConfig = computed(() => ({
  ...dtConfig(),
  lazy: true,
  totalRecords: paginatedData.value?.total,
  first: (paginatedData.value?.current_page - 1) * paginatedData.value?.per_page,
  rows: paginatedData.value?.per_page,
}));

// Delete
const deleteVisible = ref(false);
const docToDelete = ref(null);

const confirmDelete = (doc) => {
  docToDelete.value = doc;
  deleteVisible.value = true;
};

const handleDelete = () => {
  router.delete(route('admin.documents.destroy', docToDelete.value.id), {
    onSuccess: () => {
      deleteVisible.value = false;
      docToDelete.value = null;
    },
  });
};

// Toggle visibility
const toggleVisibility = (doc) => {
  router.patch(route('admin.documents.toggle-visibility', doc.id), {}, {
    preserveScroll: true,
  });
};

const isUrl = (path) => path && (path.startsWith('http://') || path.startsWith('https://'));

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};
</script>

<template>
  <AdminLayout title="Panduan & Dokumen">
    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Panduan & Dokumen</h2>
            <p class="text-slate-600">Kelola dokumen panduan dan file referensi</p>
          </div>
          <Link :href="route('admin.documents.create')"
            class="bg-blue-600 hover:bg-blue-700 text-white inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition">
            <IconPlus size="16" />
            Tambah Dokumen
          </Link>
        </div>
      </div>

      <!-- Filter -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-900">Filter & Pencarian</h3>
          <button v-if="hasActiveFilters" @click="clearFilters"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Reset
          </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <IconField>
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText v-model="searchQuery" placeholder="Cari judul, deskripsi, versi..." class="w-full"
              @keyup.enter="applyFilters" />
          </IconField>
          <MultiSelect v-model="selectedAreas" :options="documentAreasOptions" optionLabel="name" dataKey="id"
            placeholder="Filter Area Dokumen" class="w-full" :maxSelectedLabels="2"
            selectedItemsLabel="{0} area dipilih" @change="applyFilters" />
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <AdminDataTable :value="documents.data" :serverConfig="serverSideConfig" @page="onPage">
          <Column field="title" header="Judul">
            <template #body="{ data }">
              <div class="font-medium text-slate-900">{{ data.title }}</div>
              <div v-if="data.description" class="text-sm text-slate-500 truncate max-w-xs">{{ data.description }}</div>
            </template>
          </Column>

          <Column header="Area Dokumen" v-if="!isMobile">
            <template #body="{ data }">
              <span v-if="data.document_area" class="text-sm text-slate-700">{{ data.document_area.name }}</span>
              <span v-else class="text-slate-400">-</span>
            </template>
          </Column>

          <Column header="File" v-if="!isMobile">
            <template #body="{ data }">
              <div class="space-y-1">
                <div class="flex items-center gap-1.5">
                  <span class="text-xs text-slate-500 w-16 shrink-0">Dokumen Draft</span>
                  <a v-if="data.file_path && isUrl(data.file_path)" :href="data.file_path" target="_blank"
                    rel="noopener">
                    <Tag value="Link" severity="info" class="cursor-pointer hover:opacity-80" />
                  </a>
                  <a v-else-if="data.file_path" :href="`/storage/${data.file_path}`" target="_blank" rel="noopener">
                    <Tag value="PDF" severity="success" class="cursor-pointer hover:opacity-80" />
                  </a>
                  <span v-else class="text-slate-400 text-sm">-</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="text-xs text-slate-500 w-16 shrink-0">Dokumen Sah</span>
                  <a v-if="data.official_file_path && isUrl(data.official_file_path)" :href="data.official_file_path"
                    target="_blank" rel="noopener">
                    <Tag value="Link" severity="info" class="cursor-pointer hover:opacity-80" />
                  </a>
                  <a v-else-if="data.official_file_path" :href="`/storage/${data.official_file_path}`" target="_blank"
                    rel="noopener">
                    <Tag value="PDF" severity="success" class="cursor-pointer hover:opacity-80" />
                  </a>
                  <span v-else class="text-slate-400 text-sm">-</span>
                </div>
              </div>
            </template>
          </Column>

          <Column header="Visibilitas" v-if="!isMobile" style="width: 100px">
            <template #body="{ data }">
              <Tag :value="data.is_public ? 'Publik' : 'Privat'" :severity="data.is_public ? 'success' : 'secondary'" />
            </template>
          </Column>

          <Column header="Terbit" v-if="!isMobile">
            <template #body="{ data }">
              <span class="text-sm text-slate-600">{{ formatDate(data.published_at) }}</span>
            </template>
          </Column>

          <Column header="Aksi" style="width: 160px">
            <template #body="{ data }">
              <div class="flex items-center gap-1">
                <!-- Toggle Visibility -->
                <Button :icon="data.is_public ? 'pi pi-eye' : 'pi pi-eye-slash'" size="small"
                  :severity="data.is_public ? 'success' : 'secondary'" text rounded
                  :v-tooltip="data.is_public ? 'Sembunyikan dari publik' : 'Publikasikan'"
                  @click="toggleVisibility(data)" />

                <Link :href="route('admin.documents.edit', data.id)">
                  <Button icon="pi pi-pencil" size="small" severity="secondary" text rounded v-tooltip="'Edit'" />
                </Link>
                <Button icon="pi pi-trash" size="small" severity="danger" text rounded v-tooltip="'Hapus'"
                  @click="confirmDelete(data)" />
              </div>
            </template>
          </Column>

          <template #empty>
            <div class="text-center py-8 text-slate-500">
              <IconFileDescription size="40" class="mx-auto mb-3 text-slate-300" />
              <p>Belum ada panduan atau dokumen.</p>
            </div>
          </template>
        </AdminDataTable>
      </div>
    </div>

    <!-- Delete Dialog -->
    <DeleteConfirmDialog v-model:visible="deleteVisible" entityLabel="panduan" :deleteLabel="docToDelete?.title"
      @confirm="handleDelete">
      <template #item-info>
        <div v-if="docToDelete" class="space-y-1 text-sm">
          <p><span class="font-medium">Judul:</span> {{ docToDelete.title }}</p>
          <p v-if="docToDelete.version"><span class="font-medium">Versi:</span> {{ docToDelete.version }}</p>
        </div>
      </template>
    </DeleteConfirmDialog>
  </AdminLayout>
</template>
