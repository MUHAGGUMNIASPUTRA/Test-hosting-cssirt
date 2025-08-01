<script setup>
import { computed, ref, watch } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';

// Import extensions
import Highlight from '@tiptap/extension-highlight';
import TextAlign from '@tiptap/extension-text-align';
import CharacterCount from '@tiptap/extension-character-count';
import { Table } from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableHeader from '@tiptap/extension-table-header';
import TableCell from '@tiptap/extension-table-cell';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';
import ResizableImage from '../tiptap-extensions/ResizableImage';
import {
  IconHeading, IconH1, IconH2, IconH3, IconH4,
  IconList, IconListNumbers,
  IconAlignLeft, IconAlignCenter, IconAlignRight, IconAlignJustified
} from '@tabler/icons-vue';

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
      link: { openOnClick: false },
      blockquote: {
        HTMLAttributes: {
          class: 'border-l-4 border-gray-700 pl-4 not-italic text-gray-600',
        },
      },
      code: {
        HTMLAttributes: {
          class: 'border border-gray-300 bg-gray-100 text-gray-700 font-mono font-normal text-sm rounded-md px-1 py-0.5',
        },
      },
      codeBlock: {
        HTMLAttributes: {
          class: 'border border-gray-300 bg-gray-100 text-gray-700 font-mono text-sm rounded-xl p-4 my-4 overflow-x-auto',
        },
      },
    }),
    TextAlign.configure({ types: ['heading', 'paragraph'] }),
    Highlight.configure({ multicolor: true }),
    TextStyle,
    Table.configure({ resizable: true }),
    TableRow,
    TableHeader,
    TableCell,
    Color,
    CharacterCount,
    ResizableImage,
  ],
  onUpdate: () => {
    emit('update:modelValue', editor.value.getHTML());
  },
  editorProps: {
    attributes: {
      class: 'prose max-w-none focus:outline-none p-4 min-h-[300px]',
    },
  },
});

watch(() => props.modelValue, (newValue) => {
  const isSame = editor.value.getHTML() === newValue;
  if (isSame) return;
  editor.value.commands.setContent(newValue, false);
});

// --- Dropdown Panel Logic ---
const headingPanel = ref();
const listPanel = ref();
const highlightPanel = ref();
const textAlignPanel = ref();
const rowPanel = ref();
const columnPanel = ref();

const highlightColors = ref([
  { name: 'Yellow', color: '#fef9c3', border: '#fef9c3' },
  { name: 'Green', color: '#dcfce7', border: '#cafadb' },
  { name: 'Red', color: '#ffe4e6', border: '#facfd2' },
  { name: 'Blue', color: '#e0f2fe', border: '#c5e6fc' },
  { name: 'Purple', color: '#f3e8ff', border: '#e6d2fc' },
]);

const applyHighlight = (color) => {
  if (color) {
    editor.value.chain().focus().setHighlight({ color }).run();
  } else {
    editor.value.chain().focus().unsetHighlight().run();
  }
  highlightPanel.value.hide();
};

const toggleHeadingPanel = (event) => { headingPanel.value.toggle(event); };
const toggleListPanel = (event) => { listPanel.value.toggle(event); };
const toggleHighlightPanel = (event) => { highlightPanel.value.toggle(event); };
const toggleTextAlignPanel = (event) => { textAlignPanel.value.toggle(event); };
const toggleRowPanel = (event) => { rowPanel.value.toggle(event); };
const toggleColumnPanel = (event) => { columnPanel.value.toggle(event); };

// --- Dynamic Icon Logic ---
const activeHeadingIcon = computed(() => {
  if (editor.value?.isActive('heading', { level: 1 })) return IconH1;
  if (editor.value?.isActive('heading', { level: 2 })) return IconH2;
  if (editor.value?.isActive('heading', { level: 3 })) return IconH3;
  if (editor.value?.isActive('heading', { level: 4 })) return IconH4;
  return IconHeading;
});

