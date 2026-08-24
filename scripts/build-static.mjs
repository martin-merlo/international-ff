// Build 100% estático para hosting compartido Apache (cPanel).
// Uso: npm run build:static
// Resultado: carpeta dist/ con index.html, assets/ y .htaccess lista para subir.
//
// Estrategia: build SSR normal con preset "node-server" (STATIC_BUILD=1) ->
// se levanta .output/server/index.mjs en un puerto libre de loopback ->
// se crawlea el sitio desde "/" siguiendo solo links internos -> cada ruta se
// guarda como HTML en dist/ -> se copian los assets de .output/public -> .htaccess.
//
// Layout de salida (directory-index): "/" -> dist/index.html,
// "/servicios" -> dist/servicios/index.html. Es el layout que mejor funciona con
// Apache: DirectoryIndex sirve /servicios/ sin reglas extra, la URL queda limpia
// (sin ".html") y el fallback SPA solo actúa para rutas que no se prerenderizaron.
//
// Rutas semilla extra (por si alguna página no está linkeada desde el home):
//   STATIC_ROUTES="/gracias,/legal" npm run build:static
import { spawn, spawnSync } from "node:child_process";
import {
  createServer,
} from "node:net";
import {
  cpSync,
  existsSync,
  mkdirSync,
  rmSync,
  writeFileSync,
} from "node:fs";
import path from "node:path";
import process from "node:process";

const root = process.cwd();
const outputDir = path.join(root, ".output");
const outputPublic = path.join(outputDir, "public");
const serverEntry = path.join(outputDir, "server", "index.mjs");
const dist = path.join(root, "dist");

const log = (msg) => console.log(`[build:static] ${msg}`);
const fail = (msg) => {
  console.error(`[build:static] ${msg}`);
  process.exit(1);
};

// Extensiones que nunca son páginas navegables.
const NON_PAGE_EXT =
  /\.(png|jpe?g|gif|svg|webp|avif|ico|css|js|mjs|json|xml|txt|pdf|zip|mp4|webm|woff2?|ttf|eot|map)$/i;

// ---------------------------------------------------------------------------
// 1) Build SSR con preset node-server.
// ---------------------------------------------------------------------------
const viteBin = path.join(root, "node_modules", "vite", "bin", "vite.js");
const build = spawnSync(process.execPath, [viteBin, "build"], {
  stdio: "inherit",
  env: { ...process.env, STATIC_BUILD: "1" },
});
if (build.status !== 0) {
  process.exit(build.status ?? 1);
}

if (!existsSync(serverEntry)) {
  fail(
    `No se encontró ${serverEntry}. El preset "node-server" de Nitro no generó el server.`,
  );
}
if (!existsSync(outputPublic)) {
  fail(`No se encontró ${outputPublic}. El build no generó assets públicos.`);
}

// ---------------------------------------------------------------------------
// 2) Levantar el server en un puerto libre de loopback.
// ---------------------------------------------------------------------------
const port = await getFreePort();
const host = "127.0.0.1";
const origin = `http://${host}:${port}`;

log(`Levantando el server SSR en ${origin} ...`);
let serverExited = null;
const server = spawn(process.execPath, [serverEntry], {
  env: {
    ...process.env,
    PORT: String(port),
    NITRO_PORT: String(port),
    HOST: host,
    NITRO_HOST: host,
    NODE_ENV: "production",
  },
  stdio: ["ignore", "pipe", "pipe"],
});
const serverLog = [];
const collect = (chunk) => serverLog.push(chunk.toString());
server.stdout.on("data", collect);
server.stderr.on("data", collect);
server.on("exit", (code, signal) => {
  serverExited = { code, signal };
});

const stopServer = () => {
  if (!server.killed && serverExited === null) {
    server.kill();
  }
};
process.on("exit", stopServer);
process.on("SIGINT", () => {
  stopServer();
  process.exit(130);
});

