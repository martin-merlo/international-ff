<?php
/**
 * Puesta en marcha del contenido editable.
 *
 * Se ocupa de tres cosas:
 *
 *   1. Crear (o reutilizar) la página de portada y dejarla configurada.
 *   2. Cargar en la base el contenido original, para que el editor abra el panel
 *      y encuentre todo lleno en vez de campos vacíos.
 *   3. Avisar en el panel si falta algo, con botones para resolverlo.
 *
 * Regla de oro de este archivo: NADA de lo que hace acá puede tumbar el sitio.
 * La carga inicial corre solo en el panel, para administradores, envuelta en
 * try/catch, y si algo falla se anota el error y se sigue: el frontend nunca
 * depende de esto, porque siempre tiene el contenido del tema como respaldo.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'IFF_SEED_FLAG' ) ) {
	define( 'IFF_SEED_FLAG', 'iff_content_seed_pending' );
}

if ( ! defined( 'IFF_SEED_ERROR' ) ) {
	define( 'IFF_SEED_ERROR', 'iff_content_seed_error' );
}

/**
 * Al activar el tema: dejar la portada lista y marcar la carga como pendiente.
 *
 * @return void
 */
function iff_on_activate() {
	iff_ensure_front_page();
	update_option( IFF_SEED_FLAG, 1 );
	delete_option( IFF_SEED_ERROR );
}
add_action( 'after_switch_theme', 'iff_on_activate' );

/**
 * Garantiza que haya una página de portada estática configurada.
 *
 * Si el sitio ya tenía una portada propia se respeta y se usa esa: no se pisa la
 * decisión de nadie ni se crean páginas de más.
 *
 * @return int ID de la portada, 0 si no se pudo dejar configurada.
 */
function iff_ensure_front_page() {
	$front = (int) get_option( 'page_on_front' );

	// Ya hay una portada válida: se usa esa.
	if ( $front && get_post( $front ) && 'trash' !== get_post_status( $front ) ) {
		if ( 'page' !== get_option( 'show_on_front' ) ) {
			update_option( 'show_on_front', 'page' );
		}

		return $front;
	}

	// Reutiliza una página "Inicio" previa si existe, antes de crear otra.
	$existing = get_page_by_path( 'inicio' );

	if ( $existing && 'trash' !== $existing->post_status ) {
		$front = (int) $existing->ID;
	} else {
		$created = wp_insert_post(
			array(
				'post_title'   => 'Inicio',
				'post_name'    => 'inicio',
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $created ) || ! $created ) {
			return 0;
		}

		$front = (int) $created;
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front );

	return $front;
}

/**
 * Corre la carga inicial pendiente, si corresponde.
 *
 * Solo en el panel y solo para quien pueda administrar: una visita al sitio
 * jamás dispara esto.
 *
 * @return void
 */
