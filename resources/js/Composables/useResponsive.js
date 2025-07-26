import { onMounted, onUnmounted, ref, computed } from "vue";

export function useResponsive() {
  const breakpoint = ref({
    mobile: false,
    tablet: false,
    desktop: false,
    isMobile: false,
    isTablet: false,
    isDesktop: false,
  });

  const updateBreakpoints = () => {
    const width = window.innerWidth;
    breakpoint.value = {
      mobile: width < 768,
      tablet: width >= 768 && width <= 1024,
      desktop: width > 1024,
      isMobile: width < 768,
      isTablet: width >= 768 && width <= 1024,
      isDesktop: width > 1024,
    };
  };

  onMounted(() => {
    updateBreakpoints();
    window.addEventListener("resize", updateBreakpoints);
  });

  onUnmounted(() => {
    window.removeEventListener("resize", updateBreakpoints);
  });

  // Helper functions for responsive configurations
  const dtConfig = () => ({
    size: "small",
    stripedRows: true,
    removableSort: true,
    sortMode: "multiple",
    paginator: true,
    rows: 10,
    rowsPerPageOptions: [5, 10, 20, 50],
    pageLinkSize: breakpoint.value.isMobile ? 1 : 5,
    paginatorTemplate: breakpoint.value.isMobile
      ? "RowsPerPageDropdown PrevPageLink PageLinks NextPageLink CurrentPageReport"
      : "RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport",
    currentPageReportTemplate: breakpoint.value.isMobile
      ? "{first}-{last} dari {totalRecords}"
      : "Data ke {first} - {last} dari total {totalRecords}",
    responsiveLayout: "scroll",
  });

  // Create computed reactive references for convenience
  const isMobile = computed(() => breakpoint.value.isMobile);
  const isTablet = computed(() => breakpoint.value.isTablet);
  const isDesktop = computed(() => breakpoint.value.isDesktop);

  return {
    breakpoint,
    isMobile,
    isTablet,
    isDesktop,
    dtConfig,
  };
}
