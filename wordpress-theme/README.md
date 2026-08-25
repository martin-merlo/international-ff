# Tema WordPress — International Freight Forwarder

Port del sitio de Lovable (TanStack Start / React) a un tema WordPress convencional:
PHP, HTML, CSS y JavaScript vanilla. Sin React, sin router, sin Node en el servidor.

El proyecto original sigue intacto en la raíz del repo y es la fuente de verdad para
comparar.

## Instalación

1. Comprimir esta carpeta en un `.zip` (o subirla por FTP a `/wp-content/themes/`).
2. En el panel de WordPress: **Apariencia → Temas → Añadir nuevo → Subir tema**.
3. Activar **International Freight Forwarder**.
4. Instalar y activar **Advanced Custom Fields** (versión gratuita) para poder
   editar el contenido desde el panel.

Al activarlo, el tema crea solo la página **Inicio**, la define como portada y
carga en la base todo el contenido original: el sitio queda funcionando y
editable sin tocar nada más. Sin ACF el sitio se ve igual, pero el contenido no
se puede editar (el tema lo avisa en el panel).

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
│   ├── content-schema.php       Qué campos existen y su contenido original.
│   ├── content.php              Capa de acceso: lee ACF con fallback al original.
│   ├── acf-fields.php           Registra el grupo de campos del panel.
│   ├── acf-setup.php            Crea la portada, carga el contenido, avisos.
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

Todo el contenido se edita desde el panel de WordPress, en la página **Inicio**
(Páginas → Inicio, o "Editar contenido del sitio" en la barra superior). El
metabox **Contenido del sitio** organiza los campos en once pestañas: General,
Portada, Estadísticas, Servicios, Nosotros, Cómo trabajamos, Por qué elegirnos,
Testimonios, Franja de contacto, Contacto y Pie de página.

### Cómo está armado

```
inc/content-schema.php   Única fuente de verdad: qué campos existen, cómo se
                         llaman y cuál es su valor original.
        │
        ├──> inc/acf-fields.php   registra el grupo de campos que ve el editor
        ├──> inc/acf-setup.php    crea la portada y carga el contenido inicial
        └──> inc/content.php      lee los valores y se los pasa a los templates
                                     │
                                     └──> template-parts/  (marcado, sin lógica)
```

Los templates no llaman a `get_field()`: piden el contenido a la capa de acceso
(`iff_content()`, `iff_rows()`, `iff_image()`, `iff_lines()`), que resuelve de
dónde sacarlo.

### Fallbacks

Cada campo tiene como respaldo el contenido original del sitio. Si ACF se
desactiva, un campo queda vacío o todavía no hay portada configurada, el sitio
sigue mostrando el contenido correcto en vez de romperse o quedar en blanco.

Dos campos son la excepción y **sí** se pueden vaciar a propósito: el segundo
párrafo de Nosotros y el destino del botón secundario de la portada (que vacío
usa el WhatsApp de la pestaña General). Están marcados con `optional` en el
esquema.

### Agregar un campo nuevo

Se agrega una sola vez en `inc/content-schema.php`, con su etiqueta, tipo y valor
por defecto. De ahí salen solos el campo en el panel, el default y la carga
inicial. Después se usa en el template con `iff_content( 'mi_campo' )`.

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

## ACF: obligatorio para editar, no para que el sitio funcione

El tema necesita el plugin **Advanced Custom Fields** (la versión gratuita
alcanza) para que el contenido sea editable. Si no está instalado, el tema lo
avisa en el panel y el sitio sigue funcionando con el contenido que trae el tema.

**Repeaters:** los bloques que se repiten (servicios, estadísticas, pasos,
motivos, testimonios, operadores) usan campos Repeater si la instalación los
soporta. El Repeater es parte de ACF PRO; con la versión gratuita el tema
registra en su lugar campos numerados (Servicio 1, Servicio 2…). El frontend es
idéntico en los dos casos: cambia solo la comodidad de edición. Con la versión
gratuita se puede cambiar el texto de cada bloque, pero no agregar ni reordenar
bloques desde el panel.

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
