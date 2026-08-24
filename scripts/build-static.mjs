// Build 100% estático para hosting compartido Apache (cPanel).
// Uso: npm run build:static
// Resultado: carpeta dist/ con index.html, assets/ y .htaccess lista para subir.
import { spawnSync } from "node:child_process";
import {
  copyFileSync,
  cpSync,
  existsSync,
  rmSync,
  writeFileSync,
} from "node:fs";
import path from "node:path";

const root = process.cwd();
const outputPublic = path.join(root, ".output", "public");
const dist = path.join(root, "dist");

// 1) Build con STATIC_BUILD=1: activa SPA + prerender + preset "static" de Nitro.
const viteBin = path.join(root, "node_modules", "vite", "bin", "vite.js");
const build = spawnSync(process.execPath, [viteBin, "build"], {
  stdio: "inherit",
  env: { ...process.env, STATIC_BUILD: "1" },
});
if (build.status !== 0) {
  process.exit(build.status ?? 1);
}

// 2) Copiar la salida pública de Nitro a dist/.
if (!existsSync(outputPublic)) {
  console.error(
    `[build:static] No se encontró ${outputPublic}. El preset static de Nitro no generó salida pública.`,
  );
  process.exit(1);
}
rmSync(dist, { recursive: true, force: true });
cpSync(outputPublic, dist, { recursive: true });

// 3) Garantizar dist/index.html (copiándolo de _shell.html si hace falta).
const indexHtml = path.join(dist, "index.html");
if (!existsSync(indexHtml)) {
  const shell = path.join(dist, "_shell.html");
  if (!existsSync(shell)) {
    console.error(
      "[build:static] No se generó index.html ni _shell.html en la salida estática.",
    );
    process.exit(1);
  }
  copyFileSync(shell, indexHtml);
  console.log("[build:static] index.html creado a partir de _shell.html");
}

// 4) Eliminar artefactos exclusivos de Cloudflare.
for (const file of ["_headers", "_routes.json"]) {
  rmSync(path.join(dist, file), { force: true });
}

// 5) Generar .htaccess con reglas de rewrite para SPA en Apache.
writeFileSync(
  path.join(dist, ".htaccess"),
  `<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>
`,
);

console.log(
  "[build:static] Listo: dist/ contiene index.html, assets/ y .htaccess. Subí el contenido de dist/ a tu hosting Apache.",
);
