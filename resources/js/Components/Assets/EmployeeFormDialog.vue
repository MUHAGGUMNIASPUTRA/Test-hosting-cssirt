<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'

const props = defineProps({
  visible: { type: Boolean, required: true },
  employee: { type: Object, default: null },
  organizations: { type: Array, default: () => [] },
  positions: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:visible', 'saved'])

const form = useForm({
  name: '',
  nip: '',
  nik: '',
  phone: '',
  email: '',
  position_id: '',
  organization_id: '',
  year_joined: '',
  is_active: true,
})

watch(
  () => props.visible,
  (val) => {
    if (val) {
      const e = props.employee
      form.name = e?.name ?? ''
      form.nip = ''
      form.nik = ''
      form.phone = e?.phone ?? ''
      form.email = e?.email ?? ''
      form.position_id = e?.position_id ?? ''
      form.organization_id = e?.organization_id ?? ''
      form.year_joined = e?.year_joined ?? ''
      form.is_active = e?.is_active ?? true
      form.clearErrors()
    }
  },
)

const orgOptions = computed(() =>
  props.organizations.map((o) => ({ label: o.name, value: o.id })),
)
const posOptions = computed(() =>
  props.positions.map((p) => ({ label: p.name, value: p.id })),
)

const close = () => {
  emit('update:visible', false)
  form.reset()
  form.clearErrors()
}

const submit = () => {
  const opts = {
    onSuccess: () => {
      close()
      emit('saved')
    },
  }
  if (props.employee) {
    form.put(route('admin.employees.update', props.employee.id), opts)
  } else {
    form.post(route('admin.employees.store'), opts)
  }
}
</script>

<template>
  <Dialog
    :visible="visible"
    :modal="true"
    :closable="false"
    class="w-full max-w-lg"
    @update:visible="$emit('update:visible', $event)"
  >
    <template #container>
      <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 p-5">
          <h3 class="text-lg font-semibold text-slate-900">
            {{ employee ? 'Edit Pegawai' : 'Tambah Pegawai' }}
          </h3>
          <p class="mt-0.5 text-sm text-slate-500">
            NIP dan NIK disimpan dalam format tersamarkan.
          </p>
        </div>
        <form @submit.prevent="submit" class="p-5">
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
                >Tahun Bergabung</label
              >
              <InputText
                v-model="form.year_joined"
                class="w-full"
                placeholder="Contoh: 2020"
                type="number"
              />
            </div>
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium text-slate-700"
                >Pegawai Aktif</label
              >
              <ToggleSwitch v-model="form.is_active" />
            </div>
          </div>
          <div
            class="mt-4 flex justify-end gap-3 border-t border-slate-100 pt-4"
          >
            <Button
              type="button"
              severity="secondary"
              variant="outlined"
              :disabled="form.processing"
              @click="close"
              >Batal</Button
            >
            <Button type="submit" :loading="form.processing">{{
              employee ? 'Simpan' : 'Tambah'
            }}</Button>
          </div>
        </form>
      </div>
    </template>
  </Dialog>
</template>
