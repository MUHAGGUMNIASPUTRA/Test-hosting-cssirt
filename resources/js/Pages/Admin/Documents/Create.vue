<!-- Tujuan: Form buat/edit dokumen dengan tabs (Utama, Klasifikasi, Dokumen) -->
<!-- Caller: DocumentController@create, @edit -->
<!-- Side Effects: Form submission untuk store/update dokumen -->

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  document: {
    type: Object,
    default: null,
  },
  documentAreas: {
    type: Array,
    default: () => [],
  },
  stageOptions: {
    type: Array,
    default: () => [],
  },
})

const isEditMode = computed(() => !!props.document)
const isStageFinal = computed(() => form.stage === 'Final')
const activeTab = ref('0')

const form = useForm({
  title: props.document?.title || '',
  description: props.document?.description || '',
  version: props.document?.version || '',
  published_at: props.document?.published_at
    ? new Date(props.document.published_at)
    : null,
  is_public: props.document?.is_public ?? false,
  document_area_id: props.document?.document_area_id ?? null,
  doc_file_link: props.document?.draft_file_path || '',
  official_file_type: props.document?.officialAttachment?.type ?? 'file',
  official_file: null,
  official_file_link:
    (props.document?.officialAttachment?.type === 'link'
      ? props.document?.officialAttachment?.url
      : '') || '',
  reference_number: props.document?.reference_number || '',
  stage: props.document?.stage || null,
})

const mainData = computed(() => ({
  title: form.title,
  description: form.description,
  version: form.version,
  published_at: form.published_at,
  is_public: form.is_public,
}))
const updateMain = (val) =>
  Object.entries(val).forEach(([k, v]) => (form[k] = v))

const classData = computed(() => ({
  document_area_id: form.document_area_id,
  stage: form.stage,
}))
const updateClass = (val) =>
  Object.entries(val).forEach(([k, v]) => (form[k] = v))

const filesData = computed(() => ({
  doc_file_link: form.doc_file_link,
  official_file_type: form.official_file_type,
  official_file: form.official_file,
  official_file_link: form.official_file_link,
  reference_number: form.reference_number,
}))
const updateFiles = (val) =>
  Object.entries(val).forEach(([k, v]) => (form[k] = v))

const submit = () => {
  if (isEditMode.value) {
    form
      .transform((data) => ({ ...data, _method: 'PUT' }))
      .post(route('admin.documents.update', props.document.id), {
        forceFormData: true,
      })
  } else {
    form.post(route('admin.documents.store'), {
      forceFormData: true,
    })
  }
}

// Auto-switch tab saat ada error
const tabFieldMap = {
  0: ['title', 'description', 'published_at', 'is_public'],
  1: ['document_area_id', 'stage'],
  2: [
    'doc_file_link',
    'official_file',
    'official_file_link',
    'reference_number',
  ],
}

watch(
  () => form.errors,
  (errors) => {
    for (const [tab, fields] of Object.entries(tabFieldMap)) {
      if (fields.some((field) => field in errors)) {
        activeTab.value = tab
        break
      }
    }
  },
)
</script>

<template>
  <AdminLayout :title="isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen'">
    <form @submit.prevent="submit" class="space-y-4">
      <!-- Header -->
      <AdminFormHeader
        :title="isEditMode ? 'Edit Dokumen' : 'Tambah Dokumen'"
        :description="
          isEditMode
            ? 'Perbarui informasi dokumen'
            : 'Tambahkan dokumen panduan baru'
        "
        back-route="admin.documents.index"
        :processing="form.processing"
      >
        <template #actions>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <IconLoader3
              v-if="form.processing"
              class="animate-spin"
              size="16"
            />
            <IconDeviceFloppy v-else size="16" />
            {{
              form.processing
                ? 'Menyimpan...'
                : isEditMode
                  ? 'Update'
                  : 'Simpan'
            }}
          </button>
        </template>
      </AdminFormHeader>

      <!-- Tabs -->
      <Tabs v-model:value="activeTab">
        <TabList>
          <Tab value="0">Utama</Tab>
          <Tab value="1">Klasifikasi</Tab>
          <Tab value="2">Dokumen</Tab>
        </TabList>
        <TabPanels>
          <!-- Tab: Utama -->
          <TabPanel value="0" class="space-y-4">
            <DocumentMainFormSection
              :model-value="mainData"
              :errors="form.errors"
              @update:model-value="updateMain"
            />
          </TabPanel>

          <!-- Tab: Klasifikasi -->
          <TabPanel value="1" class="space-y-4">
            <DocumentClassificationFormSection
              :model-value="classData"
              :document-areas="documentAreas"
              :stage-options="stageOptions"
              :errors="form.errors"
              @update:model-value="updateClass"
            />
          </TabPanel>

          <!-- Tab: Dokumen -->
          <TabPanel value="2" class="space-y-4">
            <DocumentFilesFormSection
              :model-value="filesData"
              :is-edit-mode="isEditMode"
              :existing-official-attachment="document?.officialAttachment"
              :is-stage-final="isStageFinal"
              :errors="form.errors"
              @update:model-value="updateFiles"
            />
          </TabPanel>
        </TabPanels>
      </Tabs>
    </form>
  </AdminLayout>
</template>

<style>
.p-fileupload-header {
  display: none !important;
}

.p-fileupload-content {
  padding: 0 !important;
}
</style>
