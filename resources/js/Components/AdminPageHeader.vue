<script setup>
/**
 * AdminPageHeader — Standard page header for admin CRUD pages.
 *
 * Props:
 *   title       — (required) Page heading text
 *   description — (optional) Sub-heading / description text
 *
 * Slots:
 *   #action — Content rendered on the right side (e.g. a Button to open a create dialog)
 *
 * Usage:
 *   <AdminPageHeader title="Kelola Pengguna" description="Kelola akun pengguna sistem.">
 *     <template #action>
 *       <Button @click="openDialog">Tambah Pengguna</Button>
 *     </template>
 *   </AdminPageHeader>
 */
defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: null,
  },
})
</script>

<template>
  <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
    <div>
      <h2 class="text-xl lg:text-2xl font-bold text-slate-900">{{ title }}</h2>
      <p v-if="description" class="text-slate-600">{{ description }}</p>
    </div>
    <div v-if="$slots.action" class="w-full sm:w-auto">
      <slot name="action" />
    </div>
  </div>
</template>
