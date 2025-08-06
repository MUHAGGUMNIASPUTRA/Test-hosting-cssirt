<script setup>
// filepath: resources/js/Layouts/Admin/AdminLayout.vue

import { ref, onMounted, nextTick, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'
import { useToast } from 'primevue/usetoast'
import Toast from 'primevue/toast'

import {
  IconWorldCheck, IconLayoutBoard, IconUrgent,
  IconMailExclamation, IconBellPlus, IconNews,
  IconArticle, IconTextPlus, IconBookmarks,
  IconBook2, IconNotebook, IconFilePlus,
  IconHeartHandshake, IconHelp, IconSpeakerphone, IconUsers,
} from '@tabler/icons-vue';

defineProps({
  title: String,
})

const { isDesktop } = useResponsive()
const page = usePage()
const toast = useToast()
const sidebarOpen = ref(false)
const userMenuOpen = ref(false)

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
  {
    label: 'Insiden',
    icon: IconUrgent,
    items: [
      {
        label: 'Daftar Insiden',
        icon: IconMailExclamation,
        route: 'admin.incidents.index'
      },
      {
        label: 'Lapor Insiden Baru',
        icon: IconBellPlus,
        route: 'admin.incidents.create'
      },
    ],
  },
  {
    label: 'Konten',
    icon: IconNews,
    items: [
      {
        label: 'Daftar Artikel',
        icon: IconArticle,
        route: 'admin.posts.index'
      },
      {
        label: 'Tambah Artikel',
        icon: IconTextPlus,
        route: 'admin.posts.create'
      },
      {
        label: 'Kategori & Tag',
        icon: IconBookmarks,
        route: 'admin.taxonomy.index'
      }
    ],
  },
  {
    label: 'Panduan',
    icon: IconBook2,
    items: [
      {
        label: 'Daftar Panduan',
        icon: IconNotebook,
        route: 'admin.documents.index'
      },
      {
        label: 'Tambah Panduan',
        icon: IconFilePlus,
        route: 'admin.documents.create'
      },
    ],
  },
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
  {
    label: 'Pengguna',
    icon: IconUsers,
    route: 'admin.users.index',
    visible: () => page.props.auth.user?.role === 'admin'
  },
])

