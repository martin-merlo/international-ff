<?php
/**
 * Sección Servicios.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_services = iff_services();
?>
<section id="servicios" class="bg-background py-24">
	<div class="container-x">
		<p class="eyebrow text-navy-soft">Nuestros servicios</p>
		<h2 class="mt-4 max-w-2xl text-3xl font-extrabold text-foreground sm:text-5xl">
			Soluciones completas de logística internacional
		</h2>
		<p class="mt-5 max-w-2xl text-lg text-muted-foreground">
			Confíe sus bienes en nosotros por cualquier medio: aéreo, marítimo o terrestre. Mas de
			30 años de experiencia avalan nuestra trayectoria.
		</p>

		<div class="mt-14 grid gap-8 lg:grid-cols-3">
			<?php foreach ( $iff_services as $iff_service ) : ?>
				<article class="group overflow-hidden rounded-3xl border border-border bg-card shadow-[0_24px_60px_-40px_rgba(15,23,42,0.6)] transition-transform duration-300 hover:-translate-y-1.5">
					<div class="relative h-56 overflow-hidden">
						<img
							src="<?php echo esc_url( iff_img( $iff_service['image'] ) ); ?>"
							alt="<?php echo esc_attr( $iff_service['title'] ); ?>"
							loading="lazy"
							width="1024"
							height="768"
							class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
						/>
					</div>
					<div class="p-8">
						<h3 class="text-xl font-bold text-card-foreground"><?php echo esc_html( $iff_service['title'] ); ?></h3>
						<p class="mt-3 text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $iff_service['text'] ); ?></p>
						<ul class="mt-6 space-y-2.5">
							<?php foreach ( $iff_service['points'] as $iff_point ) : ?>
								<li class="flex items-start gap-2.5 text-sm text-foreground">
									<?php iff_icon( 'check', 'mt-0.5 h-4 w-4 shrink-0 text-navy' ); ?>
									<?php echo esc_html( $iff_point ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
