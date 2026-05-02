<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'
import { useParticles } from '@/Composables/useParticles'

const props = defineProps({
  category: Object,
  posts: Object,
})

// Animation refs
const heroRef = ref(null)
const postsRef = ref(null)
const { minimalParticlesOptions } = useParticles()

// Responsive composable
const { isMobile } = useResponsive()

const generateExcerpt = (post) => {
  let content = post.excerpt

  if (!content || content.trim() === '') {
    const tempDiv = document.createElement('div')
    tempDiv.innerHTML = post.body
    content = tempDiv.textContent || tempDiv.innerText || ''
  }

  content = content.trim()
  return content
}

const paginationLinks = computed(() => {
  const { current_page, last_page, links } = props.posts

  const prevLink = links[0]
  const nextLink = links[links.length - 1]

  let startPage
  if (current_page <= 2) {
    startPage = 1
  } else if (current_page === last_page) {
    startPage = Math.max(1, last_page - 2)
  } else {
    startPage = current_page - 1
  }

  const endPage = Math.min(last_page, startPage + 2)

  const pageLinks = []
  for (let i = startPage; i <= endPage; i++) {
    const originalLink = links.find((link) => parseInt(link.label) === i)
    if (originalLink) {
      pageLinks.push(originalLink)
    }
  }

  return [prevLink, ...pageLinks, nextLink]
})

// Scroll animations
onMounted(() => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px',
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up')
      }
    })
  }, observerOptions)

  if (postsRef.value) observer.observe(postsRef.value)
})
</script>

