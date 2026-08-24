// @lovable.dev/vite-tanstack-config already includes the following — do NOT add them manually
// or the app will break with duplicate plugins:
//   - TanStack devtools (dev-only, first), tanstackStart, viteReact, tailwindcss, tsConfigPaths,
//     nitro (build-only using cloudflare as a default target), VITE_* env injection, @ path alias,
//     React/TanStack dedupe, error logger plugins, and sandbox detection (port/host/strictPort).
// You can pass additional config via defineConfig({ vite: { ... }, etc... }) if needed.
import { defineConfig } from "@lovable.dev/vite-tanstack-config";

// STATIC_BUILD=1 (set by `npm run build:static`) switches the build to a 100%
// static output for shared Apache/cPanel hosting: SPA mode with prerendered
// pages and Nitro's "static" preset (no Cloudflare worker, no wrangler.json).
const isStaticBuild = process.env["STATIC_BUILD"] === "1";

export default defineConfig({
  tanstackStart: {
    // Redirect TanStack Start's bundled server entry to src/server.ts (our SSR error wrapper).
    // nitro/vite builds from this
    server: { entry: "server" },
    ...(isStaticBuild
      ? {
          spa: {
            enabled: true,
            prerender: { enabled: true, crawlLinks: true, retryCount: 3 },
          },
        }
      : {}),
  },
  ...(isStaticBuild ? { nitro: { preset: "static" } } : {}),
});