try {
  await waitForServer();

  // -------------------------------------------------------------------------
  // 3) Crawl desde "/" siguiendo solo links internos del mismo origen.
  // -------------------------------------------------------------------------
  const extraRoutes = (process.env["STATIC_ROUTES"] ?? "")
    .split(",")
    .map((r) => r.trim())
    .filter(Boolean)
    .map(normalizeRoute);

  const queue = ["/", ...extraRoutes];
  const seen = new Set(queue);
  /** @type {Map<string, string>} route -> html */
  const pages = new Map();

  while (queue.length > 0) {
    const route = queue.shift();
    const res = await fetch(`${origin}${route}`, {
      headers: { accept: "text/html" },
    });

    if (!res.ok) {
      if (route === "/") {
        fail(`El server SSR respondió ${res.status} para "/". Abortando.`);
      }
      log(`Aviso: ${route} respondió ${res.status}, se omite.`);
      continue;
    }

    const contentType = res.headers.get("content-type") ?? "";
    if (!contentType.includes("text/html")) {
      log(`Aviso: ${route} no devolvió HTML (${contentType}), se omite.`);
      continue;
    }

    const html = await res.text();
    pages.set(route, html);
    log(`Renderizado ${route} (${html.length} bytes)`);

    for (const link of extractInternalLinks(html, route)) {
      if (!seen.has(link)) {
        seen.add(link);
        queue.push(link);
      }
    }
  }

  if (pages.size === 0) {
    fail("El crawl no produjo ninguna página.");
  }

  // -------------------------------------------------------------------------
  // 4) Escribir dist/: primero los assets, después el HTML renderizado.
  // -------------------------------------------------------------------------
  rmSync(dist, { recursive: true, force: true });
  cpSync(outputPublic, dist, { recursive: true });

  for (const [route, html] of pages) {
    const target =
      route === "/"
        ? path.join(dist, "index.html")
        : path.join(dist, ...route.slice(1).split("/"), "index.html");
    mkdirSync(path.dirname(target), { recursive: true });
    // 5f) Ninguna URL del HTML emitido debe apuntar al server local.
    writeFileSync(target, stripLocalOrigin(html));
    log(`Escrito ${path.relative(root, target)}`);
  }

  // Artefactos exclusivos de otros hostings.
  for (const file of ["_headers", "_routes.json", "_worker.js"]) {
    rmSync(path.join(dist, file), { recursive: true, force: true });
  }
} finally {
  stopServer();
}

// ---------------------------------------------------------------------------
// 5) .htaccess para Apache.
// ---------------------------------------------------------------------------
writeFileSync(
  path.join(dist, ".htaccess"),
  `DirectoryIndex index.html

<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /

  # Archivos y directorios reales se sirven tal cual.
  RewriteCond %{REQUEST_FILENAME} -f [OR]
  RewriteCond %{REQUEST_FILENAME} -d
  RewriteRule ^ - [L]

  # Rutas prerenderizadas: /ruta -> /ruta/index.html (sin redirect, URL limpia).
  RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI}/index.html -f
  RewriteRule ^(.*)$ /$1/index.html [L]

  # Cualquier otra ruta la resuelve el router en el cliente (fallback SPA).
  RewriteRule ^ /index.html [L]
</IfModule>

# Los assets llevan hash en el nombre: se pueden cachear de forma agresiva.
<IfModule mod_headers.c>
  <FilesMatch "\\.(js|mjs|css|woff2?|png|jpe?g|gif|svg|webp|avif|ico)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
  </FilesMatch>
  <FilesMatch "\\.html$">
    Header set Cache-Control "no-cache"
  </FilesMatch>
</IfModule>
`,
);

log(
  "Listo: dist/ contiene el HTML renderizado, assets/ y .htaccess. Subí el contenido de dist/ a tu hosting Apache.",
);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function getFreePort() {
  return new Promise((resolve, reject) => {
    const srv = createServer();
    srv.once("error", reject);
    srv.listen(0, "127.0.0.1", () => {
      const { port: p } = srv.address();
      srv.close(() => resolve(p));
    });
  });
}

async function waitForServer() {
  const deadline = Date.now() + 60_000;
  while (Date.now() < deadline) {
    if (serverExited !== null) {
      console.error(serverLog.join(""));
      fail(
        `El server SSR terminó antes de responder (code=${serverExited.code}, signal=${serverExited.signal}).`,
      );
    }
    try {
      const res = await fetch(`${origin}/`, { headers: { accept: "text/html" } });
      if (res.status < 500) {
        await res.arrayBuffer();
        return;
      }
    } catch {
      // todavía no está escuchando
    }
    await new Promise((r) => setTimeout(r, 250));
  }
  console.error(serverLog.join(""));
  stopServer();
  fail("Timeout esperando a que el server SSR responda.");
}

function normalizeRoute(href) {
  let route = href.split("#")[0].split("?")[0];
  if (!route.startsWith("/")) route = `/${route}`;
  if (route.length > 1 && route.endsWith("/")) route = route.slice(0, -1);
  return route;
}

function extractInternalLinks(html, fromRoute) {
  const links = new Set();
  const anchorRe = /<a\b[^>]*\shref\s*=\s*("([^"]*)"|'([^']*)')/gi;
  let match;
  while ((match = anchorRe.exec(html)) !== null) {
    const raw = (match[2] ?? match[3] ?? "").trim();
    if (!raw) continue;
    if (/^(mailto:|tel:|javascript:|data:|#)/i.test(raw)) continue;

    let url;
    try {
      url = new URL(raw, `${origin}${fromRoute}`);
    } catch {
      continue;
    }
    if (url.origin !== origin) continue;
    if (NON_PAGE_EXT.test(url.pathname)) continue;
    if (url.pathname.startsWith("/_")) continue; // /_build, /_serverFn, etc.

    links.add(normalizeRoute(url.pathname));
  }
  return links;
}

function stripLocalOrigin(html) {
  return html
    .replaceAll(`http://${host}:${port}`, "")
    .replaceAll(`http://localhost:${port}`, "");
}
