<?php
/**
 * Sección Quiénes somos.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_cards = iff_about_cards();
$iff_image = iff_image( 'about_image' );
?>
<section id="nosotros" class="bg-secondary py-24">
	<div class="container-x grid items-center gap-14 lg:grid-cols-2">
		<div class="overflow-hidden rounded-3xl shadow-[0_40px_80px_-50px_rgba(15,23,42,0.8)]">
			<img
				src="<?php echo esc_url( $iff_image['url'] ); ?>"
				alt="<?php echo esc_attr( $iff_image['alt'] ); ?>"
				loading="lazy"
				width="1400"
				height="900"
				class="h-full w-full object-cover"
			/>
		</div>
		<div>
			<p class="eyebrow text-navy-soft"><?php echo esc_html( iff_content( 'about_eyebrow' ) ); ?></p>
			<h2 class="mt-4 text-3xl font-extrabold text-foreground sm:text-4xl">
				<?php echo esc_html( iff_content( 'about_title' ) ); ?>
			</h2>
			<p class="mt-5 text-lg leading-relaxed text-muted-foreground">
				<?php echo esc_html( iff_content( 'about_body_1' ) ); ?>
			</p>
			<?php if ( '' !== iff_content( 'about_body_2' ) ) : ?>
				<p class="mt-4 text-lg leading-relaxed text-muted-foreground">
					<?php echo esc_html( iff_content( 'about_body_2' ) ); ?>
				</p>
			<?php endif; ?>
			<div class="mt-8 grid gap-4 sm:grid-cols-3">
				<?php foreach ( $iff_cards as $iff_card ) : ?>
					<div class="rounded-2xl border border-border bg-card p-5">
						<?php iff_icon( $iff_card['icon'], 'h-6 w-6 text-navy' ); ?>
						<p class="mt-3 text-sm font-bold text-card-foreground"><?php echo esc_html( $iff_card['title'] ); ?></p>
						<p class="mt-1 text-sm text-muted-foreground"><?php echo esc_html( $iff_card['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
