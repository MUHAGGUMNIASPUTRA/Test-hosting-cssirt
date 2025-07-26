<script setup>
import { onMounted, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  post: Object,
  recentPosts: Array,
  hasRated: Boolean,
})

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
</script>

<template>
  <AppLayout :title="post.title">
    <div class="bg-white py-8 lg:py-24">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div
          class="mx-auto grid max-w-2xl grid-cols-1 grid-rows-1 items-start gap-x-8 gap-y-8 lg:mx-0 lg:max-w-none lg:grid-cols-3"
        >
          <!-- Post content -->
          <div class="lg:col-span-2 lg:row-start-1">
            <div v-if="post.image" class="relative w-full mb-6">
              <img
                :src="post.image.startsWith('http') ? post.image : '/storage/' + post.image"
                :alt="post.title"
                class="aspect-[16/9] w-full rounded-2xl bg-gray-100 object-cover sm:aspect-[2/1] lg:aspect-[16/9]"
              />
              <div
                class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10"
              ></div>
            </div>

            <div class="mt-0 sm:mt-2 space-y-4">
              <h1 class="mb-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ post.title }}</h1>

              <div class="flex items-center text-xs text-gray-400 gap-3">
                <!-- Author -->
                <div class="flex items-center gap-2">
                  <i class="pi pi-user-edit !text-xs"></i>
                  <div>{{ post.published_by }}</div>
                </div>

                <div class="border-l h-5"></div>

                <!-- Published Date -->
                <div class="flex items-center gap-2">
                  <i class="pi pi-calendar !text-xs"></i>
                  <time :datetime="post.published_at">{{
                    new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                  }}</time>
                </div>

                <div class="border-l h-5"></div>

                <!-- Views Count -->
                <div class="flex items-center gap-2">
                  <i class="pi pi-eye !text-xs"></i>
                  <div>{{ post.views_count }}x</div>
                </div>

                <div v-if="post.rating" class="border-l h-5"></div>

                <!-- Rating -->
                <div v-if="post.rating" class="flex items-center gap-2">
                  <i class="pi pi-star !text-xs"></i>
                  <div>{{ post.rating }} ({{ post.ratings_count }})</div>
                </div>
              </div>

              <!-- Excerpt -->
              <hr/>
              <div><p class="mt-4 text-gray-500">{{ post.excerpt }}</p></div>
              <hr/>

              <!-- Body -->
              <div class="prose mt-4 max-w-none text-gray-600" v-html="post.body"></div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="max-w-xl lg:col-start-3 lg:row-start-1">
            <div class="border-t border-gray-200 pt-6 lg:pt-2 lg:border-t-0 space-y-6">
              <!-- Categories -->
              <div v-if="post.categories.length > 0">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Kategori</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span
                    v-for="category in post.categories"
                    :key="category.id"
                    class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-600 ring-1 ring-inset ring-purple-500/10"
                  >
                    {{ category.name }}
                  </span>
                </div>
              </div>

              <!-- Tags -->
              <div v-if="post.tags.length > 0">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Tags</h3>
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

              <hr/>

              <!-- Rate Post -->
              <div>
                <div v-if="userHasRated" class="mt-3 p-3 bg-green-50 text-green-700 border border-green-100 rounded-md">
                  <p>Terima kasih, Anda sudah memberikan rating untuk artikel ini.</p>
                </div>
                <div v-else>
                  <h3 class="text-xl font-semibold text-gray-900">Beri Rating Artikel Ini</h3>
                  <p class="text-gray-600 mt-3 mb-1">Klik bintang untuk memberi nilai:</p>
                  <div class="flex items-center space-x-1" @mouseleave="hoverRating = 0">
                    <span v-for="star in 5" :key="star">
                      <i
                        class="pi pi-star-fill cursor-pointer !text-2xl"
                        :class="(hoverRating || currentRating) >= star ? 'text-yellow-400' : 'text-gray-300'"
                        @mouseover="hoverRating = star"
                        @click="setRating(star)"
                      ></i>
                    </span>
                  </div>
                  <small v-if="ratingForm.errors.rating" class="p-error">{{ ratingForm.errors.rating }}</small>
                </div>
              </div>

              <hr/>

              <!-- Recent Posts -->
              <div v-if="recentPosts.length > 0">
                <h3 class="text-xl font-semibold text-gray-900">Artikel Terbaru</h3>
                <ul role="list" class="mt-4 space-y-4">
                  <li v-for="recentPost in recentPosts" :key="recentPost.id">
                    <Link :href="route('posts.show', recentPost.slug)" class="group">
                      <p class="font-semibold text-gray-600 group-hover:text-blue-600">{{ recentPost.title }}</p>
                      <p class="text-sm text-gray-500">
                        {{ new Date(recentPost.published_at).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }) }}
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
