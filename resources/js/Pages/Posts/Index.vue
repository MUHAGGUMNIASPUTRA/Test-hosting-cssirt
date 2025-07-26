<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  posts: Object,
  isFirstPage: Boolean,
})

// Animation refs
const heroRef = ref(null)
const postsRef = ref(null)

// Responsive composable
const { isMobile } = useResponsive()

const generateExcerpt = (post) => {
  let content = post.excerpt;

  if (!content || content.trim() === '') {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = post.body;
    content = tempDiv.textContent || tempDiv.innerText || '';
  }

  content = content.trim();
  if (content.length <= 150) {
    return content;
  }

  let truncated = content.substring(0, 150);
  truncated = truncated.substring(0, Math.min(truncated.length, truncated.lastIndexOf(' ')));

  return truncated + '...';
};

// Get posts for grid display (exclude featured post on first page)
const gridPosts = computed(() => {
  if (props.isFirstPage && props.posts.data.length > 0) {
    return props.posts.data.slice(1); // Skip first post (featured)
  }
  return props.posts.data; // Show all posts on other pages
});

const paginationLinks = computed(() => {
  const { current_page, last_page, links } = props.posts;

  const prevLink = links[0];
  const nextLink = links[links.length - 1];

  let startPage;
  if (current_page <= 2) {
    startPage = 1;
  } else if (current_page === last_page) {
    startPage = Math.max(1, last_page - 2);
  } else {
    startPage = current_page - 1;
  }

  const endPage = Math.min(last_page, startPage + 2);

  const pageLinks = [];
  for (let i = startPage; i <= endPage; i++) {
    const originalLink = links.find(link => parseInt(link.label) === i);
    if (originalLink) {
      pageLinks.push(originalLink);
    }
  }

  return [prevLink, ...pageLinks, nextLink];
});

