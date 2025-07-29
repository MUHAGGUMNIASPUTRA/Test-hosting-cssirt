<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'

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

const getSeverityColor = (priority) => {
  const colors = {
    'Kritikal': 'text-red-600 bg-red-50',
    'Tinggi': 'text-orange-600 bg-orange-50',
    'Sedang': 'text-yellow-600 bg-yellow-50',
    'Rendah': 'text-blue-600 bg-blue-50'
  }
  return colors[priority] || colors['Sedang']
}

const getStatusColor = (status) => {
  const colors = {
    'Baru': 'text-blue-600 bg-blue-50',
    'Diverifikasi': 'text-yellow-600 bg-yellow-50',
    'Dalam Penyelidikan': 'text-orange-600 bg-orange-50',
    'Selesai': 'text-green-600 bg-green-50',
    'Ditutup': 'text-slate-600 bg-slate-50'
  }
  return colors[status] || colors['Baru']
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
    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold mb-2">
              Selamat Datang, {{ $page.props.auth.user?.name || 'Admin' }}! 👋
            </h2>
            <p class="text-blue-100">
              Ringkasan sistem keamanan hari ini. Monitor dan kelola insiden keamanan siber dengan mudah.
            </p>
          </div>
          <div class="hidden sm:block">
            <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center">
              <svg class="w-10 h-10 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Incidents -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Total Insiden</p>
              <p class="text-3xl font-bold text-slate-900">{{ stats.incidents.total }}</p>
              <p class="text-sm text-green-600 mt-1">
                <span class="font-medium">+{{ stats.incidents.thisMonth }}</span> bulan ini
              </p>
            </div>
            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Open Incidents -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Insiden Terbuka</p>
              <p class="text-3xl font-bold text-slate-900">{{ stats.incidents.open }}</p>
              <p class="text-sm text-red-600 mt-1">
                <span class="font-medium">{{ stats.incidents.critical }}</span> kritikal
              </p>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Published Posts -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Artikel Dipublikasi</p>
              <p class="text-3xl font-bold text-slate-900">{{ stats.posts.published }}</p>
              <p class="text-sm text-slate-500 mt-1">
                <span class="font-medium">{{ stats.posts.draft }}</span> draft
              </p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Users - FIXED ICON -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-600">Total Pengguna</p>
              <p class="text-3xl font-bold text-slate-900">{{ stats.users.total }}</p>
              <p class="text-sm text-slate-500 mt-1">
                <span class="font-medium">{{ stats.faqs.published }}</span> FAQ aktif
              </p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Incidents -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Insiden Terbaru</h3>
                <Link
                  href="#"
                  class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                >
                  Lihat Semua →
                </Link>
              </div>
            </div>
            <div class="divide-y divide-slate-100">
              <div
                v-if="recentIncidents.length === 0"
                class="p-6 text-center text-slate-500"
              >
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
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-xs font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded">
                        {{ incident.case_id }}
                      </span>
                      <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                        :class="getSeverityColor(incident.priority)"
                      >
                        {{ incident.priority }}
                      </span>
                    </div>
                    <h4 class="font-medium text-slate-900 text-sm mb-1 line-clamp-2">
                      {{ truncateText(incident.description, 80) }}
                    </h4>
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                      <span>{{ formatDate(incident.created_at) }}</span>
                      <span>•</span>
                      <span class="truncate">{{ incident.reporter_name }}</span>
                    </div>
                  </div>
                  <div class="flex-shrink-0">
                    <span
                      class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                      :class="getStatusColor(incident.status)"
                    >
                      {{ incident.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Alerts & Recent Posts -->
        <div class="space-y-6">
          <!-- System Alerts -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-200">
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-semibold text-slate-900">Pengumuman Sistem</h3>
              </div>
            </div>
            <div class="p-4 space-y-3">
              <div
                v-if="systemAlerts.length === 0"
                class="text-center text-slate-500 py-6"
              >
                <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-sm">Tidak ada pengumuman</p>
              </div>
              <div
                v-for="alert in systemAlerts"
                :key="alert.id"
                class="flex gap-3 p-3 rounded-lg border"
                :class="getAlertColor(alert.level)"
              >
                <div class="flex-shrink-0 mt-0.5">
                  <svg v-if="alert.level === 'info'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else-if="alert.level === 'warning'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium">{{ alert.title }}</p>
                  <p class="text-xs opacity-75 mt-1">{{ formatDate(alert.created_at) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Posts -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="p-6 border-b border-slate-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Artikel Terbaru</h3>
                <Link
                  href="#"
                  class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                >
                  Lihat Semua →
                </Link>
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
                    <h4 class="font-medium text-slate-900 text-sm mb-2 line-clamp-2">
                      {{ post.title }}
                    </h4>
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                      <span
                        class="inline-flex items-center px-2 py-1 rounded-full font-medium"
                        :class="post.status === 'Published' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700'"
                      >
                        {{ post.status }}
                      </span>
                      <span>•</span>
                      <span>{{ post.views_count || 0 }} views</span>
                    </div>
                  </div>
                  <div class="flex-shrink-0 text-xs text-slate-400">
                    {{ formatDate(post.created_at) }}
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
