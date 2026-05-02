<script setup>
import { computed, ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  techStack: { type: Object, default: null },
  categories: Array,
})

const isEdit = computed(() => !!props.techStack)

const form = useForm({
  name: props.techStack?.name ?? '',
  description: props.techStack?.description ?? '',
  category_id: props.techStack?.category_id ?? null,
  logo: null,
})

const categoryOptions = computed(() =>
  props.categories.map((c) => ({ label: c.name, value: c.id })),
)

const logoPreview = ref(props.techStack?.logo_attachment?.url ?? null)
const logoInput = ref(null)

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

const submit = () => {
  if (isEdit.value) {
    form.post(route('admin.tech-stacks.update', props.techStack.id), {
      _method: 'PUT',
    })
  } else {
    form.post(route('admin.tech-stacks.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Tech Stack' : 'Tambah Tech Stack'">
    <div class="space-y-4">
      <AdminPageHeader
        :title="isEdit ? 'Edit Tech Stack' : 'Tambah Tech Stack'"
        description="Isi data tech stack."
      />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <div class="space-y-4 lg:col-span-2">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">
                Informasi Tech Stack
              </h3>
              <div class="space-y-4">
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Nama <span class="text-red-500">*</span></label
                  >
                  <InputText
                    v-model="form.name"
                    class="w-full"
                    placeholder="Contoh: Laravel, Vue.js, PostgreSQL"
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
                  <p
                    v-if="form.errors.category_id"
                    class="mt-1 text-xs text-red-600"
                  >
                    {{ form.errors.category_id }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Deskripsi</label
                  >
                  <Textarea
                    v-model="form.description"
                    class="w-full"
                    rows="3"
                    placeholder="Deskripsi singkat tech stack..."
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">Logo</h3>
              <div class="space-y-3">
                <div
                  v-if="logoPreview"
                  class="flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 p-4"
                >
                  <img
                    :src="logoPreview"
                    alt="Logo preview"
                    class="h-20 w-20 object-contain"
                  />
                </div>
                <div
                  v-else
                  class="flex items-center justify-center rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 p-8"
                >
                  <div class="text-center text-slate-400">
                    <IconPhoto size="32" class="mx-auto mb-2" />
                    <p class="text-sm">Belum ada logo</p>
                  </div>
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
                  class="w-full"
                  @click="logoInput?.click()"
                >
                  <IconUpload size="15" class="mr-1" />
                  {{ logoPreview ? 'Ganti Logo' : 'Upload Logo' }}
                </Button>
                <Button
                  v-if="logoPreview"
                  type="button"
                  severity="danger"
                  variant="outlined"
                  class="w-full"
                  @click="removeLogo"
                  >Hapus Logo</Button
                >
              </div>
            </div>

            <div class="flex gap-3">
              <Link :href="route('admin.tech-stacks.index')" class="flex-1">
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
