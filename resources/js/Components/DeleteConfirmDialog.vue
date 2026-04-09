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
      <div class="bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
        <!-- Header merah -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
              <IconAlertTriangle class="text-white" />
            </div>
            <div class="ml-3">
              <h3 class="text-lg font-semibold text-white">Konfirmasi Penghapusan</h3>
              <p class="text-red-100 text-sm">Tindakan ini tidak dapat dibatalkan</p>
            </div>
          </div>
        </div>

        <!-- Konten -->
        <div class="p-6">
          <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
              <IconTrash class="text-red-500" />
            </div>
            <p class="text-slate-700 mb-3">
              Apakah Anda yakin ingin menghapus {{ entityLabel }}?
            </p>
            <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-left">
              <slot name="item-info" />
            </div>
            <p class="text-sm text-red-600 mt-3">
              <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan
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
