<script setup>
import { useResponsive } from '@/Composables/useResponsive'
import { formatDatetime } from '@/utils/date'

const props = defineProps({
  incident: Object,
})

const { isDesktop } = useResponsive()
</script>

<template>
  <!-- Reporter Information -->
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6">
    <div class="mb-4 flex items-center lg:mb-6">
      <div
        class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
      >
        <IconUserExclamation
          class="text-blue-600"
          :size="!isDesktop ? 18 : undefined"
        />
      </div>
      <div class="ml-3">
        <h3 class="text-xl/6 font-semibold text-slate-900">
          Informasi Pelapor
        </h3>
        <p class="text-xs text-slate-600 lg:text-sm">
          Data kontak pelapor insiden
        </p>
      </div>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
      <div>
        <label class="mb-2 block font-medium text-slate-700"
          >Nama Pelapor</label
        >
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
          <p class="text-slate-900">{{ incident.reporter_name }}</p>
        </div>
      </div>
      <div>
        <label class="mb-2 block font-medium text-slate-700"
          >Email Pelapor</label
        >
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
          <p class="text-slate-900">{{ incident.reporter_email }}</p>
        </div>
      </div>
      <div class="md:col-span-2">
        <label class="mb-2 block font-medium text-slate-700"
          >Nomor Telepon</label
        >
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
          <p class="text-slate-900">
            {{ incident.reporter_phone || 'Tidak tersedia' }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Incident Details -->
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6">
    <div class="mb-4 flex items-center lg:mb-6">
      <div
        class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 lg:h-12 lg:w-12"
      >
        <IconUrgent class="text-red-600" :size="!isDesktop ? 18 : undefined" />
      </div>
      <div class="ml-3">
        <h3 class="text-xl/6 font-semibold text-slate-900">Detail Insiden</h3>
        <p class="text-xs text-slate-600 lg:text-sm">
          Informasi lengkap tentang insiden yang terjadi
        </p>
      </div>
    </div>
    <div class="space-y-6">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
        <div>
          <label class="mb-2 block font-medium text-slate-700"
            >Kategori Insiden</label
          >
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
            <p class="text-slate-900">{{ incident.incident_type.name }}</p>
          </div>
        </div>
        <div>
          <label class="mb-2 block font-medium text-slate-700"
            >Waktu Kejadian</label
          >
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
            <p class="text-slate-900">
              {{ formatDatetime(incident.incident_at) }}
            </p>
          </div>
        </div>
        <div>
          <label class="mb-2 block font-medium text-slate-700"
            >Waktu Dilaporkan</label
          >
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
            <p class="text-slate-900">
              {{ formatDatetime(incident.reported_at) }}
            </p>
          </div>
        </div>
        <div>
          <label class="mb-2 block font-medium text-slate-700"
            >Ditugaskan Kepada</label
          >
          <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
            <p class="text-slate-900">
              {{ incident.assigned_user?.name || 'Belum ditugaskan' }}
            </p>
          </div>
        </div>
      </div>
      <div>
        <label class="mb-2 block font-medium text-slate-700"
          >Deskripsi Insiden</label
        >
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
          <p class="whitespace-pre-wrap leading-relaxed text-slate-900">
            {{ incident.description }}
          </p>
        </div>
      </div>
      <div v-if="incident.attachment">
        <label class="mb-2 block font-medium text-slate-700">Lampiran</label>
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-2">
          <a
            :href="incident.attachment.url"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center font-medium text-blue-600 hover:text-blue-700"
          >
            <IconExternalLink
              v-if="incident.attachment.type === 'link'"
              size="18"
              class="mr-2"
            />
            <IconPaperclip v-else size="18" class="mr-2" />
            {{
              incident.attachment.type === 'link'
                ? 'Buka Link'
                : incident.attachment.filename || 'Lihat Lampiran'
            }}
          </a>
        </div>
      </div>
      <div v-if="incident.access_token">
        <label class="mb-2 block font-medium text-slate-700">Akses Token</label>
        <div
          class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-2"
        >
          <p class="break-all font-mono text-sm text-slate-900">
            {{ incident.access_token }}
          </p>
          <a
            :href="
              route('incident.show', {
                caseId: incident.case_id,
                token: incident.access_token,
              })
            "
            target="_blank"
            class="ml-2 shrink-0 font-medium text-blue-600 hover:text-blue-700"
            title="Link Publik"
          >
            <IconTicket size="18" />
          </a>
        </div>
      </div>

      <!-- Aset Virtual Terdampak -->
      <div
        v-if="
          incident.web_applications?.length ||
          incident.mobile_applications?.length
        "
      >
        <label class="mb-2 block font-medium text-slate-700"
          >Aset Virtual Terdampak</label
        >
        <div class="flex flex-wrap gap-2">
          <a
            v-for="app in incident.web_applications"
            :key="app.id"
            :href="route('admin.web-applications.show', app.id)"
            class="flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-sm transition hover:border-indigo-400"
          >
            <span class="text-xs font-semibold text-indigo-400">Web</span>
            <span class="text-slate-700">{{ app.name }}</span>
          </a>
          <a
            v-for="app in incident.mobile_applications"
            :key="app.id"
            :href="route('admin.mobile-applications.show', app.id)"
            class="flex items-center gap-1.5 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-sm transition hover:border-violet-400"
          >
            <span class="text-xs font-semibold text-violet-400">Mobile</span>
            <span class="text-slate-700">{{ app.name }}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
