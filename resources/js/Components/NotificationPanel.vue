<script setup>
// filepath: resources/js/Components/NotificationPanel.vue

import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const notifications = ref({ unread: [], read: [], unread_count: 0 })
const isOpen = ref(false)
const isLoading = ref(false)
let pollInterval = null

const fetchNotifications = async () => {
  try {
    const response = await fetch(route('api.notifications.incidents'))
    const data = await response.json()
    notifications.value = data
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

const markAsRead = async (incidentId) => {
  try {
    await fetch(route('api.incidents.mark-read', incidentId), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    await fetchNotifications()
  } catch (error) {
    console.error('Failed to mark as read:', error)
  }
}

const markAllAsRead = async () => {
  isLoading.value = true
  try {
    await fetch(route('api.notifications.mark-all-read'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    await fetchNotifications()
  } catch (error) {
    console.error('Failed to mark all as read:', error)
  } finally {
    isLoading.value = false
  }
}

const goToIncident = (incident) => {
  if (!incident.is_read) {
    markAsRead(incident.id)
  }
  isOpen.value = false
  router.get(route('admin.incidents.show', incident.id))
}

const togglePanel = () => {
  isOpen.value = !isOpen.value
}

const closePanel = () => {
  isOpen.value = false
}

const formatTimeAgo = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInMinutes = Math.floor((now - date) / (1000 * 60))

  if (diffInMinutes < 1) return 'Baru saja'
  if (diffInMinutes < 60) return `${diffInMinutes} menit lalu`

  const diffInHours = Math.floor(diffInMinutes / 60)
  if (diffInHours < 24) return `${diffInHours} jam lalu`

  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) return `${diffInDays} hari lalu`

  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

const getPriorityColor = (priority) => {
  const colors = {
    'Rendah': 'text-green-600 bg-green-50',
    'Sedang': 'text-sky-600 bg-sky-50',
    'Tinggi': 'text-orange-600 bg-orange-50',
    'Kritikal': 'text-red-600 bg-red-50'
  }
  return colors[priority] || 'text-slate-600 bg-slate-50'
}

onMounted(() => {
  fetchNotifications()
  // Poll for new notifications every 30 seconds
  pollInterval = setInterval(fetchNotifications, 30000)

  // Close panel when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.notification-panel')) {
      closePanel()
    }
  })
})

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
})
</script>

<template>
  <div class="relative notification-panel">
    <!-- Notification Bell Button -->
    <button
      @click.stop="togglePanel"
      class="p-3 rounded-full border border-transparent text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors dark:text-slate-300 dark:hover:text-slate-100"
      :class="{ 'bg-slate-100 text-slate-700': isOpen }"
    >
      <IconBell size="18" />
      <!-- Red dot for unread notifications -->
      <span
        v-if="notifications.unread_count > 0"
        class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center"
      >
        <span v-if="notifications.unread_count < 10" class="text-[10px] text-white font-bold">
          {{ notifications.unread_count }}
        </span>
      </span>
    </button>

    <!-- Notification Panel -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-slate-200 z-50 max-h-100 overflow-hidden"
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-4 border-b border-slate-200 bg-slate-50">
        <div class="flex items-center">
          <h3 class="font-semibold text-slate-900">Notifikasi Insiden</h3>
          <span
            v-if="notifications.unread_count > 0"
            class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-600 rounded-full"
          >
            {{ notifications.unread_count }} baru
          </span>
        </div>
        <div class="flex items-center space-x-2">
          <button
            v-if="notifications.unread_count > 0"
            @click="markAllAsRead"
            :disabled="isLoading"
            class="text-xs text-blue-600 hover:text-blue-700 disabled:opacity-50"
            title="Tandai semua sebagai dibaca"
          >
            <IconCheckbox size="16" />
          </button>
          <button @click="closePanel" class="text-slate-400 hover:text-slate-600">
            <IconX size="16" />
          </button>
        </div>
      </div>

      <!-- Notifications List -->
      <div class="max-h-80 overflow-y-auto">
        <!-- Unread Notifications -->
        <div v-if="notifications.unread.length > 0">
          <div class="px-3 py-2 text-xs font-medium text-slate-500 bg-slate-50 border-b border-slate-100">
            Belum Dibaca
          </div>
          <div
            v-for="incident in notifications.unread"
            :key="`unread-${incident.id}`"
            @click="goToIncident(incident)"
            class="flex items-start p-3 border-b border-slate-100 hover:bg-slate-100 cursor-pointer transition-colors bg-blue-25"
          >
            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3 flex-shrink-0"></div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <p class="text-sm font-medium text-slate-900 truncate">
                  {{ incident.case_id }}
                </p>
                <span
                  class="text-xs px-2 py-1 rounded-full"
                  :class="getPriorityColor(incident.priority)"
                >
                  {{ incident.priority }}
                </span>
              </div>
              <p class="text-sm text-slate-600 line-clamp-2 mb-1">
                {{ incident.description }}
              </p>
              <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">
                  {{ incident.incident_type?.name }}
                </span>
                <span class="text-xs text-slate-400">
                  {{ formatTimeAgo(incident.created_at) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Read Notifications -->
        <div v-if="notifications.read.length > 0">
          <div class="px-3 py-2 text-xs font-medium text-slate-500 bg-slate-50 border-b border-slate-100">
            Sudah Dibaca (10 Terakhir)
          </div>
          <div
            v-for="incident in notifications.read"
            :key="`read-${incident.id}`"
            @click="goToIncident(incident)"
            class="flex items-start p-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors opacity-75"
          >
            <div class="w-2 h-2 bg-slate-300 rounded-full mt-2 mr-3 flex-shrink-0"></div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <p class="text-sm font-medium text-slate-700 truncate">
                  {{ incident.case_id }}
                </p>
                <span
                  class="text-xs px-2 py-1 rounded-full opacity-60"
                  :class="getPriorityColor(incident.priority)"
                >
                  {{ incident.priority }}
                </span>
              </div>
              <p class="text-sm text-slate-500 line-clamp-2 mb-1">
                {{ incident.description }}
              </p>
              <div class="flex items-center justify-between">
                <span class="text-xs text-slate-400">
                  {{ incident.incident_type?.name }}
                </span>
                <span class="text-xs text-slate-400">
                  {{ formatTimeAgo(incident.read_at) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="notifications.unread.length === 0 && notifications.read.length === 0"
          class="p-8 text-center"
        >
          <IconBell size="30" class="text-slate-300 mx-auto mb-2" />
          <p class="text-slate-500">Tidak ada notifikasi</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-3 border-t border-slate-200 bg-slate-50">
        <button
          @click="() => { router.get(route('admin.incidents.index')); closePanel() }"
          class="w-full text-sm text-blue-500 hover:text-blue-700 font-medium"
        >
          Lihat Semua Insiden
        </button>
      </div>
    </div>
  </div>
</template>
