# Deploy estático en Apache / cPanel (HostGator)

```bash
npm run build:static
```

Genera `dist/` lista para subir por FTP o File Manager al `public_html` del hosting.
Incluir el `.htaccess` (es un archivo oculto: activá "mostrar archivos ocultos" en cPanel).

## Cómo funciona

`npm run build:static` (ver [scripts/build-static.mjs](scripts/build-static.mjs)) hace:

1. `vite build` con `STATIC_BUILD=1`, que cambia el preset de Nitro a `node-server`.
   Es un build SSR normal: mismo entry (`src/server.ts`), mismos assets. Lo único que
   cambia respecto de `npm run build` (Cloudflare) es el preset.
2. Levanta `.output/server/index.mjs` en un puerto libre de `127.0.0.1`.
3. Crawlea desde `/` siguiendo solo links internos del mismo origen (ignora externos,
   `mailto:`/`tel:`/anclas, archivos con extensión y rutas `/_*`).
4. Copia `.output/public/*` a `dist/` y escribe encima el HTML renderizado de cada ruta.
5. Baja el server, borra artefactos de otros hostings (`_headers`, `_routes.json`,
   `_worker.js`) y escribe el `.htaccess`.

No se usa el prerenderer de Nitro ni el modo SPA: con el preset `static` el build
fallaba en la etapa final (`rolldownOptions.input should not be an html file when
building for SSR`) y el crawler de Nitro devolvía 404 para `/`.

## Layout de salida: directory-index

| Ruta | Archivo |
| --- | --- |
| `/` | `dist/index.html` |
| `/servicios` | `dist/servicios/index.html` |

Elegido sobre `servicios.html` porque en Apache funciona sin reglas extra: `DirectoryIndex`
resuelve el directorio, la URL queda limpia (sin `.html`), y anda igual con y sin barra
final. Verificado también con `npx serve dist` (200 en `/ruta`, `/ruta/` y F5).

## .htaccess

Orden de las reglas:

1. Archivos y directorios reales se sirven tal cual.
2. `/<ruta>` con `dist/<ruta>/index.html` existente se sirve internamente, sin redirect.
3. Cualquier otra ruta cae en `/index.html` y la resuelve el router en el cliente.

Más `Cache-Control` inmutable para los assets con hash y `no-cache` para el HTML.

## Rutas no linkeadas

El crawler sale de `/`. Una página que no esté linkeada desde ningún lado hay que
sembrarla a mano:

```bash
STATIC_ROUTES="/gracias,/legal" npm run build:static
```

(En Git Bash sobre Windows anteponé `MSYS_NO_PATHCONV=1`, si no la shell convierte
`/gracias` en una ruta de Windows.)

## Imágenes: dependencia de Lovable cortada

Siete imágenes (el logo principal y los seis logos de operadores del footer) no estaban
en el repo: los `src/assets/*.asset.json` apuntaban a una ruta interna que solo resuelve
la infraestructura de Lovable, así que en HostGator daban 404.

Ya se extrajeron. Ahora:

- Los binarios viven en `public/images/` y están commiteados. Se copian solos a
  `dist/images/` en el build.
- Los `src/assets/*.asset.json` apuntan a `/images/<nombre>.png`. Los componentes siguen
  importando esos JSON y leyendo `.url`, así que no se tocó ningún componente.
- El repo ya no referencia rutas de assets de Lovable en ningún lado.

| Origen | Archivo local | Usado en |
| --- | --- | --- |
| `logo-iff.png` | `public/images/logo-iff.png` | Header, Footer |
| `op-itl.png` | `public/images/logo-itl.png` | Footer (operadores) |
| `op-ccni.png` | `public/images/logo-ccni.png` | Footer (operadores) |
| `op-dhl.png` | `public/images/logo-dhl.png` | Footer (operadores) |
| `op-somarco.png` | `public/images/logo-somarco.png` | Footer (operadores) |
| `op-ups.png` | `public/images/logo-ups.png` | Footer (operadores) |
| `op-msc.png` | `public/images/logo-msc.png` | Footer (operadores) |

La extracción la hizo [scripts/extract-lovable-assets.mjs](scripts/extract-lovable-assets.mjs),
un script de **un solo uso** que no está enganchado a ningún script de `package.json` y que
no debe correr en un build: levanta el dev server con el proxy de assets de Lovable activo,
baja cada imagen, valida que sea una imagen real (magic bytes + tamaño del manifiesto) y la
guarda. Queda en el repo como registro de cómo se obtuvieron los binarios; para volver a
usarlo hay que restaurar los `.asset.json` originales desde git.

El plugin `@lovable.dev/vite-tanstack-config` **no** se sacó de `vite.config.ts`: el proxy de
assets es solo uno de los doce plugins que registra (también monta tanstackStart, Nitro,
React, Tailwind, tsconfig paths, devtools y los error loggers). Sacarlo rompe el build entero.
Además el proxy ya es inerte por defecto: solo se activa si está definida
`LOVABLE_PREVIEW_HOST`, cosa que no pasa en este repo.

### og:image y twitter:image

La imagen de preview para redes también vivía en un bucket externo del proveedor. Se bajó
con el mismo script (camino de fetch directo, sin dev server) y quedó en
`public/images/og-preview.png` (1920×1080, 1,5 MB).

Como `og:image` exige URL **absoluta**, se arma con la variable `VITE_SITE_URL`:

```
VITE_SITE_URL=https://www.internationalff.com
```

- Va en `.env` (ver [.env.example](.env.example)), sin barra final.
- Si falta o no es una URL absoluta, **el build se corta** con un mensaje explicativo, en
  vez de emitir metadatos rotos. La validación es un plugin de Vite con `apply: "build"`
  en [vite.config.ts](vite.config.ts), así que `vite dev` sigue arrancando sin la variable.
- Si el sitio se publica en otro dominio o subdominio, cambiar esa línea y rebuildear:
  la URL queda horneada en el HTML.

### Otros residuos del andamiaje original

En el mismo `__root.tsx` se sacó el meta `twitter:site` (apuntaba a la cuenta del
proveedor, no a la del cliente) y el `<html lang>` pasó de `en` a `es-AR`, que es el
idioma real del sitio.
