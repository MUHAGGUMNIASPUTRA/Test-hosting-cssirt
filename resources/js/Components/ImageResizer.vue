<script setup>
import { NodeViewWrapper, nodeViewProps } from '@tiptap/vue-3'
import { ref } from 'vue'

const props = defineProps(nodeViewProps)

const image = ref(null)

// This function handles the resizing logic
const handleMousedown = (e) => {
  // Prevent default browser behavior
  e.preventDefault()

  const initialWidth = image.value.offsetWidth
  const startX = e.clientX

  const handleMousemove = (moveEvent) => {
    const dx = moveEvent.clientX - startX
    const newWidth = initialWidth + dx
    // Update the style directly for real-time feedback
    image.value.style.width = `${newWidth}px`
  }

  const handleMouseup = () => {
    // Clean up event listeners
    window.removeEventListener('mousemove', handleMousemove)
    window.removeEventListener('mouseup', handleMouseup)

    // Persist the new width to the Tiptap document
    props.updateAttributes({
      width: image.value.style.width,
    })
  }

  // Add listeners to the window to capture mouse movement anywhere on the page
  window.addEventListener('mousemove', handleMousemove)
  window.addEventListener('mouseup', handleMouseup)
}
</script>

<template>
  <NodeViewWrapper as="div" class="relative inline-block group !w-auto">
    <img
      ref="image"
      :src="props.node.attrs.src"
      :alt="props.node.attrs.alt"
      :style="{ width: props.node.attrs.width }"
      class="resize max-w-full h-auto !my-0"
    />
    <!-- This is the custom resize handle -->
    <div
      class="absolute bottom-0 right-0 w-3 h-3 bg-blue-500 border-2 border-white rounded-full cursor-nwse-resize opacity-0 group-hover:opacity-100 transition-opacity transform translate-x-1/2 translate-y-1/2"
      @mousedown.prevent="handleMousedown"
    ></div>
  </NodeViewWrapper>
</template>

<style scoped>
/* Add a visual indicator for resizable images */
.resize {
  cursor: nwse-resize;
  border: 2px dashed transparent;
}
.resize:hover {
  border-color: #6366f1; /* Corresponds to indigo-500 */
}
</style>
