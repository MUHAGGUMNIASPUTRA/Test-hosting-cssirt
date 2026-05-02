<script setup>
import axios from 'axios'
import { ref, watch } from 'vue'

const props = defineProps({
  guides: { type: Array, default: () => [] },
  assetType: { type: String, default: null },
  assetId: { type: String, default: null },
})

const dialogVisible = ref(false)
const activeGuide = ref(null)

const localGuides = ref(props.guides.map((g) => ({ ...g })))

watch(
  () => props.guides,
  (newGuides) => {
    localGuides.value = newGuides.map((g) => ({ ...g }))
  },
)

const openGuide = (guide) => {
  activeGuide.value = guide
  dialogVisible.value = true
}

const toggleAck = async (guide) => {
  if (!props.assetId) return
  const previous = guide.acknowledged
  guide.acknowledged = !previous
  try {
    const { data } = await axios.post(
      route('admin.assets.guides.acknowledge', {
        assetType: props.assetType,
        assetId: props.assetId,
        guideId: guide.id,
      }),
    )
    guide.acknowledged = data.acknowledged
  } catch {
    guide.acknowledged = previous
  }
}
</script>

<template>
  <AdminFormSection
    title="Panduan Referensi"
    description="Panduan yang relevan untuk aset ini"
    color="amber"
  >
    <template #icon="{ iconClass }">
      <IconBook :class="iconClass" />
    </template>

    <div v-if="localGuides.length === 0" class="py-2 text-sm text-slate-400">
      Belum ada panduan referensi.
    </div>

    <ul v-else class="space-y-2">
      <li
        v-for="guide in localGuides"
        :key="guide.id"
        class="flex items-center gap-3"
      >
        <Checkbox
          :checked="guide.acknowledged"
          :disabled="!assetId"
          :binary="true"
          v-tooltip="!assetId ? 'Tersedia setelah data disimpan' : undefined"
          @change="toggleAck(guide)"
        />
        <button
          type="button"
          class="flex-1 text-left text-sm text-blue-600 hover:underline"
          @click="openGuide(guide)"
        >
          {{ guide.name }}
        </button>
        <IconCheck
          v-if="guide.acknowledged"
          size="14"
          class="flex-shrink-0 text-green-500"
        />
      </li>
    </ul>

    <!-- Guide content dialog -->
    <Dialog
      v-model:visible="dialogVisible"
      :modal="true"
      :closable="true"
      class="w-full max-w-lg"
    >
      <template #container>
        <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
          <div
            class="flex items-center justify-between border-b border-slate-200 p-5"
          >
            <h3 class="text-lg font-semibold text-slate-900">
              {{ activeGuide?.name }}
            </h3>
            <Button
              icon="pi pi-times"
              severity="secondary"
              text
              rounded
              @click="dialogVisible = false"
            />
          </div>
          <div class="p-5">
            <div
              v-if="activeGuide?.description"
              v-html="activeGuide.description"
              class="mb-4 text-sm leading-relaxed text-slate-600"
            ></div>

            <div
              v-if="activeGuide?.guide_attachments?.length"
              class="space-y-2"
            >
              <p
                class="text-xs font-semibold uppercase tracking-wider text-slate-400"
              >
                Dokumen Referensi
              </p>
              <div
                v-for="ga in activeGuide.guide_attachments"
                :key="ga.id"
                class="rounded-lg border border-slate-200 bg-slate-50 p-3"
              >
                <div class="mb-2 flex items-center gap-2">
                  <IconFile size="14" class="flex-shrink-0 text-slate-400" />
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-900">
                      {{ ga.document?.title }}
                    </p>
                    <p
                      v-if="ga.document?.reference_number"
                      class="text-xs text-slate-500"
                    >
                      {{ ga.document.reference_number }}
                    </p>
                  </div>
                </div>
                <div class="flex gap-1.5">
                  <!-- Tombol Lihat (selalu tampil jika ada official_attachment) -->
                  <a
                    v-if="ga.document?.official_attachment"
                    :href="route('documents.view', ga.document.slug)"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1 rounded border border-blue-200 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50"
                  >
                    <IconEye size="12" /> Lihat
                  </a>
                  <!-- Tombol Unduh (hanya untuk file, bukan link) -->
                  <a
                    v-if="ga.document?.official_attachment?.type === 'file'"
                    :href="route('documents.download', ga.document.slug)"
                    class="inline-flex items-center gap-1 rounded border border-green-200 px-2 py-1 text-xs text-green-600 hover:bg-green-50"
                  >
                    <IconDownload size="12" /> Unduh
                  </a>
                </div>
              </div>
            </div>

            <p
              v-else-if="!activeGuide?.description"
              class="text-sm text-slate-400"
            >
              Tidak ada konten tersedia.
            </p>
          </div>
        </div>
      </template>
    </Dialog>
  </AdminFormSection>
</template>
