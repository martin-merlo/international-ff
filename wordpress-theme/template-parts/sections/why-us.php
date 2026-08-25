<?php
/**
 * Sección Por qué elegirnos.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_reasons = iff_reasons();
?>
<section class="bg-navy py-24">
	<div class="container-x">
		<p class="eyebrow text-gold"><?php echo esc_html( iff_content( 'why_eyebrow' ) ); ?></p>
		<h2 class="mt-4 max-w-2xl text-3xl font-extrabold text-primary-foreground sm:text-5xl">
			<?php echo esc_html( iff_content( 'why_title' ) ); ?>
		</h2>
		<div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $iff_reasons as $iff_reason ) : ?>
				<div class="rounded-3xl border border-primary-foreground/15 bg-primary-foreground/5 p-8 backdrop-blur transition-colors hover:border-gold/50">
					<?php iff_icon( $iff_reason['icon'], 'h-7 w-7 text-gold' ); ?>
					<h3 class="mt-5 text-lg font-bold text-primary-foreground"><?php echo esc_html( $iff_reason['title'] ); ?></h3>
					<p class="mt-2 text-sm leading-relaxed text-primary-foreground/80"><?php echo esc_html( $iff_reason['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
