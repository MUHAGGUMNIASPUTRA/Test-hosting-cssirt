<!-- Tujuan: Dialog konfirmasi password untuk melihat data sensitif pegawai (admin only) -->
<!-- Caller: EmployeeFormDialog -->
<!-- Side Effects: axios POST ke admin.employees.reveal -->
<script setup>
import axios from 'axios'
import { ref } from 'vue'

const props = defineProps({
  visible: { type: Boolean, required: true },
  employeeId: { type: String, default: null },
})

const emit = defineEmits(['update:visible', 'revealed'])

const password = ref('')
const loading = ref(false)
const error = ref(null)

const close = () => {
  password.value = ''
  error.value = null
  emit('update:visible', false)
}

const submit = async () => {
  if (!props.employeeId || !password.value) return
  loading.value = true
  error.value = null
  try {
    const res = await axios.post(
      route('admin.employees.reveal', props.employeeId),
      { password: password.value },
    )
    emit('revealed', res.data)
    close()
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Terjadi kesalahan. Coba lagi.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Dialog
    :visible="visible"
    :modal="true"
    :closable="false"
    class="w-full max-w-sm"
    @update:visible="$emit('update:visible', $event)"
  >
    <template #container>
      <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 p-5">
          <div class="flex items-center gap-3">
            <div
              class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-100"
            >
              <IconShieldLock class="text-amber-600" :size="18" />
            </div>
            <div>
              <h3 class="text-base font-semibold text-slate-900">
                Verifikasi Identitas
              </h3>
              <p class="text-xs text-slate-500">
                Masukkan password Anda untuk melihat data sensitif
              </p>
            </div>
          </div>
        </div>
        <form class="p-5" @submit.prevent="submit">
          <div class="mb-4">
            <label class="mb-1 block text-sm font-medium text-slate-700">
              Password Admin
            </label>
            <Password
              v-model="password"
              class="w-full"
              :feedback="false"
              toggle-mask
              placeholder="Masukkan password login Anda"
              autofocus
            />
            <p v-if="error" class="mt-2 text-xs text-red-600">{{ error }}</p>
          </div>
          <div class="flex justify-end gap-3">
            <Button
              type="button"
              severity="secondary"
              variant="outlined"
              :disabled="loading"
              @click="close"
              >Batal</Button
            >
            <Button type="submit" :loading="loading" severity="warn">
              Lihat Data
            </Button>
          </div>
        </form>
      </div>
    </template>
  </Dialog>
</template>
