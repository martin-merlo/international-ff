import moving from "@/assets/service-moving.jpg";
import freight from "@/assets/service-freight.jpg";
import customs from "@/assets/service-customs.jpg";

export const CONTACT = {
  phone: "+54 9 261 506-3034",
  phoneHref: "+5492615063034",
  whatsapp:
    "https://wa.me/5492615063034?text=Hola%2C%20quisiera%20solicitar%20un%20presupuesto",
  email: "jmotta@internationalff.com",
  email2: "martinruggeri@internationalff.com",
  phone2: "+54 9 261 419-5373",
  address:
    "Moreno 3350 - 4ta. Oeste - Mendoza - CPA M5500 EGN - Rep. Argentina",
};

export const SERVICES = [
  {
    title: "Mudanzas Internacionales",
    image: moving,
    text: "Mudanzas puerta a puerta desde y hacia cualquier lugar del mundo, a través de nuestros representantes internacionales. Nos encargamos de todos los detalles, incluidas las mudanzas corporativas.",
    points: [
      "Servicio puerta a puerta",
      "Embalaje y manejo de efectos personales",
      "Mudanzas corporativas",
    ],
  },
  {
    title: "Comercio Exterior",
    image: customs,
    text: "Amplia variedad de productos e instrumentos financieros y de logística en comercio internacional para atender las necesidades locales y globales de nuestros clientes.",
    points: [
      "Importación y exportación de mercaderías",
      "Inscriptos en Dirección Nacional de Aduanas",
      "Gestiones dentro y fuera del país",
    ],
  },
  {
    title: "Agente de Cargas",
    image: freight,
    text: "Somos despachantes de aduana en Mendoza, Argentina. Lo asesoramos en todos los aspectos técnicos, operativos y jurídicos que conforman el universo normativo aduanero.",
    points: [
      "Cargas full y parciales LCL",
      "Servicio multimodal aire, tierra y mar",
      "Envío de muestras courier a todo el mundo",
    ],
  },
];

export const STATS = [
  { value: 30, suffix: "+", label: "Años de experiencia" },
  { value: 90, suffix: "+", label: "Países con cobertura" },
  { value: 5000, suffix: "+", label: "Envíos realizados" },
  { value: 98, suffix: "%", label: "Clientes satisfechos" },
];

export const STEPS = [
  {
    title: "Contacto",
    text: "Nos cuenta qué necesita enviar o mudar. Escuchamos su caso y definimos el alcance del servicio.",
  },
  {
    title: "Planificación",
    text: "Elaboramos el presupuesto y el plan logístico: modalidad aérea, marítima o terrestre, plazos y costos.",
  },
  {
    title: "Documentación",
    text: "Gestionamos permisos, despachos de aduana y toda la documentación en origen y destino.",
  },
  {
    title: "Entrega",
    text: "Seguimiento permanente hasta que su carga llega a destino en tiempo y forma.",
  },
];

export const TESTIMONIALS = [
  {
    name: "Alejandro E.",
    role: "Barcelona, España",
    quote:
      "Tuvimos que mudarnos con muy poco tiempo de preparación y elegimos IFF por recomendación de un amigo. Fue una decisión acertada.",
  },
  {
    name: "Tomás M.",
    role: "Dallas, TX",
    quote:
      "Muy buena comunicación y experiencia en general. El container llegó más tarde de lo previsto pero por cuestiones climáticas.",
  },
  {
    name: "Sofía G.",
    role: "Miami, Florida",
    quote:
      "Mi negocio ha prosperado gracias a que puedo comercializar mis productos afuera del país. La decisión de contratar los servicios de IFF fue correcta.",
  },
  {
    name: "Carla V.",
    role: "New York, USA",
    quote:
      "Me recomendaron esta compañía y debo decir que estoy muy satisfecha con los servicios prestados, cumplieron en todo lo prometido.",
  },
  {
    name: "Javier",
    role: "DFW Logistics",
    quote:
      "Hemos podido establecer una relación a largo plazo con IFF y sus servicios son cruciales para nuestra compañía.",
  },
];