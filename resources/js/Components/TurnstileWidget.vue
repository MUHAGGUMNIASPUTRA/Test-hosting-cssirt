<!-- Tujuan: Render Cloudflare Turnstile widget dan expose token via v-model. -->
<!-- Caller: Login.vue, IncidentConfirmStep.vue, IncidentSearchForm.vue. -->
<!-- Side Effects: Loads Cloudflare script, renders iframe, calls window.turnstile API. -->

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { useTurnstile } from '@/Composables/useTurnstile'

const props = defineProps({
  siteKey: {
    type: String,
    required: true,
  },
  modelValue: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const { loadTurnstileScript } = useTurnstile()
const containerRef = ref(null)
let widgetId = null

const reset = () => {
  if (widgetId !== null && window.turnstile) {
    window.turnstile.reset(widgetId)
  }
}

onMounted(async () => {
  await loadTurnstileScript()

  if (!window.turnstile || !containerRef.value) return

  widgetId = window.turnstile.render(containerRef.value, {
    sitekey: props.siteKey,
    theme: 'light',
    callback: (token) => {
      emit('update:modelValue', token)
    },
    'error-callback': () => {
      emit('update:modelValue', '')
    },
    'expired-callback': () => {
      emit('update:modelValue', '')
    },
  })
})

onBeforeUnmount(() => {
  if (widgetId !== null && window.turnstile) {
    window.turnstile.remove(widgetId)
  }
})

defineExpose({ reset })
</script>

<template>
  <div ref="containerRef" class="flex justify-center" />
</template>
