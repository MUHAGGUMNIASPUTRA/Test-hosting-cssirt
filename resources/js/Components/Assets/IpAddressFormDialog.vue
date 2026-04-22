<!-- Tujuan: Dialog tambah/edit IP Address (popup CRUD) -->
<!-- Caller: IpAddresses/Index.vue -->
<!-- Side Effects: Inertia POST/PUT ke admin.ip-addresses.store/update -->
<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
  visible: { type: Boolean, required: true },
  ipAddress: { type: Object, default: null },
})

const emit = defineEmits(['update:visible', 'saved'])

const form = useForm({
  private_ip: '',
  public_ip: '',
  description: '',
})

watch(
  () => props.visible,
  (val) => {
    if (val) {
      form.private_ip = props.ipAddress?.private_ip ?? ''
      form.public_ip = props.ipAddress?.public_ip ?? ''
      form.description = props.ipAddress?.description ?? ''
      form.clearErrors()
    }
  },
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
  if (props.ipAddress) {
    form.put(route('admin.ip-addresses.update', props.ipAddress.id), opts)
  } else {
    form.post(route('admin.ip-addresses.store'), opts)
  }
}
</script>

<template>
  <Dialog
    :visible="visible"
    :modal="true"
    :closable="false"
    class="w-full max-w-md"
    @update:visible="$emit('update:visible', $event)"
  >
    <template #container>
      <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 p-5">
          <h3 class="text-lg font-semibold text-slate-900">
            {{ ipAddress ? 'Edit IP Address' : 'Tambah IP Address' }}
          </h3>
        </div>
        <form @submit.prevent="submit" class="space-y-4 p-5">
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700"
              >IP Privat <span class="text-red-500">*</span></label
            >
            <InputText
              v-model="form.private_ip"
              class="w-full"
              placeholder="Contoh: 192.168.1.10"
              required
            />
            <p v-if="form.errors.private_ip" class="mt-1 text-xs text-red-600">
              {{ form.errors.private_ip }}
            </p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700"
              >IP Publik</label
            >
            <InputText
              v-model="form.public_ip"
              class="w-full"
              placeholder="Contoh: 103.21.45.10"
            />
            <p v-if="form.errors.public_ip" class="mt-1 text-xs text-red-600">
              {{ form.errors.public_ip }}
            </p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700"
              >Deskripsi</label
            >
            <Textarea
              v-model="form.description"
              class="w-full"
              rows="2"
              placeholder="Keterangan singkat..."
            />
          </div>
          <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
            <Button
              type="button"
              severity="secondary"
              variant="outlined"
              :disabled="form.processing"
              @click="close"
              >Batal</Button
            >
            <Button type="submit" :loading="form.processing">{{
              ipAddress ? 'Simpan' : 'Tambah'
            }}</Button>
          </div>
        </form>
      </div>
    </template>
  </Dialog>
</template>
