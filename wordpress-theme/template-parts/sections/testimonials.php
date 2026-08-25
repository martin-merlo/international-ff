<?php
/**
 * Sección Testimonios.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_testimonials = iff_testimonials();
?>
<section id="testimonios" class="bg-background py-24">
	<div class="container-x">
		<p class="eyebrow text-navy-soft"><?php echo esc_html( iff_content( 'testimonials_eyebrow' ) ); ?></p>
		<h2 class="mt-4 text-3xl font-extrabold text-foreground sm:text-5xl">
			<?php echo esc_html( iff_content( 'testimonials_title' ) ); ?>
		</h2>
		<div class="mt-14 grid gap-8 lg:grid-cols-3">
			<?php foreach ( $iff_testimonials as $iff_testimonial ) : ?>
				<figure class="rounded-3xl border border-border bg-card p-8 shadow-[0_24px_60px_-45px_rgba(15,23,42,0.7)]">
					<?php iff_icon( 'quote', 'h-8 w-8 text-gold' ); ?>
					<blockquote class="mt-5 text-base leading-relaxed text-card-foreground">
						<?php echo esc_html( $iff_testimonial['quote'] ); ?>
					</blockquote>
					<div class="mt-6 flex gap-1">
						<?php for ( $iff_star = 0; $iff_star < iff_rating( $iff_testimonial ); $iff_star++ ) : ?>
							<?php iff_icon( 'star', 'h-4 w-4 fill-gold text-gold' ); ?>
						<?php endfor; ?>
					</div>
					<figcaption class="mt-4">
						<p class="font-bold text-foreground"><?php echo esc_html( $iff_testimonial['name'] ); ?></p>
						<p class="text-sm text-muted-foreground"><?php echo esc_html( $iff_testimonial['role'] ); ?></p>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
