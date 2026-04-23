<!-- Tujuan: Form section detail insiden — jenis, waktu, deskripsi, dan panduan pelaporan -->
<!-- Caller: Admin/Incidents/Create.vue -->
<!-- Side Effects: none -->
<script setup>
import { computed } from 'vue'

const props = defineProps({
  incidentTypeId: { default: null },
  incidentAt: { default: null },
  description: { type: String, default: '' },
  incidentTypes: { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) },
})

defineEmits([
  'update:incidentTypeId',
  'update:incidentAt',
  'update:description',
])

const incidentTypeOptions = computed(() =>
  props.incidentTypes.map((t) => ({ label: t.name, value: t.id })),
)
const selectedType = computed(
  () => props.incidentTypes.find((t) => t.id === props.incidentTypeId) || null,
)
</script>

<template>
  <AdminFormSection
    title="Detail Insiden"
    description="Informasi lengkap tentang insiden yang terjadi"
    color="rose"
  >
    <template #icon="{ iconClass }">
      <IconUrgent :class="iconClass" />
    </template>
    <div class="space-y-4 lg:space-y-6">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
        <div>
          <label class="mb-2 block font-medium text-slate-700">
            Jenis Insiden <span class="text-red-500">*</span>
          </label>
          <Select
            :model-value="incidentTypeId"
            :options="incidentTypeOptions"
            option-label="label"
            option-value="value"
            placeholder="Pilih kategori insiden"
            class="w-full"
            :class="{ 'border-red-300': errors.incident_type_id }"
            @update:model-value="$emit('update:incidentTypeId', $event)"
          />
          <p v-if="errors.incident_type_id" class="mt-1 text-sm text-red-600">
            {{ errors.incident_type_id }}
          </p>
        </div>
        <div>
          <label class="mb-2 block font-medium text-slate-700">
            Waktu Kejadian <span class="text-red-500">*</span>
          </label>
          <DatePicker
            :model-value="incidentAt"
            show-time
            hour-format="24"
            date-format="dd/mm/yy"
            placeholder="Pilih tanggal dan waktu"
            class="w-full"
            :class="{ 'border-red-300': errors.incident_at }"
            @update:model-value="$emit('update:incidentAt', $event)"
          />
          <p v-if="errors.incident_at" class="mt-1 text-sm text-red-600">
            {{ errors.incident_at }}
          </p>
        </div>
      </div>

      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div
          v-if="
            selectedType && (selectedType.description || selectedType.guide)
          "
          class="overflow-hidden rounded-lg border border-blue-200 bg-blue-50"
        >
          <div
            class="flex items-center gap-2 border-b border-blue-200 bg-blue-100 px-4 py-2.5"
          >
            <IconInfoCircle size="16" class="flex-shrink-0 text-blue-600" />
            <div>
              <span class="text-sm font-semibold text-blue-900">{{
                selectedType.name
              }}</span>
              <span
                v-if="selectedType.description"
                class="ml-2 text-sm text-blue-700"
              >
                — {{ selectedType.description }}
              </span>
            </div>
          </div>
          <div v-if="selectedType.guide" class="p-4">
            <p
              class="mb-2 text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
              Panduan Pelaporan
            </p>
            <div
              class="prose prose-sm max-w-none text-slate-700 [&>h3]:mb-1.5 [&>h3]:text-sm [&>h3]:font-semibold [&>h3]:text-blue-900 [&>li]:mb-0.5 [&>ol]:mb-1.5 [&>ol]:pl-4 [&>p]:mb-1.5 [&>ul]:mb-1.5 [&>ul]:pl-4"
              v-html="selectedType.guide"
            />
          </div>
        </div>
      </Transition>

      <div>
        <label class="mb-2 block font-medium text-slate-700">
          Deskripsi Insiden <span class="text-red-500">*</span>
        </label>
        <Textarea
          :model-value="description"
          rows="5"
          placeholder="Jelaskan detail insiden yang terjadi, termasuk dampak dan langkah yang sudah diambil..."
          required
          class="w-full"
          :class="{ 'border-red-300': errors.description }"
          @update:model-value="$emit('update:description', $event)"
        />
        <p v-if="errors.description" class="mt-1 text-sm text-red-600">
          {{ errors.description }}
        </p>
      </div>

      <slot name="attachment" />
    </div>
  </AdminFormSection>
</template>
