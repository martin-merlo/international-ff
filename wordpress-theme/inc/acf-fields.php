<?php
/**
 * Registro del grupo de campos de ACF.
 *
 * Los campos se registran por código a partir de inc/content-schema.php, no
 * desde el panel. Así viajan con el tema, quedan versionados en git y no hay que
 * importar ningún JSON al instalar.
 *
 * El grupo se engancha a la página de portada: todo el contenido de la landing
 * queda en un solo lugar, sin crear entradas ni tipos de contenido de más.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ¿La instalación de ACF soporta campos Repeater?
 *
 * El Repeater es parte de ACF PRO. Si no está, el grupo se registra con campos
 * numerados (Servicio 1, Servicio 2…) que guardan lo mismo y se leen igual desde
 * inc/content.php: el frontend no cambia, solo la comodidad de edición.
 *
 * @return bool
 */
function iff_acf_supports_repeater() {
	static $supports = null;

	if ( null !== $supports ) {
		return $supports;
	}

	// Se exigen las dos señales a la vez. Solo mirar el tipo de campo no alcanza:
	// algunas versiones gratuitas registran un "repeater" de mentira para ofrecer
	// la actualización a PRO, y registrar campos que después no se saben dibujar
	// rompe la pantalla de edición.
	$is_pro = ( defined( 'ACF_PRO' ) && ACF_PRO )
		|| ( function_exists( 'acf_get_setting' ) && acf_get_setting( 'pro' ) );

	$has_field_type = function_exists( 'acf_get_field_type' ) && (bool) acf_get_field_type( 'repeater' );

	$supports = (bool) ( $is_pro && $has_field_type );

	/**
	 * Permite forzar el modo de edición de las listas.
	 *
	 * @param bool $supports Si se usan campos Repeater.
	 */
	$supports = (bool) apply_filters( 'iff_use_acf_repeater', $supports );

	return $supports;
}

/**
 * Traduce un campo del esquema a la definición que espera ACF.
 *
 * @param string $key    Clave del campo.
 * @param array  $field  Definición del esquema.
 * @param string $prefix Prefijo para la clave interna de ACF.
 * @return array<string,mixed>
 */
function iff_acf_build_field( $key, $field, $prefix = 'field_iff_' ) {
	$base = array(
		'key'          => $prefix . $key,
		'name'         => $key,
		'label'        => $field['label'],
		'instructions' => isset( $field['instructions'] ) ? $field['instructions'] : '',
	);

	switch ( $field['type'] ) {
		case 'textarea':
			return $base + array(
				'type'          => 'textarea',
				'rows'          => isset( $field['rows'] ) ? $field['rows'] : 4,
				'new_lines'     => '',
				'default_value' => isset( $field['default'] ) ? $field['default'] : '',
			);

		case 'lines':
			return $base + array(
				'type'          => 'textarea',
				'rows'          => 4,
				'new_lines'     => '',
				'default_value' => isset( $field['default'] ) && is_array( $field['default'] )
					? implode( "\n", $field['default'] )
					: '',
			);

		case 'number':
			$extra = array();
			if ( isset( $field['min'] ) ) {
				$extra['min'] = $field['min'];
			}
			if ( isset( $field['max'] ) ) {
				$extra['max'] = $field['max'];
			}

			return $base + $extra + array(
				'type'          => 'number',
				'default_value' => isset( $field['default'] ) ? $field['default'] : '',
			);

		case 'email':
			return $base + array(
				'type'          => 'email',
				'default_value' => isset( $field['default'] ) ? $field['default'] : '',
			);

		case 'url':
			return $base + array(
				'type'          => 'url',
				'default_value' => isset( $field['default'] ) ? $field['default'] : '',
			);

		case 'image':
			return $base + array(
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'mime_types'    => 'jpg,jpeg,png,webp',
			);

		case 'select':
			$choices = ( 'icons' === $field['choices'] ) ? iff_icon_choices() : $field['choices'];

			return $base + array(
				'type'          => 'select',
				'choices'       => $choices,
				'default_value' => isset( $field['default'] ) ? $field['default'] : '',
				'allow_null'    => 0,
				'ui'            => 1,
			);

		case 'text':
		default:
			return $base + array(
				'type'          => 'text',
				'default_value' => isset( $field['default'] ) ? $field['default'] : '',
			);
	}
}

