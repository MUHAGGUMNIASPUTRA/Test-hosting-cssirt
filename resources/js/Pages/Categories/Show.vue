<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

// The 'category' and 'posts' props are passed from CategoryController
defineProps({
  category: Object,
  posts: Object,
})
</script>

<template>
  <AppLayout :title="`Berita Kategori: ${category.name}`">
    <div class="bg-gray-50 px-4 pb-20 pt-16 sm:px-6 lg:px-8 lg:pb-28 lg:pt-24">
      <div
        class="relative mx-auto max-w-lg divide-y-2 divide-gray-200 lg:max-w-7xl"
      >
        <div>
          <h2
            class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl"
          >
            Kategori: <span class="text-purple-600">{{ category.name }}</span>
          </h2>
          <p class="mt-3 text-xl text-gray-500 sm:mt-4">
            Menampilkan semua artikel dalam kategori "{{ category.name }}".
          </p>
        </div>
        <div
          class="mt-12 grid gap-16 pt-12 lg:grid-cols-3 lg:gap-x-5 lg:gap-y-12"
        >
          <!-- This part is identical to Posts/Index.vue -->
          <div
            v-for="post in posts.data"
            :key="post.id"
            class="flex flex-col overflow-hidden rounded-lg shadow-lg"
          >
            <Link v-if="post.image" :href="route('posts.show', post.slug)" class="flex-shrink-0">
              <img
                :src="post.image.startsWith('http') ? post.image : '/storage/' + post.image"
                :alt="post.title"
                class="h-48 w-full object-cover"
              />
            </Link>
            <div class="flex flex-1 flex-col justify-between bg-white p-6">
              <div class="flex-1">
                <div class="text-xs font-medium text-purple-600 mb-3">
                  <span v-if="post.categories?.length > 0" class="space-x-2">
                    <template v-for="(cat, index) in post.categories" :key="cat.id">
                      <Link
                        :href="route('categories.show', cat.slug)"
                        class="relative z-10 rounded-md bg-purple-50 px-2 py-1 font-medium text-purple-600 ring-1 ring-inset ring-purple-500/10"
                        >{{ cat.name }}</Link
                      >
                    </template>
                  </span>
                  <span v-else>Artikel</span>
                </div>
                <Link :href="route('posts.show', post.slug)" class="mt-2 block">
                  <p class="text-xl font-semibold text-gray-900">
                    {{ post.title }}
                  </p>
                  <p class="mt-3 text-base text-gray-500">{{ post.excerpt }}</p>
                </Link>
              </div>
              <div class="mt-6 flex items-center">
                <div class="flex-shrink-0">
                  <i class="pi pi-user-edit text-2xl text-gray-400"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ post.published_by }}
                  </p>
                  <div class="flex space-x-1 text-sm text-gray-500">
                    <time :datetime="post.published_at">{{
                      new Date(post.published_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                      })
                    }}</time>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Pagination -->
        <!-- FIXED: Check if posts.links exists before accessing its length -->
        <div
          v-if="posts.links && posts.links.length > 3"
          class="mt-12 flex justify-center"
        >
          <div class="flex rounded-md shadow-sm">
            <Link
              v-for="(link, key) in posts.links"
              :key="key"
              :href="link.url"
              v-html="link.label"
              class="border px-4 py-2 text-sm font-medium"
              :class="{
                'border-red-500 bg-red-500 text-white': link.active,
                'border-gray-300 bg-white text-gray-500 hover:bg-gray-50':
                  !link.active,
                'rounded-l-md': key === 0,
                'rounded-r-md': key === posts.links.length - 1,
                'cursor-not-allowed opacity-50': !link.url,
              }"
              :disabled="!link.url"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
