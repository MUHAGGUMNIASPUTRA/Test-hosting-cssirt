<script setup>
// filepath: resources/js/pages/Incidents/Show.vue

import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const props = defineProps({
  incident: Object,
})

const incident = computed(() => props.incident || page.props.incident || {})

const formatDateTime = (dt) =>
  dt
    ? new Date(dt).toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      })
    : ''

const statusBadge = (status) => {
  switch (status) {
    case 'Baru':
      return 'bg-sky-50 text-sky-700 border-sky-200'
    case 'Diverifikasi':
      return 'bg-blue-50 text-blue-700 border-blue-200'
    case 'Dalam Penyelidikan':
      return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Selesai':
      return 'bg-green-50 text-green-700 border-green-200'
    case 'Ditutup':
      return 'bg-slate-100 text-slate-700 border-slate-200'
    default:
      return 'bg-sky-100 text-sky-700 border-sky-200'
  }
}

const priorityBadge = (priority) => {
  switch (priority) {
    case 'Rendah':
      return 'bg-green-100 text-green-700 border-green-200'
    case 'Sedang':
      return 'bg-sky-50 text-sky-700 border-sky-200'
    case 'Tinggi':
      return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Kritikal':
      return 'bg-red-50 text-red-700 border-red-200'
    default:
      return 'bg-sky-100 text-sky-700 border-sky-200'
  }
}

const hasAttachment = computed(() => Boolean(incident.value?.attachment?.filename))
</script>

<template>
  <AppLayout :title="`Detail Tiket ${incident?.case_id || ''}`">

    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container max-w-4xl text-center">
          <div class="animate-fade-in-up">
            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl mb-6 leading-tight">
              Detail <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">Tiket</span>
            </h1>

            <p class="mx-auto mt-6 max-w-3xl text-xl sm:text-2xl text-slate-300 mb-8">
              Detail informasi insiden keamanan siber yang telah dilaporkan
            </p>

            <p class="font-mono text-xl sm:text-2xl text-slate-300 max-w-3xl mx-auto inline-flex px-3 py-1 rounded-lg border border-white/20 bg-white/10 text-white/90">
              {{ incident.case_id }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Main -->
    <section class="py-10 sm:py-14 bg-white">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
        <!-- Summary Card -->
        <div
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 lg:p-8"
        >
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
                Tiket {{ incident.case_id }}
              </h2>
              <p class="text-slate-600">
                Dilaporkan: <strong>{{ formatDateTime(incident.reported_at) }}</strong>
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-md text-sm border"
                :class="statusBadge(incident.status)"
              >
                <IconAlertCircle class="mr-1.5" size="14" />
                {{ incident.status }}
              </span>
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-md text-sm border"
                :class="priorityBadge(incident.priority)"
              >
                <IconBolt class="mr-1.5" size="14" />
                {{ incident.priority }}
              </span>
            </div>
          </div>

          <!-- Meta -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-6">
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
              <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-blue-100 border border-blue-200 flex items-center justify-center mr-3">
                  <IconTicTac class="text-blue-600" size="18" />
                </div>
                <div>
                  <p class="text-slate-500 text-sm">Kategori Insiden</p>
                  <p class="text-slate-900 text-base/5 font-medium">
                    {{ incident.incident_type?.name || '-' }}
                  </p>
                </div>
              </div>
            </div>

            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
              <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-orange-100 border border-orange-200 flex items-center justify-center mr-3">
                  <IconClock class="text-orange-600" size="18" />
                </div>
                <div>
                  <p class="text-slate-500 text-sm">Waktu Kejadian</p>
                  <p class="text-slate-900 text-base/5 font-medium">
                    {{ formatDateTime(incident.incident_at) || '-' }}
                  </p>
                </div>
              </div>
            </div>

            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
              <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 border border-emerald-200 flex items-center justify-center mr-3">
                  <IconShieldCheck class="text-emerald-600" size="18" />
                </div>
                <div>
                  <p class="text-slate-500 text-sm">ID Tiket</p>
                  <p class="text-slate-900 text-sm/5 font-medium font-mono">{{ incident.case_id }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 lg:p-8">
          <div class="flex items-center mb-3">
            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center mr-3">
              <IconFileDescription class="text-slate-700" size="18" />
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900">Deskripsi Insiden</h3>
          </div>
          <p class="whitespace-pre-wrap leading-relaxed text-slate-800">
            {{ incident.description }}
          </p>
        </div>

        <!-- Attachment -->
        <div
          v-if="hasAttachment"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 lg:p-8"
        >
          <div class="flex items-center mb-3">
            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center mr-3">
              <IconPaperclip class="text-slate-700" size="18" />
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900">Lampiran</h3>
          </div>

          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50 border border-slate-200 rounded-xl p-4">
            <div class="min-w-0">
              <p class="text-slate-800 font-medium truncate">
                {{ incident.attachment.filename }}
              </p>
              <p class="text-slate-500 text-sm">
                {{ incident.attachment.file_size }}
              </p>
            </div>
            <a
              :href="incident.attachment.download_url"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white transition"
            >
              <IconDownload class="mr-2" size="16" />
              Unduh Lampiran
            </a>
          </div>
        </div>

        <!-- Logs Timeline -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 lg:p-8">
          <div class="flex items-center mb-4">
            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center mr-3">
              <IconTimeline class="text-slate-700" size="18" />
            </div>
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900">Riwayat Penanganan</h3>
          </div>

          <div v-if="incident.logs?.length" class="relative">
            <div class="absolute left-3 sm:left-4 top-0 bottom-0 w-px bg-slate-200"></div>
            <div class="space-y-5">
              <div
                v-for="(log, idx) in incident.logs"
                :key="idx"
                class="relative pl-8 sm:pl-10"
              >
                <div
                  class="absolute left-1.5 sm:left-2.5 mt-1.5 w-3 h-3 rounded-full bg-slate-500 ring-2 ring-white border border-slate-200"
                ></div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                  <p class="text-slate-800">{{ log.message }}</p>
                  <p class="text-slate-500 text-sm">
                    {{ formatDateTime(log.created_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <p v-else class="text-slate-600">Belum ada riwayat.</p>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
