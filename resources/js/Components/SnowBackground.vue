<template>
  <Particles id="tsparticles" :options="options" class="pointer-events-none"/>
</template>

<script setup>
import { ref } from 'vue'

const options = ref({
  preset: "snow", // preset bawaan
  background: { color: "transparent" },
  fullScreen: { enable: true, zIndex: 0 },
  interactivity: {
    events: {
      onHover: { enable: true, mode: ["repulse", "attract"] },
      resize: true
    },
    modes: {
      repulse: { distance: 150, duration: 0.4 },
      attract: { distance: 200, duration: 0.2 }
    }
  }
})
</script>

<style scoped>
:deep(#tsparticles canvas) {
  position: fixed;
  inset: 0;
}
</style>
