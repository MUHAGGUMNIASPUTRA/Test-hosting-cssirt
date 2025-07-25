<script setup>
import { computed } from 'vue';

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
  // We pass the class as a prop to keep it flexible
  imageClass: {
    type: String,
    default: 'h-48 w-full object-cover'
  }
});

const imageUrl = computed(() => {
  // If the post has no image, return a default placeholder
  if (!props.post.image) {
    return `https://placehold.co/800x400/e2e8f0/4a5568?text=Gambar+Tidak+Tersedia`;
  }

  // If the image path is a full URL (starts with http), use it directly
  if (props.post.image.startsWith('http')) {
    return props.post.image;
  }

  // Otherwise, it's a local file, so prepend the /storage/ path
  return `/storage/${props.post.image}`;
});
</script>

<template>
  <img :class="imageClass" :src="imageUrl" :alt="post.title" />
</template>
