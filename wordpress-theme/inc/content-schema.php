<?php
/**
 * Esquema de contenido: única fuente de verdad.
 *
 * De este archivo salen tres cosas, para que no puedan desincronizarse:
 *
 *   1. Los campos de ACF que ve el editor  (inc/acf-fields.php)
 *   2. Los valores por defecto              (inc/content.php, cuando ACF no responde)
 *   3. La carga inicial en la base          (inc/acf-setup.php, al activar el tema)
 *
 * Los defaults son EXACTAMENTE el contenido que tenía el sitio hardcodeado, así
 * que recién activado el tema se ve idéntico, incluso antes de tocar nada en el
 * panel.
 *
 * IMPORTANTE: acá va solo CONTENIDO (lo que el sitio dice). El diseño —clases,
 * espaciados, tipografías, animaciones— se queda en el tema y no se toca desde
 * el panel.
 *
 * Tipos soportados: text, textarea, number, url, email, image, select, lines
 * (textarea que se lee como lista, una por línea) y repeat (lista de filas).
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Iconos disponibles para los campos de tipo select.
 *
 * Son los que están portados en inc/icons.php. Se limita a esta lista para que
 * el editor no pueda elegir un icono que no existe.
 *
 * @return array<string,string>
 */
function iff_icon_choices() {
	return array(
		'award'          => 'Premio',
		'clock'          => 'Reloj',
		'file-text'      => 'Documento',
		'globe-2'        => 'Globo terráqueo',
		'handshake'      => 'Apretón de manos',
		'package'        => 'Paquete',
		'shield-check'   => 'Escudo',
		'users'          => 'Personas',
		'check'          => 'Tilde',
		'check-circle-2' => 'Tilde en círculo',
		'map-pin'        => 'Ubicación',
		'phone'          => 'Teléfono',
		'mail'           => 'Sobre',
		'message-square' => 'Mensaje',
		'quote'          => 'Comillas',
		'star'           => 'Estrella',
		'arrow-right'    => 'Flecha',
	);
}

/**
 * Esquema completo del contenido editable.
 *
 * @return array<string,array<string,mixed>>
 */
