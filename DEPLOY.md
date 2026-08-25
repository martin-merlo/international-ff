# Deploy estático en Apache / cPanel (HostGator)

```bash
npm run build
```

Genera `dist/` lista para subir por FTP o File Manager. El contenido va a
`public_html/new/lovable/dist/` (ver "Ruta base" más abajo: el sitio **no** va en la raíz).
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

## Ruta base: el sitio vive en una subcarpeta

El sitio **no** se sirve desde la raíz del dominio —ahí conviven otros sitios— sino desde:

```
https://www.internationalff.com/new/lovable/dist/
```

Esa ruta está **hardcodeada en tres lugares que tienen que coincidir sí o sí**:

| Dónde | Qué dice | Para qué |
| --- | --- | --- |
| [vite.config.ts](vite.config.ts) | `const BASE = "/new/lovable/dist/"` (con barra final) | prefija todos los assets del build y alimenta `import.meta.env.BASE_URL` |
| `.env` | `VITE_SITE_URL=https://www.internationalff.com/new/lovable/dist` (**sin** barra final) | arma las URLs absolutas de `og:image` / `twitter:image` |
| [public/.htaccess](public/.htaccess) | `RewriteBase` y el destino del fallback | que las rutas desconocidas caigan en el `index.html` correcto |

El build **falla** si `VITE_SITE_URL` y `base` apuntan a rutas distintas: es un error que de
otro modo solo se notaría en producción, con los assets cargando de una carpeta y el preview
de redes sociales de otra.

### Cómo se referencian los archivos de `public/`

Ninguna ruta a `public/` puede escribirse absoluta a mano (`/images/x.png` apuntaría a la
raíz del dominio, o sea a otro sitio). Las reglas son:

- **Desde TypeScript/JSX**: usar `assetUrl()` de [src/lib/asset-url.ts](src/lib/asset-url.ts),
  que antepone `import.meta.env.BASE_URL` (ese valor ya trae la barra final). Los
  `src/assets/*.asset.json` guardan la ruta **sin** barra inicial (`images/logo-iff.png`)
  justo para eso.
- **Desde `index.html`**: escribir la ruta absoluta normal (`/favicon.png`). Vite la reescribe
  sola con el prefijo al buildear — verificado en el `dist/index.html` emitido.
- **Desde `src/assets/`** (los JPG importados): no hace falta nada, Vite los procesa y
  prefija solo.

### El día que el sitio pase a la raíz del dominio

Tres cambios y un rebuild:

1. `vite.config.ts`: `const BASE = "/"`.
2. `.env` y `.env.example`: `VITE_SITE_URL=https://www.internationalff.com`.
3. `public/.htaccess`: `RewriteBase /` y `RewriteRule ^ /index.html [L]`.

No hay que tocar ningún componente: `assetUrl()` y `BASE_URL` se ajustan solos. Si te olvidás
de alguno de los dos primeros, el build corta con un mensaje que dice exactamente cuál.

## .htaccess

Vive en [public/.htaccess](public/.htaccess) y el build lo copia a `dist/`. Hace:

1. `RewriteBase /new/lovable/dist/`, porque el sitio cuelga de esa subcarpeta.
2. Sirve archivos y directorios reales tal cual.
3. Cualquier otra ruta cae en `/new/lovable/dist/index.html` — **nunca** en `/index.html`,
   que es otro sitio.
4. `mod_deflate` para comprimir HTML, CSS, JS, JSON, XML y SVG (los JPG/PNG ya vienen
   comprimidos y se dejan pasar).
5. `Cache-Control` inmutable para los assets con hash y `no-cache` para el HTML.

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
- Los `src/assets/*.asset.json` guardan la ruta relativa `images/<nombre>.png` (sin barra
  inicial) y los componentes la resuelven con `assetUrl()`, que le antepone la ruta base.
  Ver "Ruta base" más arriba.
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
VITE_SITE_URL=https://www.internationalff.com/new/lovable/dist
```

- Va en `.env` (ver [.env.example](.env.example)), sin barra final, e incluye la subcarpeta.
  Tiene que coincidir con `base` de vite.config.ts o el build corta.
- Si falta o no es una URL absoluta, **el build se corta** con un mensaje explicativo, en
  vez de emitir metadatos rotos. La validación es un plugin de Vite con `apply: "build"`
  en [vite.config.ts](vite.config.ts), así que `vite dev` sigue arrancando sin la variable.
- Si el sitio se publica en otro dominio o subdominio, cambiar esa línea y rebuildear:
  la URL queda horneada en el HTML.

### Otros residuos del andamiaje original

Se sacó el meta `twitter:site` (apuntaba a la cuenta del proveedor, no a la del cliente) y
el `<html lang>` pasó de `en` a `es-AR`, que es el idioma real del sitio. Ambos viven hoy
en [index.html](index.html).
