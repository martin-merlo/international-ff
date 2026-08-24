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

