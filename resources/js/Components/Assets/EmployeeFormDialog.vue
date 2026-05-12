<!-- Tujuan: Dialog create/edit pegawai dengan enkripsi field sensitif dan fitur reveal -->
<!-- Caller: Employees/Index.vue -->
<!-- Side Effects: Inertia POST/PUT ke admin.employees.store/update -->
<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  visible: { type: Boolean, required: true },
  employee: { type: Object, default: null },
  organizations: { type: Array, default: () => [] },
  positions: { type: Array, default: () => [] },
  isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits(['update:visible', 'saved'])

const isEdit = computed(() => !!props.employee)

// Per-field edit-unlock state (edit mode only)
const editingSensitive = ref({
  nip: false,
  nik: false,
  phone: false,
  email: false,
})

// Reveal dialog state
const showReveal = ref(false)
const revealedData = ref(null)

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
      form.phone = ''
      form.email = ''
      form.position_id = e?.position_id ?? ''
      form.organization_id = e?.organization_id ?? ''
      form.year_joined = e?.year_joined ?? ''
      form.is_active = e?.is_active ?? true
      form.clearErrors()
      editingSensitive.value = {
        nip: false,
        nik: false,
        phone: false,
        email: false,
      }
      revealedData.value = null
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

const unlockField = (field) => {
  editingSensitive.value[field] = true
  form[field] = ''
}

const cancelUnlock = (field) => {
  editingSensitive.value[field] = false
  form[field] = ''
}

