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
    class="group relative transform rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-2 hover:border-indigo-300 hover:shadow-xl"
    :style="animationDelay ? { animationDelay: `${animationDelay}ms` } : {}"
  >
    <!-- Ikon + Nama -->
    <div class="mb-4 flex items-center gap-4">
      <div class="relative">
        <div
          class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg transition-shadow duration-300 group-hover:shadow-xl"
        >
          <span class="material-symbols-outlined !text-2xl text-white">{{
            service.icon
          }}</span>
        </div>
        <div
          class="absolute -inset-2 -z-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-blue-600/20 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
        ></div>
      </div>
      <h4
        class="text-2xl font-semibold text-slate-900 transition-colors duration-300 group-hover:text-indigo-600"
      >
        {{ service.name }}
      </h4>
    </div>

    <!-- Deskripsi -->
    <p class="leading-relaxed text-slate-600">
      {{ service.short_description }}
    </p>

    <!-- Hover overlay -->
    <div
      class="pointer-events-none absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-500/5 to-blue-600/5 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
    ></div>
  </div>
</template>
