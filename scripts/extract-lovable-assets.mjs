// Script de UN SOLO USO: baja a public/images/ las imágenes que vivían en la
// infraestructura del proveedor original (Lovable) y que no estaban en el repo.
//
//   # assets de los manifiestos src/assets/*.asset.json (vía proxy del dev server)
//   node scripts/extract-lovable-assets.mjs --preview-host <host-de-preview>
//
//   # imagen suelta por URL absoluta (fetch directo, sin dev server)
//   node scripts/extract-lovable-assets.mjs --og <url-absoluta> --og-name og-preview.png
//
// A propósito NO está enganchado a ningún script de package.json: no debe correr
// nunca durante un build. Ya se ejecutó y los binarios están commiteados; queda en
// el repo como registro de cómo se obtuvieron.
//
// Ninguna URL ni host del proveedor está hardcodeado acá: se pasan por parámetro.
// Así el repo no conserva referencias a infraestructura externa.
//
// Los dos caminos comparten la misma validación: content-type de imagen, magic
// bytes de un formato conocido y, si el manifiesto lo declara, tamaño exacto.
//
// Camino 1 (manifiestos): los .asset.json apuntaban a una ruta interna que solo
// resuelve la infra del proveedor. El plugin de dev de
// @lovable.dev/vite-tanstack-config proxea esa ruta al preview host del proyecto,
// pero solo si LOVABLE_PREVIEW_HOST está definida (si no es un no-op y el dev
// server devuelve un 404 HTML). El script levanta el dev server en un puerto libre
// con esa variable seteada y pide los assets a través del proxy. Los manifiestos que
// ya apuntan a /images/ se saltean: están extraídos.
import { spawn } from "node:child_process";
import { createServer } from "node:net";
import { mkdirSync, readdirSync, readFileSync, writeFileSync } from "node:fs";
import path from "node:path";
import process from "node:process";

const root = process.cwd();
const assetsDir = path.join(root, "src", "assets");
const outDir = path.join(root, "public", "images");

// Nombre de archivo destino (kebab-case, descriptivo) por cada .asset.json.
const TARGET_NAMES = {
  "logo-iff.png.asset.json": "logo-iff.png",
  "op-ccni.png.asset.json": "logo-ccni.png",
  "op-dhl.png.asset.json": "logo-dhl.png",
  "op-itl.png.asset.json": "logo-itl.png",
  "op-msc.png.asset.json": "logo-msc.png",
  "op-somarco.png.asset.json": "logo-somarco.png",
  "op-ups.png.asset.json": "logo-ups.png",
};

// Firmas de formatos de imagen aceptados.
const MAGIC = [
  { ext: "png", bytes: [0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a] },
  { ext: "jpg", bytes: [0xff, 0xd8, 0xff] },
  { ext: "gif", bytes: [0x47, 0x49, 0x46, 0x38] },
  { ext: "webp", bytes: [0x52, 0x49, 0x46, 0x46] }, // RIFF....WEBP
];

const log = (msg) => console.log(`[extract-assets] ${msg}`);
const fail = (msg) => {
  console.error(`[extract-assets] ERROR: ${msg}`);
  process.exit(1);
};

const args = parseArgs(process.argv.slice(2));
const failures = [];

// ---------------------------------------------------------------------------
// Camino 2: imagen suelta por URL absoluta (fetch directo).
// ---------------------------------------------------------------------------
if (args.og) {
  const targetName = args["og-name"] ?? path.basename(new URL(args.og).pathname);
  if (!targetName || targetName === "/") {
    fail("No pude derivar un nombre de archivo desde --og. Pasá --og-name <archivo>.");
  }
  mkdirSync(outDir, { recursive: true });
  log(`Bajando ${args.og}`);
  const result = await downloadImage(args.og);
  if (result.error) {
    failures.push(`--og → ${result.error}`);
  } else {
    writeFileSync(path.join(outDir, targetName), result.bytes);
    log(
      `OK  → public/images/${targetName} (${result.bytes.length} bytes, ${result.format})`,
    );
  }
}

// ---------------------------------------------------------------------------
// Camino 1: assets de los manifiestos, vía proxy del dev server.
// ---------------------------------------------------------------------------
const manifests = readdirSync(assetsDir)
  .filter((f) => f.endsWith(".asset.json"))
  .map((file) => {
    const json = JSON.parse(readFileSync(path.join(assetsDir, file), "utf8"));
    const target = TARGET_NAMES[file];
    if (!target) {
      fail(
        `${file} no tiene nombre destino en TARGET_NAMES. Agregalo antes de correr el script.`,
      );
    }
    if (!json.url?.startsWith("/")) {
      fail(`${file} no tiene una url interna válida (url = ${json.url}).`);
    }
    return { file, target, json };
  });

const pending = manifests.filter((m) => !m.json.url.startsWith("/images/"));

