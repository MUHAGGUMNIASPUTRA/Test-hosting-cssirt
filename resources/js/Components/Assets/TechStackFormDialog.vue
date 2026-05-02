<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  visible: { type: Boolean, required: true },
  techStack: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:visible', 'saved'])

const form = useForm({
  name: '',
  description: '',
  category_id: null,
  logo: null,
})

const logoPreview = ref(null)
const logoInput = ref(null)

watch(
  () => props.visible,
  (val) => {
    if (val) {
      const ts = props.techStack
      form.name = ts?.name ?? ''
      form.description = ts?.description ?? ''
      form.category_id = ts?.category_id ?? null
      form.logo = null
      logoPreview.value = ts?.logo_attachment?.url ?? null
      form.clearErrors()
    }
  },
)

const categoryOptions = computed(() =>
  props.categories.map((c) => ({ label: c.name, value: c.id })),
)

const handleLogoChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  form.logo = file
  logoPreview.value = URL.createObjectURL(file)
}

const removeLogo = () => {
  form.logo = null
  logoPreview.value = null
  if (logoInput.value) logoInput.value.value = ''
}

const close = () => {
  emit('update:visible', false)
  form.reset()
  form.clearErrors()
  logoPreview.value = null
}

const submit = () => {
  const opts = {
    forceFormData: true,
    onSuccess: () => {
      close()
      emit('saved')
    },
  }
  if (props.techStack) {
    form.post(route('admin.tech-stacks.update', props.techStack.id), {
      ...opts,
      _method: 'PUT',
    })
  } else {
    form.post(route('admin.tech-stacks.store'), opts)
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
            {{ techStack ? 'Edit Tech Stack' : 'Tambah Tech Stack' }}
          </h3>
        </div>
        <form @submit.prevent="submit" class="p-5">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <!-- Logo upload -->
            <div class="flex flex-col items-center gap-2">
              <div
                v-if="logoPreview"
                class="flex h-20 w-20 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 p-2"
              >
                <img
                  :src="logoPreview"
                  alt="Logo"
                  class="h-full w-full object-contain"
                />
              </div>
              <div
                v-else
                class="flex h-20 w-20 items-center justify-center rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 text-slate-400"
              >
                <IconPhoto size="28" />
              </div>
              <input
                ref="logoInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleLogoChange"
              />
              <Button
                type="button"
                severity="secondary"
                variant="outlined"
                size="small"
                class="w-full"
                @click="logoInput?.click()"
              >
                <IconUpload size="13" class="mr-1" />
                {{ logoPreview ? 'Ganti' : 'Upload' }}
              </Button>
              <Button
                v-if="logoPreview"
                type="button"
                severity="danger"
                variant="text"
                size="small"
                @click="removeLogo"
                >Hapus</Button
              >
            </div>

            <!-- Fields -->
            <div class="space-y-4 sm:col-span-2">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Nama <span class="text-red-500">*</span></label
                >
                <InputText
                  v-model="form.name"
                  class="w-full"
                  placeholder="Contoh: Laravel, Vue.js"
                  required
                />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                  {{ form.errors.name }}
                </p>
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Kategori</label
                >
                <Select
                  v-model="form.category_id"
                  :options="categoryOptions"
                  option-label="label"
                  option-value="value"
                  placeholder="Pilih kategori"
                  class="w-full"
                  show-clear
                />
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700"
                  >Deskripsi</label
                >
                <Textarea
                  v-model="form.description"
                  class="w-full"
                  rows="2"
                  placeholder="Deskripsi singkat..."
                />
              </div>
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
              techStack ? 'Simpan' : 'Tambah'
            }}</Button>
          </div>
        </form>
      </div>
    </template>
  </Dialog>
</template>
