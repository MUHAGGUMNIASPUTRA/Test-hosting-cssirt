<script setup>
// filepath: resources/js/Layouts/Admin/AdminLayout.vue

import NotificationPanel from '@/Components/NotificationPanel.vue'
import AIAdminAssistant from '@/Components/AIAdminAssistant/AIAdminAssistant.vue'
import { useResponsive } from '@/Composables/useResponsive'
import { Head, router, usePage } from '@inertiajs/vue3'
import {
  IconChevronDown,
  IconLogout,
  IconMenu2,
  IconMoon,
  IconSun,
  IconUsers,
} from '@tabler/icons-vue'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import { computed, nextTick, onMounted, ref, watch } from 'vue'

defineProps({
  title: String,
})

const { isMobile, isDesktop } = useResponsive()
const page = usePage()
const toast = useToast()
const sidebarOpen = ref(false)
const userMenuOpen = ref(false)

// Dark mode
const darkMode = ref(false)

const initDarkMode = () => {
  try {
    const stored = localStorage.getItem('theme')
    if (stored === 'dark') darkMode.value = true
    else if (stored === 'light') darkMode.value = false
    else
      darkMode.value =
        window.matchMedia &&
        window.matchMedia('(prefers-color-scheme: dark)').matches
  } catch {
    darkMode.value = false
  }
  applyDarkClass()
}

const applyDarkClass = () => {
  const root = document.documentElement
  if (darkMode.value) root.classList.add('dark')
  else root.classList.remove('dark')
}

const toggleDarkMode = () => {
  darkMode.value = !darkMode.value
  try {
    localStorage.setItem('theme', darkMode.value ? 'dark' : 'light')
  } catch {}
  applyDarkClass()
}

watch(darkMode, () => applyDarkClass())

const userMenuItems = computed(() => [
  {
    label: 'Pengguna',
    icon: IconUsers,
    command: () => router.get(route('admin.users.index')),
    visible: () => page.props.auth.user?.role === 'admin',
  },
  {
    label: darkMode.value ? 'Mode Terang' : 'Mode Gelap',
    icon: darkMode.value ? IconSun : IconMoon,
    command: toggleDarkMode,
    visible: () => isMobile.value,
  },
  {
    separator: true,
    visible: () => page.props.auth.user?.role === 'admin',
  },
  {
    label: 'Logout',
    icon: IconLogout,
    command: () => router.post(route('logout')),
    class: 'text-red-600 hover:bg-red-50',
  },
])

const displayRole = (role) => {
  switch (role) {
    case 'admin':
      return 'Administrator'
    case 'editor':
      return 'Editor'
    case 'viewer':
      return 'Viewer'
    default:
      return 'User'
  }
}

const closeDropdowns = (event) => {
  if (!event.target.closest('.user-menu')) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdowns)
  initDarkMode()
})

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      nextTick(() => {
        toast.add({
          severity: 'success',
          summary: flash.success?.title || 'Berhasil',
          detail:
            flash.success?.message || flash.success || 'Operasi berhasil.',
          life: 4000,
        })
      })
    } else if (flash?.info) {
      nextTick(() => {
        toast.add({
          severity: 'info',
          summary: flash.info?.title || 'Informasi',
          detail: flash.info?.message || flash.info || 'Informasi penting.',
          life: 4000,
        })
      })
    } else if (flash?.warning) {
      nextTick(() => {
        toast.add({
          severity: 'warning',
          summary: flash.warning?.title || 'Peringatan',
          detail:
            flash.warning?.message || flash.warning || 'Perhatian diperlukan.',
          life: 4000,
        })
      })
    } else if (flash?.error) {
      nextTick(() => {
        toast.add({
          severity: 'error',
          summary: flash.error?.title || 'Kesalahan',
          detail: flash.error?.message || flash.error || 'Terjadi kesalahan.',
          life: 4000,
        })
      })
    }
  },
  { immediate: true },
)
</script>

