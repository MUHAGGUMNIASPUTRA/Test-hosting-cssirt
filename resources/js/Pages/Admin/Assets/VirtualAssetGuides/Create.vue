<!-- Tujuan: Form buat/edit panduan aset virtual dengan tabs dan document picker -->
<!-- Caller: VirtualAssetGuideController@create, @edit -->
<!-- Side Effects: Form submission, axios call ke api.admin.documents.index -->

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  guide: {
    type: Object,
    default: null,
  },
})

const isEdit = computed(() => !!props.guide)
const activeTab = ref('0')

// Existing documents in order (reactive list for UI)
const selectedDocuments = ref(
  props.guide?.guide_attachments
    ?.sort((a, b) => a.sort_order - b.sort_order)
    .map((ga) => ga.document)
    .filter(Boolean) ?? [],
)

const form = useForm({
  name: props.guide?.name ?? '',
  type: props.guide?.type ?? 'web',
  description: props.guide?.description ?? '',
  document_ids: selectedDocuments.value.map((d) => d.id),
})

const typeOptions = [
  { label: 'Aplikasi Web', value: 'web' },
  { label: 'Aplikasi Mobile', value: 'mobile' },
]

// Sync form document_ids with selectedDocuments
const syncFormDocumentIds = () => {
  form.document_ids = selectedDocuments.value.map((d) => d.id)
}

// Watch selectedDocuments to keep form in sync
watch(selectedDocuments, () => {
  syncFormDocumentIds()
})

// Submit form
const submit = () => {
  if (isEdit.value) {
    form.put(route('admin.virtual-asset-guides.update', props.guide.id))
  } else {
    form.post(route('admin.virtual-asset-guides.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Panduan' : 'Tambah Panduan'">
    <form @submit.prevent="submit" class="space-y-4">
      <AdminFormHeader
        :title="
          isEdit ? 'Edit Panduan Aset Virtual' : 'Tambah Panduan Aset Virtual'
        "
        :description="
          isEdit
            ? 'Perbarui informasi panduan pengembangan dan pengelolaan aset.'
            : 'Isi informasi panduan pengembangan dan pengelolaan aset.'
        "
        back-route="admin.virtual-asset-guides.index"
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
              form.processing ? 'Menyimpan...' : isEdit ? 'Update' : 'Simpan'
            }}
          </button>
        </template>
      </AdminFormHeader>

      <Tabs v-model:value="activeTab">
        <TabList>
          <Tab value="0">Utama</Tab>
          <Tab value="1">Lampiran</Tab>
        </TabList>
        <TabPanels>
          <!-- Tab: Utama -->
          <TabPanel value="0" class="space-y-4">
            <AdminFormSection
              title="Informasi Panduan"
              description="Nama, tipe, dan deskripsi panduan"
              color="blue"
            >
              <template #icon="{ iconClass }">
                <IconBookDownload :class="iconClass" />
              </template>
              <div class="space-y-4">
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Nama <span class="text-red-500">*</span></label
                  >
                  <InputText
                    v-model="form.name"
                    class="w-full"
                    placeholder="Nama panduan"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Tipe <span class="text-red-500">*</span></label
                  >
                  <div class="flex gap-2">
                    <Button
                      v-for="opt in typeOptions"
                      :key="opt.value"
                      type="button"
                      size="small"
                      :severity="
                        form.type === opt.value ? 'primary' : 'secondary'
                      "
                      :variant="
                        form.type === opt.value ? undefined : 'outlined'
                      "
                      @click="form.type = opt.value"
                    >
                      {{ opt.label }}
                    </Button>
                  </div>
                </div>
                <div>
                  <label class="mb-1 block text-sm font-medium text-slate-700"
                    >Deskripsi</label
                  >
                  <RichTextEditor v-model="form.description" />
                </div>
              </div>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab: Lampiran -->
          <TabPanel value="1" class="space-y-4">
            <DocumentPickerSection
              :model-value="selectedDocuments"
              @update:model-value="
                (v) => {
                  selectedDocuments = v
                  syncFormDocumentIds()
                }
              "
            />
          </TabPanel>
        </TabPanels>
      </Tabs>
    </form>
  </AdminLayout>
</template>
