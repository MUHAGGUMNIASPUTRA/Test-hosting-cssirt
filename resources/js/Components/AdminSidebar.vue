<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import {
  IconArticle,
  IconBellPlus,
  IconBookDownload,
  IconBookmarks,
  IconBriefcase,
  IconBuilding,
  IconCloud,
  IconCode,
  IconDatabase,
  IconDeviceMobile,
  IconFileDescription,
  IconFilePlus,
  IconFolders,
  IconHeartHandshake,
  IconHelp,
  IconKey,
  IconLayoutBoard,
  IconList,
  IconMailExclamation,
  IconMapPin,
  IconNetwork,
  IconNews,
  IconPlus,
  IconServer,
  IconShieldCheckFilled,
  IconSpeakerphone,
  IconStack2,
  IconTag,
  IconTextPlus,
  IconTicTac,
  IconTruck,
  IconUrgent,
  IconUser,
  IconUsers,
  IconWorldCheck,
  IconWorldWww,
} from '@tabler/icons-vue'

defineProps({
  open: { type: Boolean, default: false },
})

const emit = defineEmits(['update:open'])
const page = usePage()

const close = () => emit('update:open', false)

const closeSidebarOnMobile = () => {
  if (window.innerWidth < 1024) close()
}

const isCurrentRoute = (routeName) => {
  if (!routeName) return false
  try {
    return route().current(routeName)
  } catch {
    return false
  }
}

const sidebarItems = [
  { label: 'Website', icon: IconWorldCheck, route: 'landing' },
  { separator: true },
  { label: 'Dashboard', icon: IconLayoutBoard, route: 'admin.dashboard' },
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
  { separator: true },
  {
    label: 'Aset Virtual',
    icon: IconCloud,
    items: [
      {
        label: 'Aplikasi Web',
        icon: IconWorldWww,
        subItems: [
          {
            label: 'Daftar Aplikasi Web',
            icon: IconList,
            route: 'admin.web-applications.index',
          },
          {
            label: 'Pengajuan Aplikasi Web',
            icon: IconPlus,
            route: 'admin.web-applications.create',
          },
        ],
      },
      {
        label: 'Aplikasi Mobile',
        icon: IconDeviceMobile,
        subItems: [
          {
            label: 'Daftar Aplikasi Mobile',
            icon: IconList,
            route: 'admin.mobile-applications.index',
          },
          {
            label: 'Pengajuan Aplikasi Mobile',
            icon: IconPlus,
            route: 'admin.mobile-applications.create',
          },
        ],
      },
      {
        label: 'Lisensi',
        icon: IconKey,
        subItems: [
          {
            label: 'Daftar Lisensi',
            icon: IconList,
            route: 'admin.licenses.index',
          },
          {
            label: 'Pengajuan Lisensi',
            icon: IconPlus,
            route: 'admin.licenses.create',
          },
        ],
      },
      {
        label: 'Pengembangan',
        icon: IconCode,
        subItems: [
          {
            label: 'Daftar Tech Stack',
            icon: IconStack2,
            route: 'admin.tech-stacks.index',
          },
          {
            label: 'Kategori Tech Stack',
            icon: IconTag,
            route: 'admin.tech-stack-categories.index',
          },
          {
            label: 'Panduan Aset Virtual',
            icon: IconBookDownload,
            route: 'admin.virtual-asset-guides.index',
          },
          {
            label: 'Daftar IP',
            icon: IconNetwork,
            route: 'admin.ip-addresses.index',
          },
          {
            label: 'Daftar Subdomain',
            icon: IconWorldWww,
            route: 'admin.subdomains.index',
          },
        ],
      },
    ],
  },
  {
    label: 'Aset SDM',
    icon: IconUsers,
    items: [
      { label: 'Pegawai', icon: IconUser, route: 'admin.employees.index' },
      {
        label: 'Jabatan',
        icon: IconBriefcase,
        route: 'admin.positions.index',
      },
      { label: 'Lokasi', icon: IconMapPin, route: 'admin.locations.index' },
      {
        label: 'Bidang',
        icon: IconBuilding,
        route: 'admin.departments.index',
      },
      {
        label: 'Organisasi',
        icon: IconUsers,
        route: 'admin.organizations.index',
      },
      { label: 'Vendor', icon: IconTruck, route: 'admin.vendors.index' },
    ],
  },
  {
    label: 'Aset Fisik',
    icon: IconServer,
    items: [
      {
        label: 'Daftar Aset Fisik',
        icon: IconFileDescription,
        route: 'admin.physical-assets.index',
      },
      {
        label: 'Tambah Aset Fisik',
        icon: IconFilePlus,
        route: 'admin.physical-assets.create',
      },
    ],
  },
  {
    label: 'Aset Informasi',
    icon: IconDatabase,
    items: [
      {
        label: 'Daftar Aset Informasi',
        icon: IconFileDescription,
        route: 'admin.information-assets.index',
      },
      {
        label: 'Tambah Aset Informasi',
        icon: IconFilePlus,
        route: 'admin.information-assets.create',
      },
    ],
  },
  { separator: true },
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
  { label: 'FAQ', icon: IconHelp, route: 'admin.faqs.index' },
  {
    label: 'Pengumuman',
    icon: IconSpeakerphone,
    route: 'admin.announcements.index',
  },
]
</script>

