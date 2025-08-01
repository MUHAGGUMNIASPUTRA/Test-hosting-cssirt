<template>
  <SEOLayout :title="`Kategori: ${category.name} - CSIRT Bojonegoro`">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="container mx-auto text-center">
          <div class="animate-fade-in-up">
            <!-- Breadcrumb -->
            <nav class="flex justify-center mb-6" aria-label="Breadcrumb">
              <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                  <a href="/posts" class="inline-flex items-center text-slate-300 hover:text-white transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    Artikel
                  </a>
                </li>
                <li>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-slate-300">Kategori</span>
                  </div>
                </li>
                <li aria-current="page">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-slate-400 line-clamp-1">{{ category.name }}</span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Category Title -->
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-7xl">
              Kategori: <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">{{ category.name }}</span>
            </h1>
            <p class="mx-auto mt-6 max-w-3xl text-xl sm:text-2xl text-slate-300">
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
    <section class="py-8 sm:py-16 lg:py-20 bg-white">
      <div class="container mx-auto">

        <!-- Posts Grid -->
        <div v-if="posts.data && posts.data.length > 0">
          <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-lg font-semibold uppercase tracking-wider text-indigo-600 mb-2">
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
                <a :href="`/posts/${post.slug}`" class="block">
                  <img
                    :src="post.image || '/default-article.jpg'"
                    :alt="post.title"
                    class="h-48 w-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
              </div>

              <!-- Post Content -->
              <div class="p-6">
                <!-- Categories -->
                <div class="mb-3">
                  <div v-if="post.categories && post.categories.length > 0" class="flex flex-wrap gap-2">
                    <span v-for="postCategory in post.categories" :key="postCategory.id" class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors duration-200">
                      <a :href="`/categories/${postCategory.slug}`">{{ postCategory.name }}</a>
                    </span>
                  </div>
                  <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-800">Artikel</span>
                </div>

                <!-- Title -->
                <h3 class="text-2xl font-semibold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300 line-clamp-2">
                  <a :href="`/posts/${post.slug}`">
                    {{ post.title }}
                  </a>
                </h3>

                <!-- Excerpt -->
                <p class="text-slate-600 mb-4 line-clamp-3 leading-relaxed">
                  {{ generateExcerpt(post) }}
                </p>

                <!-- Meta Info -->
                <div class="flex items-center justify-between text-sm text-slate-500">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ post.published_by }}</span>
                  </div>
                  <time :datetime="post.published_at">
                    {{ formatDate(post.published_at) }}
                  </time>
                </div>

                <!-- Read More Link -->
                <div class="mt-4 pt-4 border-t border-slate-200">
                  <a
                    :href="`/posts/${post.slug}`"
                    class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium group/link"
                  >
                    Baca Artikel
                    <svg class="ml-1 h-3 w-3 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </a>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- Modern Pagination -->
        <div v-if="posts.links && posts.links.length > 3" class="mt-8 sm:mt-16 flex justify-center">
          <nav class="flex items-center space-x-1" aria-label="Pagination">
            <!-- First Page -->
            <a
              v-if="posts.current_page > 1"
              :href="posts.first_page_url"
              class="inline-flex items-center px-3 py-2 font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-all duration-200"
            >
              <span class="py-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
              </span>
              <span class="hidden sm:inline ml-1">Pertama</span>
            </a>

            <!-- Pagination Links -->
            <div class="flex items-center space-x-1">
              <template v-for="(link, key) in paginationLinks" :key="key">
                <span
                  v-if="link.url === null"
                  class="inline-flex items-center px-3 py-2 font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-lg cursor-not-allowed"
                >
                  <span v-if="link.label.includes('Previous')" class="py-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                  </span>
                  <span v-else-if="link.label.includes('Next')" class="py-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                  <span v-else>{{ link.label }}</span>
                </span>
                <a
                  v-else
                  :href="link.url"
                  class="inline-flex items-center py-2 font-medium border rounded-lg transition-all duration-200"
                  :class="[
                    link.active
                    ? 'text-white bg-indigo-600 border-indigo-600 shadow-lg'
                    : 'text-slate-600 bg-white border-slate-300 hover:bg-slate-50 hover:border-slate-400',
                    link.label.includes('Previous') || link.label.includes('Next') ? 'px-3' : 'px-4',
                  ]"
                >
                  <span v-if="link.label.includes('Previous')" class="py-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                  </span>
                  <span v-else-if="link.label.includes('Next')" class="py-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                  <span v-else>{{ link.label }}</span>
                </a>
              </template>
            </div>

            <!-- Last Page -->
            <a
              v-if="posts.current_page < posts.last_page"
              :href="posts.last_page_url"
              class="inline-flex items-center px-3 py-2 font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition-all duration-200"
            >
              <span class="hidden sm:inline mr-1">Terakhir</span>
              <span class="py-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
              </span>
            </a>
          </nav>
        </div>

        <!-- No Posts Message -->
        <div v-if="!posts.data || posts.data.length === 0" class="text-center py-16">
          <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <h3 class="text-2xl font-semibold text-slate-900 mb-2">Belum Ada Artikel</h3>
          <p class="text-slate-600">Belum ada artikel dalam kategori "{{ category.name }}".</p>

          <!-- Back to Articles Button -->
          <div class="mt-6">
            <a
              href="/posts"
              class="inline-flex items-center px-4 py-2 font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Kembali ke Semua Artikel
            </a>
          </div>
        </div>
      </div>
    </section>
  </SEOLayout>
</template>

<script>
import SEOLayout from '@/Layouts/SEOLayout.vue'

export default {
  name: 'SEOCategoryShow',
  components: {
    SEOLayout
  },
  props: {
    category: Object,
    posts: Object,
  },
  computed: {
    paginationLinks() {
      if (!this.posts.links) return [];

      const { current_page, last_page, links } = this.posts;

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
    },
  },
  methods: {
    generateExcerpt(post) {
      let content = post.excerpt;

      if (!content || content.trim() === '') {
        // Strip HTML tags from body content
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = post.body || '';
        content = tempDiv.textContent || tempDiv.innerText || '';
      }

      content = content.trim();
      return content.length > 150 ? content.substring(0, 150) + '...' : content;
    },
    formatDate(dateString) {
      if (!dateString) return '-';
      return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      });
    }
  }
}
</script>
