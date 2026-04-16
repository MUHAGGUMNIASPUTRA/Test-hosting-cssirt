<script setup>
import { formatDatetime } from '@/utils/date'
import { router, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      confidentiality: null,
      integrity: null,
      availability: null,
    }),
  },
  errors: { type: Object, default: () => ({}) },
  assetType: { type: String, default: null },
  assetId: { type: String, default: null },
  securityNotes: { type: Array, default: () => [] },
  readonlyScores: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

// --- CIA scoring ---
const update = (key, val) =>
  emit('update:modelValue', { ...props.modelValue, [key]: val })

const dims = [
  { key: 'confidentiality', label: 'Kerahasiaan (C)' },
  { key: 'integrity', label: 'Integritas (I)' },
  { key: 'availability', label: 'Ketersediaan (A)' },
]

const scoreOptions = [1, 2, 3, 4, 5]

const total = computed(() => {
  const { confidentiality, integrity, availability } = props.modelValue
  if (!confidentiality && !integrity && !availability) return null
  return (confidentiality || 0) + (integrity || 0) + (availability || 0)
})

const totalSeverity = computed(() => {
  const t = total.value
  if (!t) return 'secondary'
  if (t <= 5) return 'success'
  if (t <= 10) return 'warn'
  return 'danger'
})

const scoreSeverity = (score) => {
  if (!score) return 'secondary'
  if (score <= 2) return 'success'
  if (score === 3) return 'warn'
  return 'danger'
}

// --- Add note form ---
const attachmentMode = ref('none')
const addFileInput = ref(null)

const addForm = useForm({
  message: '',
  attachment_type: null,
  attachment_file: null,
  attachment_link: '',
})

const onAddFileChange = (e) => {
  addForm.attachment_file = e.target.files[0] ?? null
}

const submitAdd = () => {
  addForm.attachment_type =
    attachmentMode.value !== 'none' ? attachmentMode.value : null
  addForm.post(
    route('admin.assets.security-notes.store', {
      assetType: props.assetType,
      assetId: props.assetId,
    }),
    {
      preserveScroll: true,
      forceFormData: attachmentMode.value === 'file',
      onSuccess: () => {
        addForm.reset()
        attachmentMode.value = 'none'
        if (addFileInput.value) addFileInput.value.value = ''
      },
    },
  )
}

// --- Edit note ---
const editingNoteId = ref(null)
const editAttachmentMode = ref('none')
const editFileInput = ref(null)

const editForm = useForm({
  message: '',
  attachment_type: null,
  attachment_file: null,
  attachment_link: '',
})

const startEdit = (note) => {
  editingNoteId.value = note.id
  editForm.message = note.message
  editForm.attachment_file = null
  if (note.attachment?.type === 'link') {
    editAttachmentMode.value = 'link'
    editForm.attachment_link = note.attachment.url ?? ''
  } else if (note.attachment?.type === 'file') {
    editAttachmentMode.value = 'file'
    editForm.attachment_link = ''
  } else {
    editAttachmentMode.value = 'none'
    editForm.attachment_link = ''
  }
  editForm.clearErrors()
}

const cancelEdit = () => {
  editingNoteId.value = null
  editForm.reset()
  editAttachmentMode.value = 'none'
}

const onEditFileChange = (e) => {
  editForm.attachment_file = e.target.files[0] ?? null
}

const submitEdit = (note) => {
  editForm.attachment_type =
    editAttachmentMode.value !== 'none' ? editAttachmentMode.value : 'none'
  editForm.put(
    route('admin.assets.security-notes.update', { securityNote: note.id }),
    {
      preserveScroll: true,
      forceFormData: editAttachmentMode.value === 'file',
      onSuccess: () => {
        editingNoteId.value = null
        editForm.reset()
        editAttachmentMode.value = 'none'
      },
    },
  )
}

// --- Delete note ---
const deleteNote = (noteId) => {
  router.delete(
    route('admin.assets.security-notes.destroy', { securityNote: noteId }),
    {
      preserveScroll: true,
    },
  )
}

// --- Helpers ---
const isEdited = (note) =>
  note.updated_at &&
  note.created_at &&
  new Date(note.updated_at) > new Date(note.created_at)
</script>