<template>
  <div
    class="fixed inset-y-0 left-0 z-50 w-72 transform bg-white shadow-xl transition-transform duration-300 ease-in-out"
    :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
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
        class="rounded-md p-1 text-blue-100 hover:bg-white/10 lg:hidden"
        @click="close"
      ></button>
    </div>

    <!-- Navigation -->
    <nav
      class="h-[calc(100vh-8.5rem)] flex-1 space-y-1 overflow-y-auto p-4 pb-2"
    >
      <template
        v-for="item in sidebarItems"
        :key="item.label ?? 'sep-' + Math.random()"
      >
        <div v-if="item.separator" class="my-1 border-t border-slate-100"></div>

        <div v-else>
          <!-- Single link -->
          <Link
            v-if="item.route && !item.items"
            :href="route(item.route)"
            class="group flex items-center rounded-lg px-3 py-2 text-sm transition-colors"
            :class="[
              isCurrentRoute(item.route)
                ? 'border-r-2 border-indigo-500 bg-indigo-50 font-medium text-indigo-700'
                : 'font-normal text-slate-600 hover:bg-slate-50 hover:text-slate-900',
              item.label === 'Website'
                ? '!text-indigo-500 hover:!text-indigo-700'
                : '',
            ]"
            @click="closeSidebarOnMobile"
          >
            <component
              :is="item.icon"
              size="18"
              stroke-width="1.75"
              class="mr-3 flex-shrink-0"
              :class="[
                isCurrentRoute(item.route)
                  ? 'text-indigo-500'
                  : 'text-slate-400',
                item.label === 'Website' ? '!text-indigo-500' : '',
              ]"
            />
            {{ item.label }}
          </Link>

          <!-- Group with sub-items -->
          <div
            v-else-if="item.items && item.items.length > 0"
            class="space-y-0.5"
          >
            <div
              class="flex items-center px-3 py-2 text-sm font-medium text-slate-600"
            >
              <component
                :is="item.icon"
                size="18"
                stroke-width="1.75"
                class="mr-3 flex-shrink-0 text-slate-400"
              />
              {{ item.label }}
            </div>

            <div class="ml-6 space-y-0.5">
              <template v-for="subItem in item.items" :key="subItem.label">
                <!-- Sub-group (3-level) -->
                <div v-if="subItem.subItems" class="space-y-0.5">
                  <div
                    class="flex items-center px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    <component
                      :is="subItem.icon"
                      size="14"
                      stroke-width="1.75"
                      class="mr-2 flex-shrink-0"
                    />
                    {{ subItem.label }}
                  </div>
                  <div class="ml-4 space-y-0.5">
                    <Link
                      v-for="leaf in subItem.subItems"
                      :key="leaf.label"
                      :href="route(leaf.route)"
                      class="flex items-center rounded-md px-3 py-2 text-sm transition-colors"
                      :class="
                        isCurrentRoute(leaf.route)
                          ? 'border-r-2 border-indigo-500 bg-indigo-50 font-medium text-indigo-700'
                          : 'font-normal text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                      "
                      @click="closeSidebarOnMobile"
                    >
                      <component
                        :is="leaf.icon"
                        size="15"
                        stroke-width="1.75"
                        class="mr-2.5 flex-shrink-0"
                        :class="
                          isCurrentRoute(leaf.route)
                            ? 'text-indigo-500'
                            : 'text-slate-400'
                        "
                      />
                      {{ leaf.label }}
                    </Link>
                  </div>
                </div>

                <!-- Direct sub-link -->
                <Link
                  v-else
                  :href="route(subItem.route)"
                  class="flex items-center rounded-md px-3 py-2 text-sm transition-colors"
                  :class="
                    isCurrentRoute(subItem.route)
                      ? 'border-r-2 border-indigo-500 bg-indigo-50 font-medium text-indigo-700'
                      : 'font-normal text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                  "
                  @click="closeSidebarOnMobile"
                >
                  <component
                    :is="subItem.icon"
                    size="18"
                    stroke-width="1.75"
                    class="mr-3 flex-shrink-0"
                    :class="
                      isCurrentRoute(subItem.route)
                        ? 'text-indigo-500'
                        : 'text-slate-400'
                    "
                  />
                  {{ subItem.label }}
                </Link>
              </template>
            </div>
          </div>

          <!-- Disabled item (no route) -->
          <div
            v-else-if="!item.route"
            class="flex cursor-not-allowed items-center px-3 py-2 text-sm font-normal text-slate-400"
          >
            <component
              :is="item.icon"
              size="18"
              stroke-width="1.75"
              class="mr-3 flex-shrink-0"
            />
            {{ item.label }}
            <span
              class="ml-auto rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-500"
              >Soon</span
            >
          </div>
        </div>
      </template>
    </nav>

    <!-- User info -->
    <div class="border-t border-slate-200 p-4 px-6">
      <div class="flex items-center">
        <div
          class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-indigo-600"
        >
          <span class="text-xs font-medium text-white">
            {{ page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
          </span>
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
</template>

<style scoped>
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
