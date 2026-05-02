<template>
  <SEOLayout :title="post.title">
    <!-- Hero Section -->
    <section
      class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900"
    >
      <div class="sm:pt-16"></div>

      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div
          class="bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] absolute inset-0"
        ></div>
      </div>

      <div class="relative z-10 px-4 py-8 sm:px-6 sm:py-16 lg:px-8 lg:py-24">
        <div class="container mx-auto">
          <div class="animate-fade-in-up">
            <!-- Breadcrumb -->
            <nav
              class="mb-6 flex justify-center sm:mb-8"
              aria-label="Breadcrumb"
            >
              <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                  <a
                    href="/posts"
                    class="inline-flex items-center text-slate-300 transition-colors duration-200 hover:text-white"
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
                        d="M7 16l-4-4m0 0l4-4m-4 4h18"
                      />
                    </svg>
                    Artikel
                  </a>
                </li>
                <li v-if="post.categories && post.categories.length > 0">
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
                    <a
                      :href="`/categories/${post.categories[0].slug}`"
                      class="ml-1 text-slate-300 transition-colors duration-200 hover:text-white"
                    >
                      {{ post.categories[0].name }}
                    </a>
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
                      post.title
                    }}</span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Hero Content -->
            <div class="mx-auto max-w-7xl text-center">
              <!-- Categories -->
              <div
                v-if="post.categories && post.categories.length > 0"
                class="mb-4 sm:mb-6"
              >
                <div class="flex flex-wrap justify-center gap-2">
                  <a
                    v-for="category in post.categories"
                    :key="category.id"
                    :href="`/categories/${category.slug}`"
                    class="inline-flex items-center rounded-full bg-blue-100/20 px-3 py-1 text-sm font-medium text-blue-200 backdrop-blur-sm transition-colors duration-200 hover:bg-blue-100/30 sm:text-base"
                  >
                    {{ category.name }}
                  </a>
                </div>
              </div>

              <!-- Title -->
              <h1
                class="mb-4 text-4xl font-extrabold leading-tight tracking-tight text-white sm:mb-6 sm:text-5xl lg:text-7xl"
              >
                {{ post.title }}
              </h1>

              <!-- Excerpt -->
              <p
                v-if="post.excerpt"
                class="mx-auto mb-4 max-w-5xl text-lg/6 italic text-slate-300 sm:mb-6 sm:text-xl lg:text-2xl"
              >
                {{ post.excerpt }}
              </p>

              <!-- Meta Info -->
              <div
                class="flex flex-wrap items-center justify-center gap-2 space-x-4 text-sm text-slate-300 sm:space-x-6 sm:text-base"
              >
                <!-- Author -->
                <div class="flex items-center text-gray-400">
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
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                  {{ post.published_by }}
                </div>

                <!-- Date -->
                <div class="flex items-center text-gray-400">
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
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                  <time :datetime="post.published_at">
                    {{ formatDate(post.published_at) }}
                  </time>
                </div>

                <!-- Reading Time -->
                <div class="flex items-center text-gray-400">
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
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  {{ readingTime }} menit baca
                </div>

                <!-- Views -->
                <div class="flex items-center text-gray-400">
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
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                    />
                  </svg>
                  {{ post.views_count }} kali dilihat
                </div>

                <!-- Rating -->
                <div v-if="post.rating" class="flex items-center text-gray-400">
                  <svg
                    class="mr-1.5 h-5 w-5 text-yellow-400"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    />
                  </svg>
                  {{ post.rating }}/5 ({{ post.ratings_count }} rating)
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="bg-white py-8 sm:py-16 lg:py-24">
      <div class="container mx-auto">
        <div class="grid grid-cols-1 gap-6 sm:gap-8 lg:grid-cols-12 lg:gap-12">
          <!-- Article Content -->
          <article class="lg:col-span-8">
            <!-- Featured Image -->
            <div v-if="post.image" class="relative mb-8 lg:mb-12">
              <div
                class="aspect-[16/9] overflow-hidden rounded-3xl shadow-xl sm:shadow-2xl"
              >
                <img
                  :src="post.image"
                  :alt="post.title"
                  class="h-full w-full object-cover"
                />
              </div>
            </div>

            <!-- Article Body -->
            <div class="prose prose-lg max-w-none">
              <div v-html="post.body"></div>
            </div>

            <!-- Tags -->
            <div
              v-if="post.tags && post.tags.length > 0"
              class="mt-6 border-t border-slate-200 pt-6 sm:mt-12 sm:pt-8"
            >
              <h3 class="mb-4 text-xl font-semibold text-slate-900">Tags</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tag in post.tags"
                  :key="tag.id"
                  class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-700 transition-colors duration-200 hover:bg-slate-200"
                >
                  #{{ tag.name }}
                </span>
              </div>
            </div>

            <!-- Social Share -->
            <div class="mt-6 border-t border-slate-200 pt-6 sm:mt-8 sm:pt-8">
              <h3 class="mb-4 text-xl font-semibold text-slate-900">
                Bagikan Artikel
              </h3>
              <div class="flex flex-wrap gap-3">
                <a
                  :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`"
                  target="_blank"
                  class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors duration-200 hover:bg-blue-700"
                >
                  <svg
                    class="mr-2 h-4 w-4"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                    />
                  </svg>
                  Facebook
                </a>
                <a
                  :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent(post.title)}`"
                  target="_blank"
                  class="inline-flex items-center rounded-lg bg-sky-500 px-4 py-2 text-white transition-colors duration-200 hover:bg-sky-600"
                >
                  <svg
                    class="mr-2 h-4 w-4"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"
                    />
                  </svg>
                  Twitter
                </a>
                <a
                  :href="`https://wa.me/?text=${encodeURIComponent(post.title + ' ' + currentUrl)}`"
                  target="_blank"
                  class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-white transition-colors duration-200 hover:bg-green-700"
                >
                  <svg
                    class="mr-2 h-4 w-4"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"
                    />
                  </svg>
                  WhatsApp
                </a>
              </div>
            </div>
          </article>

          <!-- Sidebar -->
          <aside class="lg:col-span-4">
            <div class="sticky top-8 space-y-6 sm:space-y-8">
              <!-- Recent Posts -->
              <div
                v-if="popularPosts && popularPosts.length > 0"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
              >
                <h3 class="mb-6 text-xl font-bold text-slate-900">
                  Artikel Terpopuler
                </h3>
                <div class="space-y-4 sm:space-y-6">
                  <article
                    v-for="popularPost in popularPosts"
                    :key="popularPost.id"
                  >
                    <a :href="`/posts/${popularPost.slug}`" class="group block">
                      <div class="flex items-center">
                        <div
                          class="mr-3 h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg"
                        >
                          <img
                            :src="popularPost.image || '/default-article.jpg'"
                            :alt="popularPost.title"
                            class="h-full w-full object-cover object-center transition-transform duration-300 group-hover:scale-105"
                          />
                        </div>
                        <div class="flex flex-col justify-between space-y-1">
                          <h4
                            class="line-clamp-2 leading-tight text-slate-900 transition-colors duration-200 group-hover:text-indigo-600"
                          >
                            {{ popularPost.title }}
                          </h4>
                          <p class="text-sm text-slate-500">
                            {{ formatDate(popularPost.published_at) }}
                          </p>
                        </div>
                      </div>
                    </a>
                  </article>
                </div>
              </div>

              <!-- Back to Articles -->
              <div
                class="rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-blue-50 p-6"
              >
                <h3 class="mb-3 text-xl font-semibold text-slate-900">
                  Jelajahi Lebih Banyak
                </h3>
                <p class="mb-4 text-slate-600">
                  Temukan artikel dan panduan keamanan siber lainnya
                </p>
                <a
                  href="/posts"
                  class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white transition-colors duration-200 hover:bg-indigo-700"
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
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                    />
                  </svg>
                  Semua Artikel
                </a>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </SEOLayout>
</template>

<script>
import SEOLayout from '@/Layouts/SEOLayout.vue'

export default {
  name: 'SEOPostShow',
  components: {
    SEOLayout,
  },
  props: {
    post: Object,
    popularPosts: Array,
    hasRated: Boolean,
  },
  computed: {
    currentUrl() {
      return `${window.location.origin}/posts/${this.post.slug}`
    },
    readingTime() {
      if (!this.post.body) return 5
      const wordsPerMinute = 200
      const textContent = this.post.body.replace(/<[^>]*>/g, '') // Strip HTML
      const wordCount = textContent.split(/\s+/).length
      const minutes = Math.ceil(wordCount / wordsPerMinute)
      return minutes
    },
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      })
    },
  },
}
</script>
