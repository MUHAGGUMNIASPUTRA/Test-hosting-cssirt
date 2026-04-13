<script setup>
// filepath: resources/js/Components/NotificationPanel.vue

import { router } from '@inertiajs/vue3'
import { onMounted, onUnmounted, ref } from 'vue'

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
        'X-CSRF-TOKEN': document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute('content'),
      },
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
        'X-CSRF-TOKEN': document
          .querySelector('meta[name="csrf-token"]')
          ?.getAttribute('content'),
      },
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
    Rendah: 'text-green-600 bg-green-50',
    Sedang: 'text-sky-600 bg-sky-50',
    Tinggi: 'text-orange-600 bg-orange-50',
    Kritikal: 'text-red-600 bg-red-50',
  }
  return colors[priority] || 'text-slate-600 bg-slate-50'
}

onMounted(() => {
  fetchNotifications()
})

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
})
</script>

<template>
  <div class="notification-panel relative">
    <!-- Notification Bell Button -->
    <button
      @click.stop="togglePanel"
      class="rounded-full border border-transparent bg-slate-100 p-3 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-300 dark:hover:text-slate-100"
      :class="{ 'bg-slate-100 text-slate-700': isOpen }"
    >
      <IconBell size="18" />
      <!-- Red dot for unread notifications -->
      <span
        v-if="notifications.unread_count > 0"
        class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500"
      >
        <span
          v-if="notifications.unread_count < 10"
          class="text-[10px] font-bold text-white"
        >
          {{ notifications.unread_count }}
        </span>
      </span>
    </button>

    <!-- Notification Panel -->
    <div
      v-if="isOpen"
      class="max-h-100 absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg"
    >
      <!-- Header -->
      <div
        class="flex items-center justify-between border-b border-slate-200 bg-slate-50 p-4"
      >
        <div class="flex items-center">
          <h3 class="font-semibold text-slate-900">Notifikasi Insiden</h3>
          <span
            v-if="notifications.unread_count > 0"
            class="ml-2 rounded-full bg-red-100 px-2 py-1 text-xs text-red-600"
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
          <button
            @click="closePanel"
            class="text-slate-400 hover:text-slate-600"
          >
            <IconX size="16" />
          </button>
        </div>
      </div>

      <!-- Notifications List -->
      <div class="max-h-80 overflow-y-auto">
        <!-- Unread Notifications -->
        <div v-if="notifications.unread.length > 0">
          <div
            class="border-b border-slate-100 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-500"
          >
            Belum Dibaca
          </div>
          <div
            v-for="incident in notifications.unread"
            :key="`unread-${incident.id}`"
            @click="goToIncident(incident)"
            class="bg-blue-25 flex cursor-pointer items-start border-b border-slate-100 p-3 transition-colors hover:bg-slate-100"
          >
            <div
              class="mr-3 mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-blue-500"
            ></div>
            <div class="min-w-0 flex-1">
              <div class="mb-1 flex items-center justify-between">
                <p class="truncate text-sm font-medium text-slate-900">
                  {{ incident.case_id }}
                </p>
                <span
                  class="rounded-full px-2 py-1 text-xs"
                  :class="getPriorityColor(incident.priority)"
                >
                  {{ incident.priority }}
                </span>
              </div>
              <p class="mb-1 line-clamp-2 text-sm text-slate-600">
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
          <div
            class="border-b border-slate-100 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-500"
          >
            Sudah Dibaca (10 Terakhir)
          </div>
          <div
            v-for="incident in notifications.read"
            :key="`read-${incident.id}`"
            @click="goToIncident(incident)"
            class="flex cursor-pointer items-start border-b border-slate-100 p-3 opacity-75 transition-colors hover:bg-slate-50"
          >
            <div
              class="mr-3 mt-2 h-2 w-2 flex-shrink-0 rounded-full bg-slate-300"
            ></div>
            <div class="min-w-0 flex-1">
              <div class="mb-1 flex items-center justify-between">
                <p class="truncate text-sm font-medium text-slate-700">
                  {{ incident.case_id }}
                </p>
                <span
                  class="rounded-full px-2 py-1 text-xs opacity-60"
                  :class="getPriorityColor(incident.priority)"
                >
                  {{ incident.priority }}
                </span>
              </div>
              <p class="mb-1 line-clamp-2 text-sm text-slate-500">
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
          v-if="
            notifications.unread.length === 0 && notifications.read.length === 0
          "
          class="p-8 text-center"
        >
          <IconBell size="30" class="mx-auto mb-2 text-slate-300" />
          <p class="text-slate-500">Tidak ada notifikasi</p>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-t border-slate-200 bg-slate-50 p-3">
        <button
          @click="
            () => {
              router.get(route('admin.incidents.index'))
              closePanel()
            }
          "
          class="w-full text-sm font-medium text-blue-500 hover:text-blue-700"
        >
          Lihat Semua Insiden
        </button>
      </div>
    </div>
  </div>
</template>