function iff_maybe_seed_content() {
	if ( ! is_admin() || wp_doing_ajax() || ! get_option( IFF_SEED_FLAG ) ) {
		return;
	}

	if ( ! function_exists( 'update_field' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$post_id = iff_ensure_front_page();

	if ( ! $post_id ) {
		return;
	}

	// Se saca la marca ANTES de empezar: si algo explota, no queda un bucle de
	// errores en cada carga del panel.
	delete_option( IFF_SEED_FLAG );

	try {
		iff_seed_content( $post_id );
		delete_option( IFF_SEED_ERROR );
	} catch ( Throwable $e ) {
		update_option(
			IFF_SEED_ERROR,
			sprintf(
				'%s (%s:%d)',
				$e->getMessage(),
				basename( $e->getFile() ),
				$e->getLine()
			)
		);
	}
}
add_action( 'admin_init', 'iff_maybe_seed_content', 20 );

/**
 * Escribe en la base el contenido original, sin pisar lo que ya esté cargado.
 *
 * @param int $post_id Página de portada.
 * @return void
 */
function iff_seed_content( $post_id ) {
	foreach ( iff_content_schema() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			if ( 'repeat' === $field['type'] ) {
				iff_seed_rows( $key, $field, $post_id );
				continue;
			}

			if ( 'image' === $field['type'] ) {
				iff_seed_image_field( $key, $field, $post_id );
				continue;
			}

			if ( iff_not_empty( get_field( $key, $post_id ) ) ) {
				continue;
			}

			$value = isset( $field['default'] ) ? $field['default'] : '';

			if ( is_array( $value ) ) {
				$value = implode( "\n", $value );
			}

			update_field( $key, $value, $post_id );
		}
	}
}

/**
 * Carga inicial de un campo imagen suelto.
 *
 * @param string $key     Clave del campo.
 * @param array  $field   Definición.
 * @param int    $post_id Portada.
 * @return void
 */
function iff_seed_image_field( $key, $field, $post_id ) {
	if ( iff_not_empty( get_field( $key, $post_id ) ) ) {
		return;
	}

	$attachment_id = iff_import_default_image(
		isset( $field['default'] ) ? $field['default'] : '',
		isset( $field['default_alt'] ) ? $field['default_alt'] : ''
	);

	if ( $attachment_id ) {
		update_field( $key, $attachment_id, $post_id );
	}
}

/**
 * Carga inicial de un campo repetidor (Repeater o campos numerados).
 *
 * @param string $key     Clave del campo.
 * @param array  $field   Definición.
 * @param int    $post_id Portada.
 * @return void
 */
function iff_seed_rows( $key, $field, $post_id ) {
	$defaults = isset( $field['default'] ) ? $field['default'] : array();

	if ( empty( $defaults ) ) {
		return;
	}

	if ( iff_acf_supports_repeater() ) {
		if ( iff_not_empty( get_field( $key, $post_id ) ) ) {
			return;
		}

		$rows = array();

		foreach ( $defaults as $row ) {
			$clean = array();
			foreach ( $field['sub_fields'] as $name => $sub ) {
				$clean[ $name ] = iff_seed_value( $row, $name, $sub );
			}
			$rows[] = $clean;
		}

		update_field( $key, $rows, $post_id );

		return;
	}

	// Fallback: un campo por fila y subcampo.
	foreach ( $defaults as $index => $row ) {
		$i = $index + 1;

		foreach ( $field['sub_fields'] as $name => $sub ) {
			$flat_key = $key . '_' . $i . '_' . $name;

			if ( iff_not_empty( get_field( $flat_key, $post_id ) ) ) {
				continue;
			}

			$value = iff_seed_value( $row, $name, $sub );

			if ( '' !== $value && null !== $value ) {
				update_field( $flat_key, $value, $post_id );
			}
		}
	}
}

/**
 * Prepara el valor de un subcampo para guardarlo.
 *
 * @param array  $row  Fila con los valores por defecto.
 * @param string $name Nombre del subcampo.
 * @param array  $sub  Definición del subcampo.
 * @return mixed
 */
function iff_seed_value( $row, $name, $sub ) {
	$value = isset( $row[ $name ] ) ? $row[ $name ] : '';

	if ( 'image' === $sub['type'] ) {
		$alt = '';
		if ( isset( $row['name'] ) ) {
			$alt = 'Logo ' . $row['name'];
		} elseif ( isset( $row['title'] ) ) {
			$alt = $row['title'];
		}

		return iff_import_default_image( $value, $alt );
	}

	if ( is_array( $value ) ) {
		return implode( "\n", $value );
	}

	return $value;
}

/**
 * Sube a la biblioteca de medios una imagen que viene con el tema.
 *
 * Si falla (permisos de la carpeta uploads, memoria, GD ausente) devuelve 0 y no
 * pasa nada: el campo queda vacío y el frontend usa la imagen del tema, que se ve
 * exactamente igual.
 *
 * @param string $filename Archivo dentro de assets/img/.
 * @param string $alt      Texto alternativo.
 * @return int ID del adjunto, o 0 si no se pudo.
 */
function iff_import_default_image( $filename, $alt = '' ) {
	if ( '' === $filename || ! is_string( $filename ) ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_iff_default_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $filename, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$path = get_template_directory() . '/assets/img/' . $filename;

	if ( ! file_exists( $path ) ) {
		return 0;
	}

	try {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$upload = wp_upload_bits( $filename, null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( ! empty( $upload['error'] ) ) {
			return 0;
		}

		$filetype      = wp_check_filetype( $upload['file'] );
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => $filetype['type'],
				'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return 0;
		}

		// Generar miniaturas es lo más pesado de todo esto. Si falla por memoria
		// el adjunto igual sirve: se usa la imagen completa.
		$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );

		if ( ! is_wp_error( $metadata ) && ! empty( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		update_post_meta( $attachment_id, '_iff_default_image', $filename );

		if ( '' !== $alt ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
		}

		return (int) $attachment_id;
	} catch ( Throwable $e ) {
		return 0;
	}
}

/**
 * Acción manual para dejar la portada configurada desde el aviso del panel.
 *
 * @return void
 */
function iff_handle_setup_action() {
	if ( ! isset( $_GET['iff_action'] ) || 'setup' !== $_GET['iff_action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'iff_setup' );

	iff_ensure_front_page();
	update_option( IFF_SEED_FLAG, 1 );

	wp_safe_redirect( admin_url( 'index.php' ) );
	exit;
}
add_action( 'admin_init', 'iff_handle_setup_action', 5 );

/**
 * Imprime un aviso en el panel.
 *
 * @param string $type    warning|error|info|success.
 * @param string $message Mensaje (admite HTML acotado).
 * @return void
 */
function iff_notice( $type, $message ) {
	printf(
		'<div class="notice notice-%s"><p><strong>%s</strong> %s</p></div>',
		esc_attr( $type ),
		esc_html__( 'International Freight Forwarder:', 'international-ff' ),
		wp_kses_post( $message )
	);
}

/**
 * Avisos del panel cuando falta algo para poder editar el contenido.
 *
 * @return void
 */
function iff_admin_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! function_exists( 'get_field' ) ) {
		iff_notice(
			'warning',
			sprintf(
				/* translators: %s: enlace al instalador de plugins. */
				__( 'para editar el contenido desde el panel hay que instalar y activar <a href="%s">Advanced Custom Fields</a>. Mientras tanto el sitio se ve perfecto, pero muestra el contenido que trae el tema y no se puede editar.', 'international-ff' ),
				esc_url( admin_url( 'plugin-install.php?s=advanced+custom+fields&tab=search&type=term' ) )
			)
		);

		return;
	}

	$post_id = iff_content_post_id();

	if ( ! $post_id ) {
		iff_notice(
			'warning',
			sprintf(
				/* translators: %s: enlace a la acción de configuración. */
				__( 'falta la página de portada donde vive el contenido. <a href="%s">Crearla ahora</a>.', 'international-ff' ),
				esc_url( wp_nonce_url( admin_url( 'index.php?iff_action=setup' ), 'iff_setup' ) )
			)
		);

		return;
	}

	$error = get_option( IFF_SEED_ERROR );

	if ( $error ) {
		iff_notice(
			'error',
			sprintf(
				/* translators: 1: mensaje de error, 2: enlace para reintentar. */
				__( 'no se pudo cargar el contenido inicial: <code>%1$s</code>. El sitio funciona igual con el contenido del tema. <a href="%2$s">Reintentar</a>.', 'international-ff' ),
				esc_html( $error ),
				esc_url( wp_nonce_url( admin_url( 'index.php?iff_action=setup' ), 'iff_setup' ) )
			)
		);
	}
}
add_action( 'admin_notices', 'iff_admin_notices' );

/**
 * Enlace directo a la edición del contenido en la barra de administración.
 *
 * @param WP_Admin_Bar $bar Barra de administración.
 * @return void
 */
function iff_admin_bar_link( $bar ) {
	$post_id = iff_content_post_id();

	if ( ! $post_id || ! get_post( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$link = get_edit_post_link( $post_id, 'raw' );

	if ( ! $link ) {
		return;
	}

	$bar->add_node(
		array(
			'id'    => 'iff-edit-content',
			'title' => __( 'Editar contenido del sitio', 'international-ff' ),
			'href'  => $link,
		)
	);
}
add_action( 'admin_bar_menu', 'iff_admin_bar_link', 80 );
