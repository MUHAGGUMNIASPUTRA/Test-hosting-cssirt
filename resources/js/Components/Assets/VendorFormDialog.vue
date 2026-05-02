<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
  visible: { type: Boolean, required: true },
  vendor: { type: Object, default: null },
})

const emit = defineEmits(['update:visible', 'saved'])

const form = useForm({
  company_name: '',
  location: '',
  phone: '',
  email: '',
  notes: '',
  pic_name: '',
  pic_phone: '',
  pic_email: '',
})

watch(
  () => props.visible,
  (val) => {
    if (val) {
      const v = props.vendor
      form.company_name = v?.company_name ?? ''
      form.location = v?.location ?? ''
      form.phone = v?.phone ?? ''
      form.email = v?.email ?? ''
      form.notes = v?.notes ?? ''
      form.pic_name = v?.pic_name ?? ''
      form.pic_phone = v?.pic_phone ?? ''
      form.pic_email = v?.pic_email ?? ''
      form.clearErrors()
    }
  },
)

const close = () => {
  emit('update:visible', false)
  form.reset()
  form.clearErrors()
}

const submit = () => {
  const opts = {
    onSuccess: () => {
      close()
      emit('saved')
    },
  }
  if (props.vendor) {
    form.put(route('admin.vendors.update', props.vendor.id), opts)
  } else {
    form.post(route('admin.vendors.store'), opts)
  }
}
</script>

<template>
  <Dialog
    :visible="visible"
    :modal="true"
    :closable="false"
    class="w-full max-w-lg"
    @update:visible="$emit('update:visible', $event)"
  >
    <template #container>
      <div class="rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 p-5">
          <h3 class="text-lg font-semibold text-slate-900">
            {{ vendor ? 'Edit Vendor' : 'Tambah Vendor' }}
          </h3>
        </div>
        <form @submit.prevent="submit" class="p-5">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Nama Perusahaan <span class="text-red-500">*</span></label
              >
              <InputText
                v-model="form.company_name"
                class="w-full"
                placeholder="PT. ..."
                required
              />
              <p
                v-if="form.errors.company_name"
                class="mt-1 text-xs text-red-600"
              >
                {{ form.errors.company_name }}
              </p>
            </div>
            <div class="sm:col-span-2">
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Lokasi</label
              >
              <InputText
                v-model="form.location"
                class="w-full"
                placeholder="Kota / Alamat singkat"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Telepon</label
              >
              <InputText
                v-model="form.phone"
                class="w-full"
                placeholder="08xx..."
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Email</label
              >
              <InputText
                v-model="form.email"
                class="w-full"
                placeholder="email@vendor.com"
                type="email"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Catatan</label
              >
              <Textarea
                v-model="form.notes"
                class="w-full"
                rows="2"
                placeholder="Catatan tentang vendor..."
              />
            </div>

            <p
              class="text-xs font-semibold uppercase tracking-wider text-slate-400 sm:col-span-2"
            >
              Penanggung Jawab (Opsional)
            </p>

            <div class="sm:col-span-2">
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Nama PIC</label
              >
              <InputText
                v-model="form.pic_name"
                class="w-full"
                placeholder="Nama penanggung jawab"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Telepon PIC</label
              >
              <InputText
                v-model="form.pic_phone"
                class="w-full"
                placeholder="08xx..."
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700"
                >Email PIC</label
              >
              <InputText
                v-model="form.pic_email"
                class="w-full"
                placeholder="pic@vendor.com"
                type="email"
              />
            </div>
          </div>
          <div
            class="mt-4 flex justify-end gap-3 border-t border-slate-100 pt-4"
          >
            <Button
              type="button"
              severity="secondary"
              variant="outlined"
              :disabled="form.processing"
              @click="close"
              >Batal</Button
            >
            <Button type="submit" :loading="form.processing">{{
              vendor ? 'Simpan' : 'Tambah'
            }}</Button>
          </div>
        </form>
      </div>
    </template>
  </Dialog>
</template>
