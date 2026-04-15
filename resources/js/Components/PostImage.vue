<script setup>
import { computed } from 'vue'

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
  // We pass the class as a prop to keep it flexible
  imageClass: {
    type: String,
    default: 'h-48 w-full object-cover',
  },
})

const imageUrl = computed(() => {
  if (!props.post.image?.url) {
    return `https://placehold.co/800x400/e2e8f0/4a5568?text=Gambar+Tidak+Tersedia`
  }
  return props.post.image.url
})
</script>

<template>
  <img :class="imageClass" :src="imageUrl" :alt="post.title" />
</template>
