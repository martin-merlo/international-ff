<?php
/**
 * Router del servidor de preview (`php -S localhost:PORT router.php`).
 *
 * Sirve los assets estáticos del tema tal cual y cualquier otra ruta la resuelve
 * con front-page.php, igual que haría WordPress con la home.
 *
 * SOLO PARA DESARROLLO.
 *
 * @package International_FF
 */

$theme = dirname( __DIR__, 2 );
$path  = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$file  = $theme . $path;

if ( '/' !== $path && file_exists( $file ) && is_file( $file ) ) {
	$types = array(
		'css'   => 'text/css',
		'js'    => 'application/javascript',
		'png'   => 'image/png',
		'jpg'   => 'image/jpeg',
		'woff2' => 'font/woff2',
	);
	$ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
	if ( isset( $types[ $ext ] ) ) {
		header( 'Content-Type: ' . $types[ $ext ] );
	}
	readfile( $file );
	return true;
}

require __DIR__ . '/wp-stub.php';
require $theme . '/functions.php';

header( 'Content-Type: text/html; charset=UTF-8' );
require $theme . '/front-page.php';
