<?php
/**
 * Contenido del sitio.
 *
 * Portado 1:1 desde src/components/site/data.ts del proyecto original. El
 * contenido está hardcodeado a propósito (decisión de proyecto): el sitio es una
 * landing de una sola página que cambia poco.
 *
 * Está centralizado acá, y no desperdigado por los templates, justamente para que
 * el día que haga falta hacerlo editable alcance con reemplazar el cuerpo de cada
 * función por una llamada a ACF, al Customizer o a un CPT, sin tocar el marcado.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Datos de contacto de la empresa.
 *
 * @return array<string,string>
 */
function iff_contact() {
	return array(
		'phone'           => '+54 9 261 506-3034',
		'phone_href'      => '+5492615063034',
		'phone2'          => '+54 9 261 419-5373',
		'phone2_href'     => '+5492614195373',
		'whatsapp_number' => '5492615063034',
		'whatsapp'        => 'https://wa.me/5492615063034?text=Hola%2C%20quisiera%20solicitar%20un%20presupuesto',
		'email'           => 'jmotta@internationalff.com',
		'email2'          => 'martinruggeri@internationalff.com',
		'address'         => 'Moreno 3350 - 4ta. Oeste - Mendoza - CPA M5500 EGN - Rep. Argentina',
		'map_embed'       => 'https://www.google.com/maps?q=Moreno%203350%20Mendoza%20Argentina&output=embed',
	);
}

/**
 * Links de navegación (anchors de la misma página).
 *
 * @return array<int,array<string,string>>
 */
function iff_nav_links() {
	return array(
		array(
			'href'  => '#inicio',
			'label' => 'Inicio',
		),
		array(
			'href'  => '#servicios',
			'label' => 'Servicios',
		),
		array(
			'href'  => '#nosotros',
			'label' => 'Nosotros',
		),
		array(
			'href'  => '#testimonios',
			'label' => 'Testimonios',
		),
		array(
			'href'  => '#contacto',
			'label' => 'Contacto',
		),
	);
}

/**
 * Servicios que ofrece la empresa.
 *
 * @return array<int,array<string,mixed>>
 */
function iff_services() {
	return array(
		array(
			'title'  => 'Mudanzas Internacionales',
			'image'  => 'service-moving.jpg',
			'text'   => 'Mudanzas puerta a puerta desde y hacia cualquier lugar del mundo, a través de nuestros representantes internacionales. Nos encargamos de todos los detalles, incluidas las mudanzas corporativas.',
			'points' => array(
				'Servicio puerta a puerta',
				'Embalaje y manejo de efectos personales',
				'Mudanzas corporativas',
			),
		),
		array(
			'title'  => 'Comercio Exterior',
			'image'  => 'service-customs.jpg',
			'text'   => 'Amplia variedad de productos e instrumentos financieros y de logística en comercio internacional para atender las necesidades locales y globales de nuestros clientes.',
			'points' => array(
				'Importación y exportación de mercaderías',
				'Inscriptos en Dirección Nacional de Aduanas',
				'Gestiones dentro y fuera del país',
			),
		),
		array(
			'title'  => 'Agente de Cargas',
			'image'  => 'service-freight.jpg',
			'text'   => 'Somos despachantes de aduana en Mendoza, Argentina. Lo asesoramos en todos los aspectos técnicos, operativos y jurídicos que conforman el universo normativo aduanero.',
			'points' => array(
				'Cargas full y parciales LCL',
				'Servicio multimodal aire, tierra y mar',
				'Envío de muestras courier a todo el mundo',
			),
		),
	);
}

/**
 * Números de la banda de estadísticas (contadores animados).
 *
 * @return array<int,array<string,mixed>>
 */
function iff_stats() {
	return array(
		array(
			'value'  => 30,
			'suffix' => '+',
			'label'  => 'Años de experiencia',
		),
		array(
			'value'  => 90,
			'suffix' => '+',
			'label'  => 'Países con cobertura',
		),
		array(
			'value'  => 5000,
			'suffix' => '+',
			'label'  => 'Envíos realizados',
		),
		array(
			'value'  => 98,
			'suffix' => '%',
			'label'  => 'Clientes satisfechos',
		),
	);
}

/**
 * Pasos del proceso de trabajo.
 *
 * @return array<int,array<string,string>>
 */
function iff_steps() {
	return array(
		array(
			'title' => 'Contacto',
			'text'  => 'Nos cuenta qué necesita enviar o mudar. Escuchamos su caso y definimos el alcance del servicio.',
		),
		array(
			'title' => 'Planificación',
			'text'  => 'Elaboramos el presupuesto y el plan logístico: modalidad aérea, marítima o terrestre, plazos y costos.',
		),
		array(
			'title' => 'Documentación',
			'text'  => 'Gestionamos permisos, despachos de aduana y toda la documentación en origen y destino.',
		),
		array(
			'title' => 'Entrega',
			'text'  => 'Seguimiento permanente hasta que su carga llega a destino en tiempo y forma.',
		),
	);
}

