<?php
/**
 * Banda de llamado a la acción.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_contact = iff_contact();
?>
<section class="relative isolate overflow-hidden">
	<img
		src="<?php echo esc_url( iff_img( 'cta-terminal.jpg' ) ); ?>"
		alt="Terminal de contenedores de noche"
		loading="lazy"
		width="1600"
		height="900"
		class="absolute inset-0 -z-20 h-full w-full object-cover"
	/>
	<div class="absolute inset-0 -z-10 bg-[oklch(0.21_0.062_258/0.9)]"></div>
	<div class="container-x py-24 text-center">
		<h2 class="mx-auto max-w-3xl text-3xl font-extrabold text-primary-foreground sm:text-5xl">
			Contáctenos y le responderemos a la brevedad posible
		</h2>
		<p class="mx-auto mt-5 max-w-2xl text-lg text-primary-foreground/85">
			Pida un presupuesto para su mudanza internacional u operación de comercio exterior y lo
			asesoraremos con gusto.
		</p>
		<div class="mt-9 flex flex-wrap justify-center gap-4">
			<a
				href="#contacto"
				class="inline-flex items-center gap-2 rounded-full bg-gold px-7 py-4 text-sm font-bold text-navy-deep transition-transform hover:-translate-y-0.5"
			>
				Solicitar presupuesto <?php iff_icon( 'arrow-right', 'h-4 w-4' ); ?>
			</a>
			<a
				href="tel:<?php echo esc_attr( $iff_contact['phone_href'] ); ?>"
				class="inline-flex items-center gap-2 rounded-full border border-primary-foreground/40 px-7 py-4 text-sm font-bold text-primary-foreground hover:bg-primary-foreground/10"
			>
				<?php iff_icon( 'phone', 'h-4 w-4' ); ?> <?php echo esc_html( $iff_contact['phone'] ); ?>
			</a>
		</div>
	</div>
</section>
