<?php
/**
 * 404.
 *
 * Mismo marcado que el notFoundComponent del root route original.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="flex min-h-screen items-center justify-center bg-background px-4">
	<div class="max-w-md text-center">
		<h1 class="text-7xl font-bold text-foreground">404</h1>
		<h2 class="mt-4 text-xl font-semibold text-foreground">Página no encontrada</h2>
		<p class="mt-2 text-sm text-muted-foreground">
			La página que busca no existe o fue movida.
		</p>
		<div class="mt-6">
			<a
				href="<?php echo esc_url( home_url( '/' ) ); ?>"
				class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
			>
				Volver al inicio
			</a>
		</div>
	</div>
</main>
<?php
get_footer();
