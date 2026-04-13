<script setup>
// filepath: resources/js/Layouts/Admin/AdminLayout.vue

import { computed, ref, onMounted, nextTick, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'
import { useToast } from 'primevue/usetoast'
import Toast from 'primevue/toast'
import NotificationPanel from '@/Components/NotificationPanel.vue'

import {
  IconWorldCheck,
  IconLayoutBoard,
  IconUrgent,
  IconMailExclamation,
  IconBellPlus,
  IconTicTac,
  IconNews,
  IconArticle,
  IconTextPlus,
  IconBookmarks,
  IconFileDescription,
  IconFilePlus,
  IconFolders,
  IconHeartHandshake,
  IconHelp,
  IconSpeakerphone,
  IconUsers,
  IconLogout,
  IconSun,
  IconMoon,
  IconMenu2,
  IconChevronDown,
} from '@tabler/icons-vue'

defineProps({
  title: String,
})

const { isMobile, isDesktop } = useResponsive()
const page = usePage()
const toast = useToast()
const sidebarOpen = ref(false)
const userMenuOpen = ref(false)

// Dark mode state
const darkMode = ref(false)

// Initialize dark mode from localStorage or system preference
const initDarkMode = () => {
  try {
    const stored = localStorage.getItem('theme')
    if (stored === 'dark') darkMode.value = true
    else if (stored === 'light') darkMode.value = false
    else
      darkMode.value =
        window.matchMedia &&
        window.matchMedia('(prefers-color-scheme: dark)').matches
  } catch (e) {
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
  } catch (e) {}
  applyDarkClass()
}

// Sync html class if darkMode changes elsewhere
watch(darkMode, () => applyDarkClass())

// Sidebar items configuration
const sidebarItems = ref([
  {
    label: 'Website',
    icon: IconWorldCheck,
    route: 'landing',
  },
  { separator: true },
  {
    label: 'Dashboard',
    icon: IconLayoutBoard,
    route: 'admin.dashboard',
  },
  { separator: true },
  {
    label: 'Panduan',
    icon: IconFileDescription,
    items: [
      {
        label: 'Daftar Panduan',
        icon: IconFileDescription,
        route: 'admin.documents.index',
      },
      {
        label: 'Tambah Panduan',
        icon: IconFilePlus,
        route: 'admin.documents.create',
      },
      {
        label: 'Area Dokumen',
        icon: IconFolders,
        route: 'admin.document-areas.index',
      },
    ],
  },
  {
    label: 'Insiden',
    icon: IconUrgent,
    items: [
      {
        label: 'Daftar Insiden',
        icon: IconMailExclamation,
        route: 'admin.incidents.index',
      },
      {
        label: 'Lapor Insiden Baru',
        icon: IconBellPlus,
        route: 'admin.incidents.create',
      },
      {
        label: 'Kategori Insiden',
        icon: IconTicTac,
        route: 'admin.incident-types.index',
      },
    ],
  },
  { separator: true },
  {
    label: 'Konten',
    icon: IconNews,
    items: [
      {
        label: 'Daftar Artikel',
        icon: IconArticle,
        route: 'admin.posts.index',
      },
      {
        label: 'Tambah Artikel',
        icon: IconTextPlus,
        route: 'admin.posts.create',
      },
      {
        label: 'Kategori & Tag',
        icon: IconBookmarks,
        route: 'admin.taxonomy.index',
      },
    ],
  },
  { separator: true },
  {
    label: 'Layanan',
    icon: IconHeartHandshake,
    route: 'admin.services.index',
  },
  {
    label: 'FAQ',
    icon: IconHelp,
    route: 'admin.faqs.index',
  },
  {
    label: 'Pengumuman',
    icon: IconSpeakerphone,
    route: 'admin.announcements.index',
  },
])

// User menu items
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
    command: () => logout(),
    class: 'text-red-600 hover:bg-red-50',
  },
])

// Check if current route matches
const isCurrentRoute = (routeName) => {
  if (!routeName) return false
  try {
    return route().current(routeName)
  } catch (error) {
    return false
  }
}

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

// Toggle sidebar
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

// Close sidebar when clicking outside on mobile
const closeSidebarOnMobile = () => {
  if (window.innerWidth < 768) {
    sidebarOpen.value = false
  }
}

// Logout function
const logout = () => {
  router.post(route('logout'))
}

