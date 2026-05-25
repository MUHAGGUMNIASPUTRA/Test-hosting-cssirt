<script setup>
// filepath: resources/js/Pages/Admin/Dashboard.vue

import { Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      incidents: {
        total: 0,
        thisMonth: 0,
        open: 0,
        resolved: 0,
        critical: 0,
      },
      services: {
        total: 0,
        active: 0,
      },
      documents: {
        total: 0,
      },
      faqs: {
        total: 0,
        published: 0,
      },
      webApplications: {
        total: 0,
        active: 0,
      },
      mobileApplications: {
        total: 0,
        active: 0,
      },
      licenses: {
        total: 0,
        active: 0,
        expiringSoon: 0,
      },
      physicalAssets: {
        total: 0,
      },
      informationAssets: {
        total: 0,
      },
    }),
  },
  recentIncidents: {
    type: Array,
    default: () => [],
  },
  systemAlerts: {
    type: Array,
    default: () => [],
  },
})

const getAlertColor = (level) => {
  const colors = {
    info: 'text-blue-600 bg-blue-50 border-blue-200',
    warning: 'text-yellow-600 bg-yellow-50 border-yellow-200',
    critical: 'text-red-600 bg-red-50 border-red-200',
  }
  return colors[level] || colors['info']
}

const formatDate = (dateString) => {
  if (!dateString) return 'Tidak diketahui'
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 1) return 'Kemarin'
  if (diffDays < 7) return `${diffDays} hari yang lalu`
  if (diffDays < 30) return `${Math.ceil(diffDays / 7)} minggu yang lalu`
  return `${Math.ceil(diffDays / 30)} bulan yang lalu`
}

const truncateText = (text, length = 50) => {
  if (!text) return 'No description'
  return text.length > length ? text.substring(0, length) + '...' : text
}
</script>

