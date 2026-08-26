<?php
/**
 * Capa de acceso al contenido.
 *
 * Los templates NO llaman a get_field() directamente: piden el contenido acá y
 * esta capa decide de dónde sacarlo.
 *
 *   valor guardado en ACF  →  si existe, se usa
 *   valor por defecto      →  si no (ACF desactivado, campo vacío, sin portada)
 *
 * Los defaults salen de inc/content-schema.php y son exactamente el contenido
 * que el sitio tenía hardcodeado. Consecuencia práctica: si alguien desactiva
 * ACF, borra un campo o rompe la instalación, el sitio sigue mostrando el
 * contenido correcto en lugar de romperse o quedar en blanco.
 *
 * Las funciones públicas (iff_contact, iff_services, iff_stats…) devuelven las
 * mismas estructuras que antes, para que los templates casi no cambien.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ID de la entrada donde vive el contenido: la página de portada.
 *
 * @return int 0 si todavía no hay una portada estática configurada.
 */
function iff_content_post_id() {
	$front = (int) get_option( 'page_on_front' );

	if ( $front <= 0 ) {
		return 0;
	}

	// La opción puede quedar apuntando a una página borrada o en la papelera; en
	// ese caso se trabaja con el contenido del tema en vez de leer de la nada.
	$post = get_post( $front );

	if ( ! $post || 'trash' === $post->post_status ) {
		return 0;
	}

	return $front;
}

/**
 * ¿Podemos leer valores de ACF ahora mismo?
 *
 * @return bool
 */
function iff_acf_ready() {
	return function_exists( 'get_field' ) && iff_content_post_id() > 0;
}

/**
 * Valor crudo de un campo, sin fallback.
 *
 * @param string $key Clave del campo.
 * @return mixed null si no hay valor.
 */
function iff_raw( $key ) {
	if ( ! iff_acf_ready() ) {
		return null;
	}

	$value = get_field( $key, iff_content_post_id() );

	if ( null === $value || '' === $value || array() === $value ) {
		return null;
	}

	return $value;
}

/**
 * Valor de texto de un campo, con fallback al contenido original.
 *
 * Es la función que usan los templates para cualquier texto suelto.
 *
 * @param string $key      Clave del campo.
 * @param mixed  $fallback Fallback explícito; si se omite, usa el del esquema.
 * @return string
 */
function iff_content( $key, $fallback = null ) {
	$field = iff_schema_field( $key );

	// Campos marcados como opcionales: si el editor los vacía a propósito, se
	// respeta el vacío en vez de reponer el texto original.
	if ( ! empty( $field['optional'] ) && iff_acf_ready() ) {
		$raw = get_field( $key, iff_content_post_id() );

		if ( is_string( $raw ) ) {
			return $raw;
		}
	}

	$value = iff_raw( $key );

	if ( null === $value ) {
		$value = ( null !== $fallback ) ? $fallback : iff_default( $key );
	}

	return is_scalar( $value ) ? (string) $value : '';
}

/**
 * Valor de un campo de tipo interruptor (sí/no).
 *
 * @param string $key Clave del campo.
 * @return bool
 */
function iff_flag( $key ) {
	$value = iff_raw( $key );

	if ( null === $value ) {
		// ACF guarda los interruptores como "0", que iff_raw() no distingue de
		// vacío; si el campo existe pero está apagado, hay que respetarlo.
		if ( iff_acf_ready() ) {
			$raw = get_field( $key, iff_content_post_id() );
			if ( null !== $raw && '' !== $raw ) {
				return (bool) $raw;
			}
		}

		return (bool) iff_default( $key );
	}

	return (bool) $value;
}

/**
 * Campo de tipo lista (un ítem por línea).
 *
 * @param string $key Clave del campo.
 * @return array<int,string>
 */
function iff_lines( $key ) {
	$value = iff_raw( $key );

	if ( null === $value ) {
		$default = iff_default( $key );

		return is_array( $default ) ? $default : array();
	}

	if ( is_array( $value ) ) {
		return $value;
	}

	$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
	$lines = array_map( 'trim', $lines );

	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * Resuelve el valor de un campo imagen a una URL utilizable.
 *
 * Acepta lo que devuelva ACF (array, ID o URL) y, si no hay nada, cae al archivo
 * del tema que venía por defecto.
 *
 * @param mixed  $value            Valor de ACF.
 * @param string $default_filename Archivo dentro de assets/img/.
 * @return string
 */
function iff_resolve_image_url( $value, $default_filename = '' ) {
	if ( is_array( $value ) && ! empty( $value['url'] ) ) {
		return $value['url'];
	}

	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_url( (int) $value );
		if ( $url ) {
			return $url;
		}
	}

	if ( is_string( $value ) && '' !== $value ) {
		// Puede ser una URL completa o el nombre de archivo por defecto del tema.
		return ( 0 === strpos( $value, 'http' ) || 0 === strpos( $value, '/' ) )
			? $value
			: iff_img( $value );
	}

	return '' !== $default_filename ? iff_img( $default_filename ) : '';
}

