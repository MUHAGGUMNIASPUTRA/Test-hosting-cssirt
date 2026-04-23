<!-- Tujuan: Action menu (detail, edit, hapus) untuk baris tabel insiden -->
<!-- Caller: Admin/Incidents/Index.vue -->
<!-- Side Effects: router navigation via Inertia -->
<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  item: { type: Object, required: true },
})

const emit = defineEmits(['delete'])

const actionMenu = ref()

const toggle = (event) => actionMenu.value.toggle(event)

const menuItems = computed(() => [
  {
    label: 'Detail',
    icon: 'pi pi-eye',
    command: () => router.get(route('admin.incidents.show', props.item.id)),
  },
  {
    label: 'Edit',
    icon: 'pi pi-pen-to-square',
    command: () => router.get(route('admin.incidents.edit', props.item.id)),
    visible: props.item.status !== 'Ditutup',
  },
  {
    label: 'Hapus',
    icon: 'pi pi-trash',
    command: () => emit('delete', props.item),
  },
])
</script>

<template>
  <div class="flex items-center justify-end">
    <Button variant="text" class="!p-0" @click="toggle">
      <template #default>
        <div class="flex items-center text-slate-400 hover:text-blue-600">
          <IconChevronDown size="22" stroke-width="1.5" />
        </div>
      </template>
    </Button>
    <Menu
      ref="actionMenu"
      :model="menuItems"
      :popup="true"
      class="!min-w-28"
      :pt="{
        itemIcon: { class: '!text-sm mr-1' },
        itemLabel: { class: 'text-sm' },
      }"
    />
  </div>
</template>
