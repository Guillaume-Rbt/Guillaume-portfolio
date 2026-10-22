import { defineConfig, type ViteDevServer } from 'vite';
import UnoCSS from 'unocss/vite'
import { resolve } from 'node:path';


const templates = resolve(import.meta.dirname, 'templates');

function configureServer(server: ViteDevServer) {
    const reload = (f: string) => f.endsWith('.twig') && server.ws.send({ type: 'full-reload' });
    server.watcher.add(templates);
    server.watcher.on('add', reload);
    server.watcher.on('change', reload);
    server.watcher.on('unlink', reload);
}

export default defineConfig(


  {
    root: './assets',
    input: {
      main: 'main.ts'
    },
    plugins: [UnoCSS(), {name: 'twig-reload', configureServer}],
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