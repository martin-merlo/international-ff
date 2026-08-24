// @lovable.dev/vite-tanstack-config already includes the following — do NOT add them manually
// or the app will break with duplicate plugins:
//   - TanStack devtools (dev-only, first), tanstackStart, viteReact, tailwindcss, tsConfigPaths,
//     nitro (build-only using cloudflare as a default target), VITE_* env injection, @ path alias,
//     React/TanStack dedupe, error logger plugins, and sandbox detection (port/host/strictPort).
// You can pass additional config via defineConfig({ vite: { ... }, etc... }) if needed.
import { defineConfig } from "@lovable.dev/vite-tanstack-config";

// STATIC_BUILD=1 (lo setea `npm run build:static`) cambia únicamente el preset de Nitro
// a "node-server", para que .output/server/index.mjs sea ejecutable con node.
// scripts/build-static.mjs levanta ese server, crawlea el sitio y guarda cada ruta
// como HTML en dist/. No se usa el prerenderer de Nitro ni el modo SPA: el build es
// un SSR normal, idéntico al de `npm run build` salvo por el preset.
const isStaticBuild = process.env["STATIC_BUILD"] === "1";

export default defineConfig({
  tanstackStart: {
    // Redirect TanStack Start's bundled server entry to src/server.ts (our SSR error wrapper).
    // nitro/vite builds from this.
    server: { entry: "server" },
  },
  ...(isStaticBuild ? { nitro: { preset: "node-server" } } : {}),
});
