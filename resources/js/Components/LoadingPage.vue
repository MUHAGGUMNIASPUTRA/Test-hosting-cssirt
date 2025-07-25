<!-- @format -->

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const loading = ref(false)

const skipLoading = (visit) => {
  return visit.headers && visit.headers['X-Skip-Loading']
}

onMounted(() => {
  router.on('start', (event) => {
    const visit = event.detail.visit

    if (!skipLoading(visit)) {
      loading.value = true
    }
  })

  router.on('finish', (event) => {
    if (!event.detail.visit.completed) {
      loading.value = false
    }
    loading.value = false
  })
})
</script>

<template>
  <div>
    <div v-if="loading" class="loading-overlay">
      <div class="loader-container">
        <div class="squares-loader">
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
          <div class="square"></div>
        </div>
      </div>
    </div>
    <slot />
  </div>
</template>

<style>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(5px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.loader-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
}

.loading-text {
  font-size: 1rem;
  font-weight: 500;
  color: var(--p-info-700, #1d4ed8);
  letter-spacing: 0.5px;
}

.squares-loader {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 6px;
  width: 60px;
  height: 60px;
}

.square {
  width: 16px;
  height: 16px;
  background: var(--p-info-500, #3b82f6);
  border-radius: 2px;
  animation: squares-bounce 1.2s ease-in-out infinite;
}

.square:nth-child(1) {
  animation-delay: 0s;
}
.square:nth-child(2) {
  animation-delay: 0.1s;
}
.square:nth-child(3) {
  animation-delay: 0.2s;
}
.square:nth-child(4) {
  animation-delay: 0.3s;
}
.square:nth-child(5) {
  animation-delay: 0.4s;
}
.square:nth-child(6) {
  animation-delay: 0.5s;
}
.square:nth-child(7) {
  animation-delay: 0.6s;
}
.square:nth-child(8) {
  animation-delay: 0.7s;
}
.square:nth-child(9) {
  animation-delay: 0.8s;
}

@keyframes squares-bounce {
  0%,
  100% {
    transform: scale(0.6) translateY(0px);
    opacity: 0.4;
  }
  50% {
    transform: scale(1) translateY(-8px);
    opacity: 1;
  }
}
</style>
