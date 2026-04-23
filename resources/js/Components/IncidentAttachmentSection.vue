<!-- Tujuan: Input lampiran insiden (toggle file/link) — konten untuk slot #attachment IncidentDetailSection -->
<!-- Caller: Admin/Incidents/Create.vue -->
<!-- Side Effects: none -->
<script setup>
import { ref } from 'vue'

const props = defineProps({
  isEditing: { type: Boolean, default: false },
  existingAttachment: { type: Object, default: null },
  attachmentType: { type: String, default: 'file' },
  attachmentLinks: { type: String, default: '' },
})

const emit = defineEmits([
  'update:attachmentType',
  'update:attachment',
  'update:attachmentLinks',
])

const attachmentMode = ref(props.attachmentType)
const attachmentPreview = ref(null)
const uploader = ref(null)

const setAttachmentMode = (mode) => {
  attachmentMode.value = mode
  emit('update:attachmentType', mode)
  emit('update:attachment', null)
  emit('update:attachmentLinks', '')
  if (mode === 'file') {
    attachmentPreview.value = null
    if (uploader.value) uploader.value.clear()
  }
}

const handleFileSelect = (event) => {
  const file = event.files[0]
  emit('update:attachment', file)
  attachmentPreview.value = file?.type.startsWith('image/')
    ? URL.createObjectURL(file)
    : null
}

const clearAttachment = () => {
  if (uploader.value) uploader.value.clear()
  emit('update:attachment', null)
  attachmentPreview.value = null
}
</script>

<template>
  <div>
    <label class="mb-2 block font-medium text-slate-700">
      Lampiran Bukti <span class="font-normal text-slate-400">(Opsional)</span>
    </label>
    <div
      class="mb-3 flex w-fit overflow-hidden rounded-lg border border-slate-300"
    >
      <button
        type="button"
        class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium transition-colors"
        :class="
          attachmentMode === 'file'
            ? 'bg-blue-600 text-white'
            : 'bg-white text-slate-600 hover:bg-slate-50'
        "
        @click="setAttachmentMode('file')"
      >
        <IconUpload size="14" />Upload Dokumen
      </button>
      <button
        type="button"
        class="flex items-center gap-1.5 border-l border-slate-300 px-3 py-1.5 text-sm font-medium transition-colors"
        :class="
          attachmentMode === 'link'
            ? 'bg-blue-600 text-white'
            : 'bg-white text-slate-600 hover:bg-slate-50'
        "
        @click="setAttachmentMode('link')"
      >
        <IconLink size="14" />Kirim Link
      </button>
    </div>
    <div
      v-if="isEditing && existingAttachment && attachmentMode === 'file'"
      class="mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3"
    >
      <IconPaperclip size="16" class="flex-shrink-0 text-slate-500" />
      <div class="min-w-0 flex-1">
        <p class="text-sm text-slate-600">Lampiran saat ini:</p>
        <p class="truncate text-sm font-medium text-blue-600">
          {{ existingAttachment.filename }}
        </p>
      </div>
      <p class="text-xs text-slate-400">Upload baru untuk mengganti</p>
    </div>
    <div v-if="attachmentMode === 'file'">
      <FileUpload
        ref="uploader"
        name="attachment"
        :show-upload-button="false"
        :show-cancel-button="false"
        :multiple="false"
        accept=".jpg,.jpeg,.png,.pdf,.zip,.doc,.docx"
        :max-file-size="5000000"
        @select="handleFileSelect"
      >
        <template #content="{ files }">
          <div v-if="files[0]" class="bg-slate-50 p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div
                  class="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white shadow-sm"
                >
                  <img
                    v-if="attachmentPreview"
                    :src="attachmentPreview"
                    :alt="files[0].name"
                    class="h-full w-full object-cover"
                  />
                  <IconFile v-else size="20" class="text-slate-400" />
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-900">
                    {{ files[0].name }}
                  </p>
                  <p class="text-xs text-slate-500">
                    {{ (files[0].size / 1024 / 1024).toFixed(2) }} MB
                  </p>
                </div>
              </div>
              <button
                type="button"
                class="flex h-7 w-7 items-center justify-center rounded-md bg-red-100 transition-colors hover:bg-red-200"
                @click="clearAttachment"
              >
                <IconX size="14" class="text-red-600" />
              </button>
            </div>
          </div>
        </template>
        <template #empty>
          <div
            class="rounded-lg border-2 border-dashed border-slate-300 p-6 text-center"
          >
            <IconCloudUpload size="32" class="mx-auto mb-2 text-slate-400" />
            <p class="mb-1 text-sm font-medium text-slate-600">
              Seret file atau klik untuk memilih
            </p>
            <p class="text-xs text-slate-400">
              JPG, PNG, PDF, ZIP, DOC (Maks. 5MB)
            </p>
          </div>
        </template>
      </FileUpload>
    </div>
    <div v-else class="space-y-2">
      <Textarea
        :model-value="attachmentLinks"
        rows="3"
        class="w-full"
        placeholder="Masukkan URL bukti, pisahkan dengan koma jika lebih dari satu.&#10;Contoh: https://drive.google.com/file/xxx, https://example.com/screenshot.png"
        @update:model-value="$emit('update:attachmentLinks', $event)"
      />
      <p class="text-xs text-slate-500">
        <IconInfoCircle size="12" class="mr-1 inline" />
        Untuk beberapa link, pisahkan dengan koma (,)
      </p>
    </div>
  </div>
</template>
