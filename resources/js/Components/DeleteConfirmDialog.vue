<script setup>
/**
 * DeleteConfirmDialog — dialog konfirmasi hapus standar.
 *
 * Props:
 *   visible     — v-model (boolean) kontrol tampil/sembunyikan dialog
 *   entityLabel — label entitas yang dihapus, misal: "insiden ini" atau "artikel ini"
 *   deleteLabel — teks tombol konfirmasi hapus (default: "Ya, Hapus")
 *
 * Events:
 *   update:visible — untuk v-model:visible
 *   confirm        — dipanggil ketika user klik tombol hapus
 *
 * Slots:
 *   item-info — detail item yang akan dihapus (ditampilkan dalam box abu-abu)
 */
defineProps({
  visible: { type: Boolean, required: true },
  entityLabel: { type: String, default: 'data ini' },
  deleteLabel: { type: String, default: 'Ya, Hapus' },
})

defineEmits(['update:visible', 'confirm'])
</script>

<template>
  <Dialog
    :visible="visible"
    @update:visible="$emit('update:visible', $event)"
    :modal="true"
    :closable="false"
    class="w-full max-w-md"
  >
    <template #container="{ closeCallback }">
      <div
        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl"
      >
        <!-- Header merah -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
          <div class="flex items-center">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20"
            >
              <IconAlertTriangle class="text-white" />
            </div>
            <div class="ml-3">
              <h3 class="text-lg font-semibold text-white">
                Konfirmasi Penghapusan
              </h3>
              <p class="text-sm text-red-100">
                Tindakan ini tidak dapat dibatalkan
              </p>
            </div>
          </div>
        </div>

        <!-- Konten -->
        <div class="p-6">
          <div class="mb-6 text-center">
            <div
              class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50"
            >
              <IconTrash class="text-red-500" />
            </div>
            <p class="mb-3 text-slate-700">
              Apakah Anda yakin ingin menghapus {{ entityLabel }}?
            </p>
            <div
              class="rounded-lg border border-slate-100 bg-slate-50 p-3 text-left"
            >
              <slot name="item-info" />
            </div>
            <p class="mt-3 text-sm text-red-600">
              <strong>Peringatan:</strong> Data yang dihapus tidak dapat
              dikembalikan
            </p>
          </div>

          <!-- Tombol aksi -->
          <div class="flex items-center justify-end space-x-3">
            <Button
              @click="closeCallback"
              label="Batal"
              severity="secondary"
              size="small"
            />
            <Button
              @click="$emit('confirm')"
              :label="deleteLabel"
              severity="danger"
              size="small"
            />
          </div>
        </div>
      </div>
    </template>
  </Dialog>
</template>
