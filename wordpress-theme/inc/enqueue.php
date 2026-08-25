<?php
/**
 * Carga de estilos, scripts y fuentes.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Encola el CSS compilado y el JS del tema.
 *
 * El CSS ya viene compilado en el repo (ver build/). El servidor no compila nada.
 * La versión sale de filemtime() para que el navegador no sirva una copia vieja
 * después de un deploy.
 *
 * @return void
 */
function iff_enqueue_assets() {
	$css_path = get_template_directory() . '/assets/css/theme.css';
	$js_path  = get_template_directory() . '/assets/js/main.js';

	wp_enqueue_style(
		'iff-theme',
		iff_asset( 'css/theme.css' ),
		array(),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : IFF_VERSION
	);

	// style.css solo lleva la cabecera del tema; se encola por convención para
	// que los child themes puedan depender de este handle.
	wp_enqueue_style(
		'iff-style',
		get_stylesheet_uri(),
		array( 'iff-theme' ),
		IFF_VERSION
	);

	wp_enqueue_script(
		'iff-main',
		iff_asset( 'js/main.js' ),
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : IFF_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'iff_enqueue_assets' );

/**
 * Precarga las fuentes self-hosted.
 *
 * Las Archivo/Manrope están servidas desde el propio tema (nada de Google Fonts
 * CDN). Precargar el subset latin evita el salto de tipografía en la primera
 * pintada, que es donde más se nota.
 *
 * @return void
 */
function iff_preload_fonts() {
	$fonts = array( 'archivo-latin.woff2', 'manrope-latin.woff2' );

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin />' . "\n",
			esc_url( iff_asset( 'fonts/' . $font ) )
		);
	}
}
add_action( 'wp_head', 'iff_preload_fonts', 1 );
