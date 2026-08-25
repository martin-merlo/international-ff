<?php
/**
 * Página estándar de WordPress.
 *
 * No la usa la landing, pero permite publicar páginas sueltas (política de
 * privacidad, términos) sin que el tema se rompa.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="bg-background pb-24 pt-36">
	<div class="container-x">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1 class="text-3xl font-extrabold text-foreground sm:text-4xl"><?php the_title(); ?></h1>
			<div class="mt-8 space-y-4 text-base leading-relaxed text-muted-foreground">
				<?php the_content(); ?>
			</div>
			<?php
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
