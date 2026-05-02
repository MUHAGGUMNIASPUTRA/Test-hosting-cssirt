<!-- Tujuan: Form section file dokumen (Word link + PDF upload/link + reference number) -->
<!-- Caller: Documents/Create.vue -->
<!-- Side Effects: emit update:modelValue, file upload handling -->

<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  isEditMode: {
    type: Boolean,
    default: false,
  },
  existingOfficialAttachment: {
    type: Object,
    default: null,
  },
  isStageFinal: {
    type: Boolean,
    default: false,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['update:modelValue'])

const detectOfficialFileMode = () => {
  return props.existingOfficialAttachment?.type ?? 'file'
}

const officialFileMode = ref(detectOfficialFileMode())
const officialUploader = ref(null)
const officialFileName = ref(null)

const updateField = (field, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}

const setOfficialFileMode = (mode) => {
  officialFileMode.value = mode
  updateField('official_file_type', mode)
  updateField('official_file', null)
  updateField('official_file_link', '')
  officialFileName.value = null
  if (officialUploader.value) officialUploader.value.clear()
}

const handleOfficialFileSelect = (event) => {
  const file = event.files[0]
  updateField('official_file', file)
  officialFileName.value = file?.name || null
}

const clearOfficialFile = () => {
  if (officialUploader.value) officialUploader.value.clear()
  updateField('official_file', null)
  officialFileName.value = null
}
</script>

