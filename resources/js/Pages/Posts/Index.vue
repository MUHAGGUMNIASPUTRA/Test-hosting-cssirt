<script setup>
// filepath: resources/js/Pages/Posts/Index.vue

import { computed, onMounted, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'
import { useParticles } from '@/Composables/useParticles'

// generateExcerpt dipindahkan ke dalam PostCard.vue

const props = defineProps({
  posts: Object,
  isFirstPage: Boolean,
  filters: Object,
})

// Animation refs
const heroRef = ref(null)
const postsRef = ref(null)
const { minimalParticlesOptions } = useParticles()

// Search functionality - separate input value from applied search
const searchInput = ref(props.filters?.search || '') // What user is typing
const appliedSearch = ref(props.filters?.search || '') // What search is actually applied

// Responsive composable
const { isMobile } = useResponsive()

// Get posts for grid display (exclude featured post on first page)
const gridPosts = computed(() => {
  if (
    props.isFirstPage &&
    props.posts.data.length > 0 &&
    !appliedSearch.value
  ) {
    return props.posts.data.slice(1) // Skip first post (featured) only when not searching
  }
  return props.posts.data // Show all posts on other pages or when searching
})

// Show featured post only on first page and when not searching
const showFeaturedPost = computed(() => {
  return (
    props.isFirstPage && props.posts.data.length > 0 && !appliedSearch.value
  )
})

// Check if user is typing but hasn't searched yet
const isTypingWithoutSearch = computed(() => {
  return (
    searchInput.value.trim() !== '' &&
    searchInput.value.trim() !== appliedSearch.value
  )
})

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

// Search functionality
const applySearch = () => {
  const params = new URLSearchParams()
  const searchTerm = searchInput.value.trim()

  if (searchTerm) {
    params.set('search', searchTerm)
  }

  // Update appliedSearch immediately when applying search
  appliedSearch.value = searchTerm

  const queryString = params.toString()
  const url = route('posts.index') + (queryString ? '?' + queryString : '')

  router.get(
    url,
    {},
    {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    },
  )
}

const clearSearch = () => {
  searchInput.value = ''
  appliedSearch.value = ''

  router.get(
    route('posts.index'),
    {},
    {
      preserveState: true,
      preserveScroll: false,
      replace: true,
    },
  )
}

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
  <AppLayout title="Artikel & Panduan">
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
            <h1
              class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl"
            >
              Artikel &
              <span
                class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"
                >Panduan</span
              >
            </h1>
            <p
              class="mx-auto mt-6 max-w-3xl text-xl text-slate-300 sm:text-2xl"
            >
              Ikuti informasi, panduan, dan berita terkini seputar keamanan
              siber untuk meningkatkan kewaspadaan kita bersama
            </p>
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
        <!-- Search Section -->
        <div class="mb-8 sm:mb-12">
          <div class="mx-auto max-w-2xl">
            <div class="relative">
              <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
              >
                <svg
                  class="h-5 w-5 text-slate-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </div>
              <input
                v-model="searchInput"
                @keyup.enter="applySearch"
                type="text"
                class="block w-full rounded-2xl border border-slate-300 bg-white py-4 pl-12 pr-12 text-lg leading-5 placeholder-slate-500 focus:border-indigo-500 focus:placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                placeholder="Cari artikel berdasarkan judul, konten, atau kategori..."
              />
              <div
                v-if="searchInput"
                class="absolute inset-y-0 right-0 flex items-center pr-4"
              >
                <button
                  @click="clearSearch"
                  class="rounded-full p-1 transition-colors duration-200 hover:bg-slate-100"
                >
                  <svg
                    class="h-5 w-5 text-slate-400 hover:text-slate-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Search Results Info (only when search is actually applied) -->
        <div v-if="appliedSearch" class="mb-8 text-center">
          <p class="text-slate-600">
            <span v-if="posts.total > 0">
              Menampilkan {{ posts.total }} hasil untuk "<strong>{{
                appliedSearch
              }}</strong
              >"
            </span>
            <span v-else>
              Tidak ditemukan artikel untuk "<strong>{{ appliedSearch }}</strong
              >"
            </span>
          </p>
        </div>

        <!-- Featured Post (Only on First Page and No Search) -->
        <div v-if="showFeaturedPost" class="mb-8 sm:mb-16">
          <div class="mb-8 text-center sm:mb-12">
            <h2
              class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
            >
              Artikel Terbaru
            </h2>
          </div>

          <article
            class="group relative transform overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl"
          >
            <div class="lg:grid lg:grid-cols-2 lg:gap-0">
              <!-- Featured Image -->
              <div class="relative h-64 overflow-hidden lg:min-h-[400px]">
                <Link
                  :href="route('posts.show', { post: posts.data[0].slug })"
                  class="block h-full"
                >
                  <PostImage
                    :post="posts.data[0]"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                  />
                  <div
                    class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                  ></div>
                </Link>
              </div>

              <!-- Featured Content -->
              <div class="flex flex-col justify-center p-8 lg:p-12">
                <!-- Categories -->
                <div class="mb-4">
                  <div
                    v-if="posts.data[0].categories?.length > 0"
                    class="flex flex-wrap gap-2"
                  >
                    <span
                      v-for="category in posts.data[0].categories"
                      :key="category.id"
                      class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800 transition-colors duration-200 hover:bg-indigo-200"
                    >
                      <Link :href="route('categories.show', category.slug)">{{
                        category.name
                      }}</Link>
                    </span>
                  </div>
                  <span
                    v-else
                    class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-800"
                    >Artikel</span
                  >
                </div>

                <!-- Title -->
                <h3
                  class="mb-4 line-clamp-3 text-2xl font-bold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600 sm:text-3xl lg:text-4xl"
                >
                  <Link
                    :href="route('posts.show', { post: posts.data[0].slug })"
                  >
                    {{ posts.data[0].title }}
                  </Link>
                </h3>

                <!-- Excerpt -->
                <p class="mb-6 line-clamp-3 text-slate-600 sm:text-lg">
                  {{ generateExcerpt(posts.data[0]) }}
                </p>

                <!-- Meta Info -->
                <div class="mb-6 flex items-center text-slate-500">
                  <div class="flex items-center">
                    <i-lucide-user-pen class="mr-2" />
                    <span class="font-medium">{{
                      posts.data[0].published_by
                    }}</span>
                  </div>
                  <span class="mx-3">•</span>
                  <time :datetime="posts.data[0].published_at">
                    {{
                      new Date(posts.data[0].published_at).toLocaleDateString(
                        'id-ID',
                        { day: 'numeric', month: 'long', year: 'numeric' },
                      )
                    }}
                  </time>
                </div>

                <!-- Read More Button -->
                <Link
                  :href="route('posts.show', { post: posts.data[0].slug })"
                  class="group/link inline-flex items-center font-semibold text-indigo-600 hover:text-indigo-700"
                >
                  Baca Selengkapnya
                  <svg
                    class="ml-2 h-4 w-4 transition-transform duration-200 group-hover/link:translate-x-1"
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

        <!-- Regular Posts Grid -->
        <div v-if="gridPosts.length > 0">
          <div class="mb-8 text-center sm:mb-12">
            <h2
              class="mb-2 text-lg font-semibold uppercase tracking-wider text-indigo-600"
            >
              <span v-if="appliedSearch">Hasil Pencarian</span>
              <span v-else-if="showFeaturedPost">Artikel Lainnya</span>
              <span v-else>Semua Artikel</span>
            </h2>
          </div>

          <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <PostCard
              v-for="(post, index) in gridPosts"
              :key="post.id"
              :post="post"
              :animation-delay="(index + 1) * 100"
            />
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
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <h3 class="mb-2 text-2xl font-semibold text-slate-900">
            {{ appliedSearch ? 'Tidak Ditemukan Hasil' : 'Belum Ada Artikel' }}
          </h3>
          <p class="text-slate-600">
            {{
              appliedSearch
                ? `Coba gunakan kata kunci yang berbeda.`
                : 'Artikel dan panduan akan segera tersedia di sini.'
            }}
          </p>
          <button
            v-if="appliedSearch"
            @click="clearSearch"
            class="mt-4 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white transition-colors duration-200 hover:bg-indigo-700"
          >
            <IconX size="14" class="mr-2" />
            Hapus Pencarian
          </button>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
