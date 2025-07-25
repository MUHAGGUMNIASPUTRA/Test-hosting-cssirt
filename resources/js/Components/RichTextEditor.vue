<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { ref, watch } from 'vue';

// Import extensions
import TextAlign from '@tiptap/extension-text-align';
import Highlight from '@tiptap/extension-highlight';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';

// Import PrimeVue components
import Button from 'primevue/button';
import TieredMenu from 'primevue/tieredmenu';

// Import Lucide icons
// (Auto-imported by unplugin-icons)

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit.configure({ link: { openOnClick: false } }),
    TextAlign.configure({ types: ['heading', 'paragraph'] }),
    Highlight,
    TextStyle,
    Color,
  ],
  onUpdate: () => {
    emit('update:modelValue', editor.value.getHTML());
  },
  editorProps: {
    attributes: {
      class: 'prose max-w-none focus:outline-none p-4 rounded-b-md min-h-[300px]',
    },
  },
});

watch(() => props.modelValue, (newValue) => {
  const isSame = editor.value.getHTML() === newValue;
  if (isSame) {
    return;
  }
  editor.value.commands.setContent(newValue, false);
});

// --- Dropdown Menu Logic ---
const headingMenu = ref();
const listMenu = ref();

const headingItems = ref([
  { label: 'Heading 1', icon: 'i-lucide-heading-1', command: () => editor.value.chain().focus().toggleHeading({ level: 1 }).run() },
  { label: 'Heading 2', icon: 'i-lucide-heading-2', command: () => editor.value.chain().focus().toggleHeading({ level: 2 }).run() },
  { label: 'Heading 3', icon: 'i-lucide-heading-3', command: () => editor.value.chain().focus().toggleHeading({ level: 3 }).run() },
]);

const listItems = ref([
  { label: 'Bullet List', icon: 'i-lucide-list', command: () => editor.value.chain().focus().toggleBulletList().run() },
  { label: 'Numbered List', icon: 'i-lucide-list-ordered', command: () => editor.value.chain().focus().toggleOrderedList().run() },
]);

const toggleHeadingMenu = (event) => headingMenu.value.toggle(event);
const toggleListMenu = (event) => listMenu.value.toggle(event);

const setLink = () => {
  const previousUrl = editor.value.getAttributes('link').href;
  const url = window.prompt('URL', previousUrl);
  if (url === null) return;
  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
    return;
  }
  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};
</script>