<template>
  <AppLayout :title="`Kategori: ${category.name}`">
    <!-- Hero Section -->
    <section
      ref="heroRef"
      class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
    >
      <div class="absolute inset-0 z-0">
        <vue-particles
          id="tsparticles"
          :options="minimalParticlesOptions"
          class="h-full w-full"
        />
      </div>

      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container text-center">
          <div class="animate-fade-in-up">
            <!-- Breadcrumb -->
            <nav class="mb-6 flex justify-center" aria-label="Breadcrumb">
              <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                  <Link
                    :href="route('posts.index')"
                    class="inline-flex items-center text-slate-300 transition-colors duration-200 hover:text-white"
                  >
                    <i-lucide-arrow-up class="mr-2" />
                    Artikel
                  </Link>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg
                      class="h-4 w-4 text-slate-400"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span class="ml-1 text-slate-300">Kategori</span>
                  </div>
                </li>
                <li aria-current="page">
                  <div class="flex items-center">
                    <svg
                      class="h-4 w-4 text-slate-400"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    <span class="ml-1 line-clamp-1 text-slate-400">{{
                      category.name
                    }}</span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Category Title -->
            <h1
              class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-7xl"
            >
              Kategori:
              <span
                class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"
                >{{ category.name }}</span
              >
            </h1>
            <p
              class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl"
            >
              {{ posts.total }} artikel tersedia dalam kategori "{{
                category.name
              }}"
            </p>

            <!-- Category Description (if available) -->
            <div
              v-if="category.description"
              class="mx-auto mt-4 max-w-2xl text-slate-400"
            >
              {{ category.description }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Posts Section -->
    <section
      ref="postsRef"
      class="translate-y-10 bg-white py-8 opacity-0 sm:py-16 lg:py-20"
    >
      <div class="container">
        <!-- Posts Grid -->
        <div v-if="posts.data.length > 0">
          <div class="mb-8 text-center sm:mb-12">
            <h2
              class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
            >
              Artikel dalam Kategori
            </h2>
            <p class="text-slate-600">
              Menampilkan {{ posts.from }}-{{ posts.to }} dari
              {{ posts.total }} artikel
            </p>
          </div>

          <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="(post, index) in posts.data"
              :key="post.id"
              class="group transform overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-2 hover:shadow-xl"
              :style="{ animationDelay: `${index * 100}ms` }"
            >
              <!-- Post Image -->
              <div class="relative overflow-hidden">
                <Link
                  :href="route('posts.show', { post: post.slug })"
                  class="block"
                >
                  <PostImage
                    :post="post"
                    class="h-48 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                  />
                  <div
                    class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                  ></div>
                </Link>
              </div>

              <!-- Post Content -->
              <div class="p-6">
                <!-- Categories -->
                <div class="mb-3">
                  <div
                    v-if="post.categories?.length > 0"
                    class="flex flex-wrap gap-2"
                  >
                    <span
                      v-for="postCategory in post.categories"
                      :key="postCategory.id"
                      class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-1 text-sm font-medium text-indigo-800 transition-colors duration-200 hover:bg-indigo-200"
                    >
                      <Link
                        :href="route('categories.show', postCategory.slug)"
                        >{{ postCategory.name }}</Link
                      >
                    </span>
                  </div>
                  <span
                    v-else
                    class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-sm font-medium text-slate-800"
                    >Artikel</span
                  >
                </div>

                <!-- Title -->
                <h3
                  class="mb-3 line-clamp-2 text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
                >
                  <Link :href="route('posts.show', { post: post.slug })">
                    {{ post.title }}
                  </Link>
                </h3>

                <!-- Excerpt -->
                <p class="mb-4 line-clamp-3 leading-relaxed text-slate-600">
                  {{ generateExcerpt(post) }}
                </p>

                <!-- Meta Info -->
                <div
                  class="flex items-center justify-between text-sm text-slate-500"
                >
                  <div class="flex items-center">
                    <i-lucide-user-pen class="mr-2" />
                    <span>{{ post.published_by }}</span>
                  </div>
                  <time :datetime="post.published_at">
                    {{
                      new Date(post.published_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                      })
                    }}
                  </time>
                </div>

                <!-- Read More Link -->
                <div class="mt-4 border-t border-slate-200 pt-4">
                  <Link
                    :href="route('posts.show', { post: post.slug })"
                    class="group/link inline-flex items-center font-medium text-indigo-600 hover:text-indigo-700"
                  >
                    Baca Artikel
                    <svg
                      class="ml-1 h-3 w-3 transition-transform duration-200 group-hover/link:translate-x-1"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                      />
                    </svg>
                  </Link>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- Modern Pagination -->
        <div
          v-if="posts.links.length > 3"
          class="mt-8 flex justify-center sm:mt-16"
        >
          <nav class="flex items-center space-x-1" aria-label="Pagination">
            <!-- First Page -->
            <Link
              v-if="posts.current_page > 1"
              :href="posts.first_page_url"
              class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 font-medium text-slate-600 transition-all duration-200 hover:border-slate-400 hover:bg-slate-50"
              :title="isMobile ? 'Halaman Pertama' : undefined"
            >
              <span class="py-0.5">
                <i-lucide-chevrons-left />
              </span>
              <span v-if="!isMobile">Pertama</span>
            </Link>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-1">
              <template v-for="(link, key) in paginationLinks" :key="key">
                <span
                  v-if="link.url === null"
                  class="inline-flex cursor-not-allowed items-center rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 font-medium text-slate-400"
                >
                  <span v-if="link.label.includes('Previous')" class="py-0.5"
                    ><i-lucide-chevron-left
                  /></span>
                  <span v-else-if="link.label.includes('Next')" class="py-0.5"
                    ><i-lucide-chevron-right
                  /></span>
                  <span v-else>{{ link.label }}</span>
                </span>
                <Link
                  v-else
                  :href="link.url"
                  class="inline-flex items-center rounded-lg border py-2 font-medium transition-all duration-200"
                  :class="[
                    link.active
                      ? 'border-indigo-600 bg-indigo-600 text-white shadow-lg'
                      : 'border-slate-300 bg-white text-slate-600 hover:border-slate-400 hover:bg-slate-50',
                    link.label.includes('Previous') ||
                    link.label.includes('Next')
                      ? 'px-3'
                      : 'px-4',
                  ]"
                >
                  <span v-if="link.label.includes('Previous')" class="py-0.5"
                    ><i-lucide-chevron-left
                  /></span>
                  <span v-else-if="link.label.includes('Next')" class="py-0.5"
                    ><i-lucide-chevron-right
                  /></span>
                  <span v-else>{{ link.label }}</span>
                </Link>
              </template>
            </div>

            <!-- Last Page -->
            <Link
              v-if="posts.current_page < posts.last_page"
              :href="posts.last_page_url"
              class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 font-medium text-slate-600 transition-all duration-200 hover:border-slate-400 hover:bg-slate-50"
              :title="isMobile ? 'Halaman Terakhir' : undefined"
            >
              <span v-if="!isMobile">Terakhir</span>
              <span class="py-0.5">
                <i-lucide-chevrons-right />
              </span>
            </Link>
          </nav>
        </div>

        <!-- No Posts Message -->
        <div v-if="posts.data.length === 0" class="py-16 text-center">
          <div
            class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-100"
          >
            <svg
              class="h-12 w-12 text-slate-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
              />
            </svg>
          </div>
          <h3 class="mb-2 text-2xl font-semibold text-slate-900">
            Belum Ada Artikel
          </h3>
          <p class="text-slate-600">
            Belum ada artikel dalam kategori "{{ category.name }}".
          </p>

          <!-- Back to Articles Button -->
          <div class="mt-6">
            <Link
              :href="route('posts.index')"
              class="inline-flex items-center rounded-lg border border-transparent bg-indigo-600 px-4 py-2 font-medium text-white transition-colors duration-200 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
              <svg
                class="mr-2 h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"
                />
              </svg>
              Kembali ke Semua Artikel
            </Link>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
