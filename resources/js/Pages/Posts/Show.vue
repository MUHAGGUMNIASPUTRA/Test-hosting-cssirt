<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  post: Object,
  recentPosts: Array,
})
</script>

<template>
  <AppLayout :title="post.title">
    <div class="bg-white py-8 sm:py-24">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div
          class="mx-auto grid max-w-2xl grid-cols-1 grid-rows-1 items-start gap-x-8 gap-y-8 lg:mx-0 lg:max-w-none lg:grid-cols-3"
        >
          <!-- Post content -->
          <div class="lg:col-span-2 lg:row-start-1">
            <div v-if="post.image" class="relative w-full">
              <img
                :src="post.image.startsWith('http') ? post.image : '/storage/' + post.image"
                :alt="post.title"
                class="aspect-[16/9] w-full rounded-2xl bg-gray-100 object-cover sm:aspect-[2/1] lg:aspect-[16/9]"
              />
              <div
                class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10"
              ></div>
            </div>

            <div class="mt-6 sm:mt-8">
              <h1
                class="mb-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
              >
                {{ post.title }}
              </h1>
              <div class="flex items-center gap-x-4 text-xs">
                <time :datetime="post.published_at" class="text-gray-500">{{
                  new Date(post.published_at).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                  })
                }}</time>
                <span
                  v-if="post.categories.length > 0"
                    class="relative z-10 rounded-md bg-purple-50 px-2 py-1 font-medium text-purple-600 ring-1 ring-inset ring-purple-500/10"
                  >{{ post.categories[0].name }}</span
                >
              </div>

              <hr class="mt-5"/>
              <div>
                <p class="mt-4 text-gray-400">
                  {{ post.excerpt }}
                </p>
              </div>
              <hr class="mt-5"/>

              <!-- Use v-html to render the HTML content from the database -->
              <div
                class="prose mt-4 max-w-none text-gray-600"
                v-html="post.body"
              ></div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="max-w-xl lg:col-start-3 lg:row-start-1">
            <div class="border-t border-gray-200 pt-2 lg:pt-2 lg:border-t-0">
              <!-- Author -->
              <div class="flex items-center gap-x-4">
                <i class="pi pi-user-edit text-4xl text-gray-400"></i>
                <div>
                  <h3
                    class="text-base font-semibold leading-7 tracking-tight text-gray-900"
                  >
                    {{ post.published_by }}
                  </h3>
                </div>
              </div>

              <!-- Categories -->
              <!-- <div class="mt-8" v-if="post.categories.length > 0">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                  Kategori
                </h3>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span
                    v-for="category in post.categories"
                    :key="category.id"
                    class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                  >
                    {{ category.name }}
                  </span>
                </div>
              </div> -->

              <!-- Tags -->
              <div class="mt-8" v-if="post.tags.length > 0">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                  Tags
                </h3>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span
                    v-for="tag in post.tags"
                    :key="tag.id"
                    class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/10"
                  >
                    {{ tag.name }}
                  </span>
                </div>
              </div>

              <!-- Recent Posts -->
              <div class="mt-8" v-if="recentPosts.length > 0">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                  Artikel Terbaru
                </h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="recentPost in recentPosts" :key="recentPost.id">
                    <Link
                      :href="route('posts.show', recentPost.slug)"
                      class="group"
                    >
                      <p
                        class="font-semibold text-gray-900 group-hover:text-blue-600"
                      >
                        {{ recentPost.title }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{
                          new Date(recentPost.published_at).toLocaleDateString(
                            'id-ID',
                            { month: 'long', year: 'numeric' },
                          )
                        }}
                      </p>
                    </Link>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