<template>
  <div v-if="editor" class="border border-gray-300 rounded-md">
    <!-- Toolbar -->
    <div class="p-2 bg-gray-100 border-b border-gray-300 rounded-t-md flex flex-wrap items-center">
      <!-- Undo/Redo -->
      <Button @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" :severity="editor.can().undo() ? 'primary' : 'contrast'" text rounded-xl aria-label="Undo" class="h-8 w-8 !p-2"> <i-lucide-undo-2 /> </Button>
      <Button @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" :severity="editor.can().redo() ? 'primary' : 'contrast'" text rounded-xl aria-label="Redo" class="h-8 w-8 !p-2"> <i-lucide-redo-2 /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Basic Formatting -->
      <Button @click="editor.chain().focus().toggleBold().run()" :severity="editor.isActive('bold') ? 'primary' : 'contrast'" text rounded-xl aria-label="Bold" class="h-8 w-8 !p-2"> <i-lucide-bold /> </Button>
      <Button @click="editor.chain().focus().toggleItalic().run()" :severity="editor.isActive('italic') ? 'primary' : 'contrast'" text rounded-xl aria-label="Italic" class="h-8 w-8 !p-2"> <i-lucide-italic /> </Button>
      <Button @click="editor.chain().focus().toggleUnderline().run()" :severity="editor.isActive('underline') ? 'primary' : 'contrast'" text rounded-xl aria-label="Underline" class="h-8 w-8 !p-2"> <i-lucide-underline /> </Button>
      <Button @click="editor.chain().focus().toggleStrike().run()" :severity="editor.isActive('strike') ? 'primary' : 'contrast'" text rounded-xl aria-label="Strike" class="h-8 w-8 !p-2"> <i-lucide-strikethrough /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Headings Dropdown -->
      <Button @click="toggleHeadingMenu" :severity="editor.isActive('heading') ? 'primary' : 'contrast'" text rounded-xl aria-haspopup="true" aria-controls="heading_menu" aria-label="Headings" class="h-8 w-8 !p-2"> <i-lucide-heading /> </Button>
      <TieredMenu ref="headingMenu" id="heading_menu" :model="headingItems" popup />

      <!-- Lists Dropdown -->
      <Button @click="toggleListMenu" :severity="editor.isActive('bulletList') || editor.isActive('orderedList') ? 'primary' : 'contrast'" text rounded-xl aria-haspopup="true" aria-controls="list_menu" aria-label="Lists" class="h-8 w-8 !p-2"> <i-lucide-list /> </Button>
      <TieredMenu ref="listMenu" id="list_menu" :model="listItems" popup />

      <div class="border-l h-6 mx-2"></div>

      <!-- Alignment -->
      <Button @click="editor.chain().focus().setTextAlign('left').run()" :severity="editor.isActive({ textAlign: 'left' }) ? 'primary' : 'contrast'" text rounded-xl aria-label="Align Left" class="h-8 w-8 !p-2"> <i-lucide-align-left /> </Button>
      <Button @click="editor.chain().focus().setTextAlign('center').run()" :severity="editor.isActive({ textAlign: 'center' }) ? 'primary' : 'contrast'" text rounded-xl aria-label="Align Center" class="h-8 w-8 !p-2"> <i-lucide-align-center /> </Button>
      <Button @click="editor.chain().focus().setTextAlign('right').run()" :severity="editor.isActive({ textAlign: 'right' }) ? 'primary' : 'contrast'" text rounded-xl aria-label="Align Right" class="h-8 w-8 !p-2"> <i-lucide-align-right /> </Button>
      <Button @click="editor.chain().focus().setTextAlign('justify').run()" :severity="editor.isActive({ textAlign: 'justify' }) ? 'primary' : 'contrast'" text rounded-xl aria-label="Align Justify" class="h-8 w-8 !p-2"> <i-lucide-align-justify /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Advanced Formatting -->
      <Button @click="setLink" :severity="editor.isActive('link') ? 'primary' : 'contrast'" text rounded-xl aria-label="Link" class="h-8 w-8 !p-2"> <i-lucide-link /> </Button>
      <Button @click="editor.chain().focus().toggleHighlight().run()" :severity="editor.isActive('highlight') ? 'primary' : 'contrast'" text rounded-xl aria-label="Highlight" class="h-8 w-8 !p-2"> <i-lucide-highlighter /> </Button>
      <div class="inline-flex items-center p-1 rounded-md hover:bg-gray-200">
        <input type="color" @input="editor.chain().focus().setColor($event.target.value).run()" :value="editor.getAttributes('textStyle').color || '#000000'" class="w-6 h-6 border-none bg-transparent cursor-pointer" aria-label="Text Color">
      </div>

      <div class="border-l h-6 mx-2"></div>

      <!-- Block Elements -->
      <Button @click="editor.chain().focus().toggleBlockquote().run()" :severity="editor.isActive('blockquote') ? 'primary' : 'contrast'" text rounded-xl aria-label="Blockquote" class="h-8 w-8 !p-2"> <i-lucide-quote /> </Button>
      <Button @click="editor.chain().focus().toggleCodeBlock().run()" :severity="editor.isActive('codeBlock') ? 'primary' : 'contrast'" text rounded-xl aria-label="Code Block" class="h-8 w-8 !p-2"> <i-lucide-code /> </Button>
      <Button @click="editor.chain().focus().setHorizontalRule().run()" severity="contrast" text rounded-xl aria-label="Horizontal Rule" class="h-8 w-8 !p-2"> <i-lucide-minus /> </Button>
    </div>

    <!-- Editor Content -->
    <EditorContent :editor="editor" />
  </div>
</template>
