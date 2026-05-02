<script setup>
import { formatDatetime } from '@/utils/date'
import { ref } from 'vue'

const props = defineProps({
  note: { type: Object, required: true },
})

const emit = defineEmits(['refresh'])

const isEditing = ref(false)
const editMessage = ref('')
const editAttachmentMode = ref('none')
const editAttachmentLink = ref('')
const editAttachmentFile = ref(null)

const isSubmitting = ref(false)
const errors = ref({})

const isEdited = (note) =>
  note.updated_at &&
  note.created_at &&
  new Date(note.updated_at) > new Date(note.created_at)

const startEdit = () => {
  isEditing.value = true
  editMessage.value = props.note.message
  editAttachmentFile.value = null
  errors.value = {}
  if (props.note.attachment?.type === 'link') {
    editAttachmentMode.value = 'link'
    editAttachmentLink.value = props.note.attachment.url ?? ''
  } else if (props.note.attachment?.type === 'file') {
    editAttachmentMode.value = 'file'
    editAttachmentLink.value = ''
  } else {
    editAttachmentMode.value = 'none'
    editAttachmentLink.value = ''
  }
}

const cancelEdit = () => {
  isEditing.value = false
  errors.value = {}
}

const onFileChange = (e) => {
  editAttachmentFile.value = e.target.files[0] ?? null
}

const submitEdit = async () => {
  errors.value = {}
  isSubmitting.value = true

  try {
    let payload
    if (editAttachmentMode.value === 'file' && editAttachmentFile.value) {
      payload = new FormData()
      payload.append('message', editMessage.value)
      payload.append('attachment_type', 'file')
      payload.append('attachment_file', editAttachmentFile.value)
      payload.append('_method', 'PUT')
      await window.axios.post(
        route('admin.assets.security-notes.update', {
          securityNote: props.note.id,
        }),
        payload,
      )
    } else {
      await window.axios.put(
        route('admin.assets.security-notes.update', {
          securityNote: props.note.id,
        }),
        {
          message: editMessage.value,
          remove_attachment: editAttachmentMode.value === 'none',
          attachment_type: editAttachmentMode.value === 'link' ? 'link' : null,
          attachment_link:
            editAttachmentMode.value === 'link'
              ? editAttachmentLink.value
              : null,
        },
      )
    }

    isEditing.value = false
    emit('refresh')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors ?? {}
    }
  } finally {
    isSubmitting.value = false
  }
}

const deleteNote = async () => {
  try {
    await window.axios.delete(
      route('admin.assets.security-notes.destroy', {
        securityNote: props.note.id,
      }),
    )
    emit('refresh')
  } catch {
    // silently ignore
  }
}
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
    <!-- Inline edit form -->
    <template v-if="isEditing">
      <div class="space-y-3">
        <Textarea
          v-model="editMessage"
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
                mode === 'none' ? 'Hapus' : mode === 'file' ? 'File' : 'Link'
              }}
            </button>
          </div>
          <div v-if="editAttachmentMode === 'file'" class="mt-2">
            <input
              type="file"
              class="w-full text-xs text-slate-600"
              @change="onFileChange"
            />
          </div>
          <div v-if="editAttachmentMode === 'link'" class="mt-2">
            <InputText
              v-model="editAttachmentLink"
              placeholder="https://..."
              class="w-full text-sm"
            />
          </div>
        </div>
        <p v-if="errors.message" class="text-xs text-red-500">
          {{ errors.message?.[0] }}
        </p>
        <p v-if="errors.attachment_file" class="text-xs text-red-500">
          {{ errors.attachment_file?.[0] }}
        </p>
        <p v-if="errors.attachment_link" class="text-xs text-red-500">
          {{ errors.attachment_link?.[0] }}
        </p>
        <div class="flex gap-2">
          <Button
            size="small"
            severity="primary"
            :disabled="
              isSubmitting ||
              (editAttachmentMode === 'link' && !editAttachmentLink.trim()) ||
              (editAttachmentMode === 'file' &&
                !editAttachmentFile &&
                !note.attachment)
            "
            class="flex-1"
            @click="submitEdit"
          >
            <IconLoader3 v-if="isSubmitting" class="animate-spin" size="14" />
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
            <div class="flex items-center gap-1.5 text-xs text-slate-400">
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
            @click="startEdit"
          />
          <Button
            icon="pi pi-trash"
            size="small"
            severity="danger"
            text
            rounded
            v-tooltip="'Hapus catatan'"
            @click="deleteNote"
          />
        </div>
      </div>

      <p class="whitespace-pre-wrap text-sm leading-relaxed text-slate-700">
        {{ note.message }}
      </p>

      <div v-if="note.attachment" class="mt-2">
        <a
          :href="note.attachment.url"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-blue-600 hover:text-blue-800"
        >
          <IconExternalLink v-if="note.attachment.type === 'link'" size="12" />
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
</template>
