/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'media',
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
          DEFAULT: '#0B3D91',
          50: '#EAF0FB',
          100: '#C7D8F2',
          300: '#5B84C9',
          500: '#0B3D91',
          600: '#092F70',
          700: '#07234F',
          900: '#04152F'
        },
        secondary: {
          DEFAULT: '#1E6F5C',
          50: '#E7F3EF',
          300: '#5AA592',
          500: '#1E6F5C',
          700: '#134A3D'
        },
        gold: {
          DEFAULT: '#D4AF37',
          50: '#FBF6E7',
          200: '#EBD693',
          300: '#E2C876',
          400: '#D8BC55',
          500: '#D4AF37',
          600: '#B08F26',
          700: '#8A6F1D'
        },
        canvas: {
          50: '#F7F8FA',
          100: '#EEF1F6',
          200: '#E4E9F0'
        },
        midnight: {
          950: '#050B18',
          900: '#0A1428',
          800: '#0F1D3A',
          700: '#152A4D'
        }
      },
      fontFamily: {
        display: ['"Cormorant Garamond"', 'serif'],
        sans: ['Inter', '"Segoe UI"', 'sans-serif'],
        arabic: ['"Noto Naskh Arabic"', 'serif']
      },
      boxShadow: {
        glass: '0 1px 1px rgba(255,255,255,0.6) inset, 0 8px 32px 0 rgba(11, 61, 145, 0.12)',
        'glass-lg':
          '0 1px 1px rgba(255,255,255,0.7) inset, 0 -1px 1px rgba(11,61,145,0.04) inset, 0 24px 70px -16px rgba(11,61,145,0.32), 0 8px 24px -8px rgba(11,61,145,0.18)',
        'glass-lg-dark':
          '0 1px 1px rgba(255,255,255,0.06) inset, 0 24px 70px -16px rgba(0,0,0,0.55), 0 8px 24px -8px rgba(0,0,0,0.4)',
        gold: '0 0 24px rgba(212, 175, 55, 0.35)',
        'gold-glow': '0 0 32px rgba(212,175,55,0.45), 0 0 8px rgba(212,175,55,0.6)',
        chip: '0 1px 1px rgba(255,255,255,0.6) inset, 0 6px 18px -6px rgba(11,61,145,0.22)'
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
