<script setup>
// filepath: resources/js/pages/Incidents/Show.vue

import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useParticles } from '@/Composables/useParticles'

const page = usePage()
const props = defineProps({
  incident: Object,
})

const { minimalParticlesOptions } = useParticles()

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

const hasAttachment = computed(() =>
  Boolean(incident.value?.attachment?.filename),
)
</script>

<template>
  <AppLayout :title="`Detail Tiket ${incident?.case_id || ''}`">
    <!-- Hero Section -->
    <section
      ref="heroRef"
      class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
    >
      <div class="absolute inset-0 z-0">
        <vue-particles
          id="tsparticles"
          :options="minimalParticlesOptions"
          class="h-full w-full"
        />
      </div>

      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container max-w-4xl text-center">
          <div class="animate-fade-in-up">
            <h1
              class="mb-6 text-5xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl lg:text-7xl"
            >
              Detail
              <span
                class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"
                >Tiket</span
              >
            </h1>

            <p
              class="mx-auto mb-8 mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl"
            >
              Detail informasi insiden keamanan siber yang telah dilaporkan
            </p>

            <p
              class="mx-auto inline-flex max-w-3xl rounded-lg border border-white/20 bg-white/10 px-3 py-1 font-mono text-xl text-slate-300 text-white/90 sm:text-2xl"
            >
              {{ incident.case_id }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Main -->
    <section class="bg-white py-10 sm:py-14">
      <div
        class="mx-auto max-w-5xl space-y-6 px-4 sm:space-y-8 sm:px-6 lg:px-8"
      >
        <!-- Summary Card -->
        <div
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8"
        >
          <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <div>
              <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
                Tiket {{ incident.case_id }}
              </h2>
              <p class="text-slate-600">
                Dilaporkan:
                <strong>{{ formatDateTime(incident.reported_at) }}</strong>
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <span
                class="inline-flex items-center rounded-md border px-2.5 py-1 text-sm"
                :class="statusBadge(incident.status)"
              >
                <IconAlertCircle class="mr-1.5" size="14" />
                {{ incident.status }}
              </span>
              <span
                class="inline-flex items-center rounded-md border px-2.5 py-1 text-sm"
                :class="priorityBadge(incident.priority)"
              >
                <IconBolt class="mr-1.5" size="14" />
                {{ incident.priority }}
              </span>
            </div>
          </div>

          <!-- Meta -->
          <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-6">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <div class="flex items-start">
                <div
                  class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-100"
                >
                  <IconTicTac class="text-blue-600" size="18" />
                </div>
                <div>
                  <p class="text-sm text-slate-500">Kategori Insiden</p>
                  <p class="text-base/5 font-medium text-slate-900">
                    {{ incident.incident_type?.name || '-' }}
                  </p>
                </div>
              </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <div class="flex items-start">
                <div
                  class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg border border-orange-200 bg-orange-100"
                >
                  <IconClock class="text-orange-600" size="18" />
                </div>
                <div>
                  <p class="text-sm text-slate-500">Waktu Kejadian</p>
                  <p class="text-base/5 font-medium text-slate-900">
                    {{ formatDateTime(incident.incident_at) || '-' }}
                  </p>
                </div>
              </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
              <div class="flex items-start">
                <div
                  class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-100"
                >
                  <IconShieldCheck class="text-emerald-600" size="18" />
                </div>
                <div>
                  <p class="text-sm text-slate-500">ID Tiket</p>
                  <p class="font-mono text-sm/5 font-medium text-slate-900">
                    {{ incident.case_id }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8"
        >
          <div class="mb-3 flex items-center">
            <div
              class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-100"
            >
              <IconFileDescription class="text-slate-700" size="18" />
            </div>
            <h3 class="text-lg font-semibold text-slate-900 sm:text-xl">
              Deskripsi Insiden
            </h3>
          </div>
          <p class="whitespace-pre-wrap leading-relaxed text-slate-800">
            {{ incident.description }}
          </p>
        </div>

        <!-- Attachment -->
        <div
          v-if="hasAttachment"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8"
        >
          <div class="mb-3 flex items-center">
            <div
              class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-100"
            >
              <IconPaperclip class="text-slate-700" size="18" />
            </div>
            <h3 class="text-lg font-semibold text-slate-900 sm:text-xl">
              Lampiran
            </h3>
          </div>

          <div
            class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="min-w-0">
              <p class="truncate font-medium text-slate-800">
                {{ incident.attachment.filename }}
              </p>
              <p class="text-sm text-slate-500">
                {{ incident.attachment.file_size }}
              </p>
            </div>
            <a
              :href="incident.attachment.download_url"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
            >
              <IconDownload class="mr-2" size="16" />
              Unduh Lampiran
            </a>
          </div>
        </div>

        <!-- Logs Timeline -->
        <div
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8"
        >
          <div class="mb-4 flex items-center">
            <div
              class="mr-3 flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-100"
            >
              <IconTimeline class="text-slate-700" size="18" />
            </div>
            <h3 class="text-lg font-semibold text-slate-900 sm:text-xl">
              Riwayat Penanganan
            </h3>
          </div>

          <div v-if="incident.logs?.length" class="relative">
            <div
              class="absolute bottom-0 left-3 top-0 w-px bg-slate-200 sm:left-4"
            ></div>
            <div class="space-y-5">
              <div
                v-for="(log, idx) in incident.logs"
                :key="idx"
                class="relative pl-8 sm:pl-10"
              >
                <div
                  class="absolute left-1.5 mt-1.5 h-3 w-3 rounded-full border border-slate-200 bg-slate-500 ring-2 ring-white sm:left-2.5"
                ></div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                  <p class="text-slate-800">{{ log.message }}</p>
                  <p class="text-sm text-slate-500">
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
