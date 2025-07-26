<script setup>
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

defineProps({
  posts: Object,
});

const confirm = useConfirm();
const toast = useToast();

const deletePost = (post) => {
  confirm.require({
    message: `Apakah Anda yakin ingin menghapus artikel "${post.title}"?`,
    header: 'Konfirmasi Penghapusan',
    icon: 'pi pi-info-circle',
    rejectClass: 'p-button-secondary p-button-outlined',
    acceptClass: 'p-button-danger',
    acceptLabel: 'Ya, Hapus',
    rejectLabel: 'Batal',
    accept: () => {
      router.delete(route('admin.posts.destroy', post.id), {
        onSuccess: () => {
          toast.add({ severity: 'info', summary: 'Berhasil', detail: 'Artikel berhasil dihapus', life: 3000 });
        }
      });
    }
  });
};

const getStatusSeverity = (status) => {
  return status === 'Published' ? 'success' : 'warn';
};
</script>

<template>
  <AdminLayout title="Daftar Artikel">
    <ConfirmDialog></ConfirmDialog>
    <Toast />

    <div class="bg-white p-6 rounded-lg shadow">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Daftar Artikel</h1>
        <Link :href="route('admin.posts.create')">
          <Button label="Tambah Artikel" icon="pi pi-plus" />
        </Link>
      </div>

      <DataTable :value="posts.data" paginator :rows="10" tableStyle="min-width: 50rem">
        <Column field="title" header="Judul" style="width: 40%"></Column>
        <Column header="Status">
          <template #body="slotProps">
            <Tag :value="slotProps.data.status" :severity="getStatusSeverity(slotProps.data.status)" />
          </template>
        </Column>
        <Column field="views_count" header="Dilihat"></Column>
        <Column header="Tgl Publikasi">
           <template #body="slotProps">
            {{ slotProps.data.published_at ? new Date(slotProps.data.published_at).toLocaleDateString('id-ID') : '-' }}
          </template>
        </Column>
        <Column header="Aksi" style="width: 15%">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Link :href="route('admin.posts.edit', slotProps.data.id)">
                <Button icon="pi pi-pencil" severity="info" text rounded />
              </Link>
              <Button @click="deletePost(slotProps.data)" icon="pi pi-trash" severity="danger" text rounded />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AdminLayout>
</template>
