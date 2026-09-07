/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#0D4A36',
          50: '#E6F5EF',
          100: '#CDEBDE',
          200: '#A9D8C3',
          300: '#7FBFA5',
          400: '#2E7A58',
          500: '#0D4A36',
          600: '#083325',
          700: '#072A20',
          800: '#06231B',
          900: '#041A14'
        },
        secondary: {
          DEFAULT: '#C99738',
          50: '#FDF5E6',
          100: '#FBE9C7',
          200: '#F7D99B',
          300: '#EEC25F',
          400: '#D9A94A',
          500: '#C99738',
          600: '#A67A1E',
          700: '#7D5A10',
          800: '#5F4208',
          900: '#402C00'
        },
        gold: {
          DEFAULT: '#E0AD58',
          50: '#FFF9EC',
          200: '#FFE9B8',
          300: '#FFDEAA',
          400: '#F4CE6E',
          500: '#E0AD58',
          600: '#C99738',
          700: '#A67A1E'
        },
        canvas: {
          DEFAULT: '#E7FFF3',
          50: '#E7FFF3',
          100: '#DFFAED',
          200: '#D9F4E7',
          300: '#D4EEE2'
        },
        midnight: {
          DEFAULT: '#0E2B20',
          950: '#082018',
          900: '#0E2B20',
          800: '#1E352C',
          700: '#2A4436'
        },
        tertiary: {
          DEFAULT: '#1D3B53',
          50: '#EDF4FA',
          100: '#D8E7F3',
          200: '#B4CFE6',
          300: '#8FB3D6',
          400: '#2E5A7E',
          500: '#1D3B53',
          600: '#16304A',
          700: '#132A3D'
        },
        surface: {
          DEFAULT: '#FFFFFF',
          base: '#F8FAF9',
          low: '#DFFAED',
          container: '#D9F4E7',
          high: '#D4EEE2',
          highest: '#CEE9DC'
        },
        ink: {
          DEFAULT: '#082018',
          variant: '#404944',
          muted: '#4D655D'
        },
        outline: {
          DEFAULT: '#707974',
          variant: '#C0C9C2',
          soft: '#DCE4E0',
          strong: '#B6C5BF'
        },
        error: {
          DEFAULT: '#BA1A1A',
          container: '#FFDAD6',
          'on-container': '#93000A'
        },
        status: {
          success: { DEFAULT: '#127A52', bg: '#E6F5EF' },
          pending: { DEFAULT: '#A26E0B', bg: '#FDF5E6' },
          urgent: { DEFAULT: '#BA2525', bg: '#FDF0F0' },
          incoming: { DEFAULT: '#1D476E', bg: '#EDF4FA' }
        }
      },
      fontFamily: {
        display: ['"IBM Plex Sans"', '"Segoe UI"', 'sans-serif'],
        sans: ['"IBM Plex Sans"', '"Segoe UI"', 'sans-serif'],
        arabic: ['"IBM Plex Sans Arabic"', '"IBM Plex Sans"', '"Noto Naskh Arabic"', 'serif'],
        registry: ['"IBM Plex Sans"', '"Segoe UI"', 'sans-serif']
      },
      boxShadow: {
        glass: '0 1px 1px rgba(255,255,255,0.8) inset, 0 4px 16px 0 rgba(38, 49, 43, 0.08)',
        'glass-lg':
          '0 1px 1px rgba(255,255,255,0.8) inset, 0 12px 32px -20px rgba(38,49,43,0.18)',
        'glass-lg-dark':
          '0 1px 1px rgba(255,255,255,0.06) inset, 0 24px 70px -16px rgba(0,0,0,0.55), 0 8px 24px -8px rgba(0,0,0,0.4)',
        gold: '0 0 16px rgba(166,166,139,0.18)',
        'gold-glow': '0 0 18px rgba(111,129,116,0.20)',
        chip: '0 1px 1px rgba(255,255,255,0.7) inset, 0 4px 12px -6px rgba(38,49,43,0.12)',
        card: '0 20px 25px -5px rgba(13, 74, 54, 0.12), 0 8px 10px -6px rgba(13, 74, 54, 0.08)',
        'card-md': '0 10px 15px -3px rgba(13, 74, 54, 0.10), 0 4px 6px -4px rgba(13, 74, 54, 0.06)',
        field: '0 0 0 3px rgba(13, 74, 54, 0.12)'
      },
      borderRadius: {
        '3xl': '1.25rem'
      },
      transitionTimingFunction: {
        fluent: 'cubic-bezier(0.16, 1, 0.3, 1)',
        'fluent-soft': 'cubic-bezier(0.22, 0.61, 0.36, 1)'
      },
      keyframes: {
        floatY: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' }
        },
        gradientShift: {
          '0%, 100%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' }
        },
        blobDrift: {
          '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
          '33%': { transform: 'translate(3%, 4%) scale(1.06)' },
          '66%': { transform: 'translate(-2%, -3%) scale(0.97)' }
        },
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(24px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' }
        },
        scaleIn: {
          '0%': { opacity: '0', transform: 'scale(0.88)' },
          '100%': { opacity: '1', transform: 'scale(1)' }
        },
        sheen: {
          '0%, 100%': { transform: 'translateX(-140%) rotate(8deg)' },
          '50%': { transform: 'translateX(140%) rotate(8deg)' }
        },
        shimmer: {
        '0%': { transform: 'translateX(-100%)' },
        '100%': { transform: 'translateX(220%)' }
},
        patternDrift: {
          '0%': { transform: 'rotate(45deg) translate(0, 0)' },
          '100%': { transform: 'rotate(45deg) translate(64px, 64px)' }
        },
        patternDriftReverse: {
          '0%': { transform: 'rotate(-30deg) translate(0, 0)' },
          '100%': { transform: 'rotate(-30deg) translate(-96px, 48px)' }
        },
        pulseGlow: {
          '0%, 100%': { opacity: '0.55', transform: 'scale(1)' },
          '50%': { opacity: '1', transform: 'scale(1.06)' }
        }
      },
      animation: {
        floatY: 'floatY 6s ease-in-out infinite',
        gradientShift: 'gradientShift 16s ease infinite',
        blobDrift: 'blobDrift 22s ease-in-out infinite',
        fadeInUp: 'fadeInUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards',
        scaleIn: 'scaleIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
        sheen: 'sheen 7s ease-in-out infinite',
        shimmer: 'shimmer 2.4s ease-in-out infinite',
        patternDrift: 'patternDrift 40s linear infinite',
        patternDriftReverse: 'patternDriftReverse 55s linear infinite',
        pulseGlow: 'pulseGlow 3.4s ease-in-out infinite'
      }
    }
  },
  plugins: []
};
