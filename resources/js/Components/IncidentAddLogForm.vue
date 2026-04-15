<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  incidentId: [Number, String],
  isClosed: Boolean,
})

const logAttachmentMode = ref('none')
const logFileInput = ref(null)

const logForm = useForm({
  log_message: '',
  is_public: false,
  attachment_type: null,
  attachment: null,
  attachment_link: '',
})

const onLogFileChange = (e) => {
  logForm.attachment = e.target.files[0] ?? null
}

const submitLog = () => {
  logForm.attachment_type =
    logAttachmentMode.value !== 'none' ? logAttachmentMode.value : null
  logForm.post(route('admin.incidents.logs.store', props.incidentId), {
    preserveScroll: true,
    forceFormData: logAttachmentMode.value === 'file',
    onSuccess: () => {
      logForm.reset()
      logForm.attachment = null
      logAttachmentMode.value = 'none'
      if (logFileInput.value) logFileInput.value.value = ''
    },
  })
}
</script>

<template>
  <div
    v-if="!isClosed"
    class="mb-5 rounded-lg border border-blue-100 bg-blue-50 p-4"
  >
    <h4
      class="mb-3 flex items-center gap-2 text-sm font-semibold text-slate-800"
    >
      <IconSticker2 size="16" class="text-blue-600" />
      Tambah Catatan Penanganan
    </h4>

    <form @submit.prevent="submitLog" class="space-y-3">
      <div>
        <Textarea
          v-model="logForm.log_message"
          placeholder="Tulis catatan penanganan, update progres, atau informasi penting..."
          rows="4"
          class="w-full text-sm"
          :class="{ 'border-red-300': logForm.errors.log_message }"
          required
        />
        <p v-if="logForm.errors.log_message" class="mt-1 text-xs text-red-500">
          {{ logForm.errors.log_message }}
        </p>
      </div>

      <!-- Attachment mode selector -->
      <div>
        <label class="mb-1.5 block text-xs font-medium text-slate-600">
          Lampiran (opsional)
        </label>
        <div class="flex gap-2">
          <button
            v-for="mode in ['none', 'file', 'link']"
            :key="mode"
            type="button"
            class="rounded border px-2.5 py-1 text-xs font-medium transition"
            :class="
              logAttachmentMode === mode
                ? 'border-blue-500 bg-blue-600 text-white'
                : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:text-slate-700'
            "
            @click="logAttachmentMode = mode"
          >
            {{
              mode === 'none'
                ? 'Tanpa Lampiran'
                : mode === 'file'
                  ? 'Upload File'
                  : 'Link URL'
            }}
          </button>
        </div>
        <div v-if="logAttachmentMode === 'file'" class="mt-2">
          <input
            ref="logFileInput"
            type="file"
            class="w-full rounded border border-slate-200 bg-white p-1.5 text-xs text-slate-600 file:mr-2 file:rounded file:border-0 file:bg-blue-50 file:px-2 file:py-1 file:text-xs file:text-blue-600"
            @change="onLogFileChange"
          />
          <p v-if="logForm.errors.attachment" class="mt-1 text-xs text-red-500">
            {{ logForm.errors.attachment }}
          </p>
        </div>
        <div v-if="logAttachmentMode === 'link'" class="mt-2">
          <InputText
            v-model="logForm.attachment_link"
            placeholder="https://..."
            class="w-full text-sm"
          />
          <p
            v-if="logForm.errors.attachment_link"
            class="mt-1 text-xs text-red-500"
          >
            {{ logForm.errors.attachment_link }}
          </p>
        </div>
      </div>

      <!-- is_public toggle -->
      <label class="flex cursor-pointer items-center gap-2 text-sm">
        <ToggleSwitch v-model="logForm.is_public" />
        <div>
          <span class="font-medium text-slate-700">Tampilkan ke pelapor</span>
          <p class="text-xs text-slate-500">
            Catatan publik akan terlihat di halaman tiket pelapor
          </p>
        </div>
      </label>

      <Button
        type="submit"
        severity="primary"
        :disabled="logForm.processing"
        class="w-full"
      >
        <IconLoader3 v-if="logForm.processing" class="animate-spin" size="16" />
        <IconSticker2 v-else size="16" />
        {{ logForm.processing ? 'Menambahkan...' : 'Tambah Catatan' }}
      </Button>
    </form>
  </div>
</template>
