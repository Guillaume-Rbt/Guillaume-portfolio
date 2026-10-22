import { defineConfig } from 'vite';
import UnoCSS from 'unocss/vite'
export default defineConfig(


  {
    root: './assets',
    input: {
      main: 'main.ts'
    },
    plugins: [UnoCSS()],
    build: {
    rolldownOptions: {
         output: {
        manualChunks: undefined
      },
    },
    manifest: true,
    outDir: '../public/assets',
  }
});