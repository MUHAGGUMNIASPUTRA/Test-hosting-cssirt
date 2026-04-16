<script setup>
import { formatDatetime } from '@/utils/date'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  webApplication: Object,
  guides: { type: Array, default: () => [] },
})

const wa = props.webApplication

const securityData = computed(() => ({
  confidentiality: wa?.security_classification?.confidentiality ?? null,
  integrity: wa?.security_classification?.integrity ?? null,
  availability: wa?.security_classification?.availability ?? null,
}))

const stageSeverity = (val) =>
  ({
    draft: 'secondary',
    pengajuan: 'info',
    pengujian: 'warn',
    revisi: 'danger',
    persiapan: 'warn',
    diterima: 'success',
  })[val] ?? 'secondary'

const appStatusSeverity = (val) =>
  ({ aktif: 'success', idle: 'warn', nonaktif: 'secondary' })[val] ??
  'secondary'

const httpsSeverity = (val) =>
  ({ aktif: 'success', expired: 'danger', nonaktif: 'secondary' })[val] ??
  'secondary'

const dangerLevelSeverity = (val) =>
  ({ bahaya: 'danger', peringatan: 'warn', aman: 'success' })[val] ??
  'secondary'

const showDeleteDialog = ref(false)
const handleDelete = () => {
  router.delete(route('admin.web-applications.destroy', wa.id), {
    onSuccess: () => router.visit(route('admin.web-applications.index')),
  })
}
</script>

