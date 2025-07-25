<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import { watch } from 'vue';

// Import new extensions
import TextAlign from '@tiptap/extension-text-align';
import Highlight from '@tiptap/extension-highlight';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';

// Import the icons you need from Lucide
import {
  Bold, Italic, Strikethrough, Underline,
  Heading1, Heading2, Heading3,
  List, ListOrdered,
  Quote, Code, Minus,
  Undo, Redo,
  AlignLeft, AlignCenter, AlignRight, AlignJustify,
  Link as LinkIcon, Highlighter, Palette
} from 'lucide-vue-next';

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
    StarterKit.configure({
      link: {
        openOnClick: false,
      },
    }),
    TextAlign.configure({
      types: ['heading', 'paragraph'],
    }),
    Highlight,
    TextStyle,
    Color,
  ],
  onUpdate: () => {
    emit('update:modelValue', editor.value.getHTML());
  },
  editorProps: {
    attributes: {
      class: 'prose max-w-none focus:outline-none p-4 border border-gray-300 rounded-b-md min-h-[300px]',
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

const setLink = () => {
  const previousUrl = editor.value.getAttributes('link').href;
  const url = window.prompt('URL', previousUrl);

  if (url === null) {
    return;
  }

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
    <div class="p-2 bg-gray-100 border-b border-gray-300 rounded-t-md flex flex-wrap items-center gap-1">
      <!-- Undo/Redo -->
      <Button @click="editor.chain().focus().undo().run()" text rounded aria-label="Undo"> <Undo :size="18" /> </Button>
      <Button @click="editor.chain().focus().redo().run()" text rounded aria-label="Redo"> <Redo :size="18" /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Basic Formatting -->
      <Button @click="editor.chain().focus().toggleBold().run()" :severity="editor.isActive('bold') ? 'secondary' : 'contrast'" text rounded aria-label="Bold"> <Bold :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleItalic().run()" :severity="editor.isActive('italic') ? 'secondary' : 'contrast'" text rounded aria-label="Italic"> <Italic :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleUnderline().run()" :severity="editor.isActive('underline') ? 'secondary' : 'contrast'" text rounded aria-label="Underline"> <Underline :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleStrike().run()" :severity="editor.isActive('strike') ? 'secondary' : 'contrast'" text rounded aria-label="Strike"> <Strikethrough :size="18" /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Headings -->
      <Button @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :severity="editor.isActive('heading', { level: 1 }) ? 'secondary' : 'contrast'" text rounded aria-label="Heading 1"> <Heading1 :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :severity="editor.isActive('heading', { level: 2 }) ? 'secondary' : 'contrast'" text rounded aria-label="Heading 2"> <Heading2 :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :severity="editor.isActive('heading', { level: 3 }) ? 'secondary' : 'contrast'" text rounded aria-label="Heading 3"> <Heading3 :size="18" /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Alignment -->
      <Button @click="editor.chain().focus().setTextAlign('left').run()" :severity="editor.isActive({ textAlign: 'left' }) ? 'secondary' : 'contrast'" text rounded aria-label="Align Left"> <AlignLeft :size="18" /> </Button>
      <Button @click="editor.chain().focus().setTextAlign('center').run()" :severity="editor.isActive({ textAlign: 'center' }) ? 'secondary' : 'contrast'" text rounded aria-label="Align Center"> <AlignCenter :size="18" /> </Button>
      <Button @click="editor.chain().focus().setTextAlign('right').run()" :severity="editor.isActive({ textAlign: 'right' }) ? 'secondary' : 'contrast'" text rounded aria-label="Align Right"> <AlignRight :size="18" /> </Button>
      <Button @click="editor.chain().focus().setTextAlign('justify').run()" :severity="editor.isActive({ textAlign: 'justify' }) ? 'secondary' : 'contrast'" text rounded aria-label="Align Justify"> <AlignJustify :size="18" /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Advanced Formatting -->
      <Button @click="setLink" :severity="editor.isActive('link') ? 'secondary' : 'contrast'" text rounded aria-label="Link"> <LinkIcon :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleHighlight().run()" :severity="editor.isActive('highlight') ? 'secondary' : 'contrast'" text rounded aria-label="Highlight"> <Highlighter :size="18" /> </Button>
      <div class="inline-flex items-center">
        <input type="color" @input="editor.chain().focus().setColor($event.target.value).run()" :value="editor.getAttributes('textStyle').color || '#000000'" class="w-6 h-6 border-none bg-transparent cursor-pointer">
      </div>

      <div class="border-l h-6 mx-2"></div>

      <!-- Lists -->
      <Button @click="editor.chain().focus().toggleBulletList().run()" :severity="editor.isActive('bulletList') ? 'secondary' : 'contrast'" text rounded aria-label="Bullet List"> <List :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleOrderedList().run()" :severity="editor.isActive('orderedList') ? 'secondary' : 'contrast'" text rounded aria-label="Ordered List"> <ListOrdered :size="18" /> </Button>

      <div class="border-l h-6 mx-2"></div>

      <!-- Block Elements -->
      <Button @click="editor.chain().focus().toggleBlockquote().run()" :severity="editor.isActive('blockquote') ? 'secondary' : 'contrast'" text rounded aria-label="Blockquote"> <Quote :size="18" /> </Button>
      <Button @click="editor.chain().focus().toggleCodeBlock().run()" :severity="editor.isActive('codeBlock') ? 'secondary' : 'contrast'" text rounded aria-label="Code Block"> <Code :size="18" /> </Button>
      <Button @click="editor.chain().focus().setHorizontalRule().run()" text rounded aria-label="Horizontal Rule"> <Minus :size="18" /> </Button>
    </div>

    <!-- Editor Content -->
    <EditorContent :editor="editor" />
  </div>
</template>

