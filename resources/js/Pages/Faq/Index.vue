<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useParticles } from '@/Composables/useParticles'

const props = defineProps({
  faqs: Object,
  categories: Array,
})

// Animation refs
const heroRef = ref(null)
const faqRef = ref(null)
const { minimalParticlesOptions } = useParticles()

// State
const activeCategory = ref(props.categories[0] || null)
const searchQuery = ref('')
const searchResults = ref([])
const isSearching = ref(false)
const openItems = ref(new Set())

// Computed
const filteredFaqs = computed(() => {
  if (!activeCategory.value) return []
  return props.faqs[activeCategory.value] || []
})

const totalFaqCount = computed(() => {
  return Object.values(props.faqs).reduce(
    (total, categoryFaqs) => total + categoryFaqs.length,
    0,
  )
})

// Get all FAQs in a flat array for streamlined display
const allFaqs = computed(() => {
  const faqs = []
  props.categories.forEach((category) => {
    if (props.faqs[category]) {
      props.faqs[category].forEach((faq) => {
        faqs.push({
          ...faq,
          category: category,
        })
      })
    }
  })
  return faqs
})

// Methods
const setActiveCategory = (category) => {
  activeCategory.value = category
  searchQuery.value = ''
  searchResults.value = []
  openItems.value.clear()

  // Scroll to first FAQ of the category
  nextTick(() => {
    const firstFaqInCategory = allFaqs.value.find(
      (faq) => faq.category === category,
    )
    if (firstFaqInCategory) {
      const element = document.getElementById(`faq-${firstFaqInCategory.id}`)
      if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }
    }
  })
}

const toggleItem = (id) => {
  if (openItems.value.has(id)) {
    openItems.value.delete(id)
  } else {
    openItems.value.add(id)
  }
}

const searchFaqs = async () => {
  if (searchQuery.value.length < 3) {
    searchResults.value = []
    return
  }

  isSearching.value = true

  try {
    const response = await fetch(
      `/faq/search?q=${encodeURIComponent(searchQuery.value)}`,
    )
    const results = await response.json()
    searchResults.value = results
  } catch (error) {
    console.error('Search error:', error)
    searchResults.value = []
  } finally {
    isSearching.value = false
  }
}

const clearSearch = () => {
  searchQuery.value = ''
  searchResults.value = []
  activeCategory.value = props.categories[0] || null
}

const scrollToCategory = (category) => {
  setActiveCategory(category)
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

  if (faqRef.value) observer.observe(faqRef.value)
})
</script>

