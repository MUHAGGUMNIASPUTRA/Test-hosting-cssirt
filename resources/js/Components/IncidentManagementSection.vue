<!-- Tujuan: Sidebar manajemen insiden — status, prioritas, penugasan -->
<!-- Caller: Admin/Incidents/Create.vue -->
<!-- Side Effects: none -->
<script setup>
defineProps({
  status: { type: String, default: 'Baru' },
  priority: { type: String, default: 'Sedang' },
  assignedTo: { default: null },
  statusOptions: { type: Array, default: () => [] },
  priorityOptions: { type: Array, default: () => [] },
  staffUserOptions: { type: Array, default: () => [] },
})

defineEmits(['update:status', 'update:priority', 'update:assignedTo'])
</script>

<template>
  <AdminFormSection
    title="Manajemen"
    description="Status dan penugasan"
    color="green"
  >
    <template #icon="{ iconClass }">
      <IconClipboardList :class="iconClass" />
    </template>
    <div class="space-y-4 lg:space-y-6">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
        <div>
          <label class="mb-2 block font-medium text-slate-700">
            Status <span class="text-red-500">*</span>
          </label>
          <Select
            :model-value="status"
            :options="statusOptions"
            option-label="label"
            option-value="value"
            class="w-full"
            @update:model-value="$emit('update:status', $event)"
          />
        </div>
        <div>
          <label class="mb-2 block font-medium text-slate-700">
            Prioritas <span class="text-red-500">*</span>
          </label>
          <Select
            :model-value="priority"
            :options="priorityOptions"
            option-label="label"
            option-value="value"
            class="w-full"
            @update:model-value="$emit('update:priority', $event)"
          />
        </div>
      </div>
      <div>
        <label class="mb-2 block font-medium text-slate-700"
          >Tugaskan Kepada</label
        >
        <Select
          :model-value="assignedTo"
          :options="staffUserOptions"
          option-label="label"
          option-value="value"
          placeholder="Pilih staf"
          class="w-full"
          @update:model-value="$emit('update:assignedTo', $event)"
        />
      </div>
    </div>
  </AdminFormSection>
</template>
