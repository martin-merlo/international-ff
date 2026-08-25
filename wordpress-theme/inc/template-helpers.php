<?php
/**
 * Helpers de template.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL de un archivo dentro de assets/.
 *
 * @param string $path Ruta relativa a assets/, por ejemplo 'img/hero-port.jpg'.
 * @return string
 */
function iff_asset( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/**
 * URL de una imagen del tema.
 *
 * @param string $file Nombre del archivo dentro de assets/img/.
 * @return string
 */
function iff_img( $file ) {
	return iff_asset( 'img/' . $file );
}

/**
 * Arma el mensaje de WhatsApp con el texto por defecto.
 *
 * @return string
 */
function iff_whatsapp_url() {
	$contact = iff_contact();

	return $contact['whatsapp'];
}