if (pending.length === 0) {
  if (manifests.length > 0) {
    log(
      `Los ${manifests.length} assets de los manifiestos ya apuntan a /images/: nada que extraer.`,
    );
  }
} else {
  const previewHost =
    args["preview-host"]?.trim() || process.env["LOVABLE_PREVIEW_HOST"]?.trim();
  if (!previewHost) {
    fail(
      `Hay ${pending.length} asset(s) sin extraer pero no sé a qué preview host pedirlos. ` +
        "Pasá --preview-host <host sin esquema> (o la env LOVABLE_PREVIEW_HOST).",
    );
  }
  await extractViaDevServer(pending, previewHost);
}

// ---------------------------------------------------------------------------
// Resultado.
// ---------------------------------------------------------------------------
if (failures.length > 0) {
  console.error(`\n[extract-assets] Fallaron ${failures.length} asset(s):`);
  for (const f of failures) console.error(`  - ${f}`);
  console.error("\nNo se actualizó nada de lo que falló. Revisá la URL o el preview host.");
  process.exit(1);
}

log("Listo. Acordate de apuntar los consumidores a /images/<nombre>.");
process.exit(0);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function parseArgs(argv) {
  const out = {};
  for (let i = 0; i < argv.length; i++) {
    const token = argv[i];
    if (!token.startsWith("--")) continue;
    const key = token.slice(2);
    const next = argv[i + 1];
    if (next === undefined || next.startsWith("--")) {
      fail(`El parámetro --${key} necesita un valor.`);
    }
    out[key] = next;
    i++;
  }
  return out;
}

// Descarga + validación compartida por los dos caminos.
async function downloadImage(url, { expectedSize } = {}) {
  let res;
  try {
    res = await fetch(url, { headers: { accept: "image/*" } });
  } catch (err) {
    return { error: `${url} falló: ${err.message}` };
  }
  if (!res.ok) return { error: `${url} respondió ${res.status}` };

  const contentType = res.headers.get("content-type") ?? "";
  if (!contentType.startsWith("image/")) {
    return {
      error:
        `${url} devolvió "${contentType}" en vez de una imagen ` +
        "(probablemente una página de error, no el binario)",
    };
  }

  const bytes = Buffer.from(await res.arrayBuffer());
  const format = MAGIC.find((m) => m.bytes.every((b, i) => bytes[i] === b));
  if (!format) {
    const head = [...bytes.subarray(0, 8)]
      .map((b) => b.toString(16).padStart(2, "0"))
      .join(" ");
    return { error: `${url} → los primeros bytes no son de una imagen conocida (${head})` };
  }
  if (typeof expectedSize === "number" && expectedSize !== bytes.length) {
    return {
      error:
        `${url} → tamaño inesperado: el manifiesto dice ${expectedSize} bytes ` +
        `y se descargaron ${bytes.length}`,
    };
  }
  return { bytes, format: format.ext };
}

async function extractViaDevServer(items, previewHost) {
  const port = await getFreePort();
  const host = "127.0.0.1";
  const origin = `http://${host}:${port}`;

  const viteBin = path.join(root, "node_modules", "vite", "bin", "vite.js");
  log(`${items.length} assets a extraer. Preview host: ${previewHost}`);
  log(`Levantando el dev server en ${origin} ...`);

  let devExited = null;
  const dev = spawn(
    process.execPath,
    [viteBin, "dev", "--port", String(port), "--strictPort", "--host", host],
    {
      env: { ...process.env, LOVABLE_PREVIEW_HOST: previewHost },
      stdio: ["ignore", "pipe", "pipe"],
    },
  );
  const devLog = [];
  dev.stdout.on("data", (c) => devLog.push(c.toString()));
  dev.stderr.on("data", (c) => devLog.push(c.toString()));
  dev.on("exit", (code, signal) => {
    devExited = { code, signal };
  });

  const stopDev = () => {
    if (!dev.killed && devExited === null) dev.kill();
  };
  process.on("exit", stopDev);
  process.on("SIGINT", () => {
    stopDev();
    process.exit(130);
  });

  try {
    const deadline = Date.now() + 90_000;
    let ready = false;
    while (Date.now() < deadline && !ready) {
      if (devExited !== null) {
        console.error(devLog.join(""));
        fail(
          `El dev server terminó antes de responder (code=${devExited.code}, signal=${devExited.signal}).`,
        );
      }
      try {
        const res = await fetch(`${origin}/`, { headers: { accept: "text/html" } });
        if (res.status < 500) {
          await res.arrayBuffer();
          ready = true;
        }
      } catch {
        // todavía no está escuchando
      }
      if (!ready) await new Promise((r) => setTimeout(r, 250));
    }
    if (!ready) {
      console.error(devLog.join(""));
      fail("Timeout esperando al dev server.");
    }

    mkdirSync(outDir, { recursive: true });
    for (const { file, target, json } of items) {
      const result = await downloadImage(`${origin}${json.url}`, {
        expectedSize: json.size,
      });
      if (result.error) {
        failures.push(`${file} → ${result.error}`);
        continue;
      }
      writeFileSync(path.join(outDir, target), result.bytes);
      log(
        `OK  ${json.url} → public/images/${target} (${result.bytes.length} bytes, ${result.format})`,
      );
    }
  } finally {
    stopDev();
  }
}

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
