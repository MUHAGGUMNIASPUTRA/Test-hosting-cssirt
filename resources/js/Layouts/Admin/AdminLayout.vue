<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

// Define props if you need to pass a title from the page
defineProps({
  title: String,
});

const page = usePage();
const menu = ref();

// Menu items for the user dropdown
const userMenuItems = ref([
  {
    label: 'Log Out',
    icon: 'pi pi-power-off',
    command: () => {
      router.post(route('logout'));
    }
  }
]);

const sidebarItems = ref([
  {
    label: 'Dashboard',
    icon: 'pi pi-home',
    route: 'admin.dashboard',
  },
  {
    label: 'Insiden',
    icon: 'pi pi-shield',
    items: [
      {
        label: 'Daftar Insiden',
        icon: 'pi pi-fw pi-list',
        route: 'admin.incidents.index'
      },
      {
        label: 'Lapor Insiden Baru',
        icon: 'pi pi-fw pi-plus',
        route: 'admin.incidents.create'
      },
    ],
  },
  {
    label: 'Konten',
    icon: 'pi pi-pencil',
    items: [
      {
        label: 'Daftar Artikel',
        icon: 'pi pi-fw pi-file',
        route: 'admin.posts.index'
      },
      {
        label: 'Tambah Artikel',
        icon: 'pi pi-fw pi-plus-circle',
        route: 'admin.posts.create'
      },
      {
        label: 'Kategori & Tag',
        icon: 'pi pi-fw pi-tags',
        route: 'admin.taxonomy.index'
      }
    ],
  },
  {
    label: 'Pengguna',
    icon: 'pi pi-users',
    route: 'admin.users.index',
    visible: () => page.props.auth.user.role === 'admin'
  },
])

const toggleUserMenu = (event) => {
  menu.value.toggle(event);
};
</script>

<template>
  <Head :title="$page.props.title || 'Admin Panel CSIRT'" />
  <loading-page />

  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-50">
      <h1 class="text-xl font-semibold text-gray-800">Admin Panel CSIRT</h1>
      <div>
        <Button
          type="button"
          :label="page.props.auth.user.name"
          @click="toggleUserMenu"
          aria-haspopup="true"
          aria-controls="overlay_menu"
          icon="pi pi-user"
          severity="secondary"
          text
        />
        <Menu ref="menu" id="overlay_menu" :model="userMenuItems" :popup="true" />
      </div>
    </header>

    <div class="flex">
      <aside class="w-64 bg-gray-800 text-white p-4 hidden md:block">
        <nav>
          <ul>
            <template v-for="item in sidebarItems" :key="item.label">
              <li class="mb-2" v-if="!item.visible || item.visible()">
                <!-- Renders a single link -->
                <Link v-if="item.route && !item.items" :href="item.route === '#' ? '#' : route(item.route)" class="flex items-center p-2 rounded hover:bg-gray-700" :class="{ 'bg-gray-600 font-bold': item.route !== '#' && route().current(item.route) }">
                  <i :class="item.icon" class="mr-2"></i>
                  <span>{{ item.label }}</span>
                </Link>
                <!-- Renders a dropdown menu -->
                <div v-else-if="item.items">
                  <span class="flex items-center p-2 text-gray-400">
                    <i :class="item.icon" class="mr-2"></i>
                    <span>{{ item.label }}</span>
                  </span>
                  <ul class="ml-4 mt-1 border-l border-gray-700">
                    <li v-for="subItem in item.items" :key="subItem.label" class="mb-1">
                      <Link :href="subItem.route === '#' ? '#' : route(subItem.route)" class="block p-2 pl-4 rounded hover:bg-gray-700 text-sm" :class="{ 'bg-gray-600 font-bold': subItem.route !== '#' && route().current(subItem.route) }">
                        {{ subItem.label }}
                      </Link>
                    </li>
                  </ul>
                </div>
              </li>
            </template>
          </ul>
        </nav>
      </aside>

      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
