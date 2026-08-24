import { fileURLToPath } from "node:url";

import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import { defineConfig, loadEnv, type Plugin } from "vite";

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
            "    VITE_SITE_URL=https://www.internationalff.com\n" +
            "  Ver .env.example.",
        );
      }
      if (!/^https?:\/\//.test(value)) {
        throw new Error(
          `[vite.config] VITE_SITE_URL debe ser una URL absoluta con esquema (http:// o https://). Valor actual: "${value}"`,
        );
      }
    },
  };
}

// SPA de una sola página (landing con anchors). El build estándar de Vite ya
// produce dist/index.html + dist/assets/ con rutas absolutas, que es exactamente
// lo que espera el hosting Apache/cPanel. No hace falta ningún paso extra.
export default defineConfig({
  plugins: [requireSiteUrl(), react(), tailwindcss()],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
});
