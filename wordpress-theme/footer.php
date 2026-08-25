<?php
/**
 * Pie del sitio + botón flotante de WhatsApp.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_contact   = iff_contact();
$iff_operators = iff_operators();
$iff_social    = iff_social_links();
?>
	<footer class="bg-navy-deep pb-10 pt-16">
		<div class="container-x grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr]">
			<div>
				<div class="flex items-center gap-3">
					<img
						src="<?php echo esc_url( iff_img( 'logo-iff.png' ) ); ?>"
						alt="International Freight Forwarder"
						width="56"
						height="56"
						loading="lazy"
						class="h-14 w-14 shrink-0 rounded-full object-contain"
					/>
					<span class="font-display text-lg font-extrabold text-primary-foreground">
						International Freight Forwarder
					</span>
				</div>
				<p class="mt-5 max-w-md text-sm leading-relaxed text-primary-foreground/75">
					Mudanzas internacionales, comercio exterior y agente de cargas. Oficina central en
					Mendoza, Argentina, con oficinas asociadas en el resto del mundo.
				</p>
				<div class="mt-6 flex gap-3">
					<?php foreach ( $iff_social as $iff_link ) : ?>
						<?php // OJO: URL placeholder heredada del sitio original, pendiente de los perfiles reales. ?>
						<a
							href="<?php echo esc_attr( $iff_link['url'] ); ?>"
							aria-label="Red social"
							class="grid h-10 w-10 place-items-center rounded-full border border-primary-foreground/25 text-primary-foreground transition-colors hover:border-gold hover:text-gold"
						>
							<?php iff_icon( $iff_link['icon'], 'h-4 w-4' ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<div>
				<h3 class="text-sm font-bold uppercase tracking-widest text-gold">
					Red de operadores
				</h3>
				<ul class="mt-5 grid max-w-[210px] grid-cols-3 gap-2">
					<?php foreach ( $iff_operators as $iff_operator ) : ?>
						<li class="overflow-hidden rounded-md">
							<img
								src="<?php echo esc_url( iff_img( $iff_operator['image'] ) ); ?>"
								alt="Logo <?php echo esc_attr( $iff_operator['name'] ); ?>"
								width="66"
								height="66"
								loading="lazy"
								class="block h-[66px] w-[66px] object-cover"
							/>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div>
				<h3 class="text-sm font-bold uppercase tracking-widest text-gold">Contacto</h3>
				<ul class="mt-5 space-y-3 text-sm text-primary-foreground/80">
					<li class="flex gap-3">
						<?php iff_icon( 'map-pin', 'mt-0.5 h-4 w-4 shrink-0 text-gold' ); ?>
						<?php echo esc_html( $iff_contact['address'] ); ?>
					</li>
					<li class="flex gap-3">
						<?php iff_icon( 'phone', 'h-4 w-4 shrink-0 text-gold' ); ?>
						<a href="tel:<?php echo esc_attr( $iff_contact['phone_href'] ); ?>" class="hover:text-gold">
							<?php echo esc_html( $iff_contact['phone'] ); ?>
						</a>
					</li>
					<li class="flex gap-3">
						<?php iff_icon( 'mail', 'mt-0.5 h-4 w-4 shrink-0 text-gold' ); ?>
						<a href="mailto:<?php echo esc_attr( $iff_contact['email'] ); ?>" class="break-all hover:text-gold">
							<?php echo esc_html( $iff_contact['email'] ); ?>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<div class="container-x mt-12 border-t border-primary-foreground/15 pt-6">
			<p class="text-xs text-primary-foreground/60">
				© <?php echo esc_html( gmdate( 'Y' ) ); ?> International Freight Forwarder. Todos los derechos
				reservados.
			</p>
		</div>
	</footer>

	<?php get_template_part( 'template-parts/partials/whatsapp-button' ); ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
