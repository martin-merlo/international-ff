<?php
/**
 * Puesta en marcha del contenido editable.
 *
 * Se ocupa de tres cosas:
 *
 *   1. Avisar en el panel si falta ACF o si no hay una portada estática.
 *   2. Crear la página de portada la primera vez que se activa el tema.
 *   3. Cargar en la base el contenido original, para que el editor abra el panel
 *      y encuentre todo lleno en vez de campos vacíos.
 *
 * La carga inicial NUNCA pisa contenido ya guardado: solo completa lo que está
 * vacío. Reactivar el tema es inofensivo.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IFF_SEED_FLAG = 'iff_content_seed_pending';

/**
 * Al activar el tema: portada estática + marcar la carga inicial como pendiente.
 *
 * @return void
 */
function iff_on_activate() {
	$front = (int) get_option( 'page_on_front' );

	if ( ! $front || ! get_post( $front ) ) {
		$existing = get_page_by_path( 'inicio' );

		if ( $existing ) {
			$front = $existing->ID;
		} else {
			$front = wp_insert_post(
				array(
					'post_title'   => 'Inicio',
					'post_name'    => 'inicio',
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_content' => '',
				)
			);
		}

		if ( $front && ! is_wp_error( $front ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front );
		}
	}

	// La carga se hace después, cuando ACF ya registró los campos.
	update_option( IFF_SEED_FLAG, 1 );
}
add_action( 'after_switch_theme', 'iff_on_activate' );

/**
 * Ejecuta la carga inicial pendiente.
 *
 * Corre en acf/init con prioridad tardía para que el grupo de campos ya esté
 * registrado.
 *
 * @return void
 */
function iff_maybe_seed_content() {
	if ( ! get_option( IFF_SEED_FLAG ) ) {
		return;
	}

	if ( ! function_exists( 'update_field' ) ) {
		return; // Sin ACF no hay dónde guardar: se reintenta en la próxima carga.
	}

	$post_id = iff_content_post_id();

	if ( ! $post_id ) {
		return;
	}

	iff_seed_content( $post_id );
	delete_option( IFF_SEED_FLAG );
}
add_action( 'acf/init', 'iff_maybe_seed_content', 20 );

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
				$value = isset( $row[ $name ] ) ? $row[ $name ] : '';

				if ( 'image' === $sub['type'] ) {
					$alt   = isset( $row['name'] ) ? 'Logo ' . $row['name'] : ( isset( $row['title'] ) ? $row['title'] : '' );
					$value = iff_import_default_image( $value, $alt );
				} elseif ( is_array( $value ) ) {
					$value = implode( "\n", $value );
				}

				$clean[ $name ] = $value;
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

			$value = isset( $row[ $name ] ) ? $row[ $name ] : '';

			if ( 'image' === $sub['type'] ) {
				$alt   = isset( $row['name'] ) ? 'Logo ' . $row['name'] : ( isset( $row['title'] ) ? $row['title'] : '' );
				$value = iff_import_default_image( $value, $alt );
			} elseif ( is_array( $value ) ) {
				$value = implode( "\n", $value );
			}

			if ( '' !== $value ) {
				update_field( $flat_key, $value, $post_id );
			}
		}
	}
}

/**
 * Sube a la biblioteca de medios una imagen que viene con el tema.
 *
 * Si ya la subió antes (se marca con el meta _iff_default_image) reutiliza esa,
 * así reactivar el tema no llena la biblioteca de duplicados.
 *
 * @param string $filename Archivo dentro de assets/img/.
 * @param string $alt      Texto alternativo.
 * @return int ID del adjunto, o 0 si no se pudo.
 */
function iff_import_default_image( $filename, $alt = '' ) {
	if ( '' === $filename ) {
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

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
	);

	update_post_meta( $attachment_id, '_iff_default_image', $filename );

	if ( '' !== $alt ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	}

	return (int) $attachment_id;
}

/**
 * Avisos en el panel cuando falta algo para poder editar el contenido.
 *
 * @return void
 */
function iff_admin_notices() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! class_exists( 'ACF' ) && ! function_exists( 'get_field' ) ) {
		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'International Freight Forwarder:', 'international-ff' ),
			wp_kses_post(
				sprintf(
					/* translators: %s: enlace al instalador de plugins. */
					__( 'para editar el contenido desde el panel hay que instalar y activar <a href="%s">Advanced Custom Fields</a>. Mientras tanto el sitio se ve perfecto, pero muestra el contenido que trae el tema y no se puede editar.', 'international-ff' ),
					esc_url( admin_url( 'plugin-install.php?s=advanced+custom+fields&tab=search&type=term' ) )
				)
			)
		);

		return;
	}

	if ( ! iff_content_post_id() ) {
		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'International Freight Forwarder:', 'international-ff' ),
			wp_kses_post(
				sprintf(
					/* translators: %s: enlace a los ajustes de lectura. */
					__( 'falta definir una página de portada estática en <a href="%s">Ajustes → Lectura</a> para poder editar el contenido.', 'international-ff' ),
					esc_url( admin_url( 'options-reading.php' ) )
				)
			)
		);

		return;
	}

	if ( ! iff_acf_supports_repeater() && function_exists( 'get_field' ) ) {
		$screen = get_current_screen();

		if ( $screen && 'page' === $screen->id ) {
			printf(
				'<div class="notice notice-info is-dismissible"><p><strong>%s</strong> %s</p></div>',
				esc_html__( 'International Freight Forwarder:', 'international-ff' ),
				esc_html__( 'esta instalación de ACF no incluye campos repetidores, así que los servicios, estadísticas, pasos, motivos, testimonios y operadores se editan como campos numerados. Se puede cambiar el texto de cada uno, pero no agregar ni reordenar bloques desde el panel. Con ACF PRO se convierten en listas dinámicas sin tocar el sitio.', 'international-ff' )
			);
		}
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

	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$bar->add_node(
		array(
			'id'    => 'iff-edit-content',
			'title' => __( 'Editar contenido del sitio', 'international-ff' ),
			'href'  => get_edit_post_link( $post_id, 'raw' ),
		)
	);
}
add_action( 'admin_bar_menu', 'iff_admin_bar_link', 80 );
