<?php
/**
 * Bootstrap del tema.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IFF_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/content.php';
require_once get_template_directory() . '/inc/icons.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/template-helpers.php';

/**
 * Soporte de funcionalidades del tema.
 *
 * @return void
 */
function iff_setup() {
	load_theme_textdomain( 'international-ff', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
}
add_action( 'after_setup_theme', 'iff_setup' );

/**
 * Metadatos sociales de la home.
 *
 * El sitio original los tenía en el <head> de index.html. Se replican tal cual;
 * og:image necesita URL absoluta y se arma con home_url(), así que sigue al
 * dominio sin variables de entorno ni configuración extra.
 *
 * @return void
 */
function iff_meta_tags() {
	if ( ! is_front_page() ) {
		return;
	}

	$title       = 'Mudanzas Internacionales y Comercio Exterior | Mendoza';
	$description = 'Mudanzas internacionales puerta a puerta, logística de cargas, importación, exportación y despachos de aduana. Mas de 30 años de experiencia en Mendoza, Argentina.';
	$tw_title    = 'International Freight Forwarder | Mudanzas y Comercio Exterior';
	$tw_desc     = 'Mudanzas internacionales puerta a puerta, logística de cargas y despachos de aduana desde Mendoza, Argentina. Mas de 30 años de experiencia.';
	$og_image    = iff_asset( 'img/og-preview.jpg' );
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>" />
	<meta name="author" content="International Freight Forwarder" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
	<meta property="og:image" content="<?php echo esc_url( $og_image ); ?>" />
	<meta property="og:url" content="<?php echo esc_url( home_url( '/' ) ); ?>" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="<?php echo esc_attr( $tw_title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $tw_desc ); ?>" />
	<meta name="twitter:image" content="<?php echo esc_url( $og_image ); ?>" />
	<link rel="icon" href="<?php echo esc_url( iff_asset( 'img/favicon.png' ) ); ?>" type="image/png" />
	<?php
}
add_action( 'wp_head', 'iff_meta_tags', 5 );

/**
 * Título por defecto de la home.
 *
 * @param array $parts Partes del título.
 * @return array
 */
function iff_document_title( $parts ) {
	if ( is_front_page() ) {
		$parts['title']  = 'Mudanzas Internacionales y Comercio Exterior | Mendoza';
		$parts['tagline'] = '';
		unset( $parts['site'] );
	}

	return $parts;
}
add_filter( 'document_title_parts', 'iff_document_title' );
