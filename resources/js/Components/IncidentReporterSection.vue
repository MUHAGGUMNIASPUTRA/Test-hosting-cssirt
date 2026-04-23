<!-- Tujuan: Form section informasi pelapor insiden -->
<!-- Caller: Admin/Incidents/Create.vue -->
<!-- Side Effects: none -->
<script setup>
defineProps({
  name: { type: String, default: '' },
  email: { type: String, default: '' },
  phone: { type: String, default: '' },
  errors: { type: Object, default: () => ({}) },
})

defineEmits(['update:name', 'update:email', 'update:phone'])
</script>

<template>
  <AdminFormSection
    title="Informasi Pelapor"
    description="Data kontak pelapor insiden"
    color="blue"
  >
    <template #icon="{ iconClass }">
      <IconUserExclamation :class="iconClass" />
    </template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
      <div>
        <label
          for="reporter_name"
          class="mb-2 block font-medium text-slate-700"
        >
          Nama Pelapor <span class="text-red-500">*</span>
        </label>
        <InputText
          id="reporter_name"
          :model-value="name"
          placeholder="Masukkan nama lengkap pelapor"
          required
          class="w-full"
          :class="{ 'border-red-300': errors.reporter_name }"
          @update:model-value="$emit('update:name', $event)"
        />
        <p v-if="errors.reporter_name" class="mt-1 text-sm text-red-600">
          {{ errors.reporter_name }}
        </p>
      </div>
      <div>
        <label
          for="reporter_email"
          class="mb-2 block font-medium text-slate-700"
        >
          Email Pelapor <span class="text-red-500">*</span>
        </label>
        <InputText
          id="reporter_email"
          :model-value="email"
          type="email"
          placeholder="contoh@email.com"
          required
          class="w-full"
          :class="{ 'border-red-300': errors.reporter_email }"
          @update:model-value="$emit('update:email', $event)"
        />
        <p v-if="errors.reporter_email" class="mt-1 text-sm text-red-600">
          {{ errors.reporter_email }}
        </p>
      </div>
      <div class="md:col-span-2">
        <label
          for="reporter_phone"
          class="mb-2 block font-medium text-slate-700"
        >
          Nomor Telepon
        </label>
        <InputText
          id="reporter_phone"
          :model-value="phone"
          type="tel"
          placeholder="Contoh: 081234567890"
          class="w-full"
          @update:model-value="$emit('update:phone', $event)"
        />
      </div>
    </div>
  </AdminFormSection>
</template>