<template>
  <AdminLayout title="Dashboard">
    <div class="space-y-4 lg:space-y-6">
      <!-- Welcome Section -->
      <div
        class="rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 p-4 text-white lg:p-6"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-bold lg:text-2xl">
              Selamat Datang, {{ $page.props.auth.user?.name || 'Admin' }}! 👋
            </h2>
            <p class="text-blue-100">
              Ringkasan sistem keamanan hari ini. Monitor dan kelola insiden
              keamanan siber dengan mudah.
            </p>
          </div>
          <div class="hidden lg:block">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10"
            >
              <svg
                class="h-8 w-8 text-blue-200"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-6">
        <StatCard
          layout="vertical"
          color="red"
          label="Total Insiden"
          :value="stats.incidents.total"
          subtextClass="text-green-600"
        >
          <template #default="{ iconClass, iconSize }">
            <IconUrgent :class="iconClass" :size="iconSize" />
          </template>
          <template #subtext>
            <span class="font-medium">+{{ stats.incidents.thisMonth }}</span>
            bulan ini
          </template>
        </StatCard>

        <StatCard
          layout="vertical"
          color="orange"
          label="Insiden Terbuka"
          :value="stats.incidents.open"
          subtextClass="text-red-600"
        >
          <template #default="{ iconClass, iconSize }">
            <IconFileAlert :class="iconClass" :size="iconSize" />
          </template>
          <template #subtext>
            <span class="font-medium">{{ stats.incidents.critical }}</span>
            kritikal
          </template>
        </StatCard>

        <StatCard
          layout="vertical"
          color="blue"
          label="Aplikasi Web"
          :value="stats.webApplications.total"
        >
          <template #default="{ iconClass, iconSize }">
            <IconDeviceDesktop :class="iconClass" :size="iconSize" />
          </template>
          <template #subtext>
            <span class="font-medium">{{ stats.webApplications.active }}</span>
            aktif
          </template>
        </StatCard>

        <StatCard
          layout="vertical"
          color="cyan"
          label="Aplikasi Mobile"
          :value="stats.mobileApplications.total"
        >
          <template #default="{ iconClass, iconSize }">
            <IconDeviceMobile :class="iconClass" :size="iconSize" />
          </template>
          <template #subtext>
            <span class="font-medium">{{
              stats.mobileApplications.active
            }}</span>
            aktif
          </template>
        </StatCard>

        <StatCard
          layout="vertical"
          :color="stats.licenses.expiringSoon > 0 ? 'red' : 'orange'"
          label="Lisensi"
          :value="stats.licenses.total"
        >
          <template #default="{ iconClass, iconSize }">
            <IconKey :class="iconClass" :size="iconSize" />
          </template>
          <template #subtext>
            <span
              :class="
                stats.licenses.expiringSoon > 0
                  ? 'font-medium text-red-600'
                  : 'font-medium text-orange-600'
              "
              >{{ stats.licenses.expiringSoon }}</span
            >
            mau kadaluarsa
          </template>
        </StatCard>

        <StatCard
          layout="vertical"
          color="slate"
          label="Aset Fisik"
          :value="stats.physicalAssets.total"
        >
          <template #default="{ iconClass, iconSize }">
            <IconServer :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard
          layout="vertical"
          color="purple"
          label="Aset Informasi"
          :value="stats.informationAssets.total"
        >
          <template #default="{ iconClass, iconSize }">
            <IconDatabase :class="iconClass" :size="iconSize" />
          </template>
        </StatCard>

        <StatCard
          layout="vertical"
          color="purple"
          label="Dokumen Panduan"
          :value="stats.documents?.total || 0"
        >
          <template #default="{ iconClass, iconSize }">
            <IconFileTypePdf :class="iconClass" :size="iconSize" />
          </template>
          <template #subtext>
            <span class="font-medium">{{ stats.services?.total || 0 }}</span>
            layanan aktif
          </template>
        </StatCard>
      </div>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
        <!-- Recent Incidents -->
        <div class="lg:col-span-2">
          <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 lg:p-6">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-slate-900">
                  Insiden Terbaru
                </h3>
                <Link
                  :href="route('admin.incidents.index')"
                  class="text-sm font-medium text-blue-500 hover:text-blue-700 lg:text-base"
                  >Lihat Semua →</Link
                >
              </div>
            </div>
            <div class="divide-y divide-slate-100">
              <div
                v-if="recentIncidents.length === 0"
                class="py-8 text-center lg:py-12"
              >
                <IconUrgent class="mx-auto mb-2 text-slate-300" size="30" />
                <p class="text-slate-400">Belum ada insiden yang dilaporkan</p>
              </div>
              <div
                v-for="incident in recentIncidents"
                :key="incident.id"
                class="p-4 transition-colors hover:bg-slate-50 lg:p-6"
              >
                <div
                  class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between"
                >
                  <div class="min-w-0 flex-1">
                    <div class="mb-2 flex items-center justify-between gap-2">
                      <div class="flex items-center gap-2">
                        <Tag
                          :value="incident.case_id"
                          severity="secondary"
                          size="small"
                          class="font-mono !text-slate-500"
                        />
                        <StatusBadge
                          type="priority"
                          :value="incident.priority"
                        />
                      </div>
                      <StatusBadge
                        type="incident-status"
                        :value="incident.status"
                      />
                    </div>
                    <h4
                      class="mb-1 line-clamp-1 text-sm font-medium text-slate-700"
                    >
                      {{ incident.description }}
                    </h4>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                      <div class="flex items-center gap-1">
                        <IconUserExclamation size="14" stroke-width="1.5" />
                        <span class="truncate">{{
                          incident.reporter_name
                        }}</span>
                      </div>
                      <span>•</span>
                      <span class="text-slate-400">{{
                        formatDate(incident.created_at)
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Alerts & Recent Posts -->
        <div class="space-y-4 lg:space-y-6">
          <!-- System Alerts -->
          <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 lg:p-6">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-slate-900">
                  Pengumuman Sistem
                </h3>
                <Link
                  :href="route('admin.announcements.index')"
                  class="text-sm font-medium text-blue-500 hover:text-blue-700 lg:text-base"
                  >Lihat Semua →</Link
                >
              </div>
            </div>
            <div class="space-y-4 p-4 lg:p-6">
              <div
                v-if="systemAlerts.length === 0"
                class="py-4 text-center lg:py-8"
              >
                <IconSpeakerphone
                  class="mx-auto mb-2 text-slate-300"
                  size="30"
                />
                <p class="text-slate-400">Tidak ada pengumuman aktif</p>
              </div>
              <div
                v-for="alert in systemAlerts"
                :key="alert.id"
                class="flex gap-2 rounded-lg border p-3"
                :class="getAlertColor(alert.level)"
              >
                <div class="mt-0.5 flex-shrink-0">
                  <IconInfoCircle
                    v-if="alert.level === 'info'"
                    class="h-4 w-4 text-blue-600"
                  />
                  <IconAlertCircle
                    v-else-if="alert.level === 'warning'"
                    class="h-4 w-4 text-yellow-600"
                  />
                  <IconCircleX v-else class="h-4 w-4 text-red-600" />
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium">{{ alert.title }}</p>
                  <p class="mt-1 text-sm opacity-75">
                    {{ formatDate(alert.created_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
