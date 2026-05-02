<script setup>
import { useForm } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'
import { ref } from 'vue'

const props = defineProps({
  incident: Object,
  staffUsers: Array,
})

const managementForm = useForm({
  status: props.incident.status,
  priority: props.incident.priority,
  assigned_to: props.incident.assigned_to,
})

const statusOptions = [
  { label: 'Baru', value: 'Baru' },
  { label: 'Diverifikasi', value: 'Diverifikasi' },
  { label: 'Dalam Penyelidikan', value: 'Dalam Penyelidikan' },
  { label: 'Selesai', value: 'Selesai' },
  { label: 'Ditutup', value: 'Ditutup' },
]

const priorityOptions = [
  { label: 'Rendah', value: 'Rendah' },
  { label: 'Sedang', value: 'Sedang' },
  { label: 'Tinggi', value: 'Tinggi' },
  { label: 'Kritikal', value: 'Kritikal' },
]

const staffUserOptions = [
  { label: 'Tidak ditugaskan', value: null },
  ...props.staffUsers.map((u) => ({ label: u.name, value: u.id })),
]

const getPriorityButtonClasses = (priority, isSelected) => {
  const base =
    'p-2 font-medium border rounded-lg transition-all duration-200 text-center text-sm'
  const selected = {
    Rendah:
      'border-green-500 bg-green-50 text-green-700 ring-2 ring-green-500 ring-opacity-20',
    Sedang:
      'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500 ring-opacity-20',
    Tinggi:
      'border-orange-500 bg-orange-50 text-orange-700 ring-2 ring-orange-500 ring-opacity-20',
    Kritikal:
      'border-red-500 bg-red-50 text-red-700 ring-2 ring-red-500 ring-opacity-20',
  }
  const unselected = {
    Rendah:
      'border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300',
    Sedang:
      'border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300',
    Tinggi:
      'border-orange-200 text-orange-600 hover:bg-orange-50 hover:border-orange-300',
    Kritikal:
      'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300',
  }
  return `${base} ${isSelected ? selected[priority] : unselected[priority]}`
}

const { isDesktop } = useResponsive()

const submitManagement = () => {
  managementForm.put(
    route('admin.incidents.management.update', props.incident.id),
    {
      preserveScroll: true,
    },
  )
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:p-6">
    <div class="mb-4 flex items-center lg:mb-6">
      <div
        class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 lg:h-12 lg:w-12"
      >
        <IconSettings
          class="text-orange-600"
          :size="!isDesktop ? 18 : undefined"
        />
      </div>
      <div class="ml-3">
        <h3 class="text-xl/6 font-semibold text-slate-900">Kelola Insiden</h3>
        <p class="text-xs text-slate-600 lg:text-sm">
          Perbarui status dan penugasan
        </p>
      </div>
    </div>

    <form @submit.prevent="submitManagement" class="space-y-4">
      <div>
        <label class="mb-2 block font-medium text-slate-700">Status</label>
        <Select
          v-model="managementForm.status"
          :options="statusOptions"
          optionLabel="label"
          optionValue="value"
          class="w-full"
        />
      </div>

      <div>
        <label class="mb-2 block font-medium text-slate-700">Prioritas</label>
        <div class="grid grid-cols-2 gap-2">
          <button
            v-for="p in priorityOptions"
            :key="p.value"
            type="button"
            :class="
              getPriorityButtonClasses(
                p.value,
                managementForm.priority === p.value,
              )
            "
            @click="managementForm.priority = p.value"
          >
            {{ p.label }}
          </button>
        </div>
      </div>

      <div>
        <label class="mb-2 block font-medium text-slate-700"
          >Ditugaskan ke</label
        >
        <Select
          v-model="managementForm.assigned_to"
          :options="staffUserOptions"
          optionLabel="label"
          optionValue="value"
          class="w-full"
        />
      </div>

      <Button
        type="submit"
        severity="primary"
        :disabled="managementForm.processing"
        class="w-full"
      >
        <IconLoader3
          v-if="managementForm.processing"
          class="animate-spin"
          size="16"
        />
        <IconDeviceFloppy v-else size="16" />
        {{ managementForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
      </Button>
    </form>
  </div>
</template>
