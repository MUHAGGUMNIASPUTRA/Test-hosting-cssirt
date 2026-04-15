<script setup>
import { formatDatetime } from '@/utils/date'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  log: Object,
  incidentId: [Number, String],
})

const emit = defineEmits(['request-delete'])

// --- Edit state ---
const isEditing = ref(false)
const editAttachmentMode = ref('none')
const editFileInput = ref(null)

const editForm = useForm({
  log_message: '',
  is_public: false,
  attachment_type: null,
  attachment: null,
  attachment_link: '',
})

const startEdit = () => {
  isEditing.value = true
  editForm.log_message = props.log.log_message
  editForm.is_public = props.log.is_public
  editForm.attachment = null
  if (props.log.attachment?.type === 'link') {
    editAttachmentMode.value = 'link'
    editForm.attachment_link = props.log.attachment.url ?? ''
  } else if (props.log.attachment?.type === 'file') {
    editAttachmentMode.value = 'file'
    editForm.attachment_link = ''
  } else {
    editAttachmentMode.value = 'none'
    editForm.attachment_link = ''
  }
}

const cancelEdit = () => {
  isEditing.value = false
  editForm.reset()
  editAttachmentMode.value = 'none'
}

const onEditFileChange = (e) => {
  editForm.attachment = e.target.files[0] ?? null
}

const submitEdit = () => {
  editForm.attachment_type =
    editAttachmentMode.value !== 'none' ? editAttachmentMode.value : 'none'
  editForm.put(
    route('admin.incidents.logs.update', {
      incident: props.incidentId,
      log: props.log.id,
    }),
    {
      preserveScroll: true,
      forceFormData: editAttachmentMode.value === 'file',
      onSuccess: () => {
        isEditing.value = false
        editForm.reset()
        editAttachmentMode.value = 'none'
      },
    },
  )
}

// --- Helpers ---
const isEdited = (log) =>
  log.updated_at &&
  log.created_at &&
  new Date(log.updated_at) > new Date(log.created_at)

const logAttachmentUrl = (log) => log.attachment?.url ?? null

const logAttachmentLabel = (log) => {
  if (!log.attachment) return ''
  if (log.attachment.type === 'link') return 'Buka Link'
  return log.attachment.filename || 'Lihat Lampiran'
}
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
    <!-- Inline edit form -->
    <template v-if="isEditing">
      <div class="space-y-3">
        <Textarea
          v-model="editForm.log_message"
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

        <label class="flex cursor-pointer items-center gap-2 text-sm">
          <ToggleSwitch v-model="editForm.is_public" />
          <span class="text-slate-600">Tampilkan ke pelapor (publik)</span>
        </label>

        <p v-if="editForm.errors.log_message" class="text-xs text-red-500">
          {{ editForm.errors.log_message }}
        </p>

        <div class="flex gap-2">
          <Button
            size="small"
            severity="primary"
            :disabled="editForm.processing"
            @click="submitEdit"
            class="flex-1"
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
            @click="cancelEdit"
            class="flex-1"
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
            {{ log.user?.name?.charAt(0)?.toUpperCase() ?? '?' }}
          </div>
          <div>
            <span class="text-sm font-medium text-slate-800">{{
              log.user?.name
            }}</span>
            <div class="flex items-center gap-1.5 text-xs text-slate-400">
              <span>{{ formatDatetime(log.created_at) }}</span>
              <span v-if="isEdited(log)" class="italic">
                · Diedit {{ formatDatetime(log.updated_at) }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex shrink-0 items-center gap-1">
          <Tag
            :value="log.is_public ? 'Publik' : 'Internal'"
            :severity="log.is_public ? 'success' : 'secondary'"
            class="!text-xs"
          />
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
            @click="emit('request-delete', log.id)"
          />
        </div>
      </div>

      <p class="whitespace-pre-wrap text-sm leading-relaxed text-slate-700">
        {{ log.log_message }}
      </p>

      <div v-if="log.attachment" class="mt-2">
        <a
          :href="logAttachmentUrl(log)"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-blue-600 hover:text-blue-800"
        >
          <IconExternalLink v-if="log.attachment.type === 'link'" size="12" />
          <IconPaperclip v-else size="12" />
          {{ logAttachmentLabel(log) }}
        </a>
      </div>
    </template>
  </div>
</template>
