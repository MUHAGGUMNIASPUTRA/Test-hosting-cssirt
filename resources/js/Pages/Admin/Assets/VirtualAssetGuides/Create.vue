<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  guide: { type: Object, default: null },
})

const isEdit = computed(() => !!props.guide)

// Existing attachments in order (reactive list for reordering UI)
const existingAttachments = ref(
  props.guide?.guide_attachments
    ?.sort((a, b) => a.sort_order - b.sort_order)
    .map((ga) => ga.attachment)
    .filter(Boolean) ?? [],
)

const form = useForm({
  name: props.guide?.name ?? '',
  type: props.guide?.type ?? 'web',
  description: props.guide?.description ?? '',
  ordered_existing_ids: existingAttachments.value.map((a) => a.id),
  attachments: [],
  new_links: [],
})

const typeOptions = [
  { label: 'Aplikasi Web', value: 'web' },
  { label: 'Aplikasi Mobile', value: 'mobile' },
]

// --- File handling ---
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

// --- Link handling ---
const newLinks = ref([])
const newLinkInput = ref('')
const showLinkInput = ref(false)

const addLink = () => {
  const url = newLinkInput.value.trim()
  if (!url) return
  newLinks.value = [...newLinks.value, url]
  form.new_links = newLinks.value
  newLinkInput.value = ''
  showLinkInput.value = false
}

const removeNewLink = (index) => {
  newLinks.value = newLinks.value.filter((_, i) => i !== index)
  form.new_links = newLinks.value
}

// --- Existing attachment ordering ---
const syncOrderIds = () => {
  form.ordered_existing_ids = existingAttachments.value.map((a) => a.id)
}

const moveUp = (index) => {
  if (index === 0) return
  const arr = [...existingAttachments.value]
  ;[arr[index - 1], arr[index]] = [arr[index], arr[index - 1]]
  existingAttachments.value = arr
  syncOrderIds()
}

const moveDown = (index) => {
  if (index === existingAttachments.value.length - 1) return
  const arr = [...existingAttachments.value]
  ;[arr[index], arr[index + 1]] = [arr[index + 1], arr[index]]
  existingAttachments.value = arr
  syncOrderIds()
}

const removeExisting = (attachmentId) => {
  existingAttachments.value = existingAttachments.value.filter(
    (a) => a.id !== attachmentId,
  )
  syncOrderIds()
}