<template>
  <AdminLayout :title="`Detail: ${wa.name}`">
    <DeleteConfirmDialog
      v-model:visible="showDeleteDialog"
      entity-label="Aplikasi Web"
      @confirm="handleDelete"
    >
      <template #item-info>{{ wa.name }}</template>
    </DeleteConfirmDialog>

    <div class="space-y-4">
      <AdminFormHeader
        :title="wa.name"
        :description="wa.description"
        back-route="admin.web-applications.index"
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
          <Link :href="route('admin.web-applications.edit', wa.id)">
            <button
              class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
            >
              <IconEdit size="16" />
              Edit
            </button>
          </Link>
        </template>
      </AdminFormHeader>

      <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="space-y-4 xl:col-span-2">
          <!-- Status & Kondisi -->
          <AdminFormSection
            title="Status & Kondisi"
            description="Tahap, status aplikasi, dan HTTPS"
            color="orange"
          >
            <template #icon="{ iconClass }">
              <IconActivity :class="iconClass" />
            </template>
            <div class="flex flex-wrap gap-4">
              <div>
                <p class="mb-1 text-xs text-slate-500">Tahap</p>
                <Tag
                  :value="wa.stage"
                  :severity="stageSeverity(wa.stage)"
                  class="capitalize"
                />
              </div>
              <div>
                <p class="mb-1 text-xs text-slate-500">Status App</p>
                <Tag
                  :value="wa.app_status"
                  :severity="appStatusSeverity(wa.app_status)"
                  class="capitalize"
                />
              </div>
              <div>
                <p class="mb-1 text-xs text-slate-500">HTTPS</p>
                <Tag
                  :value="wa.https_status"
                  :severity="httpsSeverity(wa.https_status)"
                  class="capitalize"
                />
              </div>
            </div>
          </AdminFormSection>

          <!-- Penempatan & Kepemilikan -->
          <AdminFormSection
            title="Penempatan & Kepemilikan"
            description="Lokasi, organisasi, dan kontak"
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
                  {{ wa.location?.name ?? '—' }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-xs font-medium uppercase tracking-wider text-slate-400"
                >
                  Penyedia Aset
                </dt>
                <dd class="mt-1 text-sm text-slate-700">
                  {{ wa.provider_org?.name ?? '—' }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-xs font-medium uppercase tracking-wider text-slate-400"
                >
                  Pemilik Aset
                </dt>
                <dd class="mt-1 text-sm text-slate-700">
                  {{ wa.owner_org?.name ?? '—' }}
                </dd>
              </div>
              <div>
                <dt
                  class="text-xs font-medium uppercase tracking-wider text-slate-400"
                >
                  Vendor
                </dt>
                <dd class="mt-1 text-sm text-slate-700">
                  {{ wa.vendor?.company_name ?? '—' }}
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
                      wa.owner_contact_type === 'manual' && wa.owner_employee
                    "
                  >
                    {{ wa.owner_employee.name }}
                  </span>
                  <span v-else-if="wa.owner_org?.it_contact_name">
                    {{ wa.owner_org.it_contact_name
                    }}<span v-if="wa.owner_org.it_contact_phone">
                      · {{ wa.owner_org.it_contact_phone }}</span
                    >
                  </span>
                  <span v-else>—</span>
                </dd>
              </div>
            </dl>
          </AdminFormSection>

          <!-- VM Specs -->
          <AdminFormSection
            v-if="wa.vms?.length"
            title="Spesifikasi VM"
            description="Detail virtual machine"
            color="slate"
          >
            <template #icon="{ iconClass }">
              <IconServer :class="iconClass" />
            </template>
            <div class="space-y-2">
              <div
                v-for="(vm, i) in wa.vms"
                :key="i"
                class="grid grid-cols-3 gap-2 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"
              >
                <div>
                  <p class="text-xs text-slate-400">Prosesor</p>
                  <p class="font-medium text-slate-700">
                    {{ vm.processor || '—' }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-slate-400">RAM</p>
                  <p class="font-medium text-slate-700">{{ vm.ram || '—' }}</p>
                </div>
                <div>
                  <p class="text-xs text-slate-400">HDD</p>
                  <p class="font-medium text-slate-700">{{ vm.hdd || '—' }}</p>
                </div>
              </div>
            </div>
          </AdminFormSection>

          <!-- Network Specs -->
          <AdminFormSection
            v-if="wa.networks?.length"
            title="Spesifikasi Jaringan"
            description="Konfigurasi jaringan"
            color="cyan"
          >
            <template #icon="{ iconClass }">
              <IconNetwork :class="iconClass" />
            </template>
            <div class="space-y-2">
              <div
                v-for="(net, i) in wa.networks"
                :key="i"
                class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"
              >
                <p class="mb-1 font-medium text-slate-700">
                  {{ net.environment || `Jaringan ${i + 1}` }}
                </p>
                <div class="grid grid-cols-3 gap-2 text-xs">
                  <div>
                    <p class="text-slate-400">DNS</p>
                    <p class="text-slate-600">{{ net.dns || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400">IP Lokal</p>
                    <p class="text-slate-600">{{ net.local_ip || '—' }}</p>
                  </div>
                  <div>
                    <p class="text-slate-400">IP Publik</p>
                    <p class="text-slate-600">{{ net.public_ip || '—' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </AdminFormSection>

          <!-- Tech Stack -->
          <AdminFormSection
            title="Tech Stack"
            description="Framework dan teknologi yang digunakan"
            color="indigo"
          >
            <template #icon="{ iconClass }">
              <IconCode :class="iconClass" />
            </template>
            <p
              v-if="!wa.tech_stacks?.length"
              class="py-2 text-sm text-slate-400"
            >
              Belum ada tech stack terdaftar.
            </p>
            <div v-else class="flex flex-wrap gap-2">
              <div
                v-for="ts in wa.tech_stacks"
                :key="ts.tech_stack_id"
                class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm"
              >
                <span class="font-medium text-slate-700">{{
                  ts.tech_stack?.name
                }}</span>
                <span v-if="ts.version" class="text-xs text-slate-400"
                  >v{{ ts.version }}</span
                >
              </div>
            </div>
          </AdminFormSection>
        </div>

        <div class="flex flex-col gap-4">
          <!-- Klasifikasi Keamanan + Notes -->
          <SecurityClassificationForm
            :model-value="securityData"
            :readonly-scores="true"
            asset-type="web-application"
            :asset-id="wa.id"
            :security-notes="wa.security_notes ?? []"
          />

          <!-- Panduan Referensi -->
          <GuideReferenceSection
            :guides="guides"
            asset-type="web-application"
            :asset-id="wa.id"
          />

          <!-- Audit Log -->
          <AdminFormSection
            v-if="wa.audit_logs?.length"
            title="Audit Log"
            description="Riwayat perubahan aset"
            color="slate"
          >
            <template #icon="{ iconClass }">
              <IconClipboardList :class="iconClass" />
            </template>
            <div class="space-y-2">
              <div
                v-for="log in wa.audit_logs"
                :key="log.id"
                class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"
              >
                <div class="mb-1 flex items-center justify-between">
                  <span class="font-medium text-slate-700">{{
                    log.user?.name ?? '—'
                  }}</span>
                  <Tag
                    v-if="log.danger_level"
                    :value="log.danger_level"
                    :severity="dangerLevelSeverity(log.danger_level)"
                    class="!text-xs capitalize"
                  />
                </div>
                <p class="text-slate-600">{{ log.message }}</p>
                <p class="mt-1 text-xs text-slate-400">
                  {{ formatDatetime(log.created_at) }}
                </p>
              </div>
            </div>
          </AdminFormSection>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