<template>
  <!-- File Dokumen (Word) -->
  <AdminFormSection
    title="File Dokumen"
    description="Link dokumen Word — hanya terlihat oleh admin"
    color="amber"
  >
    <template #icon="{ iconClass }">
      <IconFileWord :class="iconClass" />
    </template>
    <template #extra>
      <Tag value="Admin Only" severity="warning" size="small" />
    </template>
    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700">
        Link Dokumen Word
        <span class="font-normal text-slate-400">(Opsional)</span>
      </label>
      <div class="flex gap-2">
        <InputText
          :model-value="modelValue.doc_file_link"
          class="w-full flex-1"
          :class="{ 'p-invalid': errors.doc_file_link }"
          placeholder="https://docs.google.com/document/d/... atau URL lainnya"
          @update:model-value="updateField('doc_file_link', $event)"
        />
        <a
          v-if="modelValue.doc_file_link"
          :href="modelValue.doc_file_link"
          target="_blank"
          rel="noopener noreferrer"
          class="flex flex-shrink-0 items-center gap-1 rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-200"
        >
          <IconExternalLink size="15" />
        </a>
      </div>
      <p class="text-xs text-slate-500">
        <IconInfoCircle size="12" class="mr-1 inline" />
        Link ini hanya dapat diakses oleh admin, tidak ditampilkan ke publik.
      </p>
      <small v-if="errors.doc_file_link" class="p-error block">
        {{ errors.doc_file_link }}
      </small>
    </div>
  </AdminFormSection>

  <!-- File Dokumen Sah (PDF) -->
  <AdminFormSection
    title="File Dokumen Sah"
    description="File PDF resmi"
    color="green"
  >
    <template #icon="{ iconClass }">
      <IconFileCertificate :class="iconClass" />
    </template>

    <!-- Mode Toggle -->
    <div
      class="mb-4 flex w-fit overflow-hidden rounded-lg border border-slate-300"
    >
      <button
        type="button"
        @click="setOfficialFileMode('file')"
        class="flex items-center gap-2 px-4 py-2 text-sm font-medium transition-colors"
        :class="
          officialFileMode === 'file'
            ? 'bg-blue-600 text-white'
            : 'bg-white text-slate-600 hover:bg-slate-50'
        "
      >
        <IconUpload size="14" />
        Upload PDF
      </button>
      <button
        type="button"
        @click="setOfficialFileMode('link')"
        class="flex items-center gap-2 border-l border-slate-300 px-4 py-2 text-sm font-medium transition-colors"
        :class="
          officialFileMode === 'link'
            ? 'bg-blue-600 text-white'
            : 'bg-white text-slate-600 hover:bg-slate-50'
        "
      >
        <IconLink size="14" />
        Kirim Link
      </button>
    </div>

    <!-- Existing file info (edit mode) -->
    <div
      v-if="
        isEditMode &&
        existingOfficialAttachment &&
        !modelValue.official_file &&
        officialFileMode === 'file'
      "
      class="mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3"
    >
      <IconFileCertificate size="16" class="flex-shrink-0 text-slate-500" />
      <div class="min-w-0 flex-1">
        <p class="text-sm text-slate-600">File saat ini:</p>
        <p class="truncate text-sm font-medium text-blue-600">
          {{ existingOfficialAttachment.filename }}
        </p>
      </div>
      <p class="flex-shrink-0 text-xs text-slate-400">
        Upload baru untuk mengganti
      </p>
    </div>

    <!-- File Upload -->
    <div v-if="officialFileMode === 'file'">
      <FileUpload
        ref="officialUploader"
        name="official_file"
        @select="handleOfficialFileSelect"
        :showUploadButton="false"
        :showCancelButton="false"
        :multiple="false"
        accept=".pdf"
        :maxFileSize="52428800"
        :class="{
          'rounded-lg border border-red-400': errors.official_file,
        }"
      >
        <template #content="{ files }">
          <div v-if="files[0]" class="bg-slate-50 p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div
                  class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-white shadow-sm"
                >
                  <IconFileCertificate size="20" class="text-green-500" />
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-900">
                    {{ files[0].name }}
                  </p>
                  <p class="text-xs text-slate-500">
                    {{ (files[0].size / 1024 / 1024).toFixed(2) }}
                    MB
                  </p>
                </div>
              </div>
              <button
                type="button"
                @click="clearOfficialFile"
                class="flex h-7 w-7 items-center justify-center rounded-md bg-red-100 transition-colors hover:bg-red-200"
              >
                <IconX size="14" class="text-red-600" />
              </button>
            </div>
          </div>
        </template>
        <template #empty>
          <div
            class="rounded-lg border-2 border-dashed border-slate-300 p-8 text-center transition-colors hover:border-green-400"
          >
            <IconCloudUpload size="36" class="mx-auto mb-3 text-slate-400" />
            <p class="mb-1 font-medium text-slate-600">
              Seret file PDF atau klik untuk memilih
            </p>
            <p class="text-sm text-slate-400">PDF saja (Maks. 50MB)</p>
          </div>
        </template>
      </FileUpload>
      <small
        v-if="
          isStageFinal &&
          !modelValue.official_file &&
          !(isEditMode && existingOfficialAttachment?.type === 'file')
        "
        class="mt-1 block text-amber-600"
      >
        File PDF Dokumen Sah wajib diupload untuk stage Final.
      </small>
      <small v-if="errors.official_file" class="p-error mt-1 block">
        {{ errors.official_file }}
      </small>
    </div>

    <!-- Link Input -->
    <div v-else class="space-y-2">
      <label class="block text-sm font-medium text-gray-700">
        Link PDF
        <span v-if="isStageFinal" class="text-red-500">*</span>
        <span v-else class="font-normal text-slate-400">(Opsional)</span>
      </label>
      <InputText
        :model-value="modelValue.official_file_link"
        class="w-full"
        :class="{ 'p-invalid': errors.official_file_link }"
        placeholder="https://example.com/dokumen-sah.pdf"
        :required="isStageFinal"
        @update:model-value="updateField('official_file_link', $event)"
      />
      <p class="text-sm text-slate-500">
        <IconInfoCircle size="13" class="mr-1 inline" />
        Pastikan link mengarah ke file PDF yang dapat diakses.
      </p>
      <small
        v-if="isStageFinal && !modelValue.official_file_link"
        class="block text-amber-600"
      >
        File Dokumen Sah wajib diisi untuk stage Final.
      </small>
      <small v-if="errors.official_file_link" class="p-error block">
        {{ errors.official_file_link }}
      </small>
    </div>

    <!-- Nomor Referensi -->
    <div class="mt-4 space-y-2 border-t border-slate-100 pt-4">
      <label class="block text-sm font-medium text-gray-700">
        Nomor Referensi
        <span v-if="isStageFinal" class="text-red-500">*</span>
        <span v-else class="font-normal text-slate-400">(Opsional)</span>
      </label>
      <InputText
        :model-value="modelValue.reference_number"
        class="w-full"
        :class="{ 'p-invalid': errors.reference_number }"
        placeholder="Contoh: No. 001/CSIRT/BPN/2024"
        :required="isStageFinal"
        @update:model-value="updateField('reference_number', $event)"
      />
      <small
        v-if="isStageFinal && !modelValue.reference_number"
        class="block text-amber-600"
      >
        Nomor Referensi wajib diisi untuk stage Final.
      </small>
      <small v-if="errors.reference_number" class="p-error block">
        {{ errors.reference_number }}
      </small>
    </div>
  </AdminFormSection>
</template>
