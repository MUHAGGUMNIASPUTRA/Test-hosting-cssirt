<!-- Tujuan: Halaman detail Aset Fisik (readonly) -->
<!-- Caller: PhysicalAssetController@show -->
<!-- Side Effects: none -->
<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  physicalAsset: { type: Object, required: true },
})
const asset = props.physicalAsset

const showDeleteDialog = ref(false)
const handleDelete = () => {
  router.delete(route('admin.physical-assets.destroy', asset.id), {
    onSuccess: () => router.visit(route('admin.physical-assets.index')),
  })
}
</script>

<template>
  <AdminLayout :title="`Detail: ${asset.name}`">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aset Fisik"
      @confirm="handleDelete"
    >
      <template #item-info>{{ asset.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminFormHeader
        :title="asset.name"
        :description="asset.description"
        back-route="admin.physical-assets.index"
        :processing="false"
      >
        <template #actions>
          <button
            class="inline-flex items-center justify-center gap-2 rounded-md border border-red-200 px-4 py-2 text-red-600 transition hover:bg-red-50"
            @click="showDeleteDialog = true"
          >
            <IconTrash size="16" />
            Hapus
          </button>
          <a :href="route('admin.physical-assets.edit', asset.id)">
            <button
              class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
            >
              <IconEdit size="16" />
              Edit
            </button>
          </a>
        </template>
      </AdminFormHeader>

      <!-- Informasi Aset -->
      <AdminFormSection
        title="Informasi Aset Fisik"
        description="Detail perangkat keras"
        color="slate"
      >
        <template #icon="{ iconClass }">
          <IconServer :class="iconClass" />
        </template>
        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Kode Aset
            </dt>
            <dd class="mt-1">
              <Tag :value="asset.asset_code" severity="secondary" />
            </dd>
          </div>
          <div>
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Tahun Pengadaan
            </dt>
            <dd class="mt-1 text-sm text-slate-700">{{ asset.year ?? '—' }}</dd>
          </div>
          <div class="sm:col-span-2">
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Nama Aset
            </dt>
            <dd class="mt-1 text-sm font-medium text-slate-700">
              {{ asset.name }}
            </dd>
          </div>
          <div class="sm:col-span-2">
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Deskripsi
            </dt>
            <dd class="mt-1 text-sm text-slate-700">
              {{ asset.description || '—' }}
            </dd>
          </div>
          <div class="sm:col-span-2">
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Spesifikasi
            </dt>
            <dd class="mt-1 whitespace-pre-line text-sm text-slate-700">
              {{ asset.specifications || '—' }}
            </dd>
          </div>
        </dl>
      </AdminFormSection>

      <!-- Lampiran -->
      <AdminFormSection
        v-if="asset.attachment"
        title="Lampiran"
        description="Dokumen terkait aset"
        color="blue"
      >
        <template #icon="{ iconClass }">
          <IconPaperclip :class="iconClass" />
        </template>
        <div v-if="asset.attachment.type === 'link'">
          <a
            :href="asset.attachment.url"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:underline"
          >
            <IconExternalLink size="14" />
            {{ asset.attachment.url }}
          </a>
        </div>
        <div v-else class="flex items-center gap-3">
          <IconFile size="20" class="text-slate-400" />
          <div>
            <p class="text-sm font-medium text-slate-700">
              {{ asset.attachment.filename }}
            </p>
            <p class="text-xs text-slate-400">
              {{ asset.attachment.file_size }}
            </p>
          </div>
          <a
            v-if="asset.attachment.url"
            :href="asset.attachment.url"
            target="_blank"
            class="ml-auto text-sm text-blue-600 hover:underline"
            >Unduh</a
          >
        </div>
      </AdminFormSection>

      <!-- Penempatan & Kepemilikan -->
      <AdminFormSection
        title="Penempatan & Kepemilikan"
        description="Lokasi dan pemilik aset"
        color="green"
      >
        <template #icon="{ iconClass }">
          <IconBuilding :class="iconClass" />
        </template>
        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Lokasi
            </dt>
            <dd class="mt-1 text-sm text-slate-700">
              {{ asset.location?.name ?? '—' }}
            </dd>
          </div>
          <div>
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Pemilik Aset
            </dt>
            <dd class="mt-1 text-sm text-slate-700">
              {{ asset.owner_org?.name ?? '—' }}
            </dd>
          </div>
          <div class="sm:col-span-2">
            <dt
              class="text-xs font-medium uppercase tracking-wider text-slate-400"
            >
              Kontak PJ
            </dt>
            <dd class="mt-1 text-sm text-slate-700">
              <span
                v-if="
                  asset.owner_contact_type === 'manual' && asset.owner_employee
                "
              >
                {{ asset.owner_employee.name }}
              </span>
              <span v-else-if="asset.owner_org?.it_contact_name">
                {{ asset.owner_org.it_contact_name
                }}<span v-if="asset.owner_org.it_contact_phone">
                  · {{ asset.owner_org.it_contact_phone }}</span
                >
              </span>
              <span v-else>—</span>
            </dd>
          </div>
        </dl>
      </AdminFormSection>
    </div>
  </AdminLayout>
</template>