<template>
  <AdminFormSection
    title="Klasifikasi Keamanan"
    description="Skor CIA dan catatan keamanan aset"
    color="rose"
  >
    <template #icon="{ iconClass }">
      <IconShieldLock :class="iconClass" />
    </template>

    <template #extra>
      <Tag
        v-if="total !== null"
        :value="`Skor: ${total}/15`"
        :severity="totalSeverity"
      />
    </template>

    <div class="space-y-5">
      <!-- CIA Score buttons / read-only display -->
      <div v-for="dim in dims" :key="dim.key">
        <div class="mb-2 flex items-center justify-between">
          <label class="text-sm font-medium text-slate-700">{{
            dim.label
          }}</label>
          <Tag
            v-if="modelValue[dim.key]"
            :value="String(modelValue[dim.key])"
            :severity="scoreSeverity(modelValue[dim.key])"
            class="!text-xs"
          />
        </div>
        <!-- Read-only mode: tampilkan skor sebagai bar statis -->
        <div v-if="readonlyScores" class="flex gap-2">
          <div
            v-for="score in scoreOptions"
            :key="score"
            class="flex h-9 w-full items-center justify-center rounded-lg border text-sm font-medium"
            :class="
              modelValue[dim.key] === score
                ? 'border-primary-500 bg-primary-500 !text-white'
                : 'border-slate-100 bg-slate-50 text-slate-300'
            "
          >
            {{ score }}
          </div>
        </div>
        <!-- Editable mode: tombol klik -->
        <div v-else class="flex gap-2">
          <button
            v-for="score in scoreOptions"
            :key="score"
            type="button"
            class="flex h-9 w-full items-center justify-center rounded-lg border text-sm font-medium transition-colors"
            :class="
              modelValue[dim.key] === score
                ? 'border-primary-500 bg-primary-500 !text-white'
                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50'
            "
            @click="update(dim.key, score)"
          >
            <span>{{ score }}</span>
          </button>
        </div>
      </div>

      <!-- Security Notes -->
      <div>
        <h4 class="mb-3 text-sm font-semibold text-slate-700">
          Catatan Keamanan
        </h4>

        <!-- Create mode: no notes available yet -->
        <p v-if="!assetId" class="text-sm text-slate-400">
          Catatan tersedia setelah data disimpan.
        </p>

        <template v-else>
          <!-- Existing notes timeline -->
          <div v-if="securityNotes.length > 0" class="mb-4 space-y-3">
            <div
              v-for="note in securityNotes"
              :key="note.id"
              class="rounded-lg border border-slate-200 bg-slate-50 p-3"
            >
              <!-- Inline edit form -->
              <template v-if="editingNoteId === note.id">
                <div class="space-y-3">
                  <Textarea
                    v-model="editForm.message"
                    rows="3"
                    class="w-full text-sm"
                    placeholder="Edit catatan..."
                  />
                  <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600"
                      >Lampiran</label
                    >
                    <div class="flex gap-2">
                      <button
                        v-for="mode in ['none', 'file', 'link']"
                        :key="mode"
                        type="button"
                        class="rounded border px-2 py-1 text-xs transition"
                        :class="
                          editAttachmentMode === mode
                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                            : 'border-slate-200 text-slate-500 hover:border-slate-300'
                        "
                        @click="editAttachmentMode = mode"
                      >
                        {{
                          mode === 'none'
                            ? 'Hapus'
                            : mode === 'file'
                              ? 'File'
                              : 'Link'
                        }}
                      </button>
                    </div>
                    <div v-if="editAttachmentMode === 'file'" class="mt-2">
                      <input
                        ref="editFileInput"
                        type="file"
                        class="w-full text-xs text-slate-600"
                        @change="onEditFileChange"
                      />
                    </div>
                    <div v-if="editAttachmentMode === 'link'" class="mt-2">
                      <InputText
                        v-model="editForm.attachment_link"
                        placeholder="https://..."
                        class="w-full text-sm"
                      />
                    </div>
                  </div>
                  <p
                    v-if="editForm.errors.message"
                    class="text-xs text-red-500"
                  >
                    {{ editForm.errors.message }}
                  </p>
                  <div class="flex gap-2">
                    <Button
                      size="small"
                      severity="primary"
                      :disabled="editForm.processing"
                      class="flex-1"
                      @click="submitEdit(note)"
                    >
                      <IconLoader3
                        v-if="editForm.processing"
                        class="animate-spin"
                        size="14"
                      />
                      <IconCheck v-else size="14" />
                      Simpan
                    </Button>
                    <Button
                      size="small"
                      severity="secondary"
                      class="flex-1"
                      @click="cancelEdit"
                    >
                      Batal
                    </Button>
                  </div>
                </div>
              </template>

              <!-- Normal display -->
              <template v-else>
                <div class="mb-2 flex items-start justify-between gap-2">
                  <div class="flex items-center gap-2">
                    <div
                      class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600"
                    >
                      {{ note.user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                    </div>
                    <div>
                      <span class="text-sm font-medium text-slate-800">{{
                        note.user?.name
                      }}</span>
                      <div
                        class="flex items-center gap-1.5 text-xs text-slate-400"
                      >
                        <span>{{ formatDatetime(note.created_at) }}</span>
                        <span v-if="isEdited(note)" class="italic">
                          · Diedit {{ formatDatetime(note.updated_at) }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="flex shrink-0 items-center gap-1">
                    <Button
                      icon="pi pi-pencil"
                      size="small"
                      severity="secondary"
                      text
                      rounded
                      v-tooltip="'Edit catatan'"
                      @click="startEdit(note)"
                    />
                    <Button
                      icon="pi pi-trash"
                      size="small"
                      severity="danger"
                      text
                      rounded
                      v-tooltip="'Hapus catatan'"
                      @click="deleteNote(note.id)"
                    />
                  </div>
                </div>

                <p
                  class="whitespace-pre-wrap text-sm leading-relaxed text-slate-700"
                >
                  {{ note.message }}
                </p>

                <div v-if="note.attachment" class="mt-2">
                  <a
                    :href="note.attachment.url"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-blue-600 hover:text-blue-800"
                  >
                    <IconExternalLink
                      v-if="note.attachment.type === 'link'"
                      size="12"
                    />
                    <IconPaperclip v-else size="12" />
                    {{
                      note.attachment.type === 'link'
                        ? 'Buka Link'
                        : note.attachment.filename || 'Lihat Lampiran'
                    }}
                  </a>
                </div>
              </template>
            </div>
          </div>

          <!-- Add new note form -->
          <div class="rounded-lg border border-slate-200 bg-white p-3">
            <p class="mb-2 text-xs font-medium text-slate-600">
              Tambah Catatan
            </p>
            <Textarea
              v-model="addForm.message"
              rows="2"
              class="mb-2 w-full text-sm"
              placeholder="Tulis catatan keamanan..."
            />

            <div class="mb-2">
              <div class="flex gap-2">
                <button
                  v-for="mode in ['none', 'file', 'link']"
                  :key="mode"
                  type="button"
                  class="rounded border px-2 py-1 text-xs transition"
                  :class="
                    attachmentMode === mode
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-slate-200 text-slate-500 hover:border-slate-300'
                  "
                  @click="attachmentMode = mode"
                >
                  {{
                    mode === 'none'
                      ? 'Tanpa Lampiran'
                      : mode === 'file'
                        ? 'File'
                        : 'Link'
                  }}
                </button>
              </div>
              <div v-if="attachmentMode === 'file'" class="mt-2">
                <input
                  ref="addFileInput"
                  type="file"
                  class="w-full text-xs text-slate-600"
                  @change="onAddFileChange"
                />
              </div>
              <div v-if="attachmentMode === 'link'" class="mt-2">
                <InputText
                  v-model="addForm.attachment_link"
                  placeholder="https://..."
                  class="w-full text-sm"
                />
              </div>
            </div>

            <p v-if="addForm.errors.message" class="mb-2 text-xs text-red-500">
              {{ addForm.errors.message }}
            </p>

            <Button
              size="small"
              :loading="addForm.processing"
              :disabled="!addForm.message.trim()"
              @click="submitAdd"
            >
              <IconPlus size="14" class="mr-1" />
              Tambah Catatan
            </Button>
          </div>
        </template>
      </div>
    </div>
  </AdminFormSection>
</template>
