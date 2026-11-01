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
    root: './assets/ts',
    input: {
      main: 'main.ts',
      admin: 'admin.ts'
    },
    plugins: [UnoCSS(), {name: 'twig-reload', configureServer}],
    build: {
    rolldownOptions: {
         output: {
          cleanDir: true,
          entryFileNames: 'js/[name].js',
          chunkFileNames: (chunk)=> { return `js/${chunk.name}.js`; },
          assetFileNames: (asset) => {
          if (asset.names?.[0]?.endsWith('.css')) {
            return 'css/[name][extname]';
          }

          return 'media/[name]-[hash][extname]';
        }, 
          codeSplitting: {
      groups: [
        {
          test: /[/\\]ts[/\\]/,
          name: (moduleId) =>
{



            const path = moduleId.split(/[/\\]/);

            if(path[path.length - 1] === "__uno.css")
            {
              return 'app.css'
            }

            const finalpath = path.slice(-2).map((segment) => segment.replace(/^ts$/, 'js').replace(/\.tsx?$/, '')).join('/');


            //@ts-ignore
            return finalpath;
        }
      }
      ]
    }
      },
      
    },
    assetsDir: "",
    emptyOutDir: true,
    manifest: true,
    outDir: '../../public/dist',
  }
});