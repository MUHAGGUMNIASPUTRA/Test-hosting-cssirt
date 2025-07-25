<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

const props = defineProps({
  categories: Array,
  tags: Array,
});

const confirm = useConfirm();
const toast = useToast();

// State for Dialog
const dialogVisible = ref(false);
const isEditing = ref(false);
const currentItem = ref(null);
const currentType = ref(''); // 'Category' or 'Tag'

const form = useForm({
  name: '',
});

const openDialog = (type, item = null) => {
  currentType.value = type;
  if (item) {
    isEditing.value = true;
    currentItem.value = item;
    form.name = item.name;
  } else {
    isEditing.value = false;
    form.reset();
  }
  dialogVisible.value = true;
};

const closeDialog = () => {
  dialogVisible.value = false;
};

const submitForm = () => {
  const isCategory = currentType.value === 'Kategori';
  const routeName = isEditing.value
    ? (isCategory ? 'admin.categories.update' : 'admin.tags.update')
    : (isCategory ? 'admin.categories.store' : 'admin.tags.store');

  const params = isEditing.value ? [currentItem.value.id] : [];

  form.submit(isEditing.value ? 'put' : 'post', route(routeName, ...params), {
    onSuccess: () => {
      closeDialog();
      toast.add({ severity: 'success', summary: 'Berhasil', detail: `${currentType.value} berhasil disimpan.`, life: 3000 });
    },
  });
};

const deleteItem = (type, item) => {
  const routeName = type === 'Kategori' ? 'admin.categories.destroy' : 'admin.tags.destroy';
  confirm.require({
    message: `Apakah Anda yakin ingin menghapus "${item.name}"?`,
    header: `Hapus ${type}`,
    icon: 'pi pi-info-circle',
    acceptClass: 'p-button-danger',
    acceptLabel: 'Ya, Hapus',
    rejectLabel: 'Batal',
    accept: () => {
      useForm({}).delete(route(routeName, item.id), {
        onSuccess: () => {
          toast.add({ severity: 'success', summary: 'Berhasil', detail: `${type} berhasil dihapus.`, life: 3000 });
        }
      });
    }
  });
};

</script>

<template>
  <AdminLayout title="Kategori & Tag">
    <Toast />
    <ConfirmDialog />

    <h1 class="text-2xl font-bold text-gray-700 mb-6">Kelola Kategori & Tag</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Categories Card -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Kategori</h2>
          <Button label="Tambah Kategori" icon="pi pi-plus" @click="openDialog('Kategori')" />
        </div>
        <DataTable :value="props.categories" size="small" stripedRows paginator :rows="5">
          <Column field="name" header="Nama"></Column>
          <Column field="posts_count" header="Jumlah Artikel"></Column>
          <Column header="Aksi">
            <template #body="slotProps">
              <div class="flex gap-2">
                <Button icon="pi pi-pencil" severity="info" text rounded @click="openDialog('Kategori', slotProps.data)" />
                <Button icon="pi pi-trash" severity="danger" text rounded @click="deleteItem('Kategori', slotProps.data)" />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>

      <!-- Tags Card -->
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Tag</h2>
          <Button label="Tambah Tag" icon="pi pi-plus" @click="openDialog('Tag')" />
        </div>
        <DataTable :value="props.tags" size="small" stripedRows paginator :rows="5">
          <Column field="name" header="Nama"></Column>
          <Column field="posts_count" header="Jumlah Artikel"></Column>
          <Column header="Aksi">
            <template #body="slotProps">
              <div class="flex gap-2">
                <Button icon="pi pi-pencil" severity="info" text rounded @click="openDialog('Tag', slotProps.data)" />
                <Button icon="pi pi-trash" severity="danger" text rounded @click="deleteItem('Tag', slotProps.data)" />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>

    <!-- Add/Edit Dialog -->
    <Dialog v-model:visible="dialogVisible" :header="`${isEditing ? 'Edit' : 'Tambah'} ${currentType}`" :modal="true" :style="{ width: '30rem' }">
      <form @submit.prevent="submitForm">
        <div class="p-field">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama {{ currentType }}</label>
          <InputText id="name" v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }" autofocus />
          <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
        </div>
        <div class="flex justify-end gap-2 mt-6">
          <Button label="Batal" severity="secondary" @click="closeDialog" />
          <Button type="submit" :label="isEditing ? 'Update' : 'Simpan'" :loading="form.processing" />
        </div>
      </form>
    </Dialog>
  </AdminLayout>
</template>
