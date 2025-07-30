<script setup>
// filepath: resources/js/Pages/Admin/Services/Create.vue

import { ref, computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  service: {
    type: Object,
    default: null
  }
})

// Determine if we're editing or creating
const isEditing = computed(() => !!props.service)
const pageTitle = computed(() =>
  isEditing.value ? `Edit Layanan: ${props.service.name}` : 'Tambah Layanan Baru'
)
const headerTitle = computed(() =>
  isEditing.value ? 'Edit Layanan' : 'Tambah Layanan Baru'
)
const headerDescription = computed(() =>
  isEditing.value
    ? 'Perbarui informasi layanan yang disediakan'
    : 'Buat layanan baru untuk ditampilkan kepada publik'
)
const submitButtonText = computed(() =>
  isEditing.value ? 'Update Layanan' : 'Simpan Layanan'
)

// Initialize form with default or existing values
const form = useForm({
  name: props.service?.name || '',
  icon: props.service?.icon || '',
  short_description: props.service?.short_description || '',
  full_description: props.service?.full_description || '',
  is_active: props.service?.is_active ?? true,
})

// Word count for short description (limit to 25 words)
const shortDescWordCount = computed(() => {
  if (!form.short_description) return 0
  return form.short_description.trim().split(/\s+/).filter(word => word.length > 0).length
})

const shortDescWordLimit = 25
const isShortDescOverLimit = computed(() => shortDescWordCount.value > shortDescWordLimit)

// Character count for short description
const shortDescCharCount = computed(() => form.short_description.length)
const shortDescCharLimit = 200
const isShortDescCharOverLimit = computed(() => shortDescCharCount.value > shortDescCharLimit)

// Combined validation check
const isShortDescInvalid = computed(() => isShortDescOverLimit.value || isShortDescCharOverLimit.value)

// Form validation check
const canSubmit = computed(() => {
  return form.name.trim() &&
         form.short_description.trim() &&
         !isShortDescInvalid.value &&
         !form.processing
})

// Popular Material Symbols icons for services
const iconSuggestions = [
  'volunteer_activism', 'security', 'shield', 'gpp_good', 'admin_panel_settings',
  'support_agent', 'help_center', 'contact_support', 'live_help', 'quiz',
  'policy', 'assignment', 'description', 'article', 'library_books',
  'cloud_sync', 'backup', 'sync', 'restore', 'update',
  'monitoring', 'analytics', 'assessment', 'bug_report', 'health_and_safety',
  'warning', 'error', 'info', 'check_circle', 'verified'
]

const filteredIconSuggestions = ref([])
const showIconSuggestions = ref(false)

// Filter icon suggestions based on input
const filterIconSuggestions = () => {
  if (form.icon.length < 2) {
    filteredIconSuggestions.value = []
    showIconSuggestions.value = false
    return
  }

  filteredIconSuggestions.value = iconSuggestions.filter(icon =>
    icon.toLowerCase().includes(form.icon.toLowerCase())
  ).slice(0, 10)

  showIconSuggestions.value = filteredIconSuggestions.value.length > 0
}

const selectIcon = (icon) => {
  form.icon = icon
  showIconSuggestions.value = false
}

const submit = () => {
  if (!canSubmit.value) {
    return
  }

  if (isEditing.value) {
    form.put(route('admin.services.update', props.service.id))
  } else {
    form.post(route('admin.services.store'))
  }
}

// Watch for icon input changes
watch(() => form.icon, filterIconSuggestions)
</script>

