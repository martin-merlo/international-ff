import { fileURLToPath } from "node:url";

import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import { defineConfig, loadEnv, type Plugin } from "vite";

// El sitio NO se sirve desde la raíz del dominio: convive con otros sitios y cuelga
// de esta subcarpeta. Cambiar esto implica también VITE_SITE_URL y public/.htaccess
// (ver DEPLOY.md, sección "Ruta base").
const BASE = "/new/lovable/dist/";

// og:image / twitter:image necesitan URL absoluta y se arman con %VITE_SITE_URL%
// en index.html. Sin esa variable el sitio publicaría metadatos rotos, así que el
// build se corta acá. Solo aplica al build: `vite dev` arranca sin ella.
function requireSiteUrl(): Plugin {
  return {
    name: "require-site-url",
    apply: "build",
    config(_config, { mode }) {
      const value = loadEnv(mode, process.cwd(), "")["VITE_SITE_URL"]?.trim();
      if (!value) {
        throw new Error(
          "[vite.config] Falta VITE_SITE_URL.\n" +
            "  og:image y twitter:image necesitan una URL absoluta y se construyen con esa variable.\n" +
            "  Definila en .env con la base pública del sitio, sin barra final. Ejemplo:\n" +
            `    VITE_SITE_URL=https://www.internationalff.com${BASE.replace(/\/$/, "")}\n` +
            "  Ver .env.example.",
        );
      }
      if (!/^https?:\/\//.test(value)) {
        throw new Error(
          `[vite.config] VITE_SITE_URL debe ser una URL absoluta con esquema (http:// o https://). Valor actual: "${value}"`,
        );
      }
      // VITE_SITE_URL y base tienen que describir la misma ubicación: si no, og:image
      // apunta a un lado y los assets a otro, y el error solo se ve en producción.
      const expectedPath = BASE.replace(/\/$/, "");
      const actualPath = new URL(value).pathname.replace(/\/$/, "");
      if (actualPath !== expectedPath) {
        throw new Error(
          `[vite.config] VITE_SITE_URL y \`base\` no coinciden.\n` +
            `  base       = "${BASE}"  (esperaba que la URL termine en "${expectedPath || "/"}")\n` +
            `  VITE_SITE_URL = "${value}"  (su path es "${actualPath || "/"}")\n` +
            "  Corregí una de las dos: og:image quedaría apuntando a otra carpeta que los assets.",
        );
      }
    },
  };
}

// SPA de una sola página (landing con anchors). El build estándar de Vite produce
// dist/index.html + dist/assets/, con todas las rutas prefijadas por `base`.
export default defineConfig({
  base: BASE,
  plugins: [requireSiteUrl(), react(), tailwindcss()],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
});
