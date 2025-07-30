<script setup>
// filepath: resources/js/Pages/Admin/Services/Show.vue

import { Link } from '@inertiajs/vue3'

const props = defineProps({
  service: Object
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
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
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Detail Layanan: {{ service.name }}</h2>
            <div class="flex items-center gap-3 mt-2">
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
            <Link
              :href="route('admin.services.edit', service.id)"
              class="bg-blue-600 hover:bg-blue-800 text-white w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 rounded-md transition"
            >
              <span class="material-symbols-outlined !text-xl">edit</span>
              Edit Layanan
            </Link>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Full Description -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-indigo-50 border border-indigo-200 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600">description</span>
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Deskripsi Layanan</h3>
                <p class="text-slate-600">Detail informasi tentang layanan ini</p>
              </div>
            </div>

            <div v-if="service.short_description" class="text-slate-700 mb-4">
              <h4 class="font-medium text-slate-900 mb-2">Deskripsi Singkat</h4>
              <p class="text-slate-500">{{ service.short_description }}</p>
            </div>

            <div v-if="service.full_description" class="text-slate-700 mb-4">
              <h4 class="font-medium text-slate-900 mb-2">Deskripsi Lengkap</h4>
              <p class="text-slate-500">{{ service.full_description }}</p>
            </div>
            <div v-else class="text-center py-8">
              <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-slate-400 !text-2xl">description</span>
              </div>
              <p class="text-slate-500">Deskripsi lengkap belum tersedia</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Service Information -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600">info</span>
              </div>
              <div class="ml-3">
                <h3 class="font-semibold text-slate-900">Informasi Layanan</h3>
                <p class="text-slate-600">Detail teknis layanan</p>
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Nama Layanan</label>
                <p class="text-slate-900 font-medium">{{ service.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Slug</label>
                <p class="text-slate-900 font-mono text-sm bg-slate-50 border border-slate-200 px-2 py-1 rounded-lg">{{ service.slug }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Ikon</label>
                <div class="flex items-center gap-2">
                  <Tag
                    :value="service.icon"
                    severity="secondary"
                    size="small"
                  />
                  <div v-if="service.icon">
                    <span class="material-symbols-outlined text-blue-600">{{ service.icon }}</span>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
                <Tag
                  :value="service.is_active ? 'Aktif' : 'Tidak Aktif'"
                  :severity="getStatusSeverity(service.is_active)"
                  size="small"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Dibuat</label>
                <p class="text-slate-900 text-sm">{{ formatDate(service.created_at) }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Terakhir Diperbarui</label>
                <p class="text-slate-900 text-sm">{{ formatDate(service.updated_at) }}</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </AdminLayout>
</template>
