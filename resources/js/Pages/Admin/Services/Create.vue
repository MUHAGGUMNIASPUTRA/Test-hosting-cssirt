<script setup>
// filepath: resources/js/Pages/Admin/Services/Create.vue

import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  service: {
    type: Object,
    default: null,
  },
})

const { isMobile, isDesktop } = useResponsive()

// Determine if we're editing or creating
const isEditing = computed(() => !!props.service)
const pageTitle = computed(() =>
  isEditing.value
    ? `Edit Layanan: ${props.service.name}`
    : 'Tambah Layanan Baru',
)
const headerTitle = computed(() =>
  isEditing.value ? 'Edit Layanan' : 'Tambah Layanan Baru',
)
const headerDescription = computed(() =>
  isEditing.value
    ? 'Perbarui informasi layanan yang disediakan'
    : 'Buat layanan baru untuk ditampilkan kepada publik',
)
const submitButtonText = computed(() =>
  isEditing.value ? 'Update Layanan' : 'Simpan Layanan',
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
  return form.short_description
    .trim()
    .split(/\s+/)
    .filter((word) => word.length > 0).length
})

const shortDescWordLimit = 25
const isShortDescOverLimit = computed(
  () => shortDescWordCount.value > shortDescWordLimit,
)

// Character count for short description
const shortDescCharCount = computed(() => form.short_description.length)
const shortDescCharLimit = 200
const isShortDescCharOverLimit = computed(
  () => shortDescCharCount.value > shortDescCharLimit,
)

// Combined validation check
const isShortDescInvalid = computed(
  () => isShortDescOverLimit.value || isShortDescCharOverLimit.value,
)

// Form validation check
const canSubmit = computed(() => {
  return (
    form.name.trim() &&
    form.short_description.trim() &&
    !isShortDescInvalid.value &&
    !form.processing
  )
})

