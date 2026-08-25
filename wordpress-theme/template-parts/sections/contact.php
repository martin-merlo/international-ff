<?php
/**
 * Sección Contacto: datos, mapa y formulario.
 *
 * El formulario replica exactamente el comportamiento original: no se envía al
 * servidor ni manda mails. main.js intercepta el submit, arma el mensaje con los
 * cinco campos y abre WhatsApp con el texto listo para enviar.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_contact  = iff_contact();
$iff_services = iff_services();
$iff_people   = array(
	array(
		'name' => 'Juan Motta',
		'tel'  => $iff_contact['phone'],
		'href' => $iff_contact['phone_href'],
		'mail' => $iff_contact['email'],
	),
	array(
		'name' => 'Martin Ruggeri',
		'tel'  => $iff_contact['phone2'],
		'href' => $iff_contact['phone2_href'],
		'mail' => $iff_contact['email2'],
	),
);
$iff_fields   = array(
	array(
		'id'        => 'nombre',
		'label'     => 'Nombre y apellido',
		'type'      => 'text',
		'maxlength' => 100,
		'required'  => true,
	),
	array(
		'id'        => 'email',
		'label'     => 'Email',
		'type'      => 'email',
		'maxlength' => 255,
		'required'  => true,
	),
	array(
		'id'        => 'telefono',
		'label'     => 'Teléfono',
		'type'      => 'tel',
		'maxlength' => 100,
		'required'  => false,
	),
);
$iff_input_class = 'mt-2 w-full rounded-xl border border-input bg-background px-4 py-3 text-sm text-foreground outline-none focus:border-navy focus:ring-2 focus:ring-navy/20';
?>
<section id="contacto" class="bg-secondary py-24">
	<div class="container-x grid gap-12 lg:grid-cols-2">
		<div>
			<p class="eyebrow text-navy-soft">Contacto</p>
			<h2 class="mt-4 text-3xl font-extrabold text-foreground sm:text-4xl">Contáctenos</h2>
			<p class="mt-5 text-lg text-muted-foreground">
				Escríbanos por cualquier necesidad de servicio de mudanzas o comercio exterior y
				responderemos a la brevedad posible.
			</p>

			<div class="mt-8 space-y-4">
				<div class="flex items-start gap-4 rounded-2xl bg-card p-5">
					<?php iff_icon( 'map-pin', 'mt-0.5 h-5 w-5 shrink-0 text-navy' ); ?>
					<p class="text-sm text-card-foreground"><?php echo esc_html( $iff_contact['address'] ); ?></p>
				</div>
				<div class="grid gap-4 sm:grid-cols-2">
					<?php foreach ( $iff_people as $iff_person ) : ?>
						<div class="rounded-2xl bg-card p-5">
							<p class="font-bold text-card-foreground"><?php echo esc_html( $iff_person['name'] ); ?></p>
							<a
								href="tel:<?php echo esc_attr( $iff_person['href'] ); ?>"
								class="mt-3 flex items-center gap-2 text-sm text-muted-foreground hover:text-navy"
							>
								<?php iff_icon( 'phone', 'h-4 w-4' ); ?> <?php echo esc_html( $iff_person['tel'] ); ?>
							</a>
							<a
								href="mailto:<?php echo esc_attr( $iff_person['mail'] ); ?>"
								class="mt-2 flex items-center gap-2 break-all text-sm text-muted-foreground hover:text-navy"
							>
								<?php iff_icon( 'mail', 'h-4 w-4 shrink-0' ); ?> <?php echo esc_html( $iff_person['mail'] ); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="mt-6 overflow-hidden rounded-2xl border border-border">
				<iframe
					title="Ubicación de la oficina en Mendoza"
					src="<?php echo esc_url( $iff_contact['map_embed'] ); ?>"
					class="h-72 w-full"
					loading="lazy"
				></iframe>
			</div>
		</div>

		<form
			id="iff-contact-form"
			data-whatsapp-number="<?php echo esc_attr( $iff_contact['whatsapp_number'] ); ?>"
			class="h-fit rounded-3xl bg-card p-8 shadow-[0_30px_70px_-50px_rgba(15,23,42,0.8)]"
		>
			<h3 class="text-xl font-bold text-card-foreground">Solicitar presupuesto</h3>
			<div class="mt-6 space-y-4">
				<?php foreach ( $iff_fields as $iff_field ) : ?>
					<div>
						<label for="<?php echo esc_attr( $iff_field['id'] ); ?>" class="text-sm font-semibold text-foreground">
							<?php echo esc_html( $iff_field['label'] ); ?>
						</label>
						<input
							id="<?php echo esc_attr( $iff_field['id'] ); ?>"
							name="<?php echo esc_attr( $iff_field['id'] ); ?>"
							type="<?php echo esc_attr( $iff_field['type'] ); ?>"
							maxlength="<?php echo esc_attr( $iff_field['maxlength'] ); ?>"
							<?php echo $iff_field['required'] ? 'required' : ''; ?>
							class="<?php echo esc_attr( $iff_input_class ); ?>"
						/>
					</div>
				<?php endforeach; ?>
				<div>
					<label for="servicio" class="text-sm font-semibold text-foreground">Servicio</label>
					<select id="servicio" name="servicio" class="<?php echo esc_attr( $iff_input_class ); ?>">
						<?php foreach ( $iff_services as $iff_service ) : ?>
							<option><?php echo esc_html( $iff_service['title'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div>
					<label for="mensaje" class="text-sm font-semibold text-foreground">Mensaje</label>
					<textarea
						id="mensaje"
						name="mensaje"
						rows="4"
						maxlength="1000"
						required
						class="<?php echo esc_attr( $iff_input_class ); ?>"
					></textarea>
				</div>
			</div>
			<button
				type="submit"
				class="mt-6 w-full rounded-full bg-navy px-6 py-4 text-sm font-bold text-primary-foreground transition-colors hover:bg-navy-deep"
			>
				Enviar consulta
			</button>
			<p id="iff-form-sent" hidden class="mt-4 text-center text-sm font-semibold text-navy">
				Abrimos WhatsApp con su consulta lista para enviar. Si no se abrió, use el botón de
				abajo.
			</p>
			<a
				href="<?php echo esc_url( $iff_contact['whatsapp'] ); ?>"
				target="_blank"
				rel="noreferrer"
				class="mt-3 flex w-full items-center justify-center gap-2 rounded-full border border-border px-6 py-4 text-sm font-bold text-foreground hover:bg-secondary"
			>
				<?php iff_icon( 'message-square', 'h-4 w-4' ); ?> Escribir por WhatsApp
			</a>
		</form>
	</div>
</section>