// --- Submit ---
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
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="
          isEdit ? 'Edit Panduan Aset Virtual' : 'Tambah Panduan Aset Virtual'
        "
        :description="
          isEdit
            ? 'Perbarui informasi panduan pengembangan dan pengelolaan aset.'
            : 'Isi informasi panduan pengembangan dan pengelolaan aset.'
        "
        back-route="admin.virtual-asset-guides.index"
        :processing="form.processing"
      >
        <template #actions>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <IconLoader3
              v-if="form.processing"
              class="animate-spin"
              size="16"
            />
            <IconDeviceFloppy v-else size="16" />
            {{
              form.processing ? 'Menyimpan...' : isEdit ? 'Update' : 'Simpan'
            }}
          </button>
        </template>
      </AdminFormHeader>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
          <!-- Informasi Panduan -->
          <AdminFormSection
            title="Informasi Panduan"
            description="Nama, tipe, dan deskripsi panduan"
            color="blue"
          >
            <template #icon="{ iconClass }">
              <IconBookDownload :class="iconClass" />
            </template>
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
                    :variant="form.type === opt.value ? undefined : 'outlined'"
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
          </AdminFormSection>

          <!-- Lampiran -->
          <AdminFormSection
            title="Lampiran"
            description="File atau link referensi panduan (dapat diurutkan)"
            color="indigo"
          >
            <template #icon="{ iconClass }">
              <IconPaperclip :class="iconClass" />
            </template>

            <!-- Existing attachments with ordering -->
            <div v-if="existingAttachments.length > 0" class="mb-4 space-y-1.5">
              <p
                class="mb-2 text-xs font-medium uppercase tracking-wider text-slate-500"
              >
                Lampiran Saat Ini
              </p>
              <div
                v-for="(att, idx) in existingAttachments"
                :key="att.id"
                class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
              >
                <!-- Order buttons -->
                <div class="flex flex-col gap-0.5">
                  <button
                    type="button"
                    :disabled="idx === 0"
                    class="flex h-5 w-5 items-center justify-center rounded text-slate-400 transition hover:bg-slate-200 hover:text-slate-600 disabled:cursor-not-allowed disabled:opacity-30"
                    @click="moveUp(idx)"
                  >
                    <IconChevronUp size="12" />
                  </button>
                  <button
                    type="button"
                    :disabled="idx === existingAttachments.length - 1"
                    class="flex h-5 w-5 items-center justify-center rounded text-slate-400 transition hover:bg-slate-200 hover:text-slate-600 disabled:cursor-not-allowed disabled:opacity-30"
                    @click="moveDown(idx)"
                  >
                    <IconChevronDown size="12" />
                  </button>
                </div>

                <!-- Icon -->
                <component
                  :is="att.type === 'link' ? IconExternalLink : IconPaperclip"
                  size="14"
                  class="flex-shrink-0 text-slate-400"
                />

                <!-- Name / URL -->
                <span class="flex-1 truncate text-sm text-slate-700">
                  {{ att.filename ?? att.url }}
                </span>

                <!-- Type badge -->
                <span
                  class="flex-shrink-0 rounded px-1.5 py-0.5 text-xs"
                  :class="
                    att.type === 'link'
                      ? 'bg-blue-100 text-blue-600'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  {{ att.type === 'link' ? 'Link' : 'File' }}
                </span>

                <!-- Remove -->
                <Button
                  type="button"
                  size="small"
                  severity="danger"
                  variant="text"
                  class="!p-0"
                  @click="removeExisting(att.id)"
                >
                  <IconX size="14" />
                </Button>
              </div>
            </div>

            <!-- New files to upload -->
            <div v-if="newFiles.length > 0" class="mb-4 space-y-1.5">
              <p
                class="mb-2 text-xs font-medium uppercase tracking-wider text-slate-500"
              >
                File Baru
              </p>
              <div
                v-for="(file, i) in newFiles"
                :key="i"
                class="flex items-center gap-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2"
              >
                <IconFile size="14" class="flex-shrink-0 text-blue-400" />
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

            <!-- New links -->
            <div v-if="newLinks.length > 0" class="mb-4 space-y-1.5">
              <p
                class="mb-2 text-xs font-medium uppercase tracking-wider text-slate-500"
              >
                Link Baru
              </p>
              <div
                v-for="(url, i) in newLinks"
                :key="i"
                class="flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-3 py-2"
              >
                <IconExternalLink
                  size="14"
                  class="flex-shrink-0 text-green-500"
                />
                <span class="flex-1 truncate text-sm text-green-700">{{
                  url
                }}</span>
                <Button
                  type="button"
                  size="small"
                  severity="danger"
                  variant="text"
                  class="!p-0"
                  @click="removeNewLink(i)"
                  ><IconX size="14"
                /></Button>
              </div>
            </div>

            <!-- Add link input -->
            <div v-if="showLinkInput" class="mb-3 flex gap-2">
              <InputText
                v-model="newLinkInput"
                class="flex-1"
                placeholder="https://..."
                @keyup.enter="addLink"
              />
              <Button
                type="button"
                severity="secondary"
                variant="outlined"
                size="small"
                @click="addLink"
                >Tambah</Button
              >
              <Button
                type="button"
                severity="secondary"
                variant="text"
                size="small"
                @click="showLinkInput = false"
                ><IconX size="14"
              /></Button>
            </div>

            <!-- Add buttons -->
            <div class="flex gap-2">
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
                class="flex-1"
                @click="fileInput?.click()"
              >
                <IconUpload size="15" class="mr-1.5" />Tambah File
              </Button>
              <Button
                type="button"
                severity="secondary"
                variant="outlined"
                class="flex-1"
                @click="showLinkInput = !showLinkInput"
              >
                <IconLink size="15" class="mr-1.5" />Tambah Link
              </Button>
            </div>
          </AdminFormSection>
        </div>

        <div class="flex flex-col gap-3">
          <Link
            :href="route('admin.virtual-asset-guides.index')"
            class="block lg:hidden"
          >
            <Button
              severity="secondary"
              variant="outlined"
              class="w-full"
              :disabled="form.processing"
              >Batal</Button
            >
          </Link>
          <Button
            type="submit"
            class="w-full lg:hidden"
            :loading="form.processing"
          >
            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Panduan' }}
          </Button>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
