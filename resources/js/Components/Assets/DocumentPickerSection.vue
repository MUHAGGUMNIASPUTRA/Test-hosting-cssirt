<!-- Tujuan: Section untuk memilih dokumen (selected list + picker dialog) -->
<!-- Caller: VirtualAssetGuides/Create.vue -->
<!-- Side Effects: axios call ke api.admin.documents.index -->

<script setup>
import axios from 'axios'
import { ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['update:modelValue'])

// Document picker dialog state
const pickerDialogVisible = ref(false)
const pickerSearch = ref('')
const pickerDocuments = ref([])
const pickerLoading = ref(false)
const pickerSelectedDocs = ref(new Set())

// Fetch documents from API for picker
const fetchPickerDocuments = async () => {
  pickerLoading.value = true
  try {
    const params = new URLSearchParams()
    if (pickerSearch.value) {
      params.set('search', pickerSearch.value)
    }
    params.set('per_page', '50')
    const { data } = await axios.get(
      route('api.admin.documents.index') +
        (params.toString() ? '?' + params : ''),
    )
    pickerDocuments.value = (data.data?.data || []).filter(Boolean)
    pickerSelectedDocs.value.clear()
  } catch {
    pickerDocuments.value = []
  } finally {
    pickerLoading.value = false
  }
}

// Open picker dialog
const openPicker = () => {
  pickerSearch.value = ''
  pickerDialogVisible.value = true
  fetchPickerDocuments()
}

// Search in picker
const onPickerSearch = () => {
  fetchPickerDocuments()
}

// Toggle document selection in picker
const toggleDocumentSelection = (docId) => {
  const newSet = new Set(pickerSelectedDocs.value)
  if (newSet.has(docId)) {
    newSet.delete(docId)
  } else {
    newSet.add(docId)
  }
  pickerSelectedDocs.value = newSet
}

// Add selected documents from picker
const addSelectedDocuments = () => {
  const selected = pickerDocuments.value.filter((d) =>
    pickerSelectedDocs.value.has(d.id),
  )

  // Add only if not already selected
  const updated = [...props.modelValue]
  for (const doc of selected) {
    if (!updated.some((d) => d.id === doc.id)) {
      updated.push(doc)
    }
  }

  emit('update:modelValue', updated)
  pickerDialogVisible.value = false
  pickerSelectedDocs.value.clear()
}

// Remove document from selected
const removeDocument = (docId) => {
  emit(
    'update:modelValue',
    props.modelValue.filter((d) => d.id !== docId),
  )
}
</script>

<template>
  <AdminFormSection
    title="Lampiran"
    description="Dokumen referensi panduan"
    color="indigo"
  >
    <template #icon="{ iconClass }">
      <IconPaperclip :class="iconClass" />
    </template>

    <!-- Selected documents -->
    <div v-if="modelValue.length > 0" class="mb-4 space-y-1.5">
      <p
        class="mb-2 text-xs font-medium uppercase tracking-wider text-slate-500"
      >
        Dokumen Terpilih
      </p>
      <div
        v-for="doc in modelValue"
        :key="doc.id"
        class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2"
      >
        <IconFile size="14" class="flex-shrink-0 text-slate-400" />
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-medium text-slate-700">
            {{ doc.title }}
          </p>
          <p
            v-if="doc.reference_number"
            class="truncate text-xs text-slate-500"
          >
            {{ doc.reference_number }}
          </p>
        </div>
        <Button
          type="button"
          size="small"
          severity="danger"
          variant="text"
          class="!p-0"
          @click="removeDocument(doc.id)"
        >
          <IconX size="14" />
        </Button>
      </div>
    </div>

    <!-- Add button -->
    <div class="flex gap-2">
      <Button
        type="button"
        severity="secondary"
        variant="outlined"
        class="flex-1"
        @click="openPicker"
      >
        <IconPlus size="15" class="mr-1.5" />Pilih Dokumen
      </Button>
    </div>
  </AdminFormSection>

  <!-- Document Picker Dialog -->
  <Dialog
    v-model:visible="pickerDialogVisible"
    :modal="true"
    :closable="true"
    class="w-full max-w-2xl"
  >
    <template #container>
      <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div
          class="flex items-center justify-between border-b border-slate-200 p-5"
        >
          <h3 class="text-lg font-semibold text-slate-900">
            Pilih Dokumen Referensi
          </h3>
          <Button
            icon="pi pi-times"
            severity="secondary"
            text
            rounded
            @click="pickerDialogVisible = false"
          />
        </div>

        <div class="p-5">
          <!-- Search -->
          <div class="mb-4">
            <InputText
              v-model="pickerSearch"
              class="w-full"
              placeholder="Cari dokumen..."
              @keyup.enter="onPickerSearch"
            />
          </div>

          <!-- Documents list -->
          <div v-if="pickerLoading" class="py-8 text-center">
            <ProgressSpinner />
          </div>
          <div
            v-else-if="pickerDocuments.length === 0"
            class="py-8 text-center text-slate-400"
          >
            Tidak ada dokumen ditemukan
          </div>
          <div v-else class="max-h-96 space-y-2 overflow-y-auto">
            <div
              v-for="doc in pickerDocuments"
              :key="doc.id"
              class="flex items-center gap-3 rounded-lg border border-slate-200 p-3 hover:bg-slate-50"
            >
              <Checkbox
                :checked="pickerSelectedDocs.has(doc.id)"
                :binary="true"
                @change="toggleDocumentSelection(doc.id)"
              />
              <div class="min-w-0 flex-1">
                <p class="truncate font-medium text-slate-700">
                  {{ doc.title }}
                </p>
                <p v-if="doc.reference_number" class="text-xs text-slate-500">
                  {{ doc.reference_number }}
                </p>
                <p v-if="doc.stage" class="text-xs text-slate-400">
                  {{ doc.stage }}
                </p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div
            class="mt-4 flex justify-end gap-2 border-t border-slate-200 pt-4"
          >
            <Button
              type="button"
              severity="secondary"
              variant="outlined"
              @click="pickerDialogVisible = false"
            >
              Batal
            </Button>
            <Button
              type="button"
              :disabled="pickerSelectedDocs.size === 0"
              @click="addSelectedDocuments"
            >
              Tambahkan ({{ pickerSelectedDocs.size }})
            </Button>
          </div>
        </div>
      </div>
    </template>
  </Dialog>
</template>
