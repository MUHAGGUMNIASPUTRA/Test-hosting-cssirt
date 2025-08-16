import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      typography: ({ theme }) => ({
        DEFAULT: {
          css: {
            '--tw-prose-body': theme('colors.gray[700]'),
            '--tw-prose-headings': theme('colors.gray[900]'),
            '--tw-prose-lead': theme('colors.gray[600]'),
            '--tw-prose-links': theme('colors.blue[500]'),
            '--tw-prose-bold': theme('colors.gray[900]'),
            '--tw-prose-counters': theme('colors.gray[500]'),
            '--tw-prose-bullets': theme('colors.gray[800]'),
            '--tw-prose-hr': theme('colors.gray[200]'),
            '--tw-prose-quotes': theme('colors.gray[900]'),
            '--tw-prose-quote-borders': theme('colors.gray[200]'),
            '--tw-prose-captions': theme('colors.gray[500]'),
            '--tw-prose-code': theme('colors.gray[900]'),
            '--tw-prose-pre-code': theme('colors.gray[200]'),
            '--tw-prose-pre-bg': theme('colors.gray[800]'),
            '--tw-prose-th-borders': theme('colors.gray[300]'),
            '--tw-prose-td-borders': theme('colors.gray[200]'),

            // Custom styling for specific elements
            p: {
              marginTop: '1.5em',    // Make paragraph spacing wider
              marginBottom: '1.5em',
            },
            a: {
              textDecoration: 'underline',
              fontWeight: '600',
              '&:hover': {
                color: theme('colors.blue[800]'),
                textDecoration: 'none',
              },
            },
          },
        },
      }),
    },
    container: {
      center: true,
      padding: {
        DEFAULT: '2rem',
        sm: '3rem',
        lg: '4rem',
        xl: '5rem',
        '2xl': '6rem',
      },
    },
  },

  plugins: [
    forms,
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    function ({ addUtilities }) {
      const newUtilities = {
        '.icon-wght-100': { 'font-variation-settings': "'wght' 100" },
        '.icon-wght-200': { 'font-variation-settings': "'wght' 200" },
        '.icon-wght-300': { 'font-variation-settings': "'wght' 300" },
        '.icon-wght-400': { 'font-variation-settings': "'wght' 400" },
        '.icon-wght-500': { 'font-variation-settings': "'wght' 500" },
        '.icon-wght-600': { 'font-variation-settings': "'wght' 600" },
        '.icon-wght-700': { 'font-variation-settings': "'wght' 700" },
      };
      addUtilities(newUtilities, ['responsive', 'hover']); // Bisa juga responsif dan hover
    },
  ],

}
