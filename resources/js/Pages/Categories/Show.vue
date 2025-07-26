<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  category: Object,
  posts: Object,
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
  <AppLayout :title="`Kategori: ${category.name}`">
    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container mx-auto text-center">
          <div class="animate-fade-in-up">
            <!-- Breadcrumb -->
            <nav class="flex justify-center mb-6" aria-label="Breadcrumb">
              <ol class="inline-flex items-center space-x-1 md:space-x-2 text-sm">
                <li class="inline-flex items-center">
                  <Link :href="route('posts.index')" class="inline-flex items-center text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L9 5.414V17a1 1 0 102 0V5.414l5.293 5.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Artikel
                  </Link>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-slate-400">Kategori</span>
                  </div>
                </li>
                <li aria-current="page">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-white font-medium">{{ category.name }}</span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Category Title -->
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
              Kategori: <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">{{ category.name }}</span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-lg text-slate-300 sm:text-xl">
              {{ posts.total }} artikel tersedia dalam kategori "{{ category.name }}"
            </p>

            <!-- Category Description (if available) -->
            <div v-if="category.description" class="mx-auto mt-4 max-w-2xl text-slate-400">
              {{ category.description }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Posts Section -->
    <section ref="postsRef" class="py-20 bg-white opacity-0 translate-y-10">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Posts Grid -->
        <div v-if="posts.data.length > 0">
          <div class="text-center mb-12">
            <h2 class="text-base font-semibold uppercase tracking-wider text-indigo-600 mb-2">
              Artikel dalam Kategori
            </h2>
            <p class="text-slate-600">
              Menampilkan {{ posts.from }}-{{ posts.to }} dari {{ posts.total }} artikel
            </p>
          </div>

          <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="(post, index) in posts.data"
              :key="post.id"
              class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200"
              :style="{ animationDelay: `${index * 100}ms` }"
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
                    <span v-for="postCategory in post.categories" :key="postCategory.id" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors duration-200">
                      <Link :href="route('categories.show', postCategory.slug)">{{ postCategory.name }}</Link>
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-slate-900 mb-2">Belum Ada Artikel</h3>
          <p class="text-slate-600">Belum ada artikel dalam kategori "{{ category.name }}".</p>

          <!-- Back to Articles Button -->
          <div class="mt-6">
            <Link
              :href="route('posts.index')"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Kembali ke Semua Artikel
            </Link>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