// User menu items
const userMenuItems = ref([
  {
    label: 'Logout',
    icon: 'pi pi-power-off',
    command: () => logout(),
    class: 'text-red-600 hover:bg-red-50'
  }
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
})

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      nextTick(() => {
        toast.add({
          severity: "success",
          summary: flash.success?.title || "Berhasil",
          detail: flash.success?.message || flash.success || "Operasi berhasil.",
          life: 4000,
        });
      });
    } else if (flash?.info) {
      nextTick(() => {
        toast.add({
          severity: "info",
          summary: flash.info?.title || "Informasi",
          detail: flash.info?.message || flash.info || "Informasi penting.",
          life: 4000,
        });
      });
    } else if (flash?.warning) {
      nextTick(() => {
        toast.add({
          severity: "warning",
          summary: flash.warning?.title || "Peringatan",
          detail: flash.warning?.message || flash.warning || "Perhatian diperlukan.",
          life: 4000,
        });
      });
    } else if (flash?.error) {
      nextTick(() => {
        toast.add({
          severity: "error",
          summary: flash.error?.title || "Kesalahan",
          detail: flash.error?.message || flash.error || "Terjadi kesalahan.",
          life: 4000,
        });
      });
    }
  },
  { immediate: true }
);
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
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
      class="fixed inset-y-0 left-0 z-50 bg-white shadow-xl transform transition-transform duration-300 ease-in-out"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
      <!-- Sidebar header -->
      <div class="flex items-center justify-between h-16 px-6 bg-gradient-to-r from-indigo-600 to-blue-600">
        <div class="flex items-center">
          <div class="h-9 w-9 rounded-full bg-white/10 border-2 border-white/20 flex items-center justify-center mr-1">
            <IconShieldCheckFilled size="16" class="text-white"/>
          </div>
          <div class="ml-3">
            <h1 class="font-bold text-white">CSIRT Bojonegoro</h1>
            <p class="text-xs text-blue-100">Admin Panel</p>
          </div>
        </div>
        <button
          @click="toggleSidebar"
          class="p-1 rounded-md text-blue-100 hover:bg-white/10 lg:hidden"
        >
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto h-[calc(100vh-8rem)]">
        <template v-for="item in sidebarItems" :key="item.label">
          <div v-if="!item.visible || item.visible()">
            <div v-if="item.separator" class="border-t border-slate-200 my-1"></div>
            <div v-else>
            <!-- Single menu item -->
              <Link
                v-if="item.route && !item.items"
                :href="route(item.route)"
                @click="closeSidebarOnMobile"
                class="flex items-center px-3 py-2 font-medium rounded-lg transition-colors group"
                :class="isCurrentRoute(item.route) ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
              >
                <component :is="item.icon" size="18" stroke-width="1.75" class="mr-3" :class="[isCurrentRoute(item.route) ? 'text-indigo-500' : 'text-slate-400']" />{{ item.label }}
              </Link>

              <!-- Menu with subitems -->
              <div v-else-if="item.items && item.items.length > 0" class="space-y-1">
                <div class="flex items-center px-3 py-2 font-medium text-slate-600">
                  <component :is="item.icon" size="18" stroke-width="1.75" class="mr-3 text-slate-400" />{{ item.label }}
                </div>
                <div class="ml-6 space-y-1">
                  <Link
                    v-for="subItem in item.items"
                    :key="subItem.label"
                    :href="route(subItem.route)"
                    @click="closeSidebarOnMobile"
                    class="flex items-center px-3 py-2 rounded-md transition-colors"
                    :class="isCurrentRoute(subItem.route) ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-500 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                  >
                    <component :is="subItem.icon" size="18" stroke-width="1.75" class="mr-3" :class="[isCurrentRoute(subItem.route) ? 'text-indigo-500' : 'text-slate-400']" />{{ subItem.label }}
                  </Link>
                </div>
              </div>

              <!-- Single menu item without route (disabled) -->
              <div v-else-if="!item.route" class="flex items-center px-3 py-2 font-medium text-slate-400 cursor-not-allowed">
                <component :is="item.icon" size="18" stroke-width="1.75" class="mr-3" />{{ item.label }}
                <span class="ml-auto text-xs bg-slate-100 text-slate-500 px-2 py-1 rounded">Soon</span>
              </div>

            </div>
          </div>
        </template>
      </nav>

      <!-- User info at bottom -->
      <div class="border-t border-slate-200 p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
              <span class="text-xs font-medium text-white">
                {{ page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </span>
            </div>
          </div>
          <div class="ml-3 flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-900 truncate">{{ page.props.auth.user?.name || 'User' }}</p>
            <p class="text-xs text-slate-500 truncate">{{ page.props.auth.user?.email || 'user@example.com' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 lg:pl-64">
      <!-- Top navbar -->
      <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-30">
        <div class="flex items-center justify-between h-16 px-3 lg:px-6">
          <div class="flex items-center">
            <button @click="toggleSidebar" class="p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 lg:hidden">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <div class="ml-4 lg:ml-0">
              <h1 class="text-lg font-semibold text-slate-900">{{ isDesktop ? title : 'CSIRT Bojonegoro: Admin Panel' }}</h1>
              <p class="text-xs text-slate-500">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <!-- User menu -->
            <div class="relative user-menu">
              <button
                @click.stop="userMenuOpen = !userMenuOpen"
                class="flex items-center p-2 text-sm rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                  <span class="text-xs font-medium text-white">{{ page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}</span>
                </div>
                <span class="ml-2 text-slate-700 hidden sm:block">{{ page.props.auth.user?.name || 'User' }}</span>
                <svg class="ml-1 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- User dropdown menu -->
              <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
                <template v-for="item in userMenuItems" :key="item.label">
                  <div v-if="item.separator" class="border-t border-slate-200 my-1"></div>
                  <button
                    v-else
                    @click="item.command"
                    class="flex items-center w-full px-4 py-2 text-sm hover:bg-slate-50 transition-colors"
                    :class="item.class || 'text-slate-700'"
                  >
                    <i :class="[item.icon, item.class ? 'text-red-500' : 'text-slate-400']" class="w-4 h-4 mr-3"></i>{{ item.label }}
                  </button>
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
    <div class="text-sm py-5 mx-6 border-t border-slate-200 text-center lg:text-right mb-0.5">
      <p class="text-slate-400">
        &copy; {{ new Date().getFullYear() }}
        <a href="https://bojonegorokab.go.id/" target="_blank" class="hover:text-blue-600 transition-colors duration-200">Pemerintah Kabupaten Bojonegoro</a>. All rights reserved.
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
