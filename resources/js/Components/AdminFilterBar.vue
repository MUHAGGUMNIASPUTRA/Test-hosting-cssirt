<script setup>
/**
 * AdminFilterBar — Standard filter/search panel for admin Index pages.
 *
 * Props:
 *   hasActiveFilters — (boolean) Whether any filter is currently active.
 *                      When true, shows a "Reset Filter" button.
 *   title            — (optional) Section heading. Defaults to "Filter & Pencarian".
 *
 * Events:
 *   @clear — Emitted when the user clicks "Reset Filter".
 *
 * Slots:
 *   #default — Filter inputs (e.g. InputText, Select dropdowns).
 *
 * Usage:
 *   <AdminFilterBar :has-active-filters="hasActiveFilters" @clear="clearFilters">
 *     <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
 *       <InputText v-model="searchQuery" placeholder="Cari..." @keyup.enter="applyFilters" />
 *     </div>
 *   </AdminFilterBar>
 */
defineProps({
  hasActiveFilters: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Filter & Pencarian',
  },
})

defineEmits(['clear'])
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-xl font-semibold text-slate-900">{{ title }}</h3>
      <button
        v-if="hasActiveFilters"
        class="text-blue-600 hover:text-blue-800 font-medium text-sm"
        @click="$emit('clear')"
      >
        Reset Filter
      </button>
    </div>
    <slot />
  </div>
</template>
