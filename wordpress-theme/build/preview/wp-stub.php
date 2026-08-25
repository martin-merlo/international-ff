<?php
/**
 * Emulación mínima de WordPress para previsualizar el tema sin instalar WP.
 *
 * SOLO PARA DESARROLLO. No forma parte del tema ni se sube al servidor: sirve
 * para levantar la home con `php -S` y comparar el resultado contra el sitio
 * original, sin montar una instalación completa de WordPress.
 *
 * Implementa las funciones de WP que el tema realmente usa, con la misma firma y
 * el mismo comportamiento observable (incluido el orden por prioridad de los
 * hooks de wp_head).
 *
 * @package International_FF
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['iff_hooks'] = array();

/**
 * Registra un callback en un hook.
 *
 * @param string   $hook     Nombre del hook.
 * @param callable $callback Callback.
 * @param int      $priority Prioridad.
 * @return void
 */
function add_action( $hook, $callback, $priority = 10, $args = 1 ) {
	$GLOBALS['iff_hooks'][ $hook ][] = array(
		'cb'       => $callback,
		'priority' => $priority,
	);
}

/**
 * Ejecuta los callbacks de un hook, ordenados por prioridad.
 *
 * @param string $hook Nombre del hook.
 * @return void
 */
function do_action( $hook ) {
	if ( empty( $GLOBALS['iff_hooks'][ $hook ] ) ) {
		return;
	}

	$callbacks = $GLOBALS['iff_hooks'][ $hook ];
	usort(
		$callbacks,
		function ( $a, $b ) {
			return $a['priority'] <=> $b['priority'];
		}
	);

	foreach ( $callbacks as $entry ) {
		call_user_func( $entry['cb'] );
	}
}

/**
 * Registra un filtro (no se ejecuta en la preview).
 *
 * @return void
 */
function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {}

/** Ruta del tema. @return string */
function get_template_directory() {
	return dirname( __DIR__, 2 );
}

/** URL del tema. En la preview el tema se sirve desde la raíz. @return string */
function get_template_directory_uri() {
	return '';
}

/** URL de style.css. @return string */
function get_stylesheet_uri() {
	return '/style.css';
}

/** URL del sitio. @param string $path Ruta. @return string */
function home_url( $path = '' ) {
	return 'https://www.internationalff.com' . $path;
}

/** Siempre estamos en la home. @return bool */
function is_front_page() {
	return true;
}

/** Escapes: en la preview alcanza con el comportamiento de PHP. */
function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function esc_url( $url ) {
	return htmlspecialchars( (string) $url, ENT_QUOTES, 'UTF-8' );
}

/** No-ops del ciclo de vida del tema. */
function add_theme_support( ...$args ) {}
function load_theme_textdomain( ...$args ) {}
function wp_enqueue_style( ...$args ) {}
function wp_enqueue_script( ...$args ) {}
function wp_body_open() {}

/** Atributos del <html>. @return void */
function language_attributes() {
	echo 'lang="es-AR"';
}

/** Info del sitio. @param string $show Qué mostrar. @return void */
function bloginfo( $show = '' ) {
	if ( 'charset' === $show ) {
		echo 'UTF-8';
	}
}

/** Clases del body. @return void */
function body_class() {
	echo 'class="home page"';
}

/**
 * Imprime el <head>: en la preview encolamos a mano el CSS y el JS del tema,
 * porque wp_enqueue_* es no-op.
 *
 * @return void
 */
function wp_head() {
	do_action( 'wp_head' );
	echo '<title>Mudanzas Internacionales y Comercio Exterior | Mendoza</title>' . "\n";
	echo '<link rel="stylesheet" href="/assets/css/theme.css" />' . "\n";
	echo '<script src="/assets/js/main.js" defer></script>' . "\n";
}

/** Pie del documento. @return void */
function wp_footer() {
	do_action( 'wp_footer' );
}

/** Incluye una parte de template. @param string $slug Ruta relativa. @return void */
function get_template_part( $slug, $name = null ) {
	include get_template_directory() . '/' . $slug . '.php';
}

/** Incluye header.php. @return void */
function get_header() {
	include get_template_directory() . '/header.php';
}

/** Incluye footer.php. @return void */
function get_footer() {
	include get_template_directory() . '/footer.php';
}
