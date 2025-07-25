<script setup>
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

defineProps({
  users: Object,
});

const confirm = useConfirm();
const toast = useToast();

const deleteUser = (user) => {
  confirm.require({
    message: `Apakah Anda yakin ingin menghapus pengguna "${user.name}"?`,
    header: 'Konfirmasi Penghapusan',
    icon: 'pi pi-info-circle',
    acceptClass: 'p-button-danger',
    acceptLabel: 'Ya, Hapus',
    rejectLabel: 'Batal',
    accept: () => {
      router.delete(route('admin.users.destroy', user.id), {
        onSuccess: () => {
          toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Pengguna berhasil dihapus', life: 3000 });
        },
        onError: (errors) => {
           toast.add({ severity: 'error', summary: 'Gagal', detail: errors.message || 'Tidak dapat menghapus pengguna.', life: 3000 });
        }
      });
    }
  });
};

const getRoleSeverity = (role) => {
  const map = { 'admin': 'danger', 'staff': 'info', 'user': 'success' };
  return map[role] || 'secondary';
};
</script>

<template>
  <AdminLayout title="Kelola Pengguna">
    <ConfirmDialog></ConfirmDialog>
    <Toast />

    <div class="bg-white p-6 rounded-lg shadow">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Kelola Pengguna</h1>
        <Link :href="route('admin.users.create')">
          <Button label="Tambah Pengguna" icon="pi pi-plus" />
        </Link>
      </div>

      <DataTable :value="users.data" paginator :rows="10" tableStyle="min-width: 50rem">
        <Column field="name" header="Nama"></Column>
        <Column field="email" header="Email"></Column>
        <Column header="Role">
          <template #body="slotProps">
            <Tag :value="slotProps.data.role" :severity="getRoleSeverity(slotProps.data.role)" />
          </template>
        </Column>
        <Column header="Tgl Dibuat">
           <template #body="slotProps">
            {{ new Date(slotProps.data.created_at).toLocaleDateString('id-ID') }}
          </template>
        </Column>
        <Column header="Aksi">
          <template #body="slotProps">
            <div class="flex gap-2">
              <Link :href="route('admin.users.edit', slotProps.data.id)">
                <Button icon="pi pi-pencil" severity="info" text rounded />
              </Link>
              <Button @click="deleteUser(slotProps.data)" icon="pi pi-trash" severity="danger" text rounded />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AdminLayout>
</template>
