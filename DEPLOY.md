# Deploy estático en Apache / cPanel (HostGator)

```bash
npm run build
```

Genera `dist/` lista para subir por FTP o File Manager al `public_html` del hosting.
Incluir el `.htaccess` (es un archivo oculto: activá "mostrar archivos ocultos" en cPanel).

## Cómo funciona

Es un **build estándar de Vite**, sin pasos extra: `vite build` toma `index.html` de la
raíz, empaqueta la SPA y escribe `dist/index.html` + `dist/assets/`. Todo lo que está en
`public/` (incluido el `.htaccess`, `favicon.png`, `robots.txt` y `images/`) se copia tal
cual a `dist/`.

No hay script de build custom, ni crawler, ni presets de Nitro, ni distinción entre build
"normal" y "estático": el sitio es una landing de una sola página con navegación por
anchors (`#inicio`, `#servicios`, `#nosotros`, `#testimonios`, `#contacto`), así que no
necesita SSR ni router.

> Historial: el proyecto venía de TanStack Start + Nitro y necesitaba
> `scripts/build-static.mjs` (build SSR con preset `node-server` + crawl local) para
> producir HTML estático. Esa infraestructura se eliminó al migrar a Vite + React puro.

## Rutas y raíz del dominio

El HTML referencia los assets con rutas **absolutas** (`/assets/...`, `/images/...`), así
que el contenido de `dist/` va en la **raíz** del dominio. Si algún día tuviera que
colgar de un subdirectorio, hay que setear `base` en [vite.config.ts](vite.config.ts).

## .htaccess

Vive en [public/.htaccess](public/.htaccess) y el build lo copia a `dist/`. Hace dos cosas:

1. Sirve archivos y directorios reales tal cual.
2. Cualquier otra ruta cae en `/index.html`.

Más `Cache-Control` inmutable para los assets con hash y `no-cache` para el HTML.

## Variables de entorno

`VITE_SITE_URL` es obligatoria para el build (ver la sección de og:image más abajo). Las
`VITE_*` se hornean en el bundle del cliente: no pongas secretos ahí. Ver
[.env.example](.env.example).

## Una nota sobre SEO

El HTML que se publica es un shell: el contenido lo renderiza React en el cliente. Google
ejecuta JavaScript y lo indexa igual, pero los previews de redes sociales y algunos
crawlers más simples solo ven los metadatos del `<head>` (que sí están completos y
estáticos). Si en algún momento hace falta HTML pre-renderizado, la opción menos invasiva
es agregar un plugin de prerender al build de Vite.

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
guarda. Queda en el repo como registro de cómo se obtuvieron los binarios.

Ese script **ya no se puede correr tal cual**: su camino de proxy dependía del plugin
`@lovable.dev/vite-tanstack-config`, que se eliminó al migrar a Vite puro. El camino de
fetch directo (`--og <url>`) sigue sirviendo para bajar cualquier imagen suelta por URL.

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
