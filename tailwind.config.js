/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.php',
    './public/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#1B6B3A',
          600: '#166534',
          700: '#14532D',
          800: '#1B4332',
          900: '#0F2A1C',
        },
        gold: {
          50:  '#FFFBEB',
          100: '#FEF3C7',
          200: '#FDE68A',
          300: '#FCD34D',
          400: '#D4A017',
          500: '#B8860B',
          600: '#92400E',
          700: '#78350F',
        },
        burgundy: {
          50:  '#FEF2F2',
          100: '#FEE2E2',
          200: '#FECACA',
          300: '#FCA5A5',
          400: '#F87171',
          500: '#7B2D26',
          600: '#6B2120',
          700: '#571A19',
          800: '#451413',
          900: '#2D0D0C',
        },
        surface: {
          50:  '#FAF8F5',
          100: '#F5F0EB',
          200: '#EBE4DB',
          300: '#D9CFC3',
          400: '#C1B3A2',
        },
        ink: {
          DEFAULT: '#1A1A2E',
          light: '#3D3D56',
          muted: '#6B6B80',
          faint: '#9CA3AF',
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['Playfair Display', 'Georgia', 'serif'],
      },
      boxShadow: {
        'card': '0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.06)',
        'card-hover': '0 4px 12px 0 rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.06)',
        'nav': '0 1px 3px 0 rgb(0 0 0 / 0.1)',
      }
    }
  },
  plugins: []
}