<script setup>
import { onMounted, ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  post: Object,
  recentPosts: Array,
  hasRated: Boolean,
})

// Animation refs
const heroRef = ref(null)
const contentRef = ref(null)
const sidebarRef = ref(null)

// Responsive composable
const { isDesktop } = useResponsive()

// State for rating
const currentRating = ref(0);
const hoverRating = ref(0);
const userHasRated = ref(props.hasRated);

const ratingForm = useForm({
  rating: 0,
});

// Check local storage
onMounted(() => {
  const ratedPosts = JSON.parse(localStorage.getItem('rated_posts') || '[]');
  if (ratedPosts.includes(props.post.id)) {
    userHasRated.value = true;
  }

  // Scroll animations
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

  if (contentRef.value) observer.observe(contentRef.value)
  if (sidebarRef.value) observer.observe(sidebarRef.value)
});

const setRating = (rate) => {
  if (userHasRated.value) return;

  ratingForm.rating = rate;
  ratingForm.post(route('posts.ratings.store', props.post.id), {
    preserveScroll: true,
    onSuccess: () => {
      userHasRated.value = true;
      // Saving the rated post ID to local storage
      const ratedPosts = JSON.parse(localStorage.getItem('rated_posts') || '[]');
      if (!ratedPosts.includes(props.post.id)) {
        ratedPosts.push(props.post.id);
        localStorage.setItem('rated_posts', JSON.stringify(ratedPosts));
      }
    }
  });
};

// Estimated reading time
const readingTime = computed(() => {
  const wordsPerMinute = 200;
  const textContent = props.post.body.replace(/<[^>]*>/g, ''); // Strip HTML
  const wordCount = textContent.split(/\s+/).length;
  const minutes = Math.ceil(wordCount / wordsPerMinute);
  return minutes;
});

// Social sharing
const shareUrl = computed(() => window.location.href);
const shareText = computed(() => props.post.title);
</script>

