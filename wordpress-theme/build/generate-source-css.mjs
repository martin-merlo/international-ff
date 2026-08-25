/**
 * Genera build/src/theme-source.css a partir del design system del proyecto
 * React original (../../src/styles.css) más las reglas @font-face de las fuentes
 * self-hosted.
 *
 * Se corre una sola vez (o cuando cambie el design system del proyecto original):
 *
 *   node generate-source-css.mjs
 *
 * Después, `npm run build` compila ese archivo a ../assets/css/theme.css.
 */
import { readFileSync, writeFileSync } from "node:fs";
import { dirname, resolve } from "node:path";
import { fileURLToPath } from "node:url";

const here = dirname(fileURLToPath(import.meta.url));
const repoRoot = resolve(here, "..", "..");

const read = (p) => readFileSync(p, "utf8").split("\r\n").join("\n");

let css = read(resolve(repoRoot, "src", "styles.css"));
const fontface = read(resolve(here, "src", "fontface.css"));

const OLD_IMPORTS = [
  '@import "tailwindcss" source(none);',
  '@source "../src";',
  '@import "tw-animate-css";',
].join("\n");

const NEW_IMPORTS = [
  '@import "tailwindcss" source(none);',
  "",
  "/* Tailwind escanea el marcado del tema para saber qué utilidades emitir. */",
  '@source "../../*.php";',
  '@source "../../inc/**/*.php";',
  '@source "../../template-parts/**/*.php";',
  '@source "../../assets/js/*.js";',
].join("\n");

if (!css.includes(OLD_IMPORTS)) {
  throw new Error(
    "El bloque de imports de src/styles.css cambió: revisá generate-source-css.mjs"
  );
}

// Las @font-face van DESPUÉS del @import: en CSS un @import precedido por otras
// reglas es inválido y se descarta.
const fontBlock = `
/* -------------------------------------------------------------------------
 * Fuentes self-hosted (Archivo + Manrope, subsets latin y latin-ext).
 * Reemplazan al CDN de Google Fonts: cero requests a terceros.
 * ---------------------------------------------------------------------- */

${fontface.trim()}
`;

// tw-animate-css se descarta a propósito: el sitio solo usa animate-rise y
// animate-pulse-ring, que están definidas más abajo en este mismo archivo.
css = css.replace(OLD_IMPORTS, NEW_IMPORTS + "\n" + fontBlock);

const header = `/*
 * Fuente del CSS del tema. Se compila con Tailwind en la máquina de desarrollo:
 *
 *   cd wordpress-theme/build && npm install && npm run build
 *
 * La salida va a ../assets/css/theme.css, que es lo único que sirve el servidor.
 * En producción NO hace falta Node ni Tailwind.
 *
 * Los design tokens vienen tal cual del src/styles.css del proyecto React
 * original, para que colores, radios y tipografías sean idénticos.
 *
 * Este archivo lo genera generate-source-css.mjs: no editarlo a mano.
 */

`;

writeFileSync(resolve(here, "src", "theme-source.css"), header + css, "utf8");
console.log(
  "[build] src/theme-source.css generado (" + (header + css).length + " bytes)"
);