function iff_content_schema() {
	static $schema = null;

	if ( null !== $schema ) {
		return $schema;
	}

	$schema = array(

		/* ---------------------------------------------------------------
		 * General
		 * ------------------------------------------------------------ */
		'general' => array(
			'label'  => 'General',
			'fields' => array(
				'company_name'     => array(
					'label'        => 'Nombre de la empresa',
					'type'         => 'text',
					'instructions' => 'Se usa en la cabecera, el pie y el aviso de copyright.',
					'default'      => 'International Freight Forwarder',
				),
				'company_tagline'  => array(
					'label'   => 'Bajada de la cabecera',
					'type'    => 'text',
					'default' => 'Mendoza, Argentina',
				),
				'phone'            => array(
					'label'        => 'Teléfono principal',
					'type'         => 'text',
					'instructions' => 'Como se muestra en pantalla. El enlace para llamar se arma solo.',
					'default'      => '+54 9 261 506-3034',
				),
				'phone2'           => array(
					'label'   => 'Teléfono secundario',
					'type'    => 'text',
					'default' => '+54 9 261 419-5373',
				),
				'email'            => array(
					'label'   => 'Email principal',
					'type'    => 'email',
					'default' => 'jmotta@internationalff.com',
				),
				'email2'           => array(
					'label'   => 'Email secundario',
					'type'    => 'email',
					'default' => 'martinruggeri@internationalff.com',
				),
				'contact_person'   => array(
					'label'   => 'Nombre del contacto principal',
					'type'    => 'text',
					'default' => 'Juan Motta',
				),
				'contact_person2'  => array(
					'label'   => 'Nombre del contacto secundario',
					'type'    => 'text',
					'default' => 'Martin Ruggeri',
				),
				'whatsapp_number'  => array(
					'label'        => 'Número de WhatsApp',
					'type'         => 'text',
					'instructions' => 'Solo dígitos, con código de país y sin el signo +. Ejemplo: 5492615063034',
					'default'      => '5492615063034',
				),
				'whatsapp_message' => array(
					'label'        => 'Mensaje inicial de WhatsApp',
					'type'         => 'text',
					'instructions' => 'Texto con el que se abre la conversación desde los botones de WhatsApp.',
					'default'      => 'Hola, quisiera solicitar un presupuesto',
				),
				'address'          => array(
					'label'   => 'Dirección postal',
					'type'    => 'textarea',
					'default' => 'Moreno 3350 - 4ta. Oeste - Mendoza - CPA M5500 EGN - Rep. Argentina',
				),
				'map_embed'        => array(
					'label'        => 'Mapa de Google (URL para insertar)',
					'type'         => 'url',
					'instructions' => 'En Google Maps: Compartir → Insertar un mapa → copiar la URL que está dentro de src="...".',
					'default'      => 'https://www.google.com/maps?q=Moreno%203350%20Mendoza%20Argentina&output=embed',
				),
				'social_facebook'  => array(
					'label'        => 'Facebook',
					'type'         => 'text',
					'instructions' => 'Pegar la URL del perfil. Mientras esté en #inicio el ícono no lleva a ningún lado.',
					'default'      => '#inicio',
				),
				'social_instagram' => array(
					'label'   => 'Instagram',
					'type'    => 'text',
					'default' => '#inicio',
				),
				'social_linkedin'  => array(
					'label'   => 'LinkedIn',
					'type'    => 'text',
					'default' => '#inicio',
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Hero
		 * ------------------------------------------------------------ */
		'hero' => array(
			'label'  => 'Portada',
			'fields' => array(
				'hero_eyebrow'       => array(
					'label'        => 'Antetítulo',
					'type'         => 'text',
					'instructions' => 'La línea chiquita en mayúsculas arriba del título.',
					'default'      => 'Mudanzas internacionales · Comercio exterior',
				),
				'hero_title'         => array(
					'label'   => 'Título principal',
					'type'    => 'textarea',
					'rows'    => 2,
					'default' => 'Servicio puerta a puerta a cualquier lugar del mundo',
				),
				'hero_description'   => array(
					'label'   => 'Descripción',
					'type'    => 'textarea',
					'default' => '30 años de servicio avalan nuestra capacidad y honestidad para que su mudanza internacional sea sin sorpresas ni sobresaltos. Logística de cargas nacional e internacional, importación y exportación de mercaderías.',
				),
				'hero_cta_text'      => array(
					'label'   => 'Botón principal: texto',
					'type'    => 'text',
					'default' => 'Solicitar presupuesto',
				),
				'hero_cta_url'       => array(
					'label'        => 'Botón principal: destino',
					'type'         => 'text',
					'instructions' => '#contacto lleva al formulario de esta misma página.',
					'default'      => '#contacto',
				),
				'hero_cta2_text'     => array(
					'label'   => 'Botón secundario: texto',
					'type'    => 'text',
					'default' => 'WhatsApp',
				),
				'hero_cta2_url'      => array(
					'label'        => 'Botón secundario: destino',
					'type'         => 'text',
					'optional'     => true,
					'instructions' => 'Dejar vacío para que use el enlace de WhatsApp configurado en General.',
					'default'      => '',
				),
				'hero_image'         => array(
					'label'        => 'Imagen de fondo',
					'type'         => 'image',
					'instructions' => 'Apaisada y grande (1920×1080 o más). El texto va encima, así que conviene que no tenga zonas muy claras a la izquierda.',
					'default'      => 'hero-port.jpg',
					'default_alt'  => 'Buque portacontenedores en puerto internacional',
				),
				'hero_bullets'       => array(
					'label'        => 'Puntos destacados',
					'type'         => 'lines',
					'instructions' => 'Uno por línea. Aparecen abajo de los botones con un tilde dorado.',
					'default'      => array(
						'Aéreo, marítimo y terrestre',
						'Despachantes de aduana',
						'Red global de agentes',
					),
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Estadísticas
		 * ------------------------------------------------------------ */
		'stats' => array(
			'label'  => 'Estadísticas',
			'fields' => array(
				'stats' => array(
					'label'        => 'Números',
					'type'         => 'repeat',
					'instructions' => 'La banda oscura con los números que suben solos. El conteo animado se mantiene automáticamente.',
					'min'          => 1,
					'max'          => 8,
					'button_label' => 'Agregar número',
					'sub_fields'   => array(
						'value'  => array(
							'label'        => 'Número',
							'type'         => 'number',
							'instructions' => 'Solo el número, sin puntos ni símbolos. El separador de miles se agrega solo.',
						),
						'suffix' => array(
							'label'        => 'Símbolo',
							'type'         => 'text',
							'instructions' => 'Lo que va pegado al número. Por ejemplo + o %.',
						),
						'label'  => array(
							'label' => 'Texto debajo',
							'type'  => 'text',
						),
					),
					'default'      => array(
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
					),
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Servicios
		 * ------------------------------------------------------------ */
		'services' => array(
			'label'  => 'Servicios',
			'fields' => array(
				'services_eyebrow' => array(
					'label'   => 'Antetítulo',
					'type'    => 'text',
					'default' => 'Nuestros servicios',
				),
				'services_title'   => array(
					'label'   => 'Título',
					'type'    => 'textarea',
					'rows'    => 2,
					'default' => 'Soluciones completas de logística internacional',
				),
				'services_intro'   => array(
					'label'   => 'Texto introductorio',
					'type'    => 'textarea',
					'default' => 'Confíe sus bienes en nosotros por cualquier medio: aéreo, marítimo o terrestre. Mas de 30 años de experiencia avalan nuestra trayectoria.',
				),
				'services'         => array(
					'label'        => 'Servicios',
					'type'         => 'repeat',
					'instructions' => 'Las tarjetas con foto. Con tres queda una fila prolija en pantallas grandes.',
					'min'          => 1,
					'button_label' => 'Agregar servicio',
					'sub_fields'   => array(
						'title'  => array(
							'label' => 'Título',
							'type'  => 'text',
						),
						'text'   => array(
							'label' => 'Descripción',
							'type'  => 'textarea',
						),
						'image'  => array(
							'label'        => 'Imagen',
							'type'         => 'image',
							'instructions' => 'Apaisada, idealmente 1024×768.',
						),
						'points' => array(
							'label'        => 'Puntos',
							'type'         => 'lines',
							'instructions' => 'Uno por línea. Se muestran como lista con tildes.',
						),
					),
					'default'      => array(
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
					),
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Nosotros
		 * ------------------------------------------------------------ */
		'about' => array(
			'label'  => 'Nosotros',
			'fields' => array(
				'about_eyebrow' => array(
					'label'   => 'Antetítulo',
					'type'    => 'text',
					'default' => 'Quiénes somos',
				),
				'about_title'   => array(
					'label'   => 'Título',
					'type'    => 'textarea',
					'rows'    => 2,
					'default' => 'Una empresa familiar a su servicio',
				),
				'about_body_1'  => array(
					'label'   => 'Primer párrafo',
					'type'    => 'textarea',
					'default' => 'Somos una empresa con vasta experiencia en todo tipo de cargas y con interés total por la satisfacción de nuestros clientes. Nuestra trayectoria se remonta a la primera generación de la familia: Don Juan Alfredo Motta, junto a la Compañía Sudamericana de Vapores, fue pionero en la salida de cargas vía Océano Pacifico.',
				),
				'about_body_2'  => array(
					'label'        => 'Segundo párrafo',
					'type'         => 'textarea',
					'optional'     => true,
					'instructions' => 'Se puede dejar vacío si alcanza con un solo párrafo.',
					'default'      => 'Entendemos que cada carga implica la posibilidad de nuevos negocios y la realización de anhelos y sueños. Por eso ofrecemos un servicio cómodo, seguro, eficaz y ágil, con trato personal en cada tema concerniente a su carga.',
				),
				'about_image'   => array(
					'label'       => 'Imagen',
					'type'        => 'image',
					'default'     => 'about-warehouse.jpg',
					'default_alt' => 'Operación logística en depósito',
				),
				'about_cards'   => array(
					'label'        => 'Tarjetas',
					'type'         => 'repeat',
					'instructions' => 'Las tres cajitas con ícono debajo del texto.',
					'button_label' => 'Agregar tarjeta',
					'sub_fields'   => array(
						'icon'  => array(
							'label'   => 'Ícono',
							'type'    => 'select',
							'choices' => 'icons',
						),
						'title' => array(
							'label' => 'Título',
							'type'  => 'text',
						),
						'text'  => array(
							'label' => 'Texto',
							'type'  => 'text',
						),
					),
					'default'      => array(
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
					),
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Proceso
		 * ------------------------------------------------------------ */
		'process' => array(
			'label'  => 'Cómo trabajamos',
			'fields' => array(
				'process_eyebrow' => array(
					'label'   => 'Antetítulo',
					'type'    => 'text',
					'default' => 'Cómo trabajamos',
				),
				'process_title'   => array(
					'label'   => 'Título',
					'type'    => 'textarea',
					'rows'    => 2,
					'default' => 'Cuatro pasos, cero sorpresas',
				),
				'steps'           => array(
					'label'        => 'Pasos',
					'type'         => 'repeat',
					'instructions' => 'La numeración (1, 2, 3…) se genera sola según el orden: no hace falta escribirla.',
					'min'          => 1,
					'button_label' => 'Agregar paso',
					'sub_fields'   => array(
						'title' => array(
							'label' => 'Título',
							'type'  => 'text',
						),
						'text'  => array(
							'label' => 'Descripción',
							'type'  => 'textarea',
						),
					),
					'default'      => array(
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
					),
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Por qué elegirnos
		 * ------------------------------------------------------------ */
		'why' => array(
			'label'  => 'Por qué elegirnos',
			'fields' => array(
				'why_eyebrow' => array(
					'label'   => 'Antetítulo',
					'type'    => 'text',
					'default' => 'Por qué elegirnos',
				),
				'why_title'   => array(
					'label'   => 'Título',
					'type'    => 'textarea',
					'rows'    => 2,
					'default' => 'Experiencia comprobable en cada envío',
				),
				'why_items'   => array(
					'label'        => 'Motivos',
					'type'         => 'repeat',
					'instructions' => 'Van de a tres por fila en pantallas grandes: con múltiplos de 3 queda parejo.',
					'min'          => 1,
					'button_label' => 'Agregar motivo',
					'sub_fields'   => array(
						'icon'  => array(
							'label'   => 'Ícono',
							'type'    => 'select',
							'choices' => 'icons',
						),
						'title' => array(
							'label' => 'Título',
							'type'  => 'text',
						),
						'text'  => array(
							'label' => 'Descripción',
							'type'  => 'textarea',
						),
					),
					'default'      => array(
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
					),
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Testimonios
		 * ------------------------------------------------------------ */
		'testimonials' => array(
			'label'  => 'Testimonios',
			'fields' => array(
				'testimonials_eyebrow' => array(
					'label'   => 'Antetítulo',
					'type'    => 'text',
					'default' => 'Testimonios',
				),
				'testimonials_title'   => array(
					'label'   => 'Título',
					'type'    => 'textarea',
					'rows'    => 2,
					'default' => 'Lo que dicen nuestros clientes',
				),
				'testimonials'         => array(
					'label'        => 'Testimonios',
					'type'         => 'repeat',
					'instructions' => 'Van de a tres por fila en pantallas grandes.',
					'min'          => 1,
					'button_label' => 'Agregar testimonio',
					'sub_fields'   => array(
						'quote'  => array(
							'label' => 'Testimonio',
							'type'  => 'textarea',
						),
						'name'   => array(
							'label' => 'Nombre',
							'type'  => 'text',
						),
						'role'   => array(
							'label'        => 'Ciudad o empresa',
							'type'         => 'text',
							'instructions' => 'Se muestra en gris debajo del nombre.',
						),
						'rating' => array(
							'label'        => 'Estrellas',
							'type'         => 'number',
							'instructions' => 'De 0 a 5.',
							'min'          => 0,
							'max'          => 5,
						),
					),
					'default'      => array(
						array(
							'name'   => 'Alejandro E.',
							'role'   => 'Barcelona, España',
							'quote'  => 'Tuvimos que mudarnos con muy poco tiempo de preparación y elegimos IFF por recomendación de un amigo. Fue una decisión acertada.',
							'rating' => 5,
						),
						array(
							'name'   => 'Tomás M.',
							'role'   => 'Dallas, TX',
							'quote'  => 'Muy buena comunicación y experiencia en general. El container llegó más tarde de lo previsto pero por cuestiones climáticas.',
							'rating' => 5,
						),
						array(
							'name'   => 'Sofía G.',
							'role'   => 'Miami, Florida',
							'quote'  => 'Mi negocio ha prosperado gracias a que puedo comercializar mis productos afuera del país. La decisión de contratar los servicios de IFF fue correcta.',
							'rating' => 5,
						),
						array(
							'name'   => 'Carla V.',
							'role'   => 'New York, USA',
							'quote'  => 'Me recomendaron esta compañía y debo decir que estoy muy satisfecha con los servicios prestados, cumplieron en todo lo prometido.',
							'rating' => 5,
						),
						array(
							'name'   => 'Javier',
							'role'   => 'DFW Logistics',
							'quote'  => 'Hemos podido establecer una relación a largo plazo con IFF y sus servicios son cruciales para nuestra compañía.',
							'rating' => 5,
						),
					),
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Banda de llamado a la acción
		 * ------------------------------------------------------------ */
		'cta' => array(
			'label'  => 'Franja de contacto',
			'fields' => array(
				'cta_title'       => array(
					'label'        => 'Título',
					'type'         => 'textarea',
					'rows'         => 2,
					'instructions' => 'La franja con foto de fondo que va antes del formulario.',
					'default'      => 'Contáctenos y le responderemos a la brevedad posible',
				),
				'cta_description' => array(
					'label'   => 'Descripción',
					'type'    => 'textarea',
					'default' => 'Pida un presupuesto para su mudanza internacional u operación de comercio exterior y lo asesoraremos con gusto.',
				),
				'cta_button_text' => array(
					'label'   => 'Botón: texto',
					'type'    => 'text',
					'default' => 'Solicitar presupuesto',
				),
				'cta_button_url'  => array(
					'label'   => 'Botón: destino',
					'type'    => 'text',
					'default' => '#contacto',
				),
				'cta_image'       => array(
					'label'       => 'Imagen de fondo',
					'type'        => 'image',
					'default'     => 'cta-terminal.jpg',
					'default_alt' => 'Terminal de contenedores de noche',
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Contacto
		 * ------------------------------------------------------------ */
		'contact' => array(
			'label'  => 'Contacto',
			'fields' => array(
				'contact_eyebrow'     => array(
					'label'   => 'Antetítulo',
					'type'    => 'text',
					'default' => 'Contacto',
				),
				'contact_title'       => array(
					'label'   => 'Título',
					'type'    => 'text',
					'default' => 'Contáctenos',
				),
				'contact_description' => array(
					'label'   => 'Descripción',
					'type'    => 'textarea',
					'default' => 'Escríbanos por cualquier necesidad de servicio de mudanzas o comercio exterior y responderemos a la brevedad posible.',
				),
				'form_title'          => array(
					'label'   => 'Formulario: título',
					'type'    => 'text',
					'default' => 'Solicitar presupuesto',
				),
				'form_label_nombre'   => array(
					'label'   => 'Formulario: etiqueta del nombre',
					'type'    => 'text',
					'default' => 'Nombre y apellido',
				),
				'form_label_email'    => array(
					'label'   => 'Formulario: etiqueta del email',
					'type'    => 'text',
					'default' => 'Email',
				),
				'form_label_telefono' => array(
					'label'   => 'Formulario: etiqueta del teléfono',
					'type'    => 'text',
					'default' => 'Teléfono',
				),
				'form_label_servicio' => array(
					'label'   => 'Formulario: etiqueta del servicio',
					'type'    => 'text',
					'default' => 'Servicio',
				),
				'form_label_mensaje'  => array(
					'label'   => 'Formulario: etiqueta del mensaje',
					'type'    => 'text',
					'default' => 'Mensaje',
				),
				'form_submit_text'    => array(
					'label'   => 'Formulario: texto del botón',
					'type'    => 'text',
					'default' => 'Enviar consulta',
				),
				'form_sent_text'      => array(
					'label'        => 'Formulario: mensaje de confirmación',
					'type'         => 'textarea',
					'instructions' => 'Aparece después de enviar. El formulario no manda mails: abre WhatsApp con la consulta lista.',
					'default'      => 'Abrimos WhatsApp con su consulta lista para enviar. Si no se abrió, use el botón de abajo.',
				),
				'form_whatsapp_text'  => array(
					'label'   => 'Formulario: texto del botón de WhatsApp',
					'type'    => 'text',
					'default' => 'Escribir por WhatsApp',
				),
				'form_open_whatsapp'  => array(
					'label'        => 'Al enviar, abrir WhatsApp',
					'type'         => 'toggle',
					'instructions' => 'Activado: al enviar se abre WhatsApp con la consulta escrita. Desactivalo si preferís recibir las consultas solo por email.',
					'default'      => 1,
				),
				'form_email_enabled'  => array(
					'label'        => 'Enviar las consultas por email',
					'type'         => 'toggle',
					'instructions' => 'Manda un correo con los datos del formulario a la casilla de abajo.',
					'default'      => 1,
				),
				'form_email_to'       => array(
					'label'        => 'Recibir las consultas en',
					'type'         => 'text',
					'instructions' => 'Casilla donde llegan las consultas. Se pueden poner varias separadas por coma. Vacío = el email del administrador del sitio.',
					'default'      => 'martinmerlo360@gmail.com',
					'optional'     => true,
				),
				'form_email_subject'  => array(
					'label'        => 'Asunto del email',
					'type'         => 'text',
					'instructions' => 'Se le agrega el nombre de quien consulta.',
					'default'      => 'Nueva consulta desde el sitio',
				),
				'form_email_from'     => array(
					'label'        => 'Dirección remitente',
					'type'         => 'text',
					'instructions' => 'IMPORTANTE: tiene que ser una casilla de tu propio dominio (por ejemplo no-reply@internationalff.com). Si acá ponés un Gmail o Hotmail, los correos van a caer en spam. Vacío = se arma solo con el dominio del sitio.',
					'default'      => '',
					'optional'     => true,
				),
				'form_error_text'     => array(
					'label'   => 'Formulario: mensaje si el envío falla',
					'type'    => 'textarea',
					'default' => 'No pudimos enviar la consulta por email. Escribinos por WhatsApp con el botón de abajo.',
				),
			),
		),

		/* ---------------------------------------------------------------
		 * Pie
		 * ------------------------------------------------------------ */
		'footer' => array(
			'label'  => 'Pie de página',
			'fields' => array(
				'footer_description'     => array(
					'label'   => 'Descripción',
					'type'    => 'textarea',
					'default' => 'Mudanzas internacionales, comercio exterior y agente de cargas. Oficina central en Mendoza, Argentina, con oficinas asociadas en el resto del mundo.',
				),
				'footer_operators_title' => array(
					'label'   => 'Título de la red de operadores',
					'type'    => 'text',
					'default' => 'Red de operadores',
				),
				'footer_contact_title'   => array(
					'label'   => 'Título de la columna de contacto',
					'type'    => 'text',
					'default' => 'Contacto',
				),
				'operators'              => array(
					'label'        => 'Red de operadores',
					'type'         => 'repeat',
					'instructions' => 'Logos cuadrados, idealmente de 66×66 píxeles. Van de a tres por fila.',
					'button_label' => 'Agregar operador',
					'sub_fields'   => array(
						'image' => array(
							'label' => 'Logo',
							'type'  => 'image',
						),
						'name'  => array(
							'label'        => 'Nombre',
							'type'         => 'text',
							'instructions' => 'No se muestra: se usa como texto alternativo de la imagen.',
						),
					),
					'default'      => array(
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
					),
				),
			),
		),
	);

	return $schema;
}

/**
 * Devuelve la definición de un campo del esquema.
 *
 * @param string $key Clave del campo.
 * @return array<string,mixed>|null
 */
function iff_schema_field( $key ) {
	foreach ( iff_content_schema() as $section ) {
		if ( isset( $section['fields'][ $key ] ) ) {
			return $section['fields'][ $key ];
		}
	}

	return null;
}

/**
 * Valor por defecto de un campo (el contenido original del sitio).
 *
 * @param string $key Clave del campo.
 * @return mixed
 */
function iff_default( $key ) {
	$field = iff_schema_field( $key );

	return isset( $field['default'] ) ? $field['default'] : '';
}