<template>
  <AppLayout :title="post.title">
    <!-- Hero Section -->
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container mx-auto max-w-7xl">
          <div class="animate-fade-in-up">
            <!-- Breadcrumb -->
            <nav class="flex justify-center mb-8" aria-label="Breadcrumb">
              <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                  <Link :href="route('posts.index')" class="inline-flex items-center text-slate-300 hover:text-white transition-colors duration-200">
                    <i-lucide-arrow-up class="mr-2" />
                    Artikel
                  </Link>
                </li>
                <li v-if="post.categories.length > 0">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <Link :href="route('categories.show', post.categories[0].slug)" class="ml-1 text-slate-300 hover:text-white transition-colors duration-200">
                      {{ post.categories[0].name }}
                    </Link>
                  </div>
                </li>
                <li v-if="isDesktop" aria-current="page">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-slate-400 line-clamp-1">{{ post.title }}</span>
                  </div>
                </li>
              </ol>
            </nav>

            <!-- Hero Content -->
            <div class="text-center max-w-7xl mx-auto">
              <!-- Categories -->
              <div v-if="post.categories.length > 0" class="mb-6">
                <div class="flex flex-wrap justify-center gap-2">
                  <Link
                    v-for="category in post.categories"
                    :key="category.id"
                    :href="route('categories.show', category.slug)"
                    class="inline-flex items-center px-3 py-1 rounded-full font-medium bg-blue-100/20 text-blue-200 hover:bg-blue-100/30 transition-colors duration-200 backdrop-blur-sm"
                  >
                    {{ category.name }}
                  </Link>
                </div>
              </div>

              <!-- Title -->
              <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl mb-6 leading-tight">
                {{ post.title }}
              </h1>

              <!-- Excerpt -->
              <p v-if="post.excerpt" class="text-2xl text-slate-300 leading-relaxed mb-8 max-w-5xl mx-auto">
                {{ post.excerpt }}
              </p>

              <!-- Meta Info -->
              <div class="flex flex-wrap items-center justify-center gap-6 text-slate-300">
                <!-- Author -->
                <div class="flex items-center">
                  <i-lucide-user-pen class="mr-2" />
                  {{ post.published_by }}
                </div>

                <!-- Date -->
                <div class="flex items-center">
                  <i-lucide-calendar-check-2 class="mr-2" />
                  <time :datetime="post.published_at">
                    {{ new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                  </time>
                </div>

                <!-- Reading Time -->
                <div class="flex items-center">
                  <i-lucide-clock class="mr-2" />
                  {{ readingTime }} menit baca
                </div>

                <!-- Views -->
                <div class="flex items-center">
                  <i-lucide-eye class="mr-2" />
                  {{ post.views_count }} kali dilihat
                </div>

                <!-- Rating -->
                <div v-if="post.rating" class="flex items-center">
                  <svg class="w-5 h-5 mr-1.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
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
    <div class="bg-white py-16 lg:py-24">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

          <!-- Article Content -->
          <article ref="contentRef" class="lg:col-span-8 opacity-0 translate-y-10">
            <!-- Featured Image -->
            <div v-if="post.image" class="relative mb-8 lg:mb-12">
              <div class="aspect-[16/9] rounded-3xl overflow-hidden shadow-2xl">
                <PostImage :post="post" class="w-full h-full object-cover" />
              </div>
            </div>

            <!-- Article Body -->
            <div class="prose prose-lg max-w-none">
              <div v-html="post.body"></div>
            </div>

            <!-- Tags -->
            <div v-if="post.tags.length > 0" class="mt-12 pt-8 border-t border-slate-200">
              <h3 class="text-xl font-semibold text-slate-900 mb-4">Tags</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tag in post.tags"
                  :key="tag.id"
                  class="inline-flex items-center px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors duration-200"
                >
                  #{{ tag.name }}
                </span>
              </div>
            </div>

            <!-- Social Share -->
            <div class="mt-8 pt-8 border-t border-slate-200">
              <h3 class="text-xl font-semibold text-slate-900 mb-4">Bagikan Artikel</h3>
              <div class="flex flex-wrap gap-3">
                <a
                  :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`"
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                >
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                  Facebook
                </a>
                <a
                  :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`"
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-200"
                >
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                  Twitter
                </a>
                <a
                  :href="`https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`"
                  target="_blank"
                  class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200"
                >
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                  </svg>
                  WhatsApp
                </a>
              </div>
            </div>
          </article>

          <!-- Sidebar -->
          <aside ref="sidebarRef" class="lg:col-span-4 opacity-0 translate-y-10">
            <div class="sticky top-8 space-y-8">

              <!-- Rating Section -->
              <div class="bg-gradient-to-br from-slate-50 to-blue-50 rounded-2xl p-6 border border-slate-200">
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Rating Artikel</h3>

                <div v-if="userHasRated" class="text-center">
                  <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <p class="text-green-700 font-medium">Terima kasih sudah memberi rating!</p>
                  <p class="text-slate-600 mt-2">Rating Anda membantu pembaca lain</p>
                </div>

                <div v-else class="text-center">
                  <p class="text-slate-700 mb-4">Seberapa bermanfaat artikel ini?</p>
                  <div class="flex justify-center items-center space-x-1 mb-4" @mouseleave="hoverRating = 0">
                    <button
                      v-for="star in 5"
                      :key="star"
                      @mouseover="hoverRating = star"
                      @click="setRating(star)"
                      class="p-1 transition-transform duration-200 hover:scale-110"
                    >
                      <svg
                        class="w-8 h-8 transition-colors duration-200"
                        :class="(hoverRating || currentRating) >= star ? 'text-yellow-400' : 'text-slate-300'"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </button>
                  </div>
                  <p class="text-sm text-slate-500">Klik bintang untuk memberi rating</p>
                  <div v-if="ratingForm.errors.rating" class="mt-2 text-red-600">
                    {{ ratingForm.errors.rating }}
                  </div>
                </div>
              </div>

              <!-- Recent Posts -->
              <div v-if="recentPosts.length > 0" class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <h3 class="text-2xl font-bold text-slate-900 mb-6">Artikel Terbaru</h3>
                <div class="space-y-4">
                  <article
                    v-for="recentPost in recentPosts"
                    :key="recentPost.id"
                    class="group"
                  >
                    <Link :href="route('posts.show', recentPost.slug)" class="block">
                      <div class="space-y-2">
                        <h4 class="font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors duration-200 line-clamp-2 leading-tight">
                          {{ recentPost.title }}
                        </h4>
                        <p class="text-slate-500">
                          {{ new Date(recentPost.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </p>
                      </div>
                    </Link>
                  </article>
                </div>
              </div>

              <!-- Back to Articles -->
              <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-6 border border-indigo-200">
                <h3 class="text-xl font-semibold text-slate-900 mb-3">Jelajahi Lebih Banyak</h3>
                <p class="text-slate-600 mb-4">Temukan artikel dan panduan keamanan siber lainnya</p>
                <Link
                  :href="route('posts.index')"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 font-medium"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                  Semua Artikel
                </Link>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
