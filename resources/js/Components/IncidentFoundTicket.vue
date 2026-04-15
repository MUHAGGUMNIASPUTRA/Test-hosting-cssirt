<script setup>
import { formatDatetime } from '@/utils/date'
import {
  IconFile,
  IconFileTypeDoc,
  IconFileTypeDocx,
  IconFileTypeJpg,
  IconFileTypePdf,
  IconFileTypePng,
  IconFileTypeZip,
} from '@tabler/icons-vue'

const props = defineProps({
  ticket: Object, // page.props.flash.incident_found
})

defineEmits(['reset'])

const getStatusSeverity = (status) => {
  const map = {
    Baru: 'info',
    Diverifikasi: 'primary',
    'Dalam Penyelidikan': 'warn',
    Selesai: 'success',
    Ditutup: 'secondary',
  }
  return map[status] || 'info'
}

const getPrioritySeverity = (priority) => {
  const map = {
    Rendah: 'success',
    Sedang: 'info',
    Tinggi: 'warn',
    Kritikal: 'danger',
  }
  return map[priority] || 'info'
}

const getFileIcon = (filename) => {
  if (!filename) return [IconFile, 'bg-slate-100', 'text-slate-600']
  const ext = filename.split('.').pop().toLowerCase()
  const iconMap = {
    pdf: [IconFileTypePdf, 'bg-red-100', 'text-red-600'],
    doc: [IconFileTypeDoc, 'bg-blue-100', 'text-blue-600'],
    docx: [IconFileTypeDocx, 'bg-blue-100', 'text-blue-600'],
    zip: [IconFileTypeZip, 'bg-yellow-100', 'text-yellow-600'],
    jpg: [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    jpeg: [IconFileTypeJpg, 'bg-green-100', 'text-green-600'],
    png: [IconFileTypePng, 'bg-green-100', 'text-green-600'],
  }
  return iconMap[ext] || [IconFile, 'bg-slate-100', 'text-slate-600']
}

const logAttachmentUrl = (log) => log.attachment?.url ?? null
</script>

<template>
  <!-- Search Result -->
  <div class="rounded-xl border-slate-200 bg-white p-6 pt-0 sm:border sm:pt-6">
    <h3 class="mb-6 flex items-center text-lg font-semibold text-slate-900">
      <IconCircleCheck size="18" class="mr-2 text-green-600" />
      Tiket Ditemukan
    </h3>

    <div class="space-y-6">
      <!-- Basic Information -->
      <div class="space-y-4">
        <div class="grid grid-cols-1 gap-4">
          <div>
            <p class="text-slate-600">ID Tiket</p>
            <p class="font-mono font-semibold">{{ ticket.case_id }}</p>
          </div>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <p class="text-slate-600">Status</p>
            <Tag
              :value="ticket.status"
              :severity="getStatusSeverity(ticket.status)"
            />
          </div>
          <div>
            <p class="text-slate-600">Prioritas</p>
            <Tag
              :value="ticket.priority"
              :severity="getPrioritySeverity(ticket.priority)"
            />
          </div>
          <div>
            <p class="text-slate-600">Kategori</p>
            <p class="font-medium">{{ ticket.incident_type?.name }}</p>
          </div>
          <div>
            <p class="text-slate-600">Dilaporkan</p>
            <p class="font-medium">{{ formatDatetime(ticket.reported_at) }}</p>
          </div>
          <div v-if="ticket.assigned_user">
            <p class="text-slate-600">Ditangani Oleh</p>
            <p class="font-medium">{{ ticket.assigned_user.name }}</p>
          </div>
        </div>
      </div>

      <!-- Attachment Section -->
      <div v-if="ticket.attachment" class="border-t border-slate-200 pt-4">
        <h4 class="mb-3 flex items-center text-lg font-semibold text-slate-900">
          <IconPaperclip size="18" class="mr-2 text-blue-600" />
          Lampiran
        </h4>
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <!-- Link type: external link icon -->
              <div
                v-if="ticket.attachment.type === 'link'"
                class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100"
              >
                <IconExternalLink class="text-blue-600" size="18" />
              </div>
              <!-- File type: file-type icon based on extension -->
              <div
                v-else
                class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg"
                :class="getFileIcon(ticket.attachment.filename)[1]"
              >
                <component
                  :is="getFileIcon(ticket.attachment.filename)[0]"
                  :class="getFileIcon(ticket.attachment.filename)[2]"
                  size="18"
                />
              </div>
              <div>
                <p class="font-medium text-slate-900">
                  {{ ticket.attachment.filename }}
                </p>
                <p class="text-sm text-slate-500">
                  <template v-if="ticket.attachment.type === 'file'">
                    <strong>{{ ticket.attachment.extension }}</strong>
                    {{ ticket.attachment.file_size || 'N/A' }}
                  </template>
                  <template v-else>Tautan eksternal</template>
                </p>
              </div>
            </div>
            <!-- Link type: open in new tab -->
            <a
              v-if="ticket.attachment.type === 'link'"
              :href="ticket.attachment.url"
              target="_blank"
              rel="noopener"
            >
              <Button variant="text">
                <IconExternalLink size="16" />
              </Button>
            </a>
            <!-- File type: download -->
            <a
              v-else
              :href="ticket.attachment.url"
              target="_blank"
              rel="noopener"
            >
              <Button variant="text">
                <IconDownload size="16" />
              </Button>
            </a>
          </div>
        </div>
      </div>

      <!-- Public Logs / Riwayat Penanganan -->
      <div class="border-t border-slate-200 pt-4">
        <h4
          class="mb-3 flex items-center text-base font-semibold text-slate-900"
        >
          <IconTimeline size="18" class="mr-2 text-purple-600" />
          Riwayat Penanganan
        </h4>

        <div v-if="ticket.logs && ticket.logs.length > 0" class="space-y-3">
          <div
            v-for="(log, index) in ticket.logs"
            :key="index"
            class="relative flex gap-3"
          >
            <!-- Timeline line -->
            <div class="flex flex-col items-center">
              <div
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-purple-100"
              >
                <IconNotes size="14" class="text-purple-600" />
              </div>
              <div
                v-if="index < ticket.logs.length - 1"
                class="mt-1 w-px flex-1 bg-slate-200"
              ></div>
            </div>

            <!-- Content -->
            <div class="min-w-0 flex-1 pb-3">
              <div
                class="mb-1 flex flex-wrap items-center gap-2 text-xs text-slate-400"
              >
                <span>{{ formatDatetime(log.created_at) }}</span>
                <span v-if="log.is_edited" class="italic">
                  · Diperbarui {{ formatDatetime(log.edited_at) }}
                </span>
              </div>
              <p
                class="whitespace-pre-wrap text-sm leading-relaxed text-slate-700"
              >
                {{ log.message }}
              </p>
              <div v-if="log.attachment" class="mt-2">
                <a
                  :href="logAttachmentUrl(log)"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-blue-600 hover:text-blue-800"
                >
                  <IconExternalLink
                    v-if="log.attachment?.type === 'link'"
                    size="12"
                  />
                  <IconPaperclip v-else size="12" />
                  {{
                    log.attachment?.type === 'link'
                      ? 'Buka Link'
                      : 'Lihat Lampiran'
                  }}
                </a>
              </div>
            </div>
          </div>
        </div>

        <div
          v-else
          class="rounded-lg border border-slate-100 bg-slate-50 py-6 text-center"
        >
          <IconHistory size="28" class="mx-auto mb-2 text-slate-300" />
          <p class="text-sm text-slate-500">Belum ada pembaruan publik</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Info Notice -->
  <div
    class="mx-6 !mt-0 rounded-xl border border-yellow-200 bg-yellow-50 p-6 sm:mx-0 sm:!mt-6"
  >
    <p class="text-slate-600">
      Untuk melihat detail lengkap tiket, gunakan tautan yang telah dikirimkan
      ke email Anda saat tiket dibuat. Jika Anda belum menerima email, periksa
      folder spam atau hubungi tim CSIRT Bojonegoro.
    </p>
  </div>
</template>
