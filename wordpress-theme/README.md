# Tema WordPress — International Freight Forwarder

Port del sitio de Lovable (TanStack Start / React) a un tema WordPress convencional:
PHP, HTML, CSS y JavaScript vanilla. Sin React, sin router, sin Node en el servidor.

El proyecto original sigue intacto en la raíz del repo y es la fuente de verdad para
comparar.

## Instalación

1. Comprimir esta carpeta en un `.zip` (o subirla por FTP a `/wp-content/themes/`).
2. En el panel de WordPress: **Apariencia → Temas → Añadir nuevo → Subir tema**.
3. Activar **International Freight Forwarder**.

No hace falta ningún plugin, ni configuración, ni variables de entorno. Tampoco hay
que crear páginas: la home usa `front-page.php`, que ya trae las nueve secciones.

**Lo que NO se sube al servidor:** la carpeta `build/`. Es la toolchain de desarrollo
para recompilar el CSS. El sitio en producción solo necesita el CSS ya compilado, que
está versionado en `assets/css/theme.css`.

## Estructura

```
international-ff/
├── style.css                    Cabecera del tema (WordPress la exige). No es la hoja de estilos real.
├── functions.php                Bootstrap: theme supports, metadatos sociales, título.
├── front-page.php               Home: arma las nueve secciones.
├── index.php                    Fallback obligatorio (misma landing).
├── page.php / 404.php           Páginas sueltas y error.
├── header.php / footer.php      Nav fija y pie.
├── screenshot.png               Vista previa del tema en el panel.
├── inc/
│   ├── content.php              TODO el contenido del sitio (portado de data.ts).
│   ├── icons.php                22 iconos SVG inline (portados de lucide-react).
│   ├── enqueue.php              Encola CSS/JS y precarga las fuentes.
│   └── template-helpers.php     iff_asset(), iff_img().
├── template-parts/
│   ├── sections/                hero, stats, services, about, process,
│   │                            why-us, testimonials, cta-band, contact
│   └── partials/                whatsapp-button
├── assets/
│   ├── css/theme.css            CSS COMPILADO. Es lo único que sirve el navegador.
│   ├── js/main.js               ~200 líneas de JS vanilla.
│   ├── fonts/                   Archivo + Manrope self-hosted (woff2, latin y latin-ext).
│   └── img/                     15 imágenes.
└── build/                       SOLO DESARROLLO. No subir al servidor.
```

## Editar el contenido

Todos los textos, teléfonos, mails, servicios y testimonios están en
[inc/content.php](inc/content.php), en funciones que devuelven arrays. Los templates
no tienen texto hardcodeado suelto: recorren esos arrays.

Está armado así a propósito. El día que haga falta que el cliente edite desde el
panel, alcanza con cambiar el cuerpo de cada función por una llamada a ACF, al
Customizer o a un CPT: **el marcado no se toca**.

## Recompilar el CSS

Solo si cambian los estilos o se agregan clases nuevas al marcado. Necesita Node en la
máquina de desarrollo (nunca en el servidor):

```bash
cd build
npm install
npm run build          # compila src/theme-source.css -> ../assets/css/theme.css
node check-classes.mjs # verifica que toda clase del marcado tenga estilo
```

`npm run source` regenera `build/src/theme-source.css` desde el `src/styles.css` del
proyecto React original, por si cambian los design tokens.

⚠️ Tailwind solo emite las clases que **encuentra escritas** en los archivos. Si armás
un nombre de clase concatenando strings en PHP, no lo va a ver. Por eso está
`check-classes.mjs`: corrélo después de cada build.

## Previsualizar sin instalar WordPress

`build/preview/` trae una emulación mínima de las funciones de WP que usa el tema:

```bash
php -S 127.0.0.1:4600 build/preview/router.php
```

Sirve para comparar contra el sitio original sin montar una instalación completa.
No es WordPress: valida el marcado y los estilos, no el comportamiento del CMS.

## Qué se dejó igual que el original

- Los cinco campos del formulario, que **no envían mail**: arman el mensaje y abren
  WhatsApp con el texto listo, exactamente como antes.
- El número de WhatsApp, teléfonos, mails, dirección y el embed de Google Maps.
- Los links de redes sociales del footer, que siguen siendo **placeholders** (`#inicio`)
  a la espera de los perfiles reales.
- Todas las animaciones, hovers y transiciones.

## Si más adelante hace falta enviar mails

El formulario actual no manda nada por mail a propósito. Para agregarlo haría falta un
plugin de formularios (Contact Form 7, WPForms, Fluent Forms) **más** un plugin SMTP
(WP Mail SMTP) con credenciales propias: el `mail()` de PHP en hosting compartido es
poco confiable y suele terminar en spam.