<template>
  <AppLayout title="Frequently Asked Questions">
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

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container max-w-7xl text-center">
          <div class="animate-fade-in-up">
            <!-- FAQ Icon -->
            <div
              class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-full bg-blue-100/20 backdrop-blur-sm"
            >
              <i class="pi pi-question-circle !text-5xl text-blue-400"></i>
            </div>

            <h1
              class="mb-6 text-5xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl lg:text-7xl"
            >
              Frequently Asked
              <span
                class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent"
                >Questions</span
              >
            </h1>

            <p
              class="mx-auto mb-8 max-w-3xl text-xl text-slate-300 sm:text-2xl"
            >
              Temukan jawaban untuk pertanyaan yang paling sering diajukan
              seputar keamanan siber dan layanan CSIRT
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section
      ref="faqRef"
      class="translate-y-10 bg-white py-12 opacity-0 sm:py-16 lg:py-24"
    >
      <div class="container">
        <div class="mx-auto max-w-7xl">
          <!-- Search Section -->
          <div class="mb-12 text-center sm:mb-16">
            <h2
              class="mb-4 text-3xl font-bold text-slate-900 sm:text-4xl lg:text-5xl"
            >
              Cari Jawaban Anda
            </h2>
            <p class="mx-auto mb-8 max-w-2xl text-lg text-slate-600">
              Gunakan pencarian untuk menemukan informasi yang Anda butuhkan
              dengan cepat
            </p>

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
                  v-model="searchQuery"
                  @input="searchFaqs"
                  type="text"
                  class="block w-full rounded-2xl border border-slate-300 bg-white py-4 pl-12 pr-12 text-lg leading-5 placeholder-slate-500 focus:border-indigo-500 focus:placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                  placeholder="Ketik pertanyaan Anda di sini..."
                />
                <div
                  v-if="searchQuery"
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

              <!-- Search Results -->
              <div
                v-if="searchQuery && searchResults.length > 0"
                class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
              >
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                  <h3 class="text-xl font-semibold text-slate-900">
                    Hasil Pencarian
                  </h3>
                </div>
                <div class="divide-y divide-slate-200">
                  <div
                    v-for="result in searchResults"
                    :key="result.id"
                    class="cursor-pointer p-6 transition-colors duration-200 hover:bg-slate-50"
                    @click="toggleItem(`search-${result.id}`)"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <h4
                          class="mb-2 text-lg font-semibold text-slate-900 sm:text-xl"
                        >
                          {{ result.question }}
                        </h4>
                        <span
                          class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800"
                        >
                          {{ result.category }}
                        </span>
                      </div>
                      <svg
                        class="h-5 w-5 text-slate-400 transition-transform duration-200"
                        :class="{
                          'rotate-180': openItems.has(`search-${result.id}`),
                        }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 9l-7 7-7-7"
                        />
                      </svg>
                    </div>
                    <div
                      v-show="openItems.has(`search-${result.id}`)"
                      class="mt-4 max-w-none text-slate-500"
                      v-html="result.answer"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- No Results -->
              <div
                v-if="searchQuery && searchResults.length === 0 && !isSearching"
                class="mt-6 text-center text-slate-500"
              >
                <svg
                  class="mx-auto mb-4 h-12 w-12 text-slate-300"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
                <p>
                  Tidak ditemukan hasil untuk "<strong>{{ searchQuery }}</strong
                  >"
                </p>
                <p class="mt-1">
                  Coba gunakan kata kunci yang berbeda atau jelajahi kategori di
                  bawah
                </p>
              </div>
            </div>
          </div>

          <!-- FAQ & Categories Content -->
          <div
            v-if="!searchQuery || searchResults.length === 0"
            class="grid grid-cols-1 gap-8 sm:gap-12 lg:grid-cols-12"
          >
            <!-- FAQ Content - Streamlined List -->
            <div class="lg:col-span-8">
              <div class="space-y-4">
                <div
                  v-for="faq in allFaqs"
                  :key="faq.id"
                  :id="`faq-${faq.id}`"
                  class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md"
                >
                  <button
                    @click="toggleItem(faq.id)"
                    class="w-full px-6 py-6 text-left transition-colors duration-200 hover:bg-slate-50 focus:outline-none"
                  >
                    <div class="flex items-start justify-between">
                      <h3
                        class="pr-4 text-lg font-semibold text-slate-900 sm:text-xl"
                      >
                        {{ faq.question }}
                      </h3>
                      <svg
                        class="h-5 w-5 flex-shrink-0 text-slate-400 transition-transform duration-200"
                        :class="{ 'rotate-180': openItems.has(faq.id) }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 9l-7 7-7-7"
                        />
                      </svg>
                    </div>
                  </button>
                  <div v-show="openItems.has(faq.id)" class="px-6 pb-6">
                    <div
                      class="mb-4 max-w-none text-slate-500"
                      v-html="faq.answer"
                    ></div>
                    <!-- Category information moved here -->
                    <div class="border-t border-slate-100 pt-4">
                      <span
                        class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700"
                      >
                        <svg
                          class="mr-1 h-3 w-3"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                          />
                        </svg>
                        {{ faq.category }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No FAQs Message -->
              <div v-if="allFaqs.length === 0" class="py-16 text-center">
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
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                </div>
                <h3 class="mb-2 text-2xl font-semibold text-slate-900">
                  FAQ Belum Tersedia
                </h3>
                <p class="text-slate-600">
                  FAQ akan segera hadir untuk membantu Anda.
                </p>
              </div>
            </div>

            <!-- Categories Sidebar -->
            <div class="lg:col-span-4">
              <div class="sticky top-8">
                <div
                  class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                  <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <h3 class="text-xl font-semibold text-slate-900">
                      Kategori
                    </h3>
                  </div>
                  <nav class="py-2">
                    <button
                      v-for="category in categories"
                      :key="category"
                      @click="scrollToCategory(category)"
                      class="w-full border-l-4 border-transparent px-6 py-3 text-left transition-colors duration-200 hover:border-indigo-500 hover:bg-slate-50"
                      :class="{
                        'border-indigo-500 bg-indigo-50 font-medium text-indigo-700':
                          activeCategory === category,
                        'text-slate-700': activeCategory !== category,
                      }"
                    >
                      <div class="flex items-center justify-between">
                        <span>{{ category }}</span>
                        <span
                          class="rounded-full bg-slate-200 px-2 py-1 text-xs text-slate-600"
                        >
                          {{ props.faqs[category]?.length || 0 }}
                        </span>
                      </div>
                    </button>
                  </nav>
                </div>

                <!-- Contact Support -->
                <div
                  class="mt-8 rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-blue-50 p-6"
                >
                  <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100"
                  >
                    <i-lucide-crosshair class="h-6 w-6 text-indigo-600" />
                  </div>
                  <h3 class="mb-2 text-xl font-semibold text-slate-900">
                    Butuh Bantuan Lebih?
                  </h3>
                  <p class="mb-4 text-slate-600">
                    Tidak menemukan jawaban yang Anda cari? Tim support kami
                    siap membantu
                  </p>
                  <Link
                    :href="route('incident.create')"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white transition-colors duration-200 hover:bg-indigo-700"
                  >
                    <i-lucide-message-circle-more class="mr-2" />
                    Hubungi Support
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </AppLayout>
</template>
