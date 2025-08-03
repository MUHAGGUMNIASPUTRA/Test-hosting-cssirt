<script setup>
// filepath: resources/js/Pages/Admin/Dashboard.vue

import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      incidents: {
        total: 0,
        thisMonth: 0,
        open: 0,
        resolved: 0,
        critical: 0
      },
      posts: {
        total: 0,
        published: 0,
        draft: 0
      },
      users: {
        total: 0,
        active: 0
      },
      faqs: {
        total: 0,
        published: 0
      }
    })
  },
  recentIncidents: {
    type: Array,
    default: () => []
  },
  recentPosts: {
    type: Array,
    default: () => []
  },
  recentUsers: {
    type: Array,
    default: () => []
  },
  systemAlerts: {
    type: Array,
    default: () => []
  }
})

const { isMobile } = useResponsive()

const getPrioritySeverity = (priority) => {
  const severities = {
    'Rendah': 'success',
    'Sedang': 'info',
    'Tinggi': 'warn',
    'Kritikal': 'danger'
  }
  return severities[priority] || 'warn'
}

const getStatusSeverity = (status) => {
  const severities = {
    'Baru': 'info',
    'Diverifikasi': 'primary',
    'Dalam Penyelidikan': 'warn',
    'Selesai': 'success',
    'Ditutup': 'secondary'
  }
  return severities[status] || 'info'
}

const getAlertColor = (level) => {
  const colors = {
    'info': 'text-blue-600 bg-blue-50 border-blue-200',
    'warning': 'text-yellow-600 bg-yellow-50 border-yellow-200',
    'critical': 'text-red-600 bg-red-50 border-red-200'
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
    <div class="space-y-4 sm:space-y-6">
      <!-- Welcome Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold mb-1">
              Selamat Datang, {{ $page.props.auth.user?.name || 'Admin' }}! 👋
            </h2>
            <p class="text-blue-100">
              Ringkasan sistem keamanan hari ini. Monitor dan kelola insiden keamanan siber dengan mudah.
            </p>
          </div>
          <div class="hidden sm:block">
            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">
              <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Incidents -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm sm:text-base font-medium text-slate-600">Total Insiden</p>
              <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ stats.incidents.total }}</p>
              <p class="text-xs sm:text-sm text-green-600 mt-1">
                <span class="font-medium">+{{ stats.incidents.thisMonth }}</span> bulan ini
              </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-50 border border-red-200 rounded-lg flex items-center justify-center">
              <IconUrgent class="text-red-600" :size="isMobile ? 18 : undefined"/>
            </div>
          </div>
        </div>

        <!-- Open Incidents -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm sm:text-base font-medium text-slate-600">Insiden Terbuka</p>
              <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ stats.incidents.open }}</p>
              <p class="text-xs sm:text-sm text-red-600 mt-1">
                <span class="font-medium">{{ stats.incidents.critical }}</span> kritikal
              </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-50 border border-orange-200 rounded-lg flex items-center justify-center">
              <IconFileAlert class="text-orange-600" :size="isMobile ? 18 : undefined"/>
            </div>
          </div>
        </div>

        <!-- Published Posts -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm sm:text-base font-medium text-slate-600">Artikel Diterbitkan</p>
              <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ stats.posts.published }}</p>
              <p class="text-xs sm:text-sm text-slate-500 mt-1">
                <span class="font-medium">{{ stats.posts.draft }}</span> draft
              </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
              <IconArticle class="text-blue-600" :size="isMobile ? 18 : undefined"/>
            </div>
          </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm sm:text-base font-medium text-slate-600">Total Pengguna</p>
              <p class="text-xl sm:text-3xl font-bold text-slate-900">{{ stats.users.total }}</p>
              <p class="text-xs sm:text-sm text-slate-500 mt-1">
                <span class="font-medium">{{ stats.faqs.published }}</span> FAQ aktif
              </p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
              <IconUsers class="text-green-600" :size="isMobile ? 18 : undefined"/>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Recent Incidents -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-200">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-slate-900">Insiden Terbaru</h3>
                <Link :href="route('admin.incidents.index')" class="text-blue-500 hover:text-blue-700 font-medium">Lihat Semua →</Link>
              </div>
            </div>
            <div class="divide-y divide-slate-100">
              <div v-if="recentIncidents.length === 0" class="p-6 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <p>Belum ada insiden yang dilaporkan</p>
              </div>
              <div
                v-for="incident in recentIncidents"
                :key="incident.id"
                class="p-4 hover:bg-slate-50 transition-colors"
              >
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-2">
                      <div class="flex items-center gap-2">
                        <Tag
                          :value="incident.case_id"
                          severity="secondary"
                          size="small"
                          class="font-mono !text-slate-500"
                        />
                        <Tag
                          :value="incident.priority"
                          :severity="getPrioritySeverity(incident.priority)"
                          size="small"
                        />
                      </div>
                      <Tag
                        :value="incident.status"
                        :severity="getStatusSeverity(incident.status)"
                        size="small"
                      />
                    </div>
                    <h4 class="font-medium text-slate-700 text-sm mb-1 line-clamp-1">
                      {{ incident.description }}
                    </h4>
                    <div class="flex items-center gap-3 text-sm text-slate-500">
                      <span class="truncate">{{ incident.reporter_name }}</span>
                      <span>•</span>
                      <span>{{ formatDate(incident.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Alerts & Recent Posts -->
        <div class="space-y-4 sm:space-y-6">
          <!-- System Alerts -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-200">
              <div class="flex items-center gap-2">
                <h3 class="text-xl font-semibold text-slate-900">Pengumuman Sistem</h3>
              </div>
            </div>
            <div class="p-4 space-y-3">
              <div v-if="systemAlerts.length === 0" class="text-center text-slate-500 py-6">
                <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-sm">Tidak ada pengumuman</p>
              </div>
              <div
                v-for="alert in systemAlerts"
                :key="alert.id"
                class="flex gap-2 p-3 rounded-lg border"
                :class="getAlertColor(alert.level)"
              >
                <div class="flex-shrink-0 mt-0.5">
                  <IconInfoCircle v-if="alert.level === 'info'" class="w-4 h-4 text-blue-600" />
                  <IconAlertCircle v-else-if="alert.level === 'warning'" class="w-4 h-4 text-yellow-600" />
                  <IconCircleX v-else class="w-4 h-4 text-red-600" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium">{{ alert.title }}</p>
                  <p class="text-sm opacity-75 mt-1">{{ formatDate(alert.created_at) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Posts -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-200">
              <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-slate-900">Artikel Terbaru</h3>
                <Link :href="route('admin.posts.index')" class="text-blue-500 hover:text-blue-700 font-medium">Lihat Semua →</Link>
              </div>
            </div>
            <div class="divide-y divide-slate-100">
              <div
                v-if="recentPosts.length === 0"
                class="p-6 text-center text-slate-500"
              >
                <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm">Belum ada artikel</p>
              </div>
              <div
                v-for="post in recentPosts"
                :key="post.id"
                class="p-4 hover:bg-slate-50 transition-colors"
              >
                <div class="flex justify-between items-start gap-3">
                  <div class="flex-1 min-w-0">
                    <h4 class="font-medium text-slate-700 text-sm mb-2 line-clamp-2">{{ post.title }}</h4>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                      <Tag
                        :value="post.status"
                        :severity="post.status === 'Published' ? 'success' : 'warning'"
                        size="small"
                      />
                      <span>•</span>
                      <span class="text-sm">{{ post.views_count || 0 }} views</span>
                      <span>•</span>
                      <span class="text-sm text-slate-400">{{ formatDate(post.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