// Popular Material Symbols icons for services
const iconSuggestions = [
  'volunteer_activism',
  'security',
  'shield',
  'gpp_good',
  'admin_panel_settings',
  'support_agent',
  'help_center',
  'contact_support',
  'live_help',
  'quiz',
  'policy',
  'assignment',
  'description',
  'article',
  'library_books',
  'cloud_sync',
  'backup',
  'sync',
  'restore',
  'update',
  'monitoring',
  'analytics',
  'assessment',
  'bug_report',
  'health_and_safety',
  'warning',
  'error',
  'info',
  'check_circle',
  'verified',
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

  filteredIconSuggestions.value = iconSuggestions
    .filter((icon) => icon.toLowerCase().includes(form.icon.toLowerCase()))
    .slice(0, 10)

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
      <div class="space-y-4 lg:space-y-6">
        <!-- Header Section -->
        <div
          class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
        >
          <div
            class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
          >
            <div>
              <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
                {{ headerTitle }}{{ isEditing ? `: ${service.name}` : '' }}
              </h2>
              <p v-if="!isEditing" class="text-slate-600">
                {{ headerDescription }}
              </p>
              <!-- Show service status when editing -->
              <div v-if="isEditing" class="mt-2 flex items-center gap-3">
                <Tag
                  :value="service.is_active ? 'Aktif' : 'Tidak Aktif'"
                  :severity="service.is_active ? 'success' : 'secondary'"
                  size="small"
                />
                <span class="text-sm text-slate-500">{{ service.slug }}</span>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <Button
                severity="secondary"
                @click="() => router.get(route('admin.services.index'))"
                class="w-full lg:w-auto"
              >
                <IconArrowLeft size="16" />
                Kembali
              </Button>
              <Button
                v-if="!isMobile"
                type="submit"
                severity="primary"
                :disabled="form.processing"
                class="w-full lg:w-auto"
              >
                <IconLoader3
                  v-if="form.processing"
                  class="animate-spin"
                  size="16"
                />
                <IconDeviceFloppy v-else size="16" />
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </Button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
          <!-- Main Content -->
          <div class="space-y-4 lg:col-span-2 lg:space-y-6">
            <!-- Service Information -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
                >
                  <IconHeart
                    class="text-blue-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Informasi Layanan
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Data dasar layanan yang akan ditampilkan
                  </p>
                </div>
              </div>

              <div class="space-y-4 lg:space-y-6">
                <div>
                  <label
                    for="name"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Nama Layanan <span class="text-red-500">*</span>
                  </label>
                  <InputText
                    id="name"
                    v-model="form.name"
                    placeholder="Masukkan nama layanan"
                    required
                    class="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.name,
                    }"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>

                <div>
                  <label
                    for="icon"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Ikon Layanan
                  </label>
                  <div class="relative">
                    <div class="flex items-center gap-3">
                      <div class="relative flex-1">
                        <InputText
                          id="icon"
                          v-model="form.icon"
                          placeholder="Masukkan nama ikon Material Symbols"
                          class="w-full"
                          :class="{
                            'border-red-300 focus:border-red-500 focus:ring-red-500':
                              form.errors.icon,
                          }"
                          @focus="filterIconSuggestions"
                        />

                        <!-- Icon suggestions dropdown -->
                        <div
                          v-if="showIconSuggestions"
                          class="absolute z-10 mt-1 max-h-48 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
                        >
                          <button
                            v-for="suggestion in filteredIconSuggestions"
                            :key="suggestion"
                            type="button"
                            @click="selectIcon(suggestion)"
                            class="flex w-full items-center gap-3 px-4 py-2 text-left hover:bg-slate-50"
                          >
                            <span
                              class="material-symbols-outlined text-slate-600"
                              >{{ suggestion }}</span
                            >
                            <span class="text-sm text-slate-700">{{
                              suggestion
                            }}</span>
                          </button>
                        </div>
                      </div>

                      <!-- Icon preview -->
                      <div
                        v-if="form.icon"
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg border border-blue-200 bg-blue-50"
                      >
                        <span
                          class="material-symbols-outlined !text-xl text-blue-600"
                          >{{ form.icon }}</span
                        >
                      </div>
                    </div>
                  </div>
                  <p v-if="form.errors.icon" class="mt-1 text-red-600">
                    {{ form.errors.icon }}
                  </p>
                  <p class="mt-1 text-sm text-slate-500">
                    Gunakan nama ikon dari
                    <a
                      href="https://fonts.google.com/icons"
                      target="_blank"
                      class="text-blue-600 hover:underline"
                    >
                      Material Symbols
                    </a>
                  </p>
                </div>

                <div>
                  <label
                    for="short_description"
                    class="mb-2 block font-medium text-slate-700"
                  >
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
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.short_description || isShortDescOverLimit,
                    }"
                  />
                  <div class="mt-1 flex items-start justify-between">
                    <div>
                      <p
                        v-if="form.errors.short_description"
                        class="text-red-600"
                      >
                        {{ form.errors.short_description }}
                      </p>
                      <p v-else class="text-sm text-slate-500">
                        Deskripsi singkat akan muncul di website publik
                      </p>
                    </div>
                    <div class="text-right">
                      <p
                        class="text-sm"
                        :class="
                          isShortDescOverLimit
                            ? 'text-red-600'
                            : 'text-slate-500'
                        "
                      >
                        {{ shortDescWordCount }}/{{ shortDescWordLimit }} kata
                      </p>
                      <p class="text-xs text-slate-400">
                        {{ shortDescCharCount }}/{{ shortDescCharLimit }}
                        karakter
                      </p>
                    </div>
                  </div>
                </div>

                <div>
                  <label
                    for="full_description"
                    class="mb-2 block font-medium text-slate-700"
                  >
                    Deskripsi Lengkap
                  </label>
                  <Textarea
                    id="full_description"
                    v-model="form.full_description"
                    rows="6"
                    placeholder="Jelaskan secara detail tentang layanan ini, manfaat, dan informasi penting lainnya..."
                    class="w-full"
                    :class="{
                      'border-red-300 focus:border-red-500 focus:ring-red-500':
                        form.errors.full_description,
                    }"
                  />
                  <p
                    v-if="form.errors.full_description"
                    class="mt-1 text-red-600"
                  >
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
          <div class="space-y-4 lg:space-y-6">
            <!-- Status Management -->
            <div
              class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
            >
              <div class="mb-4 flex items-center lg:mb-6">
                <div
                  class="flex h-10 w-10 items-center justify-center rounded-lg border border-green-200 bg-green-50 lg:h-12 lg:w-12"
                >
                  <IconToggleRight
                    class="text-green-600"
                    :size="!isDesktop ? 18 : undefined"
                  />
                </div>
                <div class="ml-3">
                  <h3 class="text-xl/6 font-semibold text-slate-900">
                    Status Layanan
                  </h3>
                  <p class="text-xs text-slate-600 lg:text-sm">
                    Kontrol visibilitas layanan
                  </p>
                </div>
              </div>

              <div>
                <div class="flex items-center justify-between">
                  <label for="is_active" class="font-medium text-slate-700">
                    Status Aktif
                  </label>
                  <ToggleSwitch id="is_active" v-model="form.is_active" />
                </div>
                <p class="text-sm text-slate-500">
                  {{
                    form.is_active
                      ? 'Layanan akan ditampilkan di website publik'
                      : 'Layanan disembunyikan dari website publik'
                  }}
                </p>
              </div>
            </div>

            <!-- Preview Card -->
            <div
              class="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:p-6"
            >
              <h3 class="mb-4 text-xl/6 font-semibold text-slate-700">
                {{ isEditing ? 'Ringkasan Perubahan' : 'Preview Layanan' }}
              </h3>
              <div class="space-y-4">
                <div class="flex items-start gap-3">
                  <div
                    class="mt-1 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg border border-blue-200 bg-blue-50"
                  >
                    <span
                      v-if="form.icon"
                      class="material-symbols-outlined !text-xl text-blue-600"
                      >{{ form.icon }}</span
                    >
                    <span
                      v-else
                      class="material-symbols-outlined !text-xl text-blue-600"
                      >volunteer_activism</span
                    >
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="truncate font-medium text-slate-900">
                      {{ form.name || 'Nama layanan' }}
                    </h4>
                    <p class="text-sm text-slate-600">
                      {{
                        form.short_description ||
                        'Deskripsi singkat akan muncul di sini'
                      }}
                    </p>
                  </div>
                </div>

                <div
                  class="flex items-center justify-between border-t border-slate-200 pt-2"
                >
                  <span class="text-sm text-slate-500">Status:</span>
                  <Tag
                    :value="form.is_active ? 'Aktif' : 'Tidak Aktif'"
                    :severity="form.is_active ? 'success' : 'secondary'"
                    size="small"
                  />
                </div>
              </div>
            </div>

            <!-- Button Submit (Mobile Only) -->
            <div v-if="isMobile">
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-800 disabled:opacity-50"
              >
                <IconLoader3
                  v-if="form.processing"
                  class="animate-spin"
                  size="16"
                />
                <IconDeviceFloppy v-else size="16" />
                {{ form.processing ? 'Menyimpan...' : submitButtonText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>
