import { defineConfig, type ViteDevServer } from 'vite';
import UnoCSS from 'unocss/vite'
import { resolve } from 'node:path';
import { readFile, readdir } from 'node:fs/promises';


const watched = ['templates', 'src', 'assets/ts'].map((d) => resolve(import.meta.dirname, d));
const watchedExt = /\.(twig|ts|php)$/;

const uno = UnoCSS();

function configureServer(server: ViteDevServer) {
    const ctx = (uno as any[]).find((p) => p?.api?.getContext)?.api.getContext();

    const reload = async (f: string) => {
        if (!watchedExt.test(f)) return;
        try {
            if (ctx) await ctx.extract(await readFile(f, 'utf-8'), f);
        } catch {}
        server.ws.send({ type: 'full-reload' });
    };
    server.watcher.add(watched);
    server.watcher.on('add', reload);
    server.watcher.on('change', reload);
    server.watcher.on('unlink', reload);
}

export default defineConfig(async () => {
  const controllerFiles = await readdir(resolve(import.meta.dirname, 'assets/ts/controllers'));
  const controllerInputs = Object.fromEntries(
    controllerFiles
      .filter((file) => file.endsWith('.ts'))
      .map((file) => [
        `controllers/${file.replace(/\.ts$/, '')}`,
        resolve(import.meta.dirname, 'assets/ts/controllers', file),
      ]),
  );

  return {
    root: './assets/ts',
    plugins: [uno, {name: 'twig-reload', configureServer}],
    build: {
    rolldownOptions: {
      input: {
        main: 'main.ts',
        admin: 'admin.ts',
        ...controllerInputs,
      },
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
    },
  };
});