const activeListIcon = computed(() => {
  if (editor.value?.isActive('bulletList')) return IconList;
  if (editor.value?.isActive('orderedList')) return IconListNumbers;
  return IconList;
});

const activeAlignIcon = computed(() => {
  if (editor.value?.isActive({ textAlign: 'center' })) return IconAlignCenter;
  if (editor.value?.isActive({ textAlign: 'right' })) return IconAlignRight;
  if (editor.value?.isActive({ textAlign: 'justify' })) return IconAlignJustified;
  return IconAlignLeft;
});

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

function adjustBrightness(hexColor, factor) {
  const color = hexColor.startsWith('#') ? hexColor.slice(1) : hexColor;

  const r = parseInt(color.substring(0, 2), 16);
  const g = parseInt(color.substring(2, 4), 16);
  const b = parseInt(color.substring(4, 6), 16);

  const newR = Math.min(255, Math.floor(r * factor));
  const newG = Math.min(255, Math.floor(g * factor));
  const newB = Math.min(255, Math.floor(b * factor));

  const toHex = (c) => ('00' + c.toString(16)).slice(-2);

  return `#${toHex(newR)}${toHex(newG)}${toHex(newB)}`;
}

// Image upload
const fileInput = ref(null);

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('image', file);

  axios.post(route('admin.images.upload'), formData)
    .then(response => {
      const url = response.data.url;
      if (url) {
        editor.value.chain().focus().setImage({ src: url }).run();
      }
    })
    .catch(error => {
      console.error('Image upload failed:', error);
      alert('Gagal mengunggah gambar. Pastikan file adalah gambar dan ukurannya di bawah 2MB.');
    });
};
</script>

<template>
  <div v-if="editor" class="border border-gray-300 rounded-md bg-white">
    <!-- Toolbar -->
    <div class="p-2 border-b border-gray-300 rounded-t-md flex flex-wrap items-center gap-[2px]">
      <!-- Undo/Redo & Clear Formatting -->
      <Button @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.can().undo() ? 'text-gray-700 hover:bg-gray-200' : 'text-gray-400 cursor-not-allowed']" title="Undo"> <IconArrowBackUp/> </Button>
      <Button @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.can().redo() ? 'text-gray-700 hover:bg-gray-200' : 'text-gray-400 cursor-not-allowed']" title="Redo"> <IconArrowForwardUp/> </Button>
      <Button @click="editor.chain().focus().unsetAllMarks().clearNodes().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Clear Formatting"> <IconEraser/> </Button>

      <div class="hidden lg:flex border-l h-6 mx-2"></div>

      <!-- Headings Panel -->
      <Button @click="toggleHeadingPanel" unstyled :class="['h-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('heading') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Headings">
        <component :is="activeHeadingIcon" :size="14" />
      </Button>
      <Popover ref="headingPanel" class="!rounded-xl" :pt="{ content: { class: '!p-1.5' } }">
        <div class="flex items-center gap-[2px]">
          <Button @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive('heading', { level: 1 }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Heading 1"> <IconH1/> </Button>
          <Button @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive('heading', { level: 2 }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Heading 2"> <IconH2/> </Button>
          <Button @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive('heading', { level: 3 }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Heading 3"> <IconH3/> </Button>
          <Button @click="editor.chain().focus().toggleHeading({ level: 4 }).run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive('heading', { level: 4 }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Heading 4"> <IconH4/> </Button>
        </div>
      </Popover>

      <!-- Lists Panel -->
      <Button @click="toggleListPanel" unstyled :class="['h-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('bulletList') || editor.isActive('orderedList') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Lists">
        <component :is="activeListIcon" :size="14" />
      </Button>
      <Popover ref="listPanel" class="!rounded-xl" :pt="{ content: { class: '!p-1.5' } }">
        <div class="flex items-center gap-[2px]">
          <Button @click="editor.chain().focus().toggleBulletList().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive('bulletList') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Bullet List"> <IconList/> </Button>
          <Button @click="editor.chain().focus().toggleOrderedList().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive('orderedList') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Ordered List"> <IconListNumbers/> </Button>
        </div>
      </Popover>

      <!-- Blockquote & Code Block -->
      <Button @click="editor.chain().focus().toggleBlockquote().run()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('blockquote') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Blockquote"> <IconBlockquote/> </Button>
      <Button @click="editor.chain().focus().toggleCodeBlock().run()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('codeBlock') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Code Block"> <i-lucide-square-code /> </Button>

      <div class="hidden lg:flex border-l h-6 mx-2"></div>

      <!-- Basic Formatting -->
      <Button @click="editor.chain().focus().toggleBold().run()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('bold') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Bold"> <IconBold stroke-width="3"/> </Button>
      <Button @click="editor.chain().focus().toggleItalic().run()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('italic') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Italic"> <IconItalic/> </Button>
      <Button @click="editor.chain().focus().toggleUnderline().run()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('underline') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Underline"> <IconUnderline/> </Button>
      <Button @click="editor.chain().focus().toggleStrike().run()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('strike') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Strike"> <IconStrikethrough/> </Button>

      <div class="hidden lg:flex border-l h-6 mx-2"></div>

      <!-- Text Align Panel -->
      <Button
        @click="toggleTextAlignPanel"
        unstyled
        class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200"
        :class="editor.isActive({ textAlign: 'left' }) || editor.isActive({ textAlign: 'center' }) || editor.isActive({ textAlign: 'right' }) || editor.isActive({ textAlign: 'justify' }) ? 'bg-gray-100 !text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'"
        title="Text Align"
      >
        <component :is="activeAlignIcon" :size="16" />
      </Button>
      <Popover ref="textAlignPanel" class="!rounded-xl" :pt="{ content: { class: '!p-1.5' } }">
        <div class="flex items-center gap-[2px]">
          <Button @click="editor.chain().focus().setTextAlign('left').run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive({ textAlign: 'left' }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Align Left"> <IconAlignLeft/> </Button>
          <Button @click="editor.chain().focus().setTextAlign('center').run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive({ textAlign: 'center' }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Align Center"> <IconAlignCenter/> </Button>
          <Button @click="editor.chain().focus().setTextAlign('right').run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive({ textAlign: 'right' }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Align Right"> <IconAlignRight/> </Button>
          <Button @click="editor.chain().focus().setTextAlign('justify').run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive({ textAlign: 'justify' }) ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'" title="Align Justify"> <IconAlignJustified/> </Button>
          <div class="border-l h-5 mx-1"></div>
          <button
            @click="editor.chain().focus().unsetTextAlign().run()"
            type="button"
            class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors hover:bg-gray-200"
            title="Unset Text Align"
          >
            <IconBan size="16"/>
          </button>
        </div>
      </Popover>

      <div class="hidden lg:flex border-l h-6 mx-2"></div>

      <!-- Code, Highlight, Link -->
      <Button @click="editor.chain().focus().toggleCode().run()" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('code') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Code"> <IconCode/> </Button>
      <Button @click="toggleHighlightPanel" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('highlight') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Highlight"> <i-lucide-highlighter /> </Button>
      <Popover ref="highlightPanel" class="!rounded-xl" :pt="{ content: { class: '!p-1.5' } }">
        <div class="flex items-center gap-[2px]">
          <button
            v-for="swatch in highlightColors"
            :key="swatch.color"
            @click="applyHighlight(swatch.color)"
            type="button"
            class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors hover:bg-gray-200"
            :class="{ 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700': editor.isActive('highlight', { color: swatch.color }) }"
            :title="swatch.name"
          >
            <span class="w-5 h-5 rounded-full" :style="{ color: swatch.color, backgroundColor: swatch.color, border: '1px solid ' + adjustBrightness(swatch.color, 0.90) }"></span>
          </button>
          <div class="border-l h-5 mx-1"></div>
          <button
            @click="applyHighlight(null)"
            type="button"
            class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors hover:bg-gray-200"
            title="Remove Highlight"
          >
            <IconBan size="16"/>
          </button>
        </div>
      </Popover>
      <Button @click="setLink" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors', editor.isActive('link') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200']" title="Link"> <IconLink/> </Button>

      <div class="hidden lg:flex border-l h-6 mx-2"></div>

      <!-- Table -->
      <Button @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors" :class="editor.isActive('table') ? 'bg-gray-100 text-blue-600 hover:bg-gray-200 hover:text-gray-700' : 'text-gray-700 hover:bg-gray-200'"  title="Insert Table"> <IconTable/> </Button>
      <template v-if="editor.isActive('table')">
        <Button @click="toggleRowPanel" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Row"> <IconLayoutRows/> </Button>
        <Popover ref="rowPanel" class="!rounded-xl" :pt="{ content: { class: '!p-1.5' } }">
          <div class="flex items-center gap-[2px]">
            <Button @click="editor.chain().focus().addRowBefore().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Add Row Before"> <IconRowInsertTop/> </Button>
            <Button @click="editor.chain().focus().addRowAfter().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Add Row After"> <IconRowInsertBottom/> </Button>
            <Button @click="editor.chain().focus().deleteRow().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-red-500 hover:bg-red-100" title="Delete Row"> <IconTrash/> </Button>
          </div>
        </Popover>
        <Button @click="toggleColumnPanel" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Column"> <IconLayoutColumns/> </Button>
        <Popover ref="columnPanel" class="!rounded-xl" :pt="{ content: { class: '!p-1.5' } }">
          <div class="flex items-center gap-[2px]">
            <Button @click="editor.chain().focus().addColumnBefore().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Add Column Before"> <IconColumnInsertLeft/> </Button>
            <Button @click="editor.chain().focus().addColumnAfter().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Add Column After"> <IconColumnInsertRight/> </Button>
            <Button @click="editor.chain().focus().deleteColumn().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-red-500 hover:bg-red-100" title="Delete Column"> <IconTrash/> </Button>
          </div>
        </Popover>
        <Button @click="editor.chain().focus().deleteTable().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center text-red-500 hover:bg-red-100" title="Delete Table"> <IconTrash/> </Button>
      </template>

      <div class="hidden lg:flex border-l h-6 mx-2"></div>

      <!-- Font Color -->
      <div class="inline-flex items-center p-1 rounded-xl hover:bg-gray-200">
        <input type="color" @input="editor.chain().focus().setColor($event.target.value).run()" :value="editor.getAttributes('textStyle').color || '#000000'" class="w-6 h-6 border-none bg-transparent cursor-pointer" title="Text Color">
      </div>

      <!-- Horizontal Line -->
      <Button @click="editor.chain().focus().setHorizontalRule().run()" unstyled class="h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200" title="Horizontal Line"> <IconSeparator/> </Button>

      <!-- Image -->
      <Button @click="triggerFileInput" unstyled :class="['h-8 w-8 !p-2 rounded-xl flex items-center justify-center transition-colors text-gray-700 hover:bg-gray-200']" title="Insert Image"> <IconPhotoPlus/> </Button>
    </div>

    <!-- Editor Content -->
    <EditorContent :editor="editor" />

    <!-- Input image -->
    <input type="file" ref="fileInput" @change="handleImageUpload" class="hidden" accept="image/*" />

    <!-- Character Count -->
    <div class="p-2 border-t border-gray-300 text-xs text-gray-500 flex justify-end gap-4">
      <span>
        {{ editor.storage.characterCount.characters() }} karakter
      </span>
      <span>
        {{ editor.storage.characterCount.words() }} kata
      </span>
    </div>
  </div>
</template>