/**
 * Imagen suelta: devuelve URL y texto alternativo.
 *
 * El alt sale de la biblioteca de medios de WordPress (el campo "Texto
 * alternativo" de la imagen); si está vacío usa el que traía el sitio.
 *
 * @param string $key Clave del campo.
 * @return array{url:string,alt:string}
 */
function iff_image( $key ) {
	$field       = iff_schema_field( $key );
	$default_img = isset( $field['default'] ) ? $field['default'] : '';
	$default_alt = isset( $field['default_alt'] ) ? $field['default_alt'] : '';

	$value = iff_raw( $key );
	$alt   = '';

	if ( is_array( $value ) && ! empty( $value['alt'] ) ) {
		$alt = $value['alt'];
	} elseif ( is_numeric( $value ) ) {
		$alt = (string) get_post_meta( (int) $value, '_wp_attachment_image_alt', true );
	}

	return array(
		'url' => iff_resolve_image_url( $value, $default_img ),
		'alt' => '' !== $alt ? $alt : $default_alt,
	);
}

/**
 * Filas de un campo repetidor.
 *
 * Funciona con el campo Repeater de ACF PRO y, si no está disponible, con los
 * campos numerados que registra el fallback (ver inc/acf-fields.php). En ambos
 * casos devuelve la misma estructura, así que los templates no se enteran.
 *
 * @param string $key Clave del campo.
 * @return array<int,array<string,mixed>>
 */
function iff_rows( $key ) {
	$field = iff_schema_field( $key );

	if ( ! $field || 'repeat' !== $field['type'] ) {
		return array();
	}

	$subs     = $field['sub_fields'];
	$defaults = isset( $field['default'] ) ? $field['default'] : array();
	$rows     = array();

	if ( iff_acf_ready() ) {
		$rows = iff_acf_supports_repeater()
			? iff_rows_from_repeater( $key, $subs )
			: iff_rows_from_flat( $key, $subs, count( $defaults ) );
	}

	if ( empty( $rows ) ) {
		$rows = $defaults;
	}

	// Las imágenes se normalizan a URL, vengan de ACF o del default del tema.
	foreach ( $rows as $index => $row ) {
		foreach ( $subs as $name => $sub ) {
			if ( 'image' === $sub['type'] ) {
				$fallback           = isset( $defaults[ $index ][ $name ] ) ? $defaults[ $index ][ $name ] : '';
				$rows[ $index ][ $name ] = iff_resolve_image_url(
					isset( $row[ $name ] ) ? $row[ $name ] : '',
					$fallback
				);
			}

			if ( 'lines' === $sub['type'] && isset( $row[ $name ] ) && ! is_array( $row[ $name ] ) ) {
				$lines                   = preg_split( '/\r\n|\r|\n/', (string) $row[ $name ] );
				$lines                   = array_map( 'trim', $lines );
				$rows[ $index ][ $name ] = array_values( array_filter( $lines, 'strlen' ) );
			}
		}
	}

	return $rows;
}

/**
 * Lee las filas desde un campo Repeater de ACF.
 *
 * @param string $key  Clave del campo.
 * @param array  $subs Definición de los subcampos.
 * @return array<int,array<string,mixed>>
 */
function iff_rows_from_repeater( $key, $subs ) {
	$value = get_field( $key, iff_content_post_id() );

	if ( ! is_array( $value ) ) {
		return array();
	}

	$rows = array();

	foreach ( $value as $row ) {
		$clean = array();
		foreach ( $subs as $name => $sub ) {
			$clean[ $name ] = isset( $row[ $name ] ) ? $row[ $name ] : '';
		}
		if ( array_filter( $clean, 'iff_not_empty' ) ) {
			$rows[] = $clean;
		}
	}

	return $rows;
}

/**
 * Lee las filas desde campos numerados (fallback sin Repeater).
 *
 * @param string $key   Clave del campo.
 * @param array  $subs  Definición de los subcampos.
 * @param int    $count Cantidad de filas registradas.
 * @return array<int,array<string,mixed>>
 */
function iff_rows_from_flat( $key, $subs, $count ) {
	$rows = array();

	for ( $i = 1; $i <= $count; $i++ ) {
		$clean = array();
		foreach ( $subs as $name => $sub ) {
			$clean[ $name ] = get_field( $key . '_' . $i . '_' . $name, iff_content_post_id() );
		}
		if ( array_filter( $clean, 'iff_not_empty' ) ) {
			$rows[] = $clean;
		}
	}

	return $rows;
}

