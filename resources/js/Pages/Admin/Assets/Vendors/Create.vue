<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  vendor: { type: Object, default: null },
})

const isEdit = computed(() => !!props.vendor)

const form = useForm({
  company_name: props.vendor?.company_name ?? '',
  location: props.vendor?.location ?? '',
  phone: props.vendor?.phone ?? '',
  email: props.vendor?.email ?? '',
  notes: props.vendor?.notes ?? '',
  pic_name: props.vendor?.pic_name ?? '',
  pic_phone: props.vendor?.pic_phone ?? '',
  pic_email: props.vendor?.pic_email ?? '',
})

const submit = () => {
  if (isEdit.value) {
    form.put(route('admin.vendors.update', props.vendor.id))
  } else {
    form.post(route('admin.vendors.store'))
  }
}
</script>

<template>
  <AdminLayout :title="isEdit ? 'Edit Vendor' : 'Tambah Vendor'">
    <div class="space-y-4">
      <AdminPageHeader
        :title="isEdit ? 'Edit Vendor' : 'Tambah Vendor'"
        description="Isi data vendor."
      />

      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <div class="space-y-4 lg:col-span-2">
            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">
                Informasi Perusahaan
              </h3>
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
                    rows="3"
                    placeholder="Catatan tentang vendor..."
                  />
                </div>
              </div>
            </div>

            <div
              class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"
            >
              <h3 class="mb-4 font-semibold text-slate-800">
                Penanggung Jawab (opsional)
              </h3>
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
            </div>
          </div>

          <div class="flex flex-col gap-3">
            <Link :href="route('admin.vendors.index')" class="block">
              <Button
                severity="secondary"
                variant="outlined"
                class="w-full"
                :disabled="form.processing"
                >Batal</Button
              >
            </Link>
            <Button type="submit" class="w-full" :loading="form.processing">
              {{ isEdit ? 'Simpan Perubahan' : 'Tambah Vendor' }}
            </Button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