/**
 * Construye un campo Repeater con sus subcampos.
 *
 * @param string $key   Clave del campo.
 * @param array  $field Definición del esquema.
 * @return array<string,mixed>
 */
function iff_acf_build_repeater( $key, $field ) {
	$sub_fields = array();

	foreach ( $field['sub_fields'] as $name => $sub ) {
		$sub_fields[] = iff_acf_build_field( $name, $sub, 'field_iff_' . $key . '_' );
	}

	$repeater = array(
		'key'          => 'field_iff_' . $key,
		'name'         => $key,
		'label'        => $field['label'],
		'instructions' => isset( $field['instructions'] ) ? $field['instructions'] : '',
		'type'         => 'repeater',
		'layout'       => 'block',
		'button_label' => isset( $field['button_label'] ) ? $field['button_label'] : 'Agregar',
		'sub_fields'   => $sub_fields,
	);

	if ( isset( $field['min'] ) ) {
		$repeater['min'] = $field['min'];
	}
	if ( isset( $field['max'] ) ) {
		$repeater['max'] = $field['max'];
	}

	return $repeater;
}

/**
 * Fallback sin Repeater: campos numerados, uno por fila.
 *
 * @param string $key   Clave del campo.
 * @param array  $field Definición del esquema.
 * @return array<int,array<string,mixed>>
 */
function iff_acf_build_flat_rows( $key, $field ) {
	$fields  = array();
	$rows    = isset( $field['default'] ) ? count( $field['default'] ) : 3;
	$heading = $field['label'];

	$fields[] = array(
		'key'     => 'field_iff_' . $key . '_intro',
		'name'    => $key . '_intro',
		'label'   => $heading,
		'type'    => 'message',
		'message' => ( isset( $field['instructions'] ) ? $field['instructions'] . ' ' : '' )
			. 'Para dejar un bloque fuera del sitio, vaciá todos sus campos.',
	);

	for ( $i = 1; $i <= $rows; $i++ ) {
		foreach ( $field['sub_fields'] as $name => $sub ) {
			$sub_copy          = $sub;
			$sub_copy['label'] = sprintf( '%s %d — %s', rtrim( $heading, 's' ), $i, $sub['label'] );

			if ( isset( $field['default'][ $i - 1 ][ $name ] ) && 'image' !== $sub['type'] ) {
				$default = $field['default'][ $i - 1 ][ $name ];
				$sub_copy['default'] = is_array( $default ) ? implode( "\n", $default ) : $default;
			}

			$fields[] = iff_acf_build_field( $key . '_' . $i . '_' . $name, $sub_copy );
		}
	}

	return $fields;
}

/**
 * Registra el grupo de campos.
 *
 * @return void
 */
function iff_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();

	foreach ( iff_content_schema() as $section_key => $section ) {
		$fields[] = array(
			'key'       => 'field_iff_tab_' . $section_key,
			'label'     => $section['label'],
			'type'      => 'tab',
			'placement' => 'left',
		);

		foreach ( $section['fields'] as $key => $field ) {
			if ( 'repeat' === $field['type'] ) {
				if ( iff_acf_supports_repeater() ) {
					$fields[] = iff_acf_build_repeater( $key, $field );
				} else {
					$fields = array_merge( $fields, iff_acf_build_flat_rows( $key, $field ) );
				}
				continue;
			}

			$fields[] = iff_acf_build_field( $key, $field );
		}
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_iff_site_content',
			'title'                 => 'Contenido del sitio',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'description'           => 'Todo el contenido de la portada. El diseño (colores, tipografías, animaciones) se maneja desde el tema.',
			'hide_on_screen'        => array( 'the_content', 'custom_fields', 'discussion', 'comments' ),
		)
	);
}
add_action( 'acf/init', 'iff_register_acf_fields' );
