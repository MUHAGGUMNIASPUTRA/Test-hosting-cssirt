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
  <NodeViewWrapper as="div" class="group relative inline-block !w-auto">
    <img
      ref="image"
      :src="props.node.attrs.src"
      :alt="props.node.attrs.alt"
      :style="{ width: props.node.attrs.width }"
      class="!my-0 h-auto max-w-full resize"
    />
    <!-- This is the custom resize handle -->
    <div
      class="absolute bottom-0 right-0 h-3 w-3 translate-x-1/2 translate-y-1/2 transform cursor-nwse-resize rounded-full border-2 border-white bg-blue-500 opacity-0 transition-opacity group-hover:opacity-100"
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