/**
 * Motivos para elegir a la empresa.
 *
 * @return array<int,array<string,string>>
 */
function iff_reasons() {
	return array(
		array(
			'icon'  => 'award',
			'title' => '30 años de trayectoria',
			'text'  => 'Miles de mudanzas internacionales realizadas.',
		),
		array(
			'icon'  => 'globe-2',
			'title' => 'Red internacional',
			'text'  => 'Oficina central en Mendoza y agentes asociados en el resto del mundo.',
		),
		array(
			'icon'  => 'shield-check',
			'title' => 'Marco aduanero',
			'text'  => 'Asesoramiento técnico, operativo y jurídico en todo el proceso.',
		),
		array(
			'icon'  => 'users',
			'title' => 'Trato personal',
			'text'  => 'Empresa familiar con dedicación especial en cada carga.',
		),
		array(
			'icon'  => 'file-text',
			'title' => 'Sin cargos imprevistos',
			'text'  => 'Presupuestos claros y planificación anticipada.',
		),
		array(
			'icon'  => 'package',
			'title' => 'Multimodal',
			'text'  => 'Aire, tierra y mar, cargas full y parciales LCL.',
		),
	);
}

/**
 * Tarjetas de la sección "Quiénes somos".
 *
 * @return array<int,array<string,string>>
 */
function iff_about_cards() {
	return array(
		array(
			'icon'  => 'clock',
			'title' => 'Nuestra historia',
			'text'  => 'Mas de 30 años de trayectoria.',
		),
		array(
			'icon'  => 'handshake',
			'title' => 'Nuestra filosofía',
			'text'  => 'Ganarnos su confianza cada día.',
		),
		array(
			'icon'  => 'package',
			'title' => 'Nuestro compromiso',
			'text'  => 'Su carga, en tiempo y forma.',
		),
	);
}

/**
 * Testimonios de clientes.
 *
 * @return array<int,array<string,string>>
 */
function iff_testimonials() {
	return array(
		array(
			'name'  => 'Alejandro E.',
			'role'  => 'Barcelona, España',
			'quote' => 'Tuvimos que mudarnos con muy poco tiempo de preparación y elegimos IFF por recomendación de un amigo. Fue una decisión acertada.',
		),
		array(
			'name'  => 'Tomás M.',
			'role'  => 'Dallas, TX',
			'quote' => 'Muy buena comunicación y experiencia en general. El container llegó más tarde de lo previsto pero por cuestiones climáticas.',
		),
		array(
			'name'  => 'Sofía G.',
			'role'  => 'Miami, Florida',
			'quote' => 'Mi negocio ha prosperado gracias a que puedo comercializar mis productos afuera del país. La decisión de contratar los servicios de IFF fue correcta.',
		),
		array(
			'name'  => 'Carla V.',
			'role'  => 'New York, USA',
			'quote' => 'Me recomendaron esta compañía y debo decir que estoy muy satisfecha con los servicios prestados, cumplieron en todo lo prometido.',
		),
		array(
			'name'  => 'Javier',
			'role'  => 'DFW Logistics',
			'quote' => 'Hemos podido establecer una relación a largo plazo con IFF y sus servicios son cruciales para nuestra compañía.',
		),
	);
}

/**
 * Red de operadores del footer.
 *
 * @return array<int,array<string,string>>
 */
function iff_operators() {
	return array(
		array(
			'image' => 'logo-itl.png',
			'name'  => 'International Trade Logistics',
		),
		array(
			'image' => 'logo-ccni.png',
			'name'  => 'CCNI',
		),
		array(
			'image' => 'logo-dhl.png',
			'name'  => 'DHL',
		),
		array(
			'image' => 'logo-somarco.png',
			'name'  => 'Somarco',
		),
		array(
			'image' => 'logo-ups.png',
			'name'  => 'UPS',
		),
		array(
			'image' => 'logo-msc.png',
			'name'  => 'MSC',
		),
	);
}

/**
 * Redes sociales del footer.
 *
 * OJO: las URLs son placeholders (#inicio), tal cual estaban en el proyecto
 * original. Reemplazar por los perfiles reales cuando estén disponibles.
 *
 * @return array<int,array<string,string>>
 */
function iff_social_links() {
	return array(
		array(
			'icon' => 'facebook',
			'url'  => '#inicio',
		),
		array(
			'icon' => 'instagram',
			'url'  => '#inicio',
		),
		array(
			'icon' => 'linkedin',
			'url'  => '#inicio',
		),
	);
}
