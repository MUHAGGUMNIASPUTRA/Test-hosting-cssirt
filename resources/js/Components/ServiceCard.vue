<script setup>
/**
 * ServiceCard — kartu layanan untuk halaman Landing dan Services/Index.
 *
 * Props:
 *   service        — object service (icon, name, short_description)
 *   animationDelay — delay animasi CSS dalam ms (opsional)
 */
defineProps({
  service: { type: Object, required: true },
  animationDelay: { type: Number, default: 0 },
})
</script>

<template>
  <div
    class="group relative bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-slate-200 hover:border-indigo-300"
    :style="animationDelay ? { animationDelay: `${animationDelay}ms` } : {}"
  >
    <!-- Ikon + Nama -->
    <div class="flex items-center gap-4 mb-4">
      <div class="relative">
        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
          <span class="material-symbols-outlined text-white !text-2xl">{{ service.icon }}</span>
        </div>
        <div class="absolute -inset-2 bg-gradient-to-br from-indigo-500/20 to-blue-600/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
      </div>
      <h4 class="text-2xl font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors duration-300">
        {{ service.name }}
      </h4>
    </div>

    <!-- Deskripsi -->
    <p class="text-slate-600 leading-relaxed">{{ service.short_description }}</p>

    <!-- Hover overlay -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-500/5 to-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
  </div>
</template>