const handleRevealed = (data) => {
  revealedData.value = data
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
  <EmployeeRevealDialog
    v-model:visible="showReveal"
    :employee-id="employee?.id ?? null"
    @revealed="handleRevealed"
  />

  <Dialog
    :visible="visible"
    :modal="true"
    :closable="false"
    class="w-full max-w-xl"
    @update:visible="$emit('update:visible', $event)"
  >
    <template #container>
      <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 p-5">
          <h3 class="text-lg font-semibold text-slate-900">
            {{ employee ? 'Edit Pegawai' : 'Tambah Pegawai' }}
          </h3>
          <p class="mt-0.5 text-sm text-slate-500">
            {{
              isEdit
                ? 'Data sensitif ditampilkan tersamarkan. Klik ikon kunci untuk melihat atau ikon pensil untuk mengubah.'
                : 'NIP dan NIK wajib diisi dan akan dienkripsi saat disimpan.'
            }}
          </p>
        </div>

        <!-- Revealed data panel (admin only, after reveal) -->
        <div
          v-if="isEdit && revealedData"
          class="mx-5 mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3"
        >
          <p
            class="mb-2 flex items-center gap-1.5 text-xs font-semibold text-amber-700"
          >
            <IconShieldLock :size="13" />
            Data Asli (sesi ini saja — refresh untuk menghapus)
          </p>
          <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
            <div v-if="revealedData.nip">
              <dt class="text-slate-400">NIP</dt>
              <dd class="font-mono font-medium text-slate-800">
                {{ revealedData.nip }}
              </dd>
            </div>
            <div v-if="revealedData.nik">
              <dt class="text-slate-400">NIK</dt>
              <dd class="font-mono font-medium text-slate-800">
                {{ revealedData.nik }}
              </dd>
            </div>
            <div v-if="revealedData.phone">
              <dt class="text-slate-400">Telepon</dt>
              <dd class="font-mono font-medium text-slate-800">
                {{ revealedData.phone }}
              </dd>
            </div>
            <div v-if="revealedData.email">
              <dt class="text-slate-400">Email</dt>
              <dd class="font-mono font-medium text-slate-800">
                {{ revealedData.email }}
              </dd>
            </div>
          </dl>
        </div>

        <form class="p-5" @submit.prevent="submit">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Nama -->
            <div class="sm:col-span-2">
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Nama Lengkap <span class="text-red-500">*</span>
              </label>
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

            <!-- NIP -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                NIP <span v-if="!isEdit" class="text-red-500">*</span>
              </label>
              <!-- Edit: masked display -->
              <template v-if="isEdit && !editingSensitive.nip">
                <div
                  class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
                >
                  <span class="flex-1 font-mono text-sm text-slate-600">
                    {{ employee?.nip_masked ?? '—' }}
                  </span>
                  <button
                    v-if="isAdmin"
                    type="button"
                    class="text-slate-400 hover:text-amber-600"
                    title="Lihat data asli"
                    @click="showReveal = true"
                  >
                    <IconEye :size="15" />
                  </button>
                  <button
                    type="button"
                    class="text-slate-400 hover:text-blue-600"
                    title="Ubah NIP"
                    @click="unlockField('nip')"
                  >
                    <IconPencil :size="15" />
                  </button>
                </div>
              </template>
              <!-- Create or unlocked edit -->
              <template v-else>
                <div class="flex gap-1">
                  <InputText
                    v-model="form.nip"
                    class="w-full"
                    :placeholder="
                      isEdit
                        ? 'Nilai baru NIP (kosongkan = tidak diubah)'
                        : 'Nomor Induk Pegawai'
                    "
                    :required="!isEdit"
                  />
                  <button
                    v-if="isEdit"
                    type="button"
                    class="rounded-md border border-slate-200 px-2 text-slate-400 hover:bg-slate-50"
                    title="Batal"
                    @click="cancelUnlock('nip')"
                  >
                    <IconX :size="14" />
                  </button>
                </div>
                <p v-if="!isEdit" class="mt-0.5 text-xs text-slate-400">
                  Akan dienkripsi saat disimpan
                </p>
              </template>
              <p v-if="form.errors.nip" class="mt-1 text-xs text-red-600">
                {{ form.errors.nip }}
              </p>
            </div>

            <!-- NIK -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                NIK <span v-if="!isEdit" class="text-red-500">*</span>
              </label>
              <template v-if="isEdit && !editingSensitive.nik">
                <div
                  class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
                >
                  <span class="flex-1 font-mono text-sm text-slate-600">
                    {{ employee?.nik_masked ?? '—' }}
                  </span>
                  <button
                    v-if="isAdmin"
                    type="button"
                    class="text-slate-400 hover:text-amber-600"
                    title="Lihat data asli"
                    @click="showReveal = true"
                  >
                    <IconEye :size="15" />
                  </button>
                  <button
                    type="button"
                    class="text-slate-400 hover:text-blue-600"
                    title="Ubah NIK"
                    @click="unlockField('nik')"
                  >
                    <IconPencil :size="15" />
                  </button>
                </div>
              </template>
              <template v-else>
                <div class="flex gap-1">
                  <InputText
                    v-model="form.nik"
                    class="w-full"
                    :placeholder="
                      isEdit
                        ? 'Nilai baru NIK (kosongkan = tidak diubah)'
                        : 'Nomor Induk Keluarga'
                    "
                    :required="!isEdit"
                  />
                  <button
                    v-if="isEdit"
                    type="button"
                    class="rounded-md border border-slate-200 px-2 text-slate-400 hover:bg-slate-50"
                    title="Batal"
                    @click="cancelUnlock('nik')"
                  >
                    <IconX :size="14" />
                  </button>
                </div>
                <p v-if="!isEdit" class="mt-0.5 text-xs text-slate-400">
                  Akan dienkripsi saat disimpan
                </p>
              </template>
              <p v-if="form.errors.nik" class="mt-1 text-xs text-red-600">
                {{ form.errors.nik }}
              </p>
            </div>

            <!-- Telepon -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                No. Telepon
              </label>
              <template v-if="isEdit && !editingSensitive.phone">
                <div
                  class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
                >
                  <span class="flex-1 font-mono text-sm text-slate-600">
                    {{ employee?.phone_masked ?? '—' }}
                  </span>
                  <button
                    v-if="isAdmin"
                    type="button"
                    class="text-slate-400 hover:text-amber-600"
                    title="Lihat data asli"
                    @click="showReveal = true"
                  >
                    <IconEye :size="15" />
                  </button>
                  <button
                    type="button"
                    class="text-slate-400 hover:text-blue-600"
                    title="Ubah No. Telepon"
                    @click="unlockField('phone')"
                  >
                    <IconPencil :size="15" />
                  </button>
                </div>
              </template>
              <template v-else>
                <div class="flex gap-1">
                  <InputText
                    v-model="form.phone"
                    class="w-full"
                    :placeholder="
                      isEdit
                        ? 'Nilai baru (kosongkan = tidak diubah)'
                        : '08xx...'
                    "
                  />
                  <button
                    v-if="isEdit"
                    type="button"
                    class="rounded-md border border-slate-200 px-2 text-slate-400 hover:bg-slate-50"
                    title="Batal"
                    @click="cancelUnlock('phone')"
                  >
                    <IconX :size="14" />
                  </button>
                </div>
              </template>
            </div>

            <!-- Email (full-width karena nilai masked bisa panjang) -->
            <div class="sm:col-span-2">
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Email
              </label>
              <template v-if="isEdit && !editingSensitive.email">
                <div
                  class="flex min-w-0 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
                >
                  <span
                    class="min-w-0 flex-1 truncate font-mono text-sm text-slate-600"
                    :title="employee?.email_masked ?? ''"
                  >
                    {{ employee?.email_masked ?? '—' }}
                  </span>
                  <button
                    v-if="isAdmin"
                    type="button"
                    class="text-slate-400 hover:text-amber-600"
                    title="Lihat data asli"
                    @click="showReveal = true"
                  >
                    <IconEye :size="15" />
                  </button>
                  <button
                    type="button"
                    class="text-slate-400 hover:text-blue-600"
                    title="Ubah Email"
                    @click="unlockField('email')"
                  >
                    <IconPencil :size="15" />
                  </button>
                </div>
              </template>
              <template v-else>
                <div class="flex gap-1">
                  <InputText
                    v-model="form.email"
                    class="w-full"
                    type="email"
                    :placeholder="
                      isEdit
                        ? 'Nilai baru (kosongkan = tidak diubah)'
                        : 'email@domain.com'
                    "
                  />
                  <button
                    v-if="isEdit"
                    type="button"
                    class="rounded-md border border-slate-200 px-2 text-slate-400 hover:bg-slate-50"
                    title="Batal"
                    @click="cancelUnlock('email')"
                  >
                    <IconX :size="14" />
                  </button>
                </div>
              </template>
              <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">
                {{ form.errors.email }}
              </p>
            </div>

            <!-- Jabatan -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Jabatan <span class="text-red-500">*</span>
              </label>
              <Select
                v-model="form.position_id"
                :options="posOptions"
                option-label="label"
                option-value="value"
                placeholder="Pilih jabatan"
                class="w-full"
                show-clear
                filter
                required
              />
              <p
                v-if="form.errors.position_id"
                class="mt-1 text-xs text-red-600"
              >
                {{ form.errors.position_id }}
              </p>
            </div>

            <!-- Organisasi -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Organisasi <span class="text-red-500">*</span>
              </label>
              <Select
                v-model="form.organization_id"
                :options="orgOptions"
                option-label="label"
                option-value="value"
                placeholder="Pilih organisasi"
                class="w-full"
                show-clear
                required
              />
              <p
                v-if="form.errors.organization_id"
                class="mt-1 text-xs text-red-600"
              >
                {{ form.errors.organization_id }}
              </p>
            </div>

            <!-- Tahun Bergabung -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700">
                Tahun Bergabung
              </label>
              <InputText
                v-model="form.year_joined"
                class="w-full"
                placeholder="Contoh: 2020"
                type="number"
              />
            </div>

            <!-- Status -->
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium text-slate-700">
                Pegawai Aktif
              </label>
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
