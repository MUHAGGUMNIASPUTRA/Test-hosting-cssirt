/** @format */

import { definePreset } from "@primeuix/themes";
import Aura from "@primeuix/themes/aura";

const Noir = definePreset(Aura, {
  semantic: {
    primary: {
      50: "{blue.50}",
      100: "{blue.100}",
      200: "{blue.200}",
      300: "{blue.300}",
      400: "{blue.400}",
      500: "{blue.500}",
      600: "{blue.600}",
      700: "{blue.700}",
      800: "{blue.800}",
      900: "{blue.900}",
      950: "{blue.950}",
    },
    colorScheme: {
      light: {
        primary: {
          color: "{primary.600}",
          contrastColor: "#ffffff",
          hoverColor: "{primary.700}",
          activeColor: "{primary.800}",
        },
        highlight: {
          background: "{primary.700}",
          focusBackground: "{primary.700}",
          color: "#ffffff",
          focusColor: "#ffffff",
        },
      },
      dark: {
        primary: {
          color: "{primary.500}",
          contrastColor: "{primary.950}",
          hoverColor: "{primary.400}",
          activeColor: "{primary.300}",
        },
        highlight: {
          background: "{primary.300}",
          focusBackground: "{primary.300}",
          color: "{primary.950}",
          focusColor: "{primary.950}",
        },
        content: {
          hoverBackground: "{primary.800}",
          activeBackground: "{primary.700}",
        },
      },
    },
  },
  components: {
    datatable: {
      "column-title-font-weight": 900,
      // "column-title-font-size": "1.2rem",
      "header-cell-hover-background": "var(--p-primary-50)",
    },
  },
});

export default Noir;
