<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

/**
 * PostCard — kartu artikel untuk halaman Landing dan Posts/Index.
 *
 * Props:
 *   post           — object post (title, slug, image, excerpt, body,
 *                    categories, published_by, published_at, created_at)
 *   animationDelay — delay animasi CSS dalam ms (opsional)
 */
const props = defineProps({
  post: { type: Object, required: true },
  animationDelay: { type: Number, default: 0 },
})

const postUrl = computed(() => route('posts.show', { post: props.post.slug }))

const excerpt = computed(() => {
  const p = props.post
  if (p.excerpt?.trim()) return p.excerpt.trim()

  // Fallback: strip HTML dari body
  if (typeof document !== 'undefined') {
    const div = document.createElement('div')
    div.innerHTML = p.body || ''
    return (div.textContent || div.innerText || '').trim()
  }
  // SSR fallback
  return (p.body || '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim()
})

const formattedDate = computed(() => {
  const d = props.post.published_at || props.post.created_at
  if (!d) return ''
  return new Date(d).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
})
</script>

<template>
  <article
    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-slate-200"
    :style="animationDelay ? { animationDelay: `${animationDelay}ms` } : {}"
  >
    <!-- Gambar -->
    <div class="relative overflow-hidden">
      <Link :href="postUrl" class="block">
        <PostImage
          :post="post"
          class="h-48 w-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </Link>
    </div>

    <!-- Konten -->
    <div class="p-6">
      <!-- Kategori -->
      <div class="mb-3">
        <div v-if="post.categories?.length" class="flex flex-wrap gap-2">
          <span
            v-for="category in post.categories"
            :key="category.id"
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition-colors duration-200"
          >
            <Link :href="route('categories.show', category.slug)">{{ category.name }}</Link>
          </span>
        </div>
        <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-800">
          Artikel
        </span>
      </div>

      <!-- Judul -->
      <h3 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors duration-300 line-clamp-2">
        <Link :href="postUrl">{{ post.title }}</Link>
      </h3>

      <!-- Excerpt -->
      <p class="text-slate-600 mb-4 line-clamp-3 leading-relaxed">{{ excerpt }}</p>

      <!-- Meta: penulis + tanggal -->
      <div class="flex items-center justify-between text-sm text-slate-500">
        <div v-if="post.published_by" class="flex items-center gap-1">
          <i-lucide-user-pen class="h-4 w-4" />
          <span>{{ post.published_by }}</span>
        </div>
        <time v-if="formattedDate" :datetime="post.published_at || post.created_at">
          {{ formattedDate }}
        </time>
      </div>

      <!-- Baca Artikel -->
      <div class="mt-4 pt-4 border-t border-slate-200">
        <Link
          :href="postUrl"
          class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium group/link"
        >
          Baca Artikel
          <svg class="ml-1 h-3 w-3 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </Link>
      </div>
    </div>
  </article>
</template>
