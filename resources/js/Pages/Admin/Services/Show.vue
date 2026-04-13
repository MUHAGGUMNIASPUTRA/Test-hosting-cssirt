<script setup>
// filepath: resources/js/Pages/Admin/Services/Show.vue

import { router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

const props = defineProps({
  service: Object,
})

const { isDesktop } = useResponsive()

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getStatusSeverity = (isActive) => {
  return isActive ? 'success' : 'secondary'
}
</script>

<template>
  <AdminLayout :title="`Detail Layanan: ${service.name}`">
    <div class="space-y-6">
      <!-- Header Section -->
      <div
        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
      >
        <div
          class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
        >
          <div>
            <h2 class="text-xl font-bold text-slate-900 lg:text-2xl">
              Detail Layanan: {{ service.name }}
            </h2>
            <div class="mt-2 flex items-center gap-3">
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
              severity="primary"
              @click="
                () => router.get(route('admin.services.edit', service.id))
              "
              class="w-full lg:w-auto"
            >
              <IconEdit size="16" />
              Edit Layanan
            </Button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
        <!-- Main Content -->
        <div class="space-y-4 lg:col-span-2 lg:space-y-6">
          <!-- Full Description -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
          >
            <div class="mb-4 flex items-center lg:mb-6">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 lg:h-12 lg:w-12"
              >
                <IconFileDescription
                  class="text-indigo-600"
                  :size="!isDesktop ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="text-xl/6 font-semibold text-slate-900">
                  Deskripsi Layanan
                </h3>
                <p class="text-xs text-slate-600 lg:text-sm">
                  Detail informasi tentang layanan ini
                </p>
              </div>
            </div>

            <div v-if="service.short_description" class="mb-4 text-slate-700">
              <h4 class="mb-2 font-medium text-slate-900">Deskripsi Singkat</h4>
              <p class="text-slate-500">{{ service.short_description }}</p>
            </div>

            <div v-if="service.full_description" class="mb-4 text-slate-700">
              <h4 class="mb-2 font-medium text-slate-900">Deskripsi Lengkap</h4>
              <p class="text-slate-500">{{ service.full_description }}</p>
            </div>
            <div v-else class="py-8 text-center">
              <div
                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 lg:h-16 lg:w-16"
              >
                <IconFileDescription
                  class="text-slate-400"
                  :size="!isDesktop ? 18 : undefined"
                />
              </div>
              <p class="text-slate-400">Deskripsi lengkap belum tersedia</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
          <!-- Service Information -->
          <div
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6"
          >
            <div class="mb-6 flex items-center">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
              >
                <IconMoodHeart
                  class="text-blue-600"
                  :size="!isDesktop ? 18 : undefined"
                />
              </div>
              <div class="ml-3">
                <h3 class="text-xl/6 font-semibold text-slate-900">
                  Informasi Layanan
                </h3>
                <p class="text-xs text-slate-600 lg:text-sm">
                  Detail teknis layanan
                </p>
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-600"
                  >Nama Layanan</label
                >
                <p class="font-medium text-slate-900">{{ service.name }}</p>
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-slate-600"
                  >Slug</label
                >
                <p
                  class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 font-mono text-sm text-slate-900"
                >
                  {{ service.slug }}
                </p>
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-slate-600"
                  >Ikon</label
                >
                <div class="flex items-center gap-2">
                  <Tag
                    :value="service.icon"
                    severity="secondary"
                    size="small"
                  />
                  <div v-if="service.icon">
                    <span class="material-symbols-outlined text-blue-600">{{
                      service.icon
                    }}</span>
                  </div>
                </div>
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-slate-600"
                  >Status</label
                >
                <Tag
                  :value="service.is_active ? 'Aktif' : 'Tidak Aktif'"
                  :severity="getStatusSeverity(service.is_active)"
                  size="small"
                />
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-slate-600"
                  >Dibuat</label
                >
                <p class="text-sm text-slate-900">
                  {{ formatDate(service.created_at) }}
                </p>
              </div>

              <div>
                <label class="mb-1 block text-sm font-medium text-slate-600"
                  >Terakhir Diperbarui</label
                >
                <p class="text-sm text-slate-900">
                  {{ formatDate(service.updated_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
