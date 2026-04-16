<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  guides: { type: Array, default: () => [] },
  assetType: { type: String, default: null },
  assetId: { type: String, default: null },
})

const dialogVisible = ref(false)
const activeGuide = ref(null)

const openGuide = (guide) => {
  activeGuide.value = guide
  dialogVisible.value = true
}

const toggleAck = (guide) => {
  if (!props.assetId) return
  router.post(
    route('admin.assets.guides.acknowledge', {
      assetType: props.assetType,
      assetId: props.assetId,
      guideId: guide.id,
    }),
    {},
    { preserveScroll: true },
  )
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

    <div v-if="guides.length === 0" class="py-2 text-sm text-slate-400">
      Belum ada panduan referensi.
    </div>

    <ul v-else class="space-y-2">
      <li
        v-for="guide in guides"
        :key="guide.id"
        class="flex items-center gap-3"
      >
        <Checkbox
          :model-value="guide.acknowledged"
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
            <p
              v-if="activeGuide?.description"
              class="mb-4 text-sm leading-relaxed text-slate-600"
            >
              {{ activeGuide.description }}
            </p>

            <div
              v-if="activeGuide?.guide_attachments?.length"
              class="space-y-2"
            >
              <p
                class="text-xs font-semibold uppercase tracking-wider text-slate-400"
              >
                Lampiran
              </p>
              <a
                v-for="ga in activeGuide.guide_attachments"
                :key="ga.id"
                :href="ga.attachment?.url"
                target="_blank"
                rel="noopener"
                class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-blue-600 hover:bg-slate-100 hover:text-blue-800"
              >
                <IconExternalLink
                  v-if="ga.attachment?.type === 'link'"
                  size="14"
                  class="flex-shrink-0"
                />
                <IconPaperclip v-else size="14" class="flex-shrink-0" />
                <span class="truncate">{{
                  ga.attachment?.filename || ga.attachment?.url || 'Lampiran'
                }}</span>
              </a>
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
