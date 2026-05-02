<script setup>
import { ref } from 'vue'

const props = defineProps({
  assetType: { type: String, required: true },
  assetId: { type: String, required: true },
})

const emit = defineEmits(['refresh'])

const message = ref('')
const attachmentMode = ref('none')
const attachmentLink = ref('')
const attachmentFile = ref(null)
const fileInput = ref(null)
const isSubmitting = ref(false)
const errors = ref({})

const onFileChange = (e) => {
  attachmentFile.value = e.target.files[0] ?? null
}

const reset = () => {
  message.value = ''
  attachmentMode.value = 'none'
  attachmentLink.value = ''
  attachmentFile.value = null
  errors.value = {}
  if (fileInput.value) fileInput.value.value = ''
}

const submit = async () => {
  errors.value = {}
  isSubmitting.value = true

  try {
    let payload
    if (attachmentMode.value === 'file' && attachmentFile.value) {
      payload = new FormData()
      payload.append('message', message.value)
      payload.append('attachment_type', 'file')
      payload.append('attachment_file', attachmentFile.value)
    } else {
      payload = {
        message: message.value,
        attachment_type:
          attachmentMode.value !== 'none' ? attachmentMode.value : null,
        attachment_link:
          attachmentMode.value === 'link' ? attachmentLink.value : null,
      }
    }

    await window.axios.post(
      route('admin.assets.security-notes.store', {
        assetType: props.assetType,
        assetId: props.assetId,
      }),
      payload,
    )

    reset()
    emit('refresh')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors ?? {}
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white p-3">
    <p class="mb-2 text-xs font-medium text-slate-600">Tambah Catatan</p>
    <Textarea
      v-model="message"
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
          ref="fileInput"
          type="file"
          class="w-full text-xs text-slate-600"
          @change="onFileChange"
        />
      </div>
      <div v-if="attachmentMode === 'link'" class="mt-2">
        <InputText
          v-model="attachmentLink"
          placeholder="https://..."
          class="w-full text-sm"
        />
      </div>
    </div>

    <p v-if="errors.message" class="mb-2 text-xs text-red-500">
      {{ errors.message?.[0] }}
    </p>
    <p v-if="errors.attachment_file" class="mb-2 text-xs text-red-500">
      {{ errors.attachment_file?.[0] }}
    </p>
    <p v-if="errors.attachment_link" class="mb-2 text-xs text-red-500">
      {{ errors.attachment_link?.[0] }}
    </p>

    <Button
      size="small"
      :loading="isSubmitting"
      :disabled="
        !message.trim() ||
        (attachmentMode === 'file' && !attachmentFile) ||
        (attachmentMode === 'link' && !attachmentLink.trim())
      "
      @click="submit"
    >
      <IconPlus size="14" class="mr-1" />
      Tambah Catatan
    </Button>
  </div>
</template>
