<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useAdminTable } from '@/Composables/useAdminTable';
import { useResponsive } from '@/Composables/useResponsive';

const props = defineProps({
  documents: Object,
  filters: Object,
});

const { isMobile } = useResponsive();

const searchQuery = ref(props.filters?.search || '');
const paginatedData = computed(() => props.documents);
const { serverSideConfig, applyFilters, onPage, clearFilters, hasActiveFilters } =
  useAdminTable(paginatedData, 'admin.documents.index', { search: searchQuery });

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
          <Link
            :href="route('admin.documents.create')"
            class="bg-blue-600 hover:bg-blue-700 text-white inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
          >
            <IconPlus size="16" />
            Tambah Panduan
          </Link>
        </div>
      </div>

      <!-- Search -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-900">Pencarian</h3>
          <button v-if="hasActiveFilters" @click="clearFilters" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Reset
          </button>
        </div>
        <IconField class="w-full">
          <InputIcon><i class="pi pi-search" /></InputIcon>
          <InputText
            v-model="searchQuery"
            placeholder="Cari berdasarkan judul, deskripsi, atau versi..."
            class="w-full"
            @keyup.enter="applyFilters"
          />
        </IconField>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <AdminDataTable
          :value="documents.data"
          :serverConfig="serverSideConfig"
          @page="onPage"
        >
          <Column field="title" header="Judul">
            <template #body="{ data }">
              <div class="font-medium text-slate-900">{{ data.title }}</div>
              <div v-if="data.description" class="text-sm text-slate-500 truncate max-w-xs">{{ data.description }}</div>
            </template>
          </Column>
          <Column header="Versi" v-if="!isMobile" style="width: 80px">
            <template #body="{ data }">
              <Tag v-if="data.version" :value="data.version" severity="secondary" />
              <span v-else class="text-slate-400">-</span>
            </template>
          </Column>
          <Column header="File/Link" v-if="!isMobile">
            <template #body="{ data }">
              <div v-if="data.file_path">
                <Tag v-if="isUrl(data.file_path)" value="Link" severity="info" />
                <Tag v-else value="File" severity="success" />
              </div>
              <Tag v-else value="Tidak Ada" severity="secondary" />
            </template>
          </Column>
          <Column header="Tanggal Terbit" v-if="!isMobile">
            <template #body="{ data }">
              <span class="text-sm text-slate-600">{{ formatDate(data.published_at) }}</span>
            </template>
          </Column>
          <Column header="Aksi" style="width: 120px">
            <template #body="{ data }">
              <div class="flex items-center gap-2">
                <a v-if="data.file_path && isUrl(data.file_path)" :href="data.file_path" target="_blank" rel="noopener">
                  <Button icon="pi pi-external-link" size="small" severity="info" text rounded v-tooltip="'Buka Link'" />
                </a>
                <a v-else-if="data.file_path" :href="`/storage/${data.file_path}`" target="_blank" rel="noopener">
                  <Button icon="pi pi-download" size="small" severity="info" text rounded v-tooltip="'Unduh'" />
                </a>
                <Link :href="route('admin.documents.edit', data.id)">
                  <Button icon="pi pi-pencil" size="small" severity="secondary" text rounded v-tooltip="'Edit'" />
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
            <div class="text-center py-8 text-slate-500">
              <IconFileDescription size="40" class="mx-auto mb-3 text-slate-300" />
              <p>Belum ada panduan atau dokumen.</p>
            </div>
          </template>
        </AdminDataTable>
      </div>
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
          <p v-if="docToDelete.version"><span class="font-medium">Versi:</span> {{ docToDelete.version }}</p>
        </div>
      </template>
    </DeleteConfirmDialog>
  </AdminLayout>
</template>
