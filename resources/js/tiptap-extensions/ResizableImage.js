import Image from '@tiptap/extension-image'
import { VueNodeViewRenderer } from '@tiptap/vue-3'
import ImageResizer from '../Components/ImageResizer.vue'

export default Image.extend({
  // Extend the default attributes to include a custom width
  addAttributes() {
    return {
      ...this.parent?.(),
      width: {
        default: '100%',
        renderHTML: attributes => {
          return {
            width: attributes.width,
          }
        },
      },
    }
  },

  // Tell Tiptap to use our custom Vue component to render this image node
  addNodeView() {
    return VueNodeViewRenderer(ImageResizer)
  },
})
