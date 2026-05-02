<!-- Tujuan: Menampilkan detail dokumen dengan opsi edit dan delete -->
<!-- Caller: DocumentController@show -->
<!-- Side Effects: router.delete untuk hapus dokumen -->

<script setup>
import { formatDate } from '@/utils/date'
import { getSeverity } from '@/utils/status'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  document: {
    type: Object,
    required: true,
  },
})

const doc = props.document
const deleteVisible = ref(false)

const handleDelete = () => {
  router.delete(route('admin.documents.destroy', doc.id), {
    onSuccess: () => {
      deleteVisible.value = false
    },
  })
}

const stageSeverity = (stage) => getSeverity('document-stage', stage)
</script>

<template>
  <AdminLayout title="Detail Dokumen">
    <div class="space-y-4 sm:space-y-6">
      <!-- Header -->
      <AdminFormHeader
        :title="doc.title"
        :description="doc.description"
        back-route="admin.documents.index"
        :processing="false"
      >
        <template #actions>
          <Link :href="route('admin.documents.edit', doc.id)">
            <Button
              label="Edit"
              icon="pi pi-pencil"
              class="mr-2"
              severity="secondary"
            />
          </Link>
          <Button
            label="Hapus"
            icon="pi pi-trash"
            severity="danger"
            outlined
            @click="deleteVisible = true"
          />
        </template>
      </AdminFormHeader>

      <!-- Tabs -->
      <Tabs value="0">
        <TabList>
          <Tab value="0">Utama</Tab>
          <Tab value="1">Klasifikasi</Tab>
          <Tab value="2">Dokumen</Tab>
        </TabList>
        <TabPanels>
          <!-- Tab: Utama -->
          <TabPanel value="0" class="space-y-4">
            <!-- Informasi Dokumen -->
            <AdminFormSection
              title="Informasi Dokumen"
              color="blue"
              description="Judul dan deskripsi dokumen"
            >
              <template #icon="{ iconClass }">
                <IconFileDescription :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Judul
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">{{ doc.title }}</dd>
                </div>
                <div v-if="doc.description" class="sm:col-span-2">
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Deskripsi
                  </dt>
                  <dd class="prose mt-1 max-w-none text-sm text-slate-600">
                    {{ doc.description }}
                  </dd>
                </div>
              </dl>
            </AdminFormSection>

            <!-- Info Publikasi -->
            <AdminFormSection
              title="Info Publikasi"
              color="purple"
              description="Versi dan tanggal terbit"
            >
              <template #icon="{ iconClass }">
                <IconCalendar :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Versi
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ doc.version || '—' }}
                  </dd>
                </div>
                <div>
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Terbit
                  </dt>
                  <dd class="mt-1 text-sm text-slate-700">
                    {{ doc.published_at ? formatDate(doc.published_at) : '—' }}
                  </dd>
                </div>
              </dl>
            </AdminFormSection>

            <!-- Visibilitas -->
            <AdminFormSection
              title="Visibilitas"
              color="teal"
              description="Tampilkan ke publik"
            >
              <template #icon="{ iconClass }">
                <IconEye :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-4">
                <div>
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Status
                  </dt>
                  <dd class="mt-1">
                    <Tag
                      :value="doc.is_public ? 'Publik' : 'Privat'"
                      :severity="doc.is_public ? 'success' : 'secondary'"
                    />
                  </dd>
                </div>
              </dl>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab: Klasifikasi -->
          <TabPanel value="1" class="space-y-4">
            <!-- Area Dokumen -->
            <AdminFormSection
              title="Area Dokumen"
              color="indigo"
              description="Kategori pengelompokan"
            >
              <template #icon="{ iconClass }">
                <IconFolders :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Area
                  </dt>
                  <dd
                    v-if="doc.document_area"
                    class="mt-1 text-sm text-slate-700"
                  >
                    {{ doc.document_area.name }}
                  </dd>
                  <dd v-else class="mt-1 text-sm text-slate-400">—</dd>
                </div>
              </dl>
            </AdminFormSection>

            <!-- Stage -->
            <AdminFormSection
              title="Stage"
              color="orange"
              description="Tahap pengerjaan dokumen"
            >
              <template #icon="{ iconClass }">
                <IconCircleCheck :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-4">
                <div v-if="doc.stage">
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Stage
                  </dt>
                  <dd class="mt-1">
                    <Tag
                      :value="doc.stage"
                      :severity="stageSeverity(doc.stage)"
                    />
                  </dd>
                </div>
                <div v-else>
                  <dd class="text-sm text-slate-400">—</dd>
                </div>
              </dl>
            </AdminFormSection>
          </TabPanel>

          <!-- Tab: Dokumen -->
          <TabPanel value="2" class="space-y-4">
            <!-- File Dokumen (Word) -->
            <AdminFormSection
              title="File Dokumen"
              color="amber"
              description="Link dokumen Word — hanya terlihat admin"
            >
              <template #icon="{ iconClass }">
                <IconFileWord :class="iconClass" />
              </template>
              <div v-if="doc.draft_file_path">
                <a
                  :href="doc.draft_file_path"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-amber-600 hover:bg-amber-100"
                >
                  <IconExternalLink size="16" />
                  Buka File Draft
                </a>
              </div>
              <div v-else class="text-sm text-slate-400">
                Tidak ada file draft
              </div>
            </AdminFormSection>

            <!-- File Dokumen Sah (PDF) -->
            <AdminFormSection
              title="File Dokumen Sah"
              color="green"
              description="File PDF resmi"
            >
              <template #icon="{ iconClass }">
                <IconFileCertificate :class="iconClass" />
              </template>
              <div v-if="doc.official_attachment">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50"
                  >
                    <IconFileCertificate
                      v-if="doc.official_attachment.type === 'file'"
                      size="20"
                      class="text-green-600"
                    />
                    <IconExternalLink v-else size="20" class="text-blue-600" />
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900">
                      {{
                        doc.official_attachment.filename ||
                        doc.official_attachment.url ||
                        'Dokumen Sah'
                      }}
                    </p>
                    <p
                      v-if="doc.official_attachment.type === 'file'"
                      class="text-xs text-slate-500"
                    >
                      {{ doc.official_attachment.file_size }}
                    </p>
                  </div>
                  <Tag
                    :value="
                      doc.official_attachment.type === 'file' ? 'PDF' : 'Link'
                    "
                    :severity="
                      doc.official_attachment.type === 'file'
                        ? 'success'
                        : 'info'
                    "
                  />
                </div>
                <div class="mt-3 flex gap-2">
                  <a
                    :href="doc.official_attachment.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-blue-600 hover:bg-blue-100"
                  >
                    <IconEye size="16" />
                    Lihat
                  </a>
                  <a
                    v-if="doc.official_attachment.type === 'file'"
                    :href="doc.official_attachment.url"
                    download
                    class="inline-flex items-center gap-2 rounded-md border border-green-200 bg-green-50 px-3 py-2 text-green-600 hover:bg-green-100"
                  >
                    <IconDownload size="16" />
                    Unduh
                  </a>
                </div>
              </div>
              <div v-else class="text-sm text-slate-400">
                Tidak ada file dokumen sah
              </div>
            </AdminFormSection>

            <!-- Nomor Referensi -->
            <AdminFormSection
              title="Nomor Referensi"
              color="rose"
              description="Identifikasi dokumen"
            >
              <template #icon="{ iconClass }">
                <IconHash :class="iconClass" />
              </template>
              <dl class="grid grid-cols-1 gap-4">
                <div v-if="doc.reference_number">
                  <dt
                    class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Nomor Referensi
                  </dt>
                  <dd class="mt-1 font-mono text-sm text-slate-700">
                    {{ doc.reference_number }}
                  </dd>
                </div>
                <div v-else>
                  <dd class="text-sm text-slate-400">—</dd>
                </div>
              </dl>
            </AdminFormSection>
          </TabPanel>
        </TabPanels>
      </Tabs>
    </div>

    <!-- Delete Dialog -->
    <DeleteConfirmDialog
      v-model:visible="deleteVisible"
      entity-label="dokumen"
      :delete-label="doc.title"
      @confirm="handleDelete"
    >
      <template #item-info>
        <div class="space-y-1 text-sm">
          <p><span class="font-medium">Judul:</span> {{ doc.title }}</p>
          <p v-if="doc.reference_number">
            <span class="font-medium">Nomor Referensi:</span>
            {{ doc.reference_number }}
          </p>
        </div>
      </template>
    </DeleteConfirmDialog>
  </AdminLayout>
</template>
