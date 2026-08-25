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
?>
<section id="nosotros" class="bg-secondary py-24">
	<div class="container-x grid items-center gap-14 lg:grid-cols-2">
		<div class="overflow-hidden rounded-3xl shadow-[0_40px_80px_-50px_rgba(15,23,42,0.8)]">
			<img
				src="<?php echo esc_url( iff_img( 'about-warehouse.jpg' ) ); ?>"
				alt="Operación logística en depósito"
				loading="lazy"
				width="1400"
				height="900"
				class="h-full w-full object-cover"
			/>
		</div>
		<div>
			<p class="eyebrow text-navy-soft">Quiénes somos</p>
			<h2 class="mt-4 text-3xl font-extrabold text-foreground sm:text-4xl">
				Una empresa familiar a su servicio
			</h2>
			<p class="mt-5 text-lg leading-relaxed text-muted-foreground">
				Somos una empresa con vasta experiencia en todo tipo de cargas y con interés total por
				la satisfacción de nuestros clientes. Nuestra trayectoria se remonta a la primera
				generación de la familia: Don Juan Alfredo Motta, junto a la Compañía Sudamericana de
				Vapores, fue pionero en la salida de cargas vía Océano Pacifico.
			</p>
			<p class="mt-4 text-lg leading-relaxed text-muted-foreground">
				Entendemos que cada carga implica la posibilidad de nuevos negocios y la realización de
				anhelos y sueños. Por eso ofrecemos un servicio cómodo, seguro, eficaz y ágil, con
				trato personal en cada tema concerniente a su carga.
			</p>
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
