<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  faqs: Object,
  categories: Array,
})

// Animation refs
const heroRef = ref(null)
const faqRef = ref(null)

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
  return Object.values(props.faqs).reduce((total, categoryFaqs) => total + categoryFaqs.length, 0)
})

// Get all FAQs in a flat array for streamlined display
const allFaqs = computed(() => {
  const faqs = []
  props.categories.forEach(category => {
    if (props.faqs[category]) {
      props.faqs[category].forEach(faq => {
        faqs.push({
          ...faq,
          category: category
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
    const firstFaqInCategory = allFaqs.value.find(faq => faq.category === category)
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
    const response = await fetch(`/faq/search?q=${encodeURIComponent(searchQuery.value)}`)
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
    rootMargin: '0px 0px -50px 0px'
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
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
    <section ref="heroRef" class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      <div class="relative z-10 px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="container max-w-7xl text-center">
          <div class="animate-fade-in-up">
            <!-- FAQ Icon -->
            <div class="w-20 h-20 bg-blue-100/20 rounded-full flex items-center justify-center mx-auto mb-8 backdrop-blur-sm">
              <i class="pi pi-question-circle !text-5xl text-blue-400"></i>
            </div>

            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl mb-6 leading-tight">
              Frequently Asked <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">Questions</span>
            </h1>

            <p class="text-xl sm:text-2xl text-slate-300 mb-8 max-w-3xl mx-auto">
              Temukan jawaban untuk pertanyaan yang paling sering diajukan seputar keamanan siber dan layanan CSIRT
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section ref="faqRef" class="py-12 sm:py-16 lg:py-24 bg-white opacity-0 translate-y-10">
      <div class="container">
        <div class="max-w-7xl mx-auto">

          <!-- Search Section -->
          <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 mb-4">Cari Jawaban Anda</h2>
            <p class="text-slate-600 mb-8 text-lg max-w-2xl mx-auto">
              Gunakan pencarian untuk menemukan informasi yang Anda butuhkan dengan cepat
            </p>

            <div class="max-w-2xl mx-auto">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input
                  v-model="searchQuery"
                  @input="searchFaqs"
                  type="text"
                  class="block w-full pl-12 pr-12 py-4 text-lg border border-slate-300 rounded-2xl leading-5 bg-white placeholder-slate-500 focus:outline-none focus:placeholder-slate-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Ketik pertanyaan Anda di sini..."
                />
                <div v-if="searchQuery" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                  <button
                    @click="clearSearch"
                    class="p-1 rounded-full hover:bg-slate-100 transition-colors duration-200"
                  >
                    <svg class="h-5 w-5 text-slate-400 hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Search Results -->
              <div v-if="searchQuery && searchResults.length > 0" class="mt-6 bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                  <h3 class="text-xl font-semibold text-slate-900">Hasil Pencarian</h3>
                </div>
                <div class="divide-y divide-slate-200">
                  <div
                    v-for="result in searchResults"
                    :key="result.id"
                    class="p-6 hover:bg-slate-50 transition-colors duration-200 cursor-pointer"
                    @click="toggleItem(`search-${result.id}`)"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <h4 class="text-lg sm:text-xl font-semibold text-slate-900 mb-2">{{ result.question }}</h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                          {{ result.category }}
                        </span>
                      </div>
                      <svg
                        class="w-5 h-5 text-slate-400 transition-transform duration-200"
                        :class="{ 'rotate-180': openItems.has(`search-${result.id}`) }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
              <div v-if="searchQuery && searchResults.length === 0 && !isSearching" class="mt-6 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>Tidak ditemukan hasil untuk "<strong>{{ searchQuery }}</strong>"</p>
                <p class="mt-1">Coba gunakan kata kunci yang berbeda atau jelajahi kategori di bawah</p>
              </div>
            </div>
          </div>

          <!-- FAQ & Categories Content -->
          <div v-if="!searchQuery || searchResults.length === 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12">

            <!-- FAQ Content - Streamlined List -->
            <div class="lg:col-span-8">
              <div class="space-y-4">
                <div
                  v-for="faq in allFaqs"
                  :key="faq.id"
                  :id="`faq-${faq.id}`"
                  class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-200"
                >
                  <button
                    @click="toggleItem(faq.id)"
                    class="w-full text-left px-6 py-6 focus:outline-none hover:bg-slate-50 transition-colors duration-200"
                  >
                    <div class="flex items-start justify-between">
                      <h3 class="text-lg sm:text-xl font-semibold text-slate-900 pr-4">{{ faq.question }}</h3>
                      <svg
                        class="w-5 h-5 text-slate-400 transition-transform duration-200 flex-shrink-0"
                        :class="{ 'rotate-180': openItems.has(faq.id) }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                  </button>
                  <div
                    v-show="openItems.has(faq.id)"
                    class="px-6 pb-6"
                  >
                    <div class="max-w-none text-slate-500 mb-4" v-html="faq.answer"></div>
                    <!-- Category information moved here -->
                    <div class="pt-4 border-t border-slate-100">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-700">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ faq.category }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No FAQs Message -->
              <div v-if="allFaqs.length === 0" class="text-center py-16">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                  <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 class="text-2xl font-semibold text-slate-900 mb-2">FAQ Belum Tersedia</h3>
                <p class="text-slate-600">FAQ akan segera hadir untuk membantu Anda.</p>
              </div>
            </div>

            <!-- Categories Sidebar -->
            <div class="lg:col-span-4">
              <div class="sticky top-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                  <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-xl font-semibold text-slate-900">Kategori</h3>
                  </div>
                  <nav class="py-2">
                    <button
                      v-for="category in categories"
                      :key="category"
                      @click="scrollToCategory(category)"
                      class="w-full text-left px-6 py-3 hover:bg-slate-50 transition-colors duration-200 border-l-4 border-transparent hover:border-indigo-500"
                      :class="{
                        'bg-indigo-50 border-indigo-500 text-indigo-700 font-medium': activeCategory === category,
                        'text-slate-700': activeCategory !== category
                      }"
                    >
                      <div class="flex items-center justify-between">
                        <span>{{ category }}</span>
                        <span class="text-xs bg-slate-200 text-slate-600 px-2 py-1 rounded-full">
                          {{ props.faqs[category]?.length || 0 }}
                        </span>
                      </div>
                    </button>
                  </nav>
                </div>

                <!-- Contact Support -->
                <div class="mt-8 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-6 border border-indigo-200">
                  <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                    <i-lucide-crosshair class="w-6 h-6 text-indigo-600" />
                  </div>
                  <h3 class="text-xl font-semibold text-slate-900 mb-2">Butuh Bantuan Lebih?</h3>
                  <p class="text-slate-600 mb-4">Tidak menemukan jawaban yang Anda cari? Tim support kami siap membantu</p>
                  <Link
                    :href="route('incident.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 font-medium"
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
