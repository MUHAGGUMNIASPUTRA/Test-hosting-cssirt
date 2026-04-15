<script setup>
import { computed, ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  guide: { type: Object, default: null },
})

const isEdit = computed(() => !!props.guide)

const existingAttachments = ref(
  props.guide?.guide_attachments?.map((ga) => ga.attachment).filter(Boolean) ??
    [],
)

const form = useForm({
  name: props.guide?.name ?? '',
  type: props.guide?.type ?? 'web',
  description: props.guide?.description ?? '',
  existing_attachment_ids: existingAttachments.value.map((a) => a.id),
  attachments: [],
})

const typeOptions = [
  { label: 'Aplikasi Web', value: 'web' },
  { label: 'Aplikasi Mobile', value: 'mobile' },
]

const newFiles = ref([])
const fileInput = ref(null)

const handleFileChange = (e) => {
  const files = Array.from(e.target.files)
  newFiles.value = [...newFiles.value, ...files]
  form.attachments = newFiles.value
  if (fileInput.value) fileInput.value.value = ''
}

const removeNewFile = (index) => {
  newFiles.value = newFiles.value.filter((_, i) => i !== index)
  form.attachments = newFiles.value
}

const removeExisting = (attachmentId) => {
  existingAttachments.value = existingAttachments.value.filter(
    (a) => a.id !== attachmentId,
  )
  form.existing_attachment_ids = existingAttachments.value.map((a) => a.id)
}

const submit = () => {
  if (isEdit.value) {
    form.post(route('admin.virtual-asset-guides.update', props.guide.id), {
      _method: 'PUT',
    })
  } else {
    form.post(route('admin.virtual-asset-guides.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Panduan' : 'Tambah Panduan'">
    <div class="space-y-4">
      <AdminPageHeader
        :title="
          isEdit ? 'Edit Panduan Aset Virtual' : 'Tambah Panduan Aset Virtual'
        "
        description="Isi informasi panduan pengembangan dan pengelolaan aset."
      />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <div class="space-y-4 lg:col-span-2">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">
                Informasi Panduan
              </h3>
              <div class="space-y-4">
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Nama <span class="text-red-500">*</span></label
                  >
                  <InputText
                    v-model="form.name"
                    class="w-full"
                    placeholder="Nama panduan"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Tipe <span class="text-red-500">*</span></label
                  >
                  <div class="flex gap-2">
                    <Button
                      v-for="opt in typeOptions"
                      :key="opt.value"
                      type="button"
                      size="small"
                      :severity="
                        form.type === opt.value ? 'primary' : 'secondary'
                      "
                      :variant="
                        form.type === opt.value ? undefined : 'outlined'
                      "
                      @click="form.type = opt.value"
                      >{{ opt.label }}</Button
                    >
                  </div>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Deskripsi</label
                  >
                  <RichTextEditor v-model="form.description" />
                </div>
              </div>
            </div>

            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">Lampiran</h3>

              <div v-if="existingAttachments.length > 0" class="mb-3 space-y-2">
                <p
                  class="text-xs font-medium uppercase tracking-wider text-slate-500"
                >
                  Lampiran Saat Ini
                </p>
                <div
                  v-for="att in existingAttachments"
                  :key="att.id"
                  class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
                >
                  <IconPaperclip
                    size="15"
                    class="flex-shrink-0 text-slate-400"
                  />
                  <span class="flex-1 truncate text-sm text-slate-700">{{
                    att.filename
                  }}</span>
                  <Button
                    type="button"
                    size="small"
                    severity="danger"
                    variant="text"
                    class="!p-0"
                    @click="removeExisting(att.id)"
                    ><IconX size="14"
                  /></Button>
                </div>
              </div>

              <div v-if="newFiles.length > 0" class="mb-3 space-y-2">
                <p
                  class="text-xs font-medium uppercase tracking-wider text-slate-500"
                >
                  Akan Diunggah
                </p>
                <div
                  v-for="(file, i) in newFiles"
                  :key="i"
                  class="flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2"
                >
                  <IconFile size="15" class="flex-shrink-0 text-blue-400" />
                  <span class="flex-1 truncate text-sm text-blue-700">{{
                    file.name
                  }}</span>
                  <Button
                    type="button"
                    size="small"
                    severity="danger"
                    variant="text"
                    class="!p-0"
                    @click="removeNewFile(i)"
                    ><IconX size="14"
                  /></Button>
                </div>
              </div>

              <input
                ref="fileInput"
                type="file"
                multiple
                class="hidden"
                @change="handleFileChange"
              />
              <Button
                type="button"
                severity="secondary"
                variant="outlined"
                class="w-full"
                @click="fileInput?.click()"
              >
                <IconUpload size="15" class="mr-1" />Tambah Lampiran
              </Button>
            </div>
          </div>

          <div class="flex flex-col gap-3">
            <Link
              :href="route('admin.virtual-asset-guides.index')"
              class="block"
            >
              <Button
                severity="secondary"
                variant="outlined"
                class="w-full"
                :disabled="form.processing"
                >Batal</Button
              >
            </Link>
            <Button type="submit" class="w-full" :loading="form.processing">
              {{ isEdit ? 'Simpan Perubahan' : 'Tambah Panduan' }}
            </Button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