<template>
  <div class="flex min-h-screen flex-col bg-slate-50">
    <Head :title="title" />

    <loading-page />

    <!-- Mobile sidebar backdrop -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-slate-600 bg-opacity-75 transition-opacity lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar component -->
    <AdminSidebar v-model:open="sidebarOpen" />

    <!-- Main content -->
    <div class="flex-1 lg:pl-72">
      <!-- Top navbar -->
      <header
        class="sticky top-0 z-30 border-b border-slate-200 bg-white shadow-sm"
      >
        <div class="flex h-[4.5rem] items-center justify-between px-4 lg:px-6">
          <div class="flex items-center">
            <button
              class="rounded-md p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-500 lg:hidden"
              @click="sidebarOpen = true"
            >
              <IconMenu2 size="20" />
            </button>
            <div class="ml-4 lg:ml-0">
              <h1 class="text-lg/5 font-semibold text-slate-900">
                {{
                  isDesktop
                    ? displayRole(page.props.auth.user?.role)
                    : 'CSIRT Bojonegoro: Admin Panel'
                }}
              </h1>
              <p class="text-xs text-slate-500">
                {{
                  new Date().toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                  })
                }}
              </p>
            </div>
          </div>

          <div class="flex items-center space-x-3">
            <!-- Dark mode toggle (desktop only) -->
            <button
              v-if="!isMobile"
              class="dark-mode-toggle rounded-full border border-transparent bg-slate-100 p-3 text-slate-500 transition-colors hover:bg-slate-200 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-slate-300 dark:hover:text-slate-100"
              :aria-label="
                darkMode ? 'Switch to light mode' : 'Switch to dark mode'
              "
              :title="darkMode ? 'Mode Terang' : 'Mode Gelap'"
              @click="toggleDarkMode"
            >
              <IconMoon v-if="!darkMode" size="18" />
              <IconSun v-else size="18" />
            </button>

            <!-- Notifications -->
            <NotificationPanel />

            <!-- User menu -->
            <div class="user-menu relative">
              <button
                class="flex items-center rounded-full bg-slate-100 p-2 text-sm hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                @click.stop="userMenuOpen = !userMenuOpen"
              >
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600"
                >
                  <span class="text-xs font-medium text-white">{{
                    page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U'
                  }}</span>
                </div>
                <IconChevronDown
                  size="16"
                  class="ml-1 text-slate-400 transition-transform"
                  :class="{ 'rotate-180': userMenuOpen }"
                />
              </button>

              <!-- Dropdown menu -->
              <div
                v-if="userMenuOpen"
                class="absolute right-0 z-50 mt-2 rounded-lg border border-slate-200 bg-white pb-1 shadow-lg"
              >
                <div
                  class="rounded-t-lg bg-slate-50 px-4 py-3 font-medium text-slate-700"
                >
                  <p>{{ page.props.auth.user?.name || 'User' }}</p>
                  <p class="text-sm text-slate-400">
                    {{ page.props.auth.user?.email || 'user@example.com' }}
                  </p>
                </div>
                <div class="mb-1 border-t border-slate-200"></div>
                <template v-for="item in userMenuItems" :key="item.label">
                  <div v-if="!item.visible || item.visible()">
                    <div
                      v-if="item.separator"
                      class="my-1 border-t border-slate-200"
                    ></div>
                    <button
                      v-else
                      class="flex w-full items-center px-4 py-2 transition-colors hover:bg-slate-100"
                      :class="item.class || 'text-slate-500'"
                      @click="item.command"
                    >
                      <component
                        v-if="item.icon"
                        :is="item.icon"
                        size="16"
                        class="mr-3"
                        :class="item.class ? item.class : 'text-slate-400'"
                      />{{ item.label }}
                    </button>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-4 lg:p-6">
        <slot />
      </main>
    </div>

    <!-- Footer -->
    <div
      class="mx-6 mb-0.5 border-t border-slate-200 py-5 text-center text-sm lg:text-right"
    >
      <p class="text-slate-400">
        &copy; {{ new Date().getFullYear() }}
        <a
          href="https://bojonegorokab.go.id/"
          target="_blank"
          class="transition-colors duration-200 hover:text-indigo-600"
          >Pemerintah Kabupaten Bojonegoro</a
        >. All rights reserved.
      </p>
    </div>

    <!-- AI Admin Assistant Floating Button -->
    <AIAdminAssistant />

    <Toast position="top-right" class="z-[9999]" />
  </div>
</template>