// Scroll animations
onMounted(() => {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
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
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container mx-auto text-center">
          <div class="animate-fade-in-up">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
              Artikel & <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">Panduan</span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-lg text-slate-300 sm:text-xl">
              Ikuti informasi, panduan, dan berita terkini seputar keamanan siber
              untuk meningkatkan kewaspadaan kita bersama
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Posts Section -->
    <section ref="postsRef" class="py-20 bg-white opacity-0 translate-y-10">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Featured Post (Only on First Page) -->
        <div v-if="isFirstPage && posts.data.length > 0" class="mb-16">
          <div class="text-center mb-12">
            <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-600 mb-2">
              Artikel Terbaru
            </h2>
          </div>

          <article class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-slate-200">
            <div class="lg:grid lg:grid-cols-2 lg:gap-0">
              <!-- Featured Image -->
              <div class="relative h-64 lg:h-auto">
                <Link :href="route('posts.show', { post: posts.data[0].slug })" class="block h-full">
                  <PostImage :post="posts.data[0]" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </Link>
              </div>

              <!-- Featured Content -->
              <div class="p-8 lg:p-12 flex flex-col justify-center">
                <!-- Categories -->
                <div class="mb-4">
                  <div v-if="posts.data[0].categories?.length > 0" class="flex flex-wrap gap-2">
                    <span v-for="category in posts.data[0].categories" :key="category.id" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors duration-200">
                      <Link :href="route('categories.show', category.slug)">{{ category.name }}</Link>
                    </span>
                  </div>
                  <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Artikel</span>
                </div>

                <!-- Title -->
                <h3 class="text-2xl lg:text-3xl font-bold text-slate-900 mb-4 group-hover:text-indigo-600 transition-colors duration-300">
                  <Link :href="route('posts.show', { post: posts.data[0].slug })">
                    {{ posts.data[0].title }}
                  </Link>
                </h3>

                <!-- Excerpt -->
                <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                  {{ generateExcerpt(posts.data[0]) }}
                </p>

                <!-- Meta Info -->
                <div class="flex items-center text-sm text-slate-500 mb-6">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium">{{ posts.data[0].published_by }}</span>
                  </div>
                  <span class="mx-3">•</span>
                  <time :datetime="posts.data[0].published_at">
                    {{ new Date(posts.data[0].published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                  </time>
                </div>

                <!-- Read More Button -->
                <Link
                  :href="route('posts.show', { post: posts.data[0].slug })"
                  class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-semibold group/link"
                >
                  Baca Selengkapnya
                  <svg class="ml-2 h-4 w-4 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </div>
            </div>
          </article>
        </div>

        <!-- Regular Posts Grid -->
        <div v-if="gridPosts.length > 0">
          <div class="text-center mb-12">
            <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-600 mb-2">
              {{ isFirstPage ? 'Artikel Lainnya' : 'Semua Artikel' }}
            </h2>
          </div>

          <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="(post, index) in gridPosts"
              :key="post.id"
              class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200"
              :style="{ animationDelay: `${(index + 1) * 100}ms` }"
            >
              <!-- Post Image -->
              <div class="relative overflow-hidden">
                <Link :href="route('posts.show', { post: post.slug })" class="block">
                  <PostImage :post="post" class="h-48 w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </Link>
              </div>

              <!-- Post Content -->
              <div class="p-6">
                <!-- Categories -->
                <div class="mb-3">
                  <div v-if="post.categories?.length > 0" class="flex flex-wrap gap-2">
                    <span v-for="category in post.categories" :key="category.id" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors duration-200">
                      <Link :href="route('categories.show', category.slug)">{{ category.name }}</Link>
                    </span>
                  </div>
                  <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Artikel</span>
                </div>

                <!-- Title -->
                <h3 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300 line-clamp-2">
                  <Link :href="route('posts.show', { post: post.slug })">
                    {{ post.title }}
                  </Link>
                </h3>

                <!-- Excerpt -->
                <p class="text-slate-600 mb-4 line-clamp-3 text-sm leading-relaxed">
                  {{ generateExcerpt(post) }}
                </p>

                <!-- Meta Info -->
                <div class="flex items-center justify-between text-xs text-slate-500">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ post.published_by }}</span>
                  </div>
                  <time :datetime="post.published_at">
                    {{ new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) }}
                  </time>
                </div>

                <!-- Read More Link -->
                <div class="mt-4 pt-4 border-t border-slate-200">
                  <Link
                    :href="route('posts.show', { post: post.slug })"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium text-sm group/link"
                  >
                    Baca Artikel
                    <svg class="ml-1 h-3 w-3 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </Link>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- Modern Pagination -->
        <div v-if="posts.links.length > 3" class="mt-16 flex justify-center">
          <nav class="flex items-center space-x-2" aria-label="Pagination">
            <!-- First Page -->
            <Link
              v-if="posts.current_page > 1"
              :href="posts.first_page_url"
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-all duration-200"
              :title="isMobile ? 'Halaman Pertama' : undefined"
            >
              <svg class="w-4 h-4" :class="isMobile ? '' : 'mr-1'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
              </svg>
              <span v-if="!isMobile">Pertama</span>
            </Link>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-1">
              <template v-for="(link, key) in paginationLinks" :key="key">
                <span
                  v-if="link.url === null"
                  class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-lg cursor-not-allowed"
                >
                  <svg v-if="link.label.includes('Previous')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                  <svg v-else-if="link.label.includes('Next')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  <span v-else>{{ link.label }}</span>
                </span>
                <Link
                  v-else
                  :href="link.url"
                  class="inline-flex items-center px-3 py-2 text-sm font-medium border rounded-lg transition-all duration-200"
                  :class="link.active
                    ? 'text-white bg-indigo-600 border-indigo-600 shadow-lg'
                    : 'text-slate-600 bg-white border-slate-300 hover:bg-slate-50 hover:border-slate-400'"
                >
                  <svg v-if="link.label.includes('Previous')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                  <svg v-else-if="link.label.includes('Next')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  <span v-else>{{ link.label }}</span>
                </Link>
              </template>
            </div>

            <!-- Last Page -->
            <Link
              v-if="posts.current_page < posts.last_page"
              :href="posts.last_page_url"
              class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-all duration-200"
              :title="isMobile ? 'Halaman Terakhir' : undefined"
            >
              <span v-if="!isMobile">Terakhir</span>
              <svg class="w-4 h-4" :class="isMobile ? '' : 'ml-1'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
              </svg>
            </Link>
          </nav>
        </div>

        <!-- No Posts Message -->
        <div v-if="posts.data.length === 0" class="text-center py-16">
          <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-slate-900 mb-2">Belum Ada Artikel</h3>
          <p class="text-slate-600">Artikel dan panduan akan segera tersedia di sini.</p>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