<template>
  <AdminLayout :title="pageTitle">
    <form @submit.prevent="submit">
      <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ headerTitle }}{{ isEditing ? `: ${service.name}` : '' }}</h2>
              <p v-if="!isEditing" class="text-slate-600">{{ headerDescription }}</p>
              <!-- Show service status when editing -->
              <div v-if="isEditing" class="flex items-center gap-3 mt-2">
                <Tag
                  :value="service.is_active ? 'Aktif' : 'Tidak Aktif'"
                  :severity="service.is_active ? 'success' : 'secondary'"
                  size="small"
                />
                <span class="text-slate-500 text-sm">{{ service.slug }}</span>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Link
                :href="route('admin.services.index')"
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
              >
                <span class="material-symbols-outlined !text-xl">west</span>
                  Kembali
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <span class="material-symbols-outlined !text-xl" :class="{ 'animate-spin': form.processing }">
                  {{ form.processing ? 'progress_activity' : 'save' }}
                </span>
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Service Information -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-blue-600">volunteer_activism</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Informasi Layanan</h3>
                  <p class="text-slate-600">Data dasar layanan yang akan ditampilkan</p>
                </div>
              </div>

              <div class="space-y-6">
                <div>
                  <label for="name" class="block font-medium text-slate-700 mb-2">
                    Nama Layanan <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="name"
                    v-model="form.name"
                    placeholder="Masukkan nama layanan"
                    required
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.name }"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>

                <div>
                  <label for="icon" class="block font-medium text-slate-700 mb-2">
                    Ikon Layanan
                  </label>
                  <div class="relative">
                    <div class="flex items-center gap-3">
                      <div class="flex-1 relative">
                        <InputText
                          id="icon"
                          v-model="form.icon"
                          placeholder="Masukkan nama ikon Material Symbols"
                          class="w-full"
                          :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.icon }"
                          @focus="filterIconSuggestions"
                        />

                        <!-- Icon suggestions dropdown -->
                        <div v-if="showIconSuggestions" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                          <button
                            v-for="suggestion in filteredIconSuggestions"
                            :key="suggestion"
                            type="button"
                            @click="selectIcon(suggestion)"
                            class="w-full flex items-center gap-3 px-4 py-2 hover:bg-slate-50 text-left"
                          >
                            <span class="material-symbols-outlined text-slate-600">{{ suggestion }}</span>
                            <span class="text-sm text-slate-700">{{ suggestion }}</span>
                          </button>
                        </div>
                      </div>

                      <!-- Icon preview -->
                      <div v-if="form.icon" class="w-10 h-10 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-blue-600 !text-xl">{{ form.icon }}</span>
                      </div>
                    </div>
                  </div>
                  <p v-if="form.errors.icon" class="mt-1 text-red-600">
                    {{ form.errors.icon }}
                  </p>
                  <p class="mt-1 text-slate-500 text-sm">
                    Gunakan nama ikon dari
                    <a href="https://fonts.google.com/icons" target="_blank" class="text-blue-600 hover:underline">
                      Material Symbols
                    </a>
                  </p>
                </div>

                <div>
                  <label for="short_description" class="block font-medium text-slate-700 mb-2">
                    Deskripsi Singkat <span class="text-red-500">*</span>
                  </label>
                  <Textarea
                    id="short_description"
                    v-model="form.short_description"
                    rows="3"
                    placeholder="Jelaskan secara singkat tentang layanan ini..."
                    required
                    class="w-full"
                    :class="{
                      'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.short_description || isShortDescOverLimit
                    }"
                  />
                  <div class="flex justify-between items-start mt-1">
                    <div>
                      <p v-if="form.errors.short_description" class="text-red-600">
                        {{ form.errors.short_description }}
                      </p>
                      <p v-else class="text-slate-500 text-sm">
                        Deskripsi singkat akan muncul di website publik
                      </p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm" :class="isShortDescOverLimit ? 'text-red-600' : 'text-slate-500'">
                        {{ shortDescWordCount }}/{{ shortDescWordLimit }} kata
                      </p>
                      <p class="text-xs text-slate-400">
                        {{ shortDescCharCount }}/{{ shortDescCharLimit }} karakter
                      </p>
                    </div>
                  </div>
                </div>

                <div>
                  <label for="full_description" class="block font-medium text-slate-700 mb-2">
                    Deskripsi Lengkap
                  </label>
                  <Textarea
                    id="full_description"
                    v-model="form.full_description"
                    rows="6"
                    placeholder="Jelaskan secara detail tentang layanan ini, manfaat, dan informasi penting lainnya..."
                    class="w-full"
                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.full_description }"
                  />
                  <p v-if="form.errors.full_description" class="mt-1 text-red-600">
                    {{ form.errors.full_description }}
                  </p>
                  <!-- <p class="mt-1 text-slate-500 text-sm">
                    Deskripsi lengkap akan ditampilkan di halaman detail layanan
                  </p> -->
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Status Management -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
              <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-lg flex items-center justify-center">
                  <span class="material-symbols-outlined text-green-600">toggle_on</span>
                </div>
                <div class="ml-3">
                  <h3 class="font-semibold text-slate-900">Status Layanan</h3>
                  <p class="text-slate-600">Kontrol visibilitas layanan</p>
                </div>
              </div>

              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <label for="is_active" class="font-medium text-slate-700">
                    Status Aktif
                  </label>
                  <ToggleSwitch
                    id="is_active"
                    v-model="form.is_active"
                  />
                </div>
                <p class="text-sm text-slate-500">
                  {{ form.is_active ? 'Layanan akan ditampilkan di website publik' : 'Layanan disembunyikan dari website publik' }}
                </p>
              </div>
            </div>

            <!-- Preview Card -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-6">
              <h3 class="font-medium text-slate-700 mb-4">
                {{ isEditing ? 'Ringkasan Perubahan' : 'Preview Layanan' }}
              </h3>
              <div class="space-y-3">
                <div class="flex items-start gap-3">
                  <div class="w-10 h-10 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span v-if="form.icon" class="material-symbols-outlined text-blue-600 !text-xl">{{ form.icon }}</span>
                    <span v-else class="material-symbols-outlined text-blue-600 !text-xl">volunteer_activism</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="font-medium text-slate-900 truncate">
                      {{ form.name || 'Nama layanan' }}
                    </h4>
                    <p class="text-sm text-slate-600">
                      {{ form.short_description || 'Deskripsi singkat akan muncul di sini' }}
                    </p>
                  </div>
                </div>

                <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                  <span class="text-slate-500 text-sm">Status:</span>
                  <Tag
                    :value="form.is_active ? 'Aktif' : 'Tidak Aktif'"
                    :severity="form.is_active ? 'success' : 'secondary'"
                    size="small"
                  />
                </div>
              </div>
            </div>

            <!-- Button Submit (Mobile Only) -->
            <div class="block sm:hidden">
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-800 text-white w-full inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition disabled:opacity-50"
              >
                <span class="material-symbols-outlined !text-xl" :class="{ 'animate-spin': form.processing }">
                  {{ form.processing ? 'progress_activity' : 'save' }}
                </span>
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </button>
            </div>

          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
