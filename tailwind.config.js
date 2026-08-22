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
          DEFAULT: '#6F8174',
          50: '#F2F5F3',
          100: '#E3EAE5',
          300: '#B4C2B8',
          500: '#6F8174',
          600: '#5F7165',
          700: '#4F6055',
          900: '#26312B'
        },
        secondary: {
          DEFAULT: '#84988A',
          50: '#F1F5F2',
          300: '#BFCBBF',
          500: '#84988A',
          700: '#687B6D'
        },
        gold: {
          DEFAULT: '#A6A68B',
          50: '#F7F7F1',
          200: '#E2E2D1',
          300: '#D3D3BB',
          400: '#BFC09F',
          500: '#A6A68B',
          600: '#8E8E75',
          700: '#73735E'
        },
        canvas: {
          50: '#F7F8FA',
          100: '#F1F3F2',
          200: '#E3E7E4'
        },
        midnight: {
          950: '#1E2822',
          900: '#26312B',
          800: '#334039',
          700: '#435148'
        }
      },
      fontFamily: {
        display: ['"Cormorant Garamond"', 'serif'],
        sans: ['Inter', '"Segoe UI"', 'sans-serif'],
        arabic: ['"Noto Naskh Arabic"', 'serif']
      },
      boxShadow: {
        glass: '0 1px 1px rgba(255,255,255,0.8) inset, 0 4px 16px 0 rgba(38, 49, 43, 0.08)',
        'glass-lg':
          '0 1px 1px rgba(255,255,255,0.8) inset, 0 12px 32px -20px rgba(38,49,43,0.18)',
        'glass-lg-dark':
          '0 1px 1px rgba(255,255,255,0.06) inset, 0 24px 70px -16px rgba(0,0,0,0.55), 0 8px 24px -8px rgba(0,0,0,0.4)',
        gold: '0 0 16px rgba(166,166,139,0.18)',
        'gold-glow': '0 0 18px rgba(111,129,116,0.20)',
        chip: '0 1px 1px rgba(255,255,255,0.7) inset, 0 4px 12px -6px rgba(38,49,43,0.12)'
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
