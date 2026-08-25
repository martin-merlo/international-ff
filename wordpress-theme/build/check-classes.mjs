/**
 * Chequeo de cobertura: toda clase que aparezca en el marcado PHP tiene que
 * existir en el CSS compilado.
 *
 * Corre después de `npm run build`. Es la red de seguridad contra el error
 * clásico de Tailwind: una clase que el scanner no ve y termina sin estilo,
 * cosa que en el navegador se nota como un detalle visual roto y difícil de
 * rastrear.
 *
 *   node check-classes.mjs
 */
import { readFileSync, readdirSync, statSync } from "node:fs";
import { dirname, join, resolve } from "node:path";
import { fileURLToPath } from "node:url";

const here = dirname(fileURLToPath(import.meta.url));
const themeRoot = resolve(here, "..");

// Clases que no son utilidades de Tailwind: son ganchos de JS/CSS propios.
const NON_UTILITY = new Set(["group", "is-scrolled", "lucide", "%s"]);

function walk(dir, out = []) {
  for (const entry of readdirSync(dir)) {
    if (entry === "build" || entry === "node_modules") continue;
    const full = join(dir, entry);
    if (statSync(full).isDirectory()) walk(full, out);
    else if (full.endsWith(".php")) out.push(full);
  }
  return out;
}

const classes = new Set();
for (const file of walk(themeRoot)) {
  const src = readFileSync(file, "utf8");
  for (const m of src.matchAll(/class="([^"]*)"/g)) {
    // Atributos que interpolan PHP se leen aparte, desde la variable.
    if (m[1].includes("<?")) continue;
    for (const token of m[1].split(/\s+/)) {
      if (token) classes.add(token);
    }
  }

  // Clases guardadas en una variable PHP ($iff_input_class = '...').
  for (const m of src.matchAll(/\$\w*_class\s*=\s*'([^']*)'/g)) {
    for (const token of m[1].split(/\s+/)) if (token) classes.add(token);
  }

  // Clases que se pasan al helper de iconos: iff_icon( 'star', 'h-4 w-4' ).
  for (const m of src.matchAll(/iff_icon\(\s*'[^']*'\s*,\s*'([^']*)'/g)) {
    for (const token of m[1].split(/\s+/)) if (token) classes.add(token);
  }
}

const css = readFileSync(resolve(themeRoot, "assets/css/theme.css"), "utf8");

// Tailwind escapa en el selector los caracteres que no son válidos en un ident.
const escapeClass = (cls) => cls.replace(/[:.[\]/(),%#&!+*~<>=@$^|?'"]/g, (c) => "\\" + c);

const missing = [];
for (const cls of [...classes].sort()) {
  if (NON_UTILITY.has(cls)) continue;
  if (css.includes("." + escapeClass(cls))) continue;
  missing.push(cls);
}

console.log(`[check] ${classes.size} clases distintas en el marcado`);

if (missing.length) {
  console.error(`[check] ${missing.length} SIN estilo en el CSS compilado:`);
  for (const cls of missing) console.error("   - " + cls);
  process.exit(1);
}

console.log("[check] todas las clases tienen estilo en assets/css/theme.css");