/**
 * ¿El valor tiene contenido? (0 cuenta como contenido, '' y null no).
 *
 * @param mixed $value Valor.
 * @return bool
 */
function iff_not_empty( $value ) {
	if ( is_array( $value ) ) {
		return ! empty( $value );
	}

	return null !== $value && '' !== $value;
}

/* -------------------------------------------------------------------------
 * API pública para los templates.
 *
 * Devuelven las mismas estructuras que cuando el contenido estaba hardcodeado.
 * ---------------------------------------------------------------------- */

/**
 * Datos de contacto de la empresa.
 *
 * @return array<string,string>
 */
function iff_contact() {
	$phone    = iff_content( 'phone' );
	$phone2   = iff_content( 'phone2' );
	$number   = preg_replace( '/\D/', '', iff_content( 'whatsapp_number' ) );
	$message  = iff_content( 'whatsapp_message' );

	return array(
		'phone'           => $phone,
		'phone_href'      => iff_tel_href( $phone ),
		'phone2'          => $phone2,
		'phone2_href'     => iff_tel_href( $phone2 ),
		'person'          => iff_content( 'contact_person' ),
		'person2'         => iff_content( 'contact_person2' ),
		'whatsapp_number' => $number,
		'whatsapp'        => 'https://wa.me/' . $number . '?text=' . rawurlencode( $message ),
		'email'           => iff_content( 'email' ),
		'email2'          => iff_content( 'email2' ),
		'address'         => iff_content( 'address' ),
		'map_embed'       => iff_content( 'map_embed' ),
	);
}

/**
 * Convierte un teléfono legible en un href tel:.
 *
 * @param string $phone Teléfono como se muestra.
 * @return string
 */
function iff_tel_href( $phone ) {
	return preg_replace( '/[^+\d]/', '', $phone );
}

/**
 * Links de navegación (anchors de la misma página).
 *
 * Se quedan en el tema: están atados a los IDs de las secciones, así que
 * editarlos desde el panel rompería la navegación.
 *
 * @return array<int,array<string,string>>
 */
function iff_nav_links() {
	return array(
		array(
			'href'  => '#inicio',
			'label' => 'Inicio',
		),
		array(
			'href'  => '#servicios',
			'label' => 'Servicios',
		),
		array(
			'href'  => '#nosotros',
			'label' => 'Nosotros',
		),
		array(
			'href'  => '#testimonios',
			'label' => 'Testimonios',
		),
		array(
			'href'  => '#contacto',
			'label' => 'Contacto',
		),
	);
}

/**
 * Servicios.
 *
 * @return array<int,array<string,mixed>>
 */
function iff_services() {
	return iff_rows( 'services' );
}

/**
 * Estadísticas.
 *
 * @return array<int,array<string,mixed>>
 */
function iff_stats() {
	return iff_rows( 'stats' );
}

/**
 * Pasos del proceso.
 *
 * @return array<int,array<string,string>>
 */
function iff_steps() {
	return iff_rows( 'steps' );
}

/**
 * Motivos para elegir a la empresa.
 *
 * @return array<int,array<string,string>>
 */
function iff_reasons() {
	return iff_rows( 'why_items' );
}

/**
 * Tarjetas de la sección "Quiénes somos".
 *
 * @return array<int,array<string,string>>
 */
function iff_about_cards() {
	return iff_rows( 'about_cards' );
}

/**
 * Testimonios.
 *
 * @return array<int,array<string,mixed>>
 */
function iff_testimonials() {
	return iff_rows( 'testimonials' );
}

/**
 * Cantidad de estrellas de un testimonio.
 *
 * Si el campo está vacío devuelve 5, que es como se mostraban antes de que la
 * puntuación fuera editable.
 *
 * @param array $testimonial Fila del testimonio.
 * @return int Entre 0 y 5.
 */
function iff_rating( $testimonial ) {
	if ( ! isset( $testimonial['rating'] ) || '' === $testimonial['rating'] ) {
		return 5;
	}

	return max( 0, min( 5, (int) $testimonial['rating'] ) );
}

/**
 * Red de operadores del pie.
 *
 * @return array<int,array<string,string>>
 */
function iff_operators() {
	return iff_rows( 'operators' );
}

/**
 * Redes sociales del pie.
 *
 * @return array<int,array<string,string>>
 */
function iff_social_links() {
	return array(
		array(
			'icon' => 'facebook',
			'url'  => iff_content( 'social_facebook' ),
		),
		array(
			'icon' => 'instagram',
			'url'  => iff_content( 'social_instagram' ),
		),
		array(
			'icon' => 'linkedin',
			'url'  => iff_content( 'social_linkedin' ),
		),
	);
}
