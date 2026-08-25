<?php
/**
 * Sección Cómo trabajamos.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_steps = iff_steps();
$iff_total = count( $iff_steps );
?>
<section class="bg-background py-24">
	<div class="container-x">
		<p class="eyebrow text-navy-soft"><?php echo esc_html( iff_content( 'process_eyebrow' ) ); ?></p>
		<h2 class="mt-4 text-3xl font-extrabold text-foreground sm:text-5xl">
			<?php echo esc_html( iff_content( 'process_title' ) ); ?>
		</h2>
		<div class="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $iff_steps as $iff_index => $iff_step ) : ?>
				<div class="relative rounded-3xl bg-secondary p-8">
					<span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy font-display text-lg font-extrabold text-primary-foreground">
						<?php echo esc_html( $iff_index + 1 ); ?>
					</span>
					<h3 class="mt-5 text-lg font-bold text-foreground"><?php echo esc_html( $iff_step['title'] ); ?></h3>
					<p class="mt-2 text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $iff_step['text'] ); ?></p>
					<?php if ( $iff_index < $iff_total - 1 ) : ?>
						<span class="absolute right-6 top-12 hidden text-gold lg:block">
							<?php iff_icon( 'arrow-right', 'h-5 w-5' ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
