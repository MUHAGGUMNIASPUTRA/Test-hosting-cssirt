<!-- Tujuan: Form section utama dokumen (title, description, version, published_at, is_public) -->
<!-- Caller: Documents/Create.vue -->
<!-- Side Effects: emit update:modelValue -->

<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['update:modelValue'])

const updateField = (field, value) => {
  emit('update:modelValue', {
    ...props.modelValue,
    [field]: value,
  })
}
</script>

<template>
  <!-- Informasi Dokumen -->
  <AdminFormSection
    title="Informasi Dokumen"
    description="Judul dan deskripsi dokumen"
    color="blue"
  >
    <template #icon="{ iconClass }">
      <IconFileDescription :class="iconClass" />
    </template>
    <div class="space-y-4">
      <!-- Title -->
      <div>
        <label class="mb-2 block font-medium text-gray-700">
          Judul Dokumen <span class="text-red-500">*</span>
        </label>
        <InputText
          :model-value="modelValue.title"
          class="w-full"
          :class="{ 'p-invalid': errors.title }"
          placeholder="Masukkan judul dokumen..."
          required
          @update:model-value="updateField('title', $event)"
        />
        <small v-if="errors.title" class="p-error mt-1 block">
          {{ errors.title }}
        </small>
      </div>

      <!-- Description -->
      <div>
        <label class="mb-2 block font-medium text-gray-700">
          Deskripsi
          <span class="font-normal text-slate-400">(Opsional)</span>
        </label>
        <Textarea
          :model-value="modelValue.description"
          rows="3"
          class="w-full"
          placeholder="Jelaskan isi atau tujuan dokumen ini..."
          @update:model-value="updateField('description', $event)"
        />
        <small v-if="errors.description" class="p-error mt-1 block">
          {{ errors.description }}
        </small>
      </div>
    </div>
  </AdminFormSection>

  <!-- Info Publikasi -->
  <AdminFormSection
    title="Info Publikasi"
    description="Versi dan tanggal terbit"
    color="purple"
  >
    <template #icon="{ iconClass }">
      <IconCalendar :class="iconClass" />
    </template>
    <div class="space-y-4">
      <!-- Version -->
      <div>
        <label class="mb-2 block font-medium text-gray-700">
          Versi
          <span class="font-normal text-slate-400">(Opsional)</span>
        </label>
        <InputText
          :model-value="modelValue.version"
          class="w-full"
          placeholder="Contoh: 1.0, 2.1, v3..."
          @update:model-value="updateField('version', $event)"
        />
        <small v-if="errors.version" class="p-error mt-1 block">
          {{ errors.version }}
        </small>
      </div>

      <!-- Published At -->
      <div>
        <label class="mb-2 block font-medium text-gray-700">
          Tanggal Terbit
          <span class="font-normal text-slate-400">(Opsional)</span>
        </label>
        <DatePicker
          :model-value="modelValue.published_at"
          dateFormat="dd/mm/yy"
          placeholder="Pilih tanggal"
          class="w-full"
          showButtonBar
          @update:model-value="updateField('published_at', $event)"
        />
        <small v-if="errors.published_at" class="p-error mt-1 block">
          {{ errors.published_at }}
        </small>
      </div>
    </div>
  </AdminFormSection>

  <!-- Visibilitas -->
  <AdminFormSection
    title="Visibilitas"
    description="Tampilkan ke publik"
    color="teal"
  >
    <template #icon="{ iconClass }">
      <IconEye :class="iconClass" />
    </template>
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-slate-700">Publik</p>
        <p class="mt-0.5 text-xs text-slate-500">
          {{
            modelValue.is_public
              ? 'Ditampilkan di halaman publik'
              : 'Hanya terlihat admin'
          }}
        </p>
      </div>
      <ToggleSwitch
        :model-value="modelValue.is_public"
        @update:model-value="updateField('is_public', $event)"
      />

      <small v-if="errors.is_public" class="p-error mt-1 block">
        {{ errors.is_public }}
      </small>
    </div>
  </AdminFormSection>
</template>
