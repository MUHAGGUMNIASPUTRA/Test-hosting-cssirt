<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  posts: Object,
})

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

  // Get Previous and Next links from Laravel
  const prevLink = links[0];
  const nextLink = links[links.length - 1];

  // Specify the range of pages to be displayed (maximum 3)
  let startPage;
  if (current_page <= 2) {
    startPage = 1;
  } else if (current_page === last_page) {
    startPage = Math.max(1, last_page - 2);
  } else {
    startPage = current_page - 1;
  }

  const endPage = Math.min(last_page, startPage + 2);

  // Create an array for page number links
  const pageLinks = [];
  for (let i = startPage; i <= endPage; i++) {
    // Find the original link from Laravel to get the correct URL
    const originalLink = links.find(link => parseInt(link.label) === i);
    if (originalLink) {
      pageLinks.push(originalLink);
    }
  }

  // Merge all links into one array
  return [prevLink, ...pageLinks, nextLink];
});
</script>

<template>
  <AppLayout title="Artikel & Panduan">
    <div class="bg-gray-50 px-4 pb-20 pt-16 sm:px-6 lg:px-8 lg:pb-28 lg:pt-24">
      <div class="relative mx-auto max-w-lg divide-y-2 divide-gray-200 lg:max-w-7xl">
        <div>
          <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Artikel & Panduan</h2>
          <p class="mt-3 text-xl text-gray-500 sm:mt-4">
            Ikuti informasi, panduan, dan berita terkini seputar keamanan siber
            untuk meningkatkan kewaspadaan kita bersama.
          </p>
        </div>

        <!-- Posts Grid -->
        <div class="mt-6 pt-6 grid gap-6 lg:grid-cols-3 lg:gap-x-5">
          <div v-for="post in posts.data" :key="post.id" class="flex flex-col overflow-hidden rounded-lg shadow-lg bg-white">
            <Link v-if="post.image" :href="route('posts.show', { post: post.slug })" class="flex-shrink-0">
              <PostImage :post="post" />
            </Link>

            <div class="flex flex-col bg-white p-6 space-y-4">
              <!-- Categories -->
              <div class="text-xs font-medium text-purple-600">
                <div v-if="post.categories?.length > 0" class="flex flex-wrap gap-2">
                  <span v-for="(category, index) in post.categories" :key="category.id" class="relative z-10 rounded-md bg-purple-50 px-2 py-1 font-medium text-purple-600 ring-1 ring-inset ring-purple-500/10">
                    <Link :href="route('categories.show', category.slug)">{{ category.name }}</Link>
                  </span>
                </div>
                <span v-else>Artikel</span>
              </div>

              <!-- Post Title & Excerpt -->
              <div>
                <a :href="route('posts.show', { post: post.slug })" class="text-xl font-semibold text-gray-900 hover:text-blue-600">{{ post.title }}</a>
                <p class="mt-3 text-base text-gray-500">{{ generateExcerpt(post) }}</p>
              </div>

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
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="posts.links.length > 3" class="mt-8 flex justify-center">
          <div class="mt-6 flex rounded-md shadow-sm">
            <!-- First Page Link -->
            <Link
              :href="posts.first_page_url"
              class="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500"
              :class="{ 'hover:bg-gray-50': posts.current_page > 1, 'cursor-not-allowed opacity-50': posts.current_page === 1 }"
              v-html="'&laquo;'"
            />

            <!-- Links from our custom computed property -->
            <template v-for="(link, key) in paginationLinks" :key="key">
              <span
                v-if="link.url === null"
                class="relative -ml-px inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed"
                v-html="link.label.includes('Previous') ? '&lsaquo;' : '&rsaquo;'"
              />
              <Link
                v-else
                :href="link.url"
                class="relative -ml-px inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                :class="{ '!border-blue-500 !bg-blue-500 !text-white z-10': link.active }"
                v-html="link.label.includes('Previous') ? '&lsaquo;' : (link.label.includes('Next') ? '&rsaquo;' : link.label)"
              />
            </template>

            <!-- Last Page Link -->
            <Link
              :href="posts.last_page_url"
              class="relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500"
              :class="{ 'hover:bg-gray-50': posts.current_page < posts.last_page, 'cursor-not-allowed opacity-50': posts.current_page === posts.last_page }"
              v-html="'&raquo;'"
            />
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
