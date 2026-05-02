<!-- Tujuan: Dialog tambah/edit Subdomain (popup CRUD) -->
<!-- Caller: Subdomains/Index.vue -->
<!-- Side Effects: Inertia POST/PUT ke admin.subdomains.store/update -->
<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
  visible: { type: Boolean, required: true },
  subdomain: { type: Object, default: null },
})

const emit = defineEmits(['update:visible', 'saved'])

const form = useForm({
  subdomain: '',
  description: '',
})

watch(
  () => props.visible,
  (val) => {
    if (val) {
      form.subdomain = props.subdomain?.subdomain ?? ''
      form.description = props.subdomain?.description ?? ''
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
  if (props.subdomain) {
    form.put(route('admin.subdomains.update', props.subdomain.id), opts)
  } else {
    form.post(route('admin.subdomains.store'), opts)
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
            {{ subdomain ? 'Edit Subdomain' : 'Tambah Subdomain' }}
          </h3>
        </div>
        <form @submit.prevent="submit" class="space-y-4 p-5">
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-700"
              >Subdomain <span class="text-red-500">*</span></label
            >
            <InputText
              v-model="form.subdomain"
              class="w-full"
              placeholder="Contoh: app.example.go.id"
              required
            />
            <p v-if="form.errors.subdomain" class="mt-1 text-xs text-red-600">
              {{ form.errors.subdomain }}
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
              subdomain ? 'Simpan' : 'Tambah'
            }}</Button>
          </div>
        </form>
      </div>
    </template>
  </Dialog>
</template>
