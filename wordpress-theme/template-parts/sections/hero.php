<?php
/**
 * Sección Hero.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_contact = iff_contact();
$iff_bullets = iff_lines( 'hero_bullets' );
$iff_hero    = iff_image( 'hero_image' );
$iff_cta2    = iff_content( 'hero_cta2_url' );

if ( '' === $iff_cta2 ) {
	$iff_cta2 = $iff_contact['whatsapp'];
}
?>
<section id="inicio" class="relative isolate min-h-[92vh] overflow-hidden">
	<img
		src="<?php echo esc_url( $iff_hero['url'] ); ?>"
		alt="<?php echo esc_attr( $iff_hero['alt'] ); ?>"
		width="1920"
		height="1088"
		fetchpriority="high"
		class="absolute inset-0 -z-20 h-full w-full object-cover"
	/>
	<div class="absolute inset-0 -z-10 bg-[linear-gradient(100deg,oklch(0.21_0.062_258/0.94)_0%,oklch(0.21_0.062_258/0.82)_45%,oklch(0.21_0.062_258/0.55)_100%)]"></div>
	<div class="container-x flex min-h-[92vh] flex-col justify-center pb-24 pt-36">
		<div class="max-w-3xl animate-rise">
			<p class="eyebrow text-gold"><?php echo esc_html( iff_content( 'hero_eyebrow' ) ); ?></p>
			<h1 class="mt-5 text-4xl font-extrabold leading-[1.05] text-primary-foreground sm:text-6xl lg:text-7xl">
				<?php echo esc_html( iff_content( 'hero_title' ) ); ?>
			</h1>
			<p class="mt-6 max-w-2xl text-lg leading-relaxed text-primary-foreground/90">
				<?php echo esc_html( iff_content( 'hero_description' ) ); ?>
			</p>
			<div class="mt-9 flex flex-wrap gap-4">
				<a
					href="<?php echo esc_attr( iff_content( 'hero_cta_url' ) ); ?>"
					class="inline-flex items-center gap-2 rounded-full bg-gold px-7 py-4 text-sm font-bold text-navy-deep shadow-[0_18px_40px_-18px_rgba(0,0,0,0.8)] transition-transform hover:-translate-y-0.5"
				>
					<?php echo esc_html( iff_content( 'hero_cta_text' ) ); ?> <?php iff_icon( 'arrow-right', 'h-4 w-4' ); ?>
				</a>
				<a
					href="<?php echo esc_url( $iff_cta2 ); ?>"
					target="_blank"
					rel="noreferrer"
					class="inline-flex items-center gap-2 rounded-full border border-primary-foreground/40 bg-primary-foreground/10 px-7 py-4 text-sm font-bold text-primary-foreground backdrop-blur transition-colors hover:bg-primary-foreground/20"
				>
					<?php iff_icon( 'message-square', 'h-4 w-4' ); ?> <?php echo esc_html( iff_content( 'hero_cta2_text' ) ); ?>
				</a>
			</div>
			<div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-primary-foreground/85">
				<?php foreach ( $iff_bullets as $iff_bullet ) : ?>
					<span class="inline-flex items-center gap-2">
						<?php iff_icon( 'check-circle-2', 'h-4 w-4 text-gold' ); ?> <?php echo esc_html( $iff_bullet ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
