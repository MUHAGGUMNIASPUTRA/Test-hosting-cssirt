import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { useResponsive } from '@/Composables/useResponsive'

/**
 * useAdminTable — komposabel untuk server-side DataTable pada halaman admin.
 *
 * @param {import('vue').ComputedRef | import('vue').Ref} paginatedData
 *   Ref/ComputedRef ke object paginasi Laravel ({ data, total, per_page, current_page })
 *
 * @param {string} routeName
 *   Nama route Ziggy, misal: 'admin.incidents.index'
 *
 * @param {Record<string, import('vue').Ref<string>>} filterRefs
 *   Object berisi ref-ref filter, key = nama param URL.
 *   Contoh: { search: searchQuery, status: selectedStatus, priority: selectedPriority }
 *
 * @returns {{ serverSideConfig, applyFilters, onPage, clearFilters, hasActiveFilters }}
 */
export function useAdminTable(paginatedData, routeName, filterRefs = {}) {
  const { dtConfig } = useResponsive()

  const lazyParams = ref({
    first: 0,
    rows: paginatedData.value?.per_page ?? 10,
    page: paginatedData.value?.current_page ?? 1,
  })

  const buildUrl = (page = lazyParams.value.page) => {
    const params = new URLSearchParams()

    for (const [key, filterRef] of Object.entries(filterRefs)) {
      if (filterRef.value) params.set(key, filterRef.value)
    }

    if (page > 1) params.set('page', page)

    const qs = params.toString()
    return route(routeName) + (qs ? '?' + qs : '')
  }

  const navigate = (page = lazyParams.value.page) => {
    router.get(
      buildUrl(page),
      {},
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      },
    )
  }

  /** Dipanggil saat filter berubah — tetap di halaman saat ini (seperti perilaku asli) */
  const applyFilters = () => navigate()

  /** Dipanggil oleh event @page dari PrimeVue DataTable */
  const onPage = (event) => {
    lazyParams.value.first = event.first
    lazyParams.value.rows = event.rows
    lazyParams.value.page = Math.floor(event.first / event.rows) + 1
    navigate()
  }

  /** Reset semua filter dan kembali ke halaman 1 */
  const clearFilters = () => {
    for (const filterRef of Object.values(filterRefs)) {
      filterRef.value = ''
    }
    lazyParams.value.page = 1
    lazyParams.value.first = 0
    navigate(1)
  }

  /** Config yang di-bind ke PrimeVue <DataTable> */
  const serverSideConfig = computed(() => ({
    ...dtConfig(),
    lazy: true,
    totalRecords: paginatedData.value?.total,
    first:
      (paginatedData.value?.current_page - 1) * paginatedData.value?.per_page,
    rows: paginatedData.value?.per_page,
  }))

  /** true jika ada filter aktif */
  const hasActiveFilters = computed(() =>
    Object.values(filterRefs).some((f) => !!f.value),
  )

  return {
    lazyParams,
    serverSideConfig,
    applyFilters,
    onPage,
    clearFilters,
    hasActiveFilters,
  }
}
