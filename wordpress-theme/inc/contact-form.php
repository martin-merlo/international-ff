<?php
/**
 * Envío del formulario de contacto por email.
 *
 * El formulario sigue funcionando igual que siempre para el visitante: abre
 * WhatsApp con la consulta escrita. Lo que se agrega es que, además, manda una
 * copia por email a la casilla configurada en el panel.
 *
 * El envío va por AJAX y en segundo plano, DESPUÉS de abrir WhatsApp, para no
 * demorar ni bloquear nada. Si el email falla, el visitante no se entera: su
 * consulta ya salió por WhatsApp.
 *
 * Sobre el remitente: el "De:" tiene que ser una dirección del propio dominio.
 * Si se pone un Gmail o un Hotmail, los servidores de correo lo leen como
 * suplantación de identidad (falla SPF/DMARC) y el mensaje termina en spam o
 * directamente rechazado. El email de quien consulta va en "Responder a", así
 * que al darle Responder se le contesta al cliente.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nombre de la acción AJAX.
 */
const IFF_FORM_ACTION = 'iff_contact_submit';

/**
 * Registra el endpoint para visitantes (no hace falta estar logueado).
 */
add_action( 'wp_ajax_' . IFF_FORM_ACTION, 'iff_handle_contact_submit' );
add_action( 'wp_ajax_nopriv_' . IFF_FORM_ACTION, 'iff_handle_contact_submit' );

/**
 * Dirección de destino de las consultas.
 *
 * @return array<int,string>
 */
function iff_form_recipients() {
	$raw = iff_content( 'form_email_to' );

	if ( '' === trim( $raw ) ) {
		$raw = get_option( 'admin_email' );
	}

	$emails = array_map( 'trim', explode( ',', $raw ) );
	$emails = array_filter( $emails, 'is_email' );

	return array_values( $emails );
}

/**
 * Dirección remitente.
 *
 * Si no se configuró una, se arma con el dominio del sitio, que es lo que los
 * filtros antispam esperan ver.
 *
 * @return string
 */
function iff_form_from_address() {
	$configured = trim( iff_content( 'form_email_from' ) );

	if ( '' !== $configured && is_email( $configured ) ) {
		return $configured;
	}

	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$host = preg_replace( '/^www\./i', '', (string) $host );

	return 'no-reply@' . $host;
}

/**
 * Procesa el envío del formulario.
 *
 * @return void
 */
function iff_handle_contact_submit() {
	if ( ! check_ajax_referer( IFF_FORM_ACTION, 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => 'sesion_vencida' ), 403 );
	}

	// Trampa para bots: es un campo oculto que una persona nunca completa.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => 'ok' ) );
	}

	if ( ! iff_flag( 'form_email_enabled' ) ) {
		wp_send_json_success( array( 'message' => 'email_desactivado' ) );
	}

	// Límite por IP: 5 envíos cada 10 minutos.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'iff_form_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= 5 ) {
		wp_send_json_error( array( 'message' => 'demasiados_envios' ), 429 );
	}

	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$nombre   = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$telefono = isset( $_POST['telefono'] ) ? sanitize_text_field( wp_unslash( $_POST['telefono'] ) ) : '';
	$servicio = isset( $_POST['servicio'] ) ? sanitize_text_field( wp_unslash( $_POST['servicio'] ) ) : '';
	$mensaje  = isset( $_POST['mensaje'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mensaje'] ) ) : '';

	$nombre   = mb_substr( $nombre, 0, 100 );
	$telefono = mb_substr( $telefono, 0, 40 );
	$servicio = mb_substr( $servicio, 0, 120 );
	$mensaje  = mb_substr( $mensaje, 0, 1000 );

	if ( '' === $nombre || '' === $mensaje || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'datos_incompletos' ), 400 );
	}

	$recipients = iff_form_recipients();

	if ( empty( $recipients ) ) {
		wp_send_json_error( array( 'message' => 'sin_destinatario' ), 500 );
	}

	$subject = trim( iff_content( 'form_email_subject' ) );
	$subject = ( '' !== $subject ? $subject : 'Nueva consulta desde el sitio' ) . ' — ' . $nombre;

	$lines = array(
		'Nueva consulta desde ' . home_url( '/' ),
		'',
		'Nombre:   ' . $nombre,
		'Email:    ' . $email,
		'Teléfono: ' . ( '' !== $telefono ? $telefono : '(no indicado)' ),
		'Servicio: ' . $servicio,
		'',
		'Mensaje:',
		$mensaje,
		'',
		'---',
		'Enviado el ' . wp_date( 'd/m/Y H:i' ) . ' desde el formulario de contacto.',
		'Respondiendo a este correo le escribís directamente a ' . $email . '.',
	);

	$from    = iff_form_from_address();
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', iff_content( 'company_name' ), $from ),
		sprintf( 'Reply-To: %s <%s>', $nombre, $email ),
	);

	$sent = wp_mail( $recipients, $subject, implode( "\n", $lines ), $headers );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => 'fallo_envio' ), 500 );
	}

	wp_send_json_success( array( 'message' => 'ok' ) );
}

/**
 * Deja registrado el motivo cuando wp_mail() falla.
 *
 * Sin esto, un fallo de correo en un hosting compartido es una caja negra.
 *
 * @param WP_Error $error Error de PHPMailer.
 * @return void
 */
function iff_log_mail_failure( $error ) {
	update_option( 'iff_last_mail_error', $error->get_error_message() . ' (' . gmdate( 'Y-m-d H:i' ) . ' UTC)' );
}
add_action( 'wp_mail_failed', 'iff_log_mail_failure' );
