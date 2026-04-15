<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  employee: { type: Object, default: null },
  organizations: Array,
  departments: Array,
  positions: Array,
})

const isEdit = computed(() => !!props.employee)

const form = useForm({
  name: props.employee?.name ?? '',
  nip: '',
  nik: '',
  phone: props.employee?.phone ?? '',
  email: props.employee?.email ?? '',
  position_id: props.employee?.position_id ?? '',
  organization_id: props.employee?.organization_id ?? '',
  year_joined: props.employee?.year_joined ?? '',
  is_active: props.employee?.is_active ?? true,
})

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)
const deptOptions = computed(() =>
  props.departments.map((d) => ({
    label: `${d.name} — ${props.organizations.find((o) => o.id === d.organization_id)?.name ?? ''}`,
    value: d.id,
  })),
)
const posOptions = computed(() =>
  props.positions.map((p) => ({ label: p.name, value: p.id })),
)

const submit = () => {
  if (isEdit.value) {
    form.put(route('admin.employees.update', props.employee.id))
  } else {
    form.post(route('admin.employees.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Pegawai' : 'Tambah Pegawai'">
    <div class="space-y-4">
      <AdminPageHeader
        :title="isEdit ? 'Edit Pegawai' : 'Tambah Pegawai'"
        description="Isi data pegawai. NIP dan NIK akan disimpan dalam format tersamarkan."
      />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <!-- Main fields -->
          <div class="space-y-4 lg:col-span-2">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">Informasi Dasar</h3>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Nama Lengkap <span class="text-red-500">*</span></label
                  >
                  <InputText
                    v-model="form.name"
                    class="w-full"
                    placeholder="Nama lengkap"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >NIP</label
                  >
                  <InputText
                    v-model="form.nip"
                    class="w-full"
                    placeholder="Nomor Induk Pegawai"
                  />
                  <p class="mt-1 text-xs text-slate-400">
                    Disimpan tersamarkan (5 awal + *** + 3 akhir)
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >NIK</label
                  >
                  <InputText
                    v-model="form.nik"
                    class="w-full"
                    placeholder="Nomor Induk Keluarga"
                  />
                  <p class="mt-1 text-xs text-slate-400">
                    Disimpan tersamarkan
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >No. Telepon</label
                  >
                  <InputText
                    v-model="form.phone"
                    class="w-full"
                    placeholder="08xx..."
                  />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Email</label
                  >
                  <InputText
                    v-model="form.email"
                    class="w-full"
                    placeholder="email@domain.com"
                    type="email"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Tahun Bergabung</label
                  >
                  <InputText
                    v-model="form.year_joined"
                    class="w-full"
                    placeholder="Contoh: 2020"
                    type="number"
                  />
                </div>
              </div>
            </div>

            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">Penempatan</h3>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Organisasi</label
                  >
                  <Select
                    v-model="form.organization_id"
                    :options="orgOptions"
                    option-label="label"
                    option-value="value"
                    placeholder="Pilih organisasi"
                    class="w-full"
                    show-clear
                  />
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Jabatan</label
                  >
                  <Select
                    v-model="form.position_id"
                    :options="posOptions"
                    option-label="label"
                    option-value="value"
                    placeholder="Pilih jabatan"
                    class="w-full"
                    show-clear
                    filter
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-4">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">Status</h3>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-700">Pegawai Aktif</span>
                <ToggleSwitch v-model="form.is_active" />
              </div>
            </div>

            <div class="flex gap-3">
              <Link :href="route('admin.employees.index')" class="flex-1">
                <Button
                  severity="secondary"
                  variant="outlined"
                  class="w-full"
                  :disabled="form.processing"
                  >Batal</Button
                >
              </Link>
              <Button type="submit" class="flex-1" :loading="form.processing">
                {{ isEdit ? 'Simpan' : 'Tambah' }}
              </Button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
