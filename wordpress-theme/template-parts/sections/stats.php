<?php
/**
 * Banda de estadísticas con contadores animados.
 *
 * El número final va en data-count y el sufijo en data-suffix: main.js observa
 * el contenedor con IntersectionObserver (threshold 0.3, igual que el original)
 * y anima con requestAnimationFrame. Si el JS no corre, el valor final ya está
 * escrito en el HTML, así que la sección nunca queda en cero.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_stats = iff_stats();
?>
<section class="bg-navy-deep py-16">
	<div id="iff-stats" class="container-x grid grid-cols-2 gap-8 lg:grid-cols-4">
		<?php foreach ( $iff_stats as $iff_stat ) : ?>
			<div class="text-center">
				<p class="font-display text-4xl font-extrabold text-gold sm:text-5xl">
					<span
						data-counter
						data-count="<?php echo esc_attr( $iff_stat['value'] ); ?>"
						data-suffix="<?php echo esc_attr( $iff_stat['suffix'] ); ?>"
					><?php echo esc_html( number_format( $iff_stat['value'], 0, ',', '.' ) . $iff_stat['suffix'] ); ?></span>
				</p>
				<p class="mt-2 text-sm font-semibold text-primary-foreground/85"><?php echo esc_html( $iff_stat['label'] ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
