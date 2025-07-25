<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'staff',
});

const roleOptions = [
  { label: 'Admin', value: 'admin' },
  { label: 'Staf', value: 'staff' },
  { label: 'Pengguna', value: 'user' },
];

const submit = () => {
  form.post(route('admin.users.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <AdminLayout title="Tambah Pengguna Baru">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
      <h1 class="text-2xl font-bold text-gray-700 mb-6">Tambah Pengguna Baru</h1>
      <form @submit.prevent="submit">
        <div class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <InputText id="name" v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }" required/>
            <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <InputText id="email" v-model="form.email" type="email" class="w-full" :class="{ 'p-invalid': form.errors.email }" required/>
            <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <Password id="password" v-model="form.password" class="w-full" inputClass="w-full" toggleMask :class="{ 'p-invalid': form.errors.password }" required/>
            <small v-if="form.errors.password" class="p-error">{{ form.errors.password }}</small>
          </div>
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <Password id="password_confirmation" v-model="form.password_confirmation" class="w-full" inputClass="w-full" :feedback="false" toggleMask required/>
          </div>
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <Select id="role" v-model="form.role" :options="roleOptions" optionLabel="label" optionValue="value" class="w-full" required/>
          </div>
          <div class="flex justify-end">
            <Button type="submit" label="Simpan Pengguna" :loading="form.processing" />
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