// Close dropdowns when clicking outside
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
      @click="toggleSidebar"
    ></div>

    <!-- Sidebar -->
    <div
      class="fixed inset-y-0 left-0 z-50 w-72 transform bg-white shadow-xl transition-transform duration-300 ease-in-out"
      :class="
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      "
    >
      <!-- Sidebar header -->
      <div
        class="flex h-[4.5rem] items-center justify-between bg-gradient-to-r from-indigo-600 to-blue-600 px-8"
      >
        <div class="flex items-center">
          <div
            class="mr-1 flex h-9 w-9 items-center justify-center rounded-full border-2 border-white/20 bg-white/10"
          >
            <IconShieldCheckFilled size="16" class="text-white" />
          </div>
          <div class="ml-3">
            <h1 class="text-lg/5 font-bold text-white">CSIRT Bojonegoro</h1>
            <p class="text-xs text-blue-100">Admin Panel</p>
          </div>
        </div>
        <button
          @click="toggleSidebar"
          class="rounded-md p-1 text-blue-100 hover:bg-white/10 lg:hidden"
        ></button>
      </div>

      <!-- Navigation -->
      <nav class="h-[calc(100vh-8.5rem)] flex-1 space-y-2 overflow-y-auto p-6">
        <template v-for="item in sidebarItems" :key="item.label">
          <div v-if="!item.visible || item.visible()">
            <div
              v-if="item.separator"
              class="my-1 border-t border-slate-100"
            ></div>
            <div v-else>
              <!-- Single menu item -->
              <Link
                v-if="item.route && !item.items"
                :href="route(item.route)"
                @click="closeSidebarOnMobile"
                class="group flex items-center rounded-lg px-3 py-2 transition-colors"
                :class="[
                  isCurrentRoute(item.route)
                    ? 'border-r-2 border-indigo-500 bg-indigo-50 font-medium text-indigo-700'
                    : 'font-normal text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                  item.label === 'Website'
                    ? '!text-indigo-500 hover:!text-indigo-700'
                    : '',
                ]"
              >
                <component
                  :is="item.icon"
                  size="18"
                  stroke-width="1.75"
                  class="mr-3"
                  :class="[
                    isCurrentRoute(item.route)
                      ? 'text-indigo-500'
                      : 'text-slate-400',
                    item.label === 'Website' ? '!text-indigo-500' : '',
                  ]"
                />{{ item.label }}
              </Link>

              <!-- Menu with subitems -->
              <div
                v-else-if="item.items && item.items.length > 0"
                class="space-y-1"
              >
                <div
                  class="flex items-center px-3 py-2 font-medium text-slate-600"
                >
                  <component
                    :is="item.icon"
                    size="18"
                    stroke-width="1.75"
                    class="mr-3 text-slate-400"
                  />{{ item.label }}
                </div>
                <div class="ml-6 space-y-1">
                  <Link
                    v-for="subItem in item.items"
                    :key="subItem.label"
                    :href="route(subItem.route)"
                    @click="closeSidebarOnMobile"
                    class="flex items-center rounded-md px-3 py-2 transition-colors"
                    :class="
                      isCurrentRoute(subItem.route)
                        ? 'border-r-2 border-indigo-500 bg-indigo-50 font-medium text-indigo-700'
                        : 'font-normal text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    "
                  >
                    <component
                      :is="subItem.icon"
                      size="18"
                      stroke-width="1.75"
                      class="mr-3"
                      :class="
                        isCurrentRoute(subItem.route)
                          ? 'text-indigo-500'
                          : 'text-slate-400'
                      "
                    />{{ subItem.label }}
                  </Link>
                </div>
              </div>

              <!-- Single menu item without route (disabled) -->
              <div
                v-else-if="!item.route"
                class="flex cursor-not-allowed items-center px-3 py-2 font-normal text-slate-400"
              >
                <component
                  :is="item.icon"
                  size="18"
                  stroke-width="1.75"
                  class="mr-3"
                />{{ item.label }}
                <span
                  class="ml-auto rounded bg-slate-100 px-2 py-1 text-xs text-slate-500"
                  >Soon</span
                >
              </div>
            </div>
          </div>
        </template>
      </nav>

      <!-- User info at bottom -->
      <div class="border-t border-slate-200 p-4 px-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div
              class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600"
            >
              <span class="text-xs font-medium text-white">
                {{
                  page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U'
                }}
              </span>
            </div>
          </div>
          <div class="ml-3 min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-slate-900">
              {{ page.props.auth.user?.name || 'User' }}
            </p>
            <p class="truncate text-xs text-slate-500">
              {{ page.props.auth.user?.email || 'user@example.com' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 lg:pl-72">
      <!-- Top navbar -->
      <header
        class="sticky top-0 z-30 border-b border-slate-200 bg-white shadow-sm"
      >
        <div class="flex h-[4.5rem] items-center justify-between px-4 lg:px-6">
          <div class="flex items-center">
            <button
              @click="toggleSidebar"
              class="rounded-md p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-500 lg:hidden"
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
            <!-- Dark mode toggle -->
            <button
              v-if="!isMobile"
              @click="toggleDarkMode"
              class="dark-mode-toggle rounded-full border border-transparent bg-slate-100 p-3 text-slate-500 transition-colors hover:bg-slate-200 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-slate-300 dark:hover:text-slate-100"
              :aria-label="
                darkMode ? 'Switch to light mode' : 'Switch to dark mode'
              "
              :title="darkMode ? 'Mode Terang' : 'Mode Gelap'"
            >
              <IconMoon v-if="!darkMode" size="18" />
              <IconSun v-else size="18" />
            </button>

            <!-- Notifications -->
            <NotificationPanel />

            <!-- User menu -->
            <div class="user-menu relative">
              <button
                @click.stop="userMenuOpen = !userMenuOpen"
                class="flex items-center rounded-full bg-slate-100 p-2 text-sm hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
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

              <!-- User dropdown menu -->
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
                      @click="item.command"
                      class="flex w-full items-center px-4 py-2 transition-colors hover:bg-slate-100"
                      :class="item.class || 'text-slate-500'"
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

    <!-- Toast notifications -->
    <Toast position="top-right" class="z-[9999]" />
  </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: transparent;
}

nav::-webkit-scrollbar-thumb {
  background: rgba(148, 163, 184, 0.3);
  border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 0.5);
}
</style>
