<script setup>
import { computed } from 'vue'

/**
 * StatusBadge — wrapper tipis di atas PrimeVue <Tag>
 * Memusatkan mapping severity dan label untuk dipakai di seluruh halaman admin.
 *
 * Props:
 *   type  — 'incident-status' | 'priority' | 'post-status' | 'published'
 *   value — nilai yang akan ditampilkan (string atau boolean untuk type='published')
 *   size  — ukuran PrimeVue Tag (default: 'small')
 */
const props = defineProps({
  type: { type: String, default: 'incident-status' },
  value: { type: [String, Boolean], required: true },
  size: { type: String, default: 'small' },
})

const severityMap = {
  'incident-status': {
    'Baru':               'info',
    'Diverifikasi':       'primary',
    'Dalam Penyelidikan': 'warn',
    'Selesai':            'success',
    'Ditutup':            'secondary',
  },
  'priority': {
    'Rendah':  'success',
    'Sedang':  'info',
    'Tinggi':  'warn',
    'Kritikal': 'danger',
  },
  'post-status': {
    'Published': 'success',
    'Draft':     'warn',
  },
  'published': {
    true:  'success',
    false: 'secondary',
  },
}

const labelMap = {
  'post-status': {
    'Published': 'Diterbitkan',
    'Draft':     'Draft',
  },
  'published': {
    true:  'Diterbitkan',
    false: 'Draft',
  },
}

const severity = computed(() => {
  const map = severityMap[props.type]
  return map?.[props.value] ?? 'info'
})

const label = computed(() => {
  const map = labelMap[props.type]
  return map ? (map[props.value] ?? String(props.value)) : String(props.value)
})
</script>

<template>
  <Tag :value="label" :severity="severity" :size="size" />
</template>
