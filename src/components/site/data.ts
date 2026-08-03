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
    text: "Mudanzas puerta a puerta desde y hacia cualquier lugar del mundo, a traves de nuestros representantes internacionales. Nos encargamos de todos los detalles, incluidas las mudanzas corporativas.",
    points: [
      "Servicio puerta a puerta",
      "Embalaje y manejo de efectos personales",
      "Mudanzas corporativas",
    ],
  },
  {
    title: "Comercio Exterior",
    image: customs,
    text: "Amplia variedad de productos e instrumentos financieros y de logistica en comercio internacional para atender las necesidades locales y globales de nuestros clientes.",
    points: [
      "Importacion y exportacion de mercaderias",
      "Inscriptos en Direccion Nacional de Aduanas",
      "Gestiones dentro y fuera del pais",
    ],
  },
  {
    title: "Agente de Cargas",
    image: freight,
    text: "Somos despachantes de aduana en Mendoza, Argentina. Lo asesoramos en todos los aspectos tecnicos, operativos y juridicos que conforman el universo normativo aduanero.",
    points: [
      "Cargas full y parciales LCL",
      "Servicio multimodal aire, tierra y mar",
      "Envio de muestras courier a todo el mundo",
    ],
  },
];

export const STATS = [
  { value: 30, suffix: "+", label: "Anos de experiencia" },
  { value: 90, suffix: "+", label: "Paises con cobertura" },
  { value: 5000, suffix: "+", label: "Envios realizados" },
  { value: 98, suffix: "%", label: "Clientes satisfechos" },
];

export const STEPS = [
  {
    title: "Contacto",
    text: "Nos cuenta que necesita enviar o mudar. Escuchamos su caso y definimos el alcance del servicio.",
  },
  {
    title: "Planificacion",
    text: "Elaboramos el presupuesto y el plan logistico: modalidad aerea, maritima o terrestre, plazos y costos.",
  },
  {
    title: "Documentacion",
    text: "Gestionamos permisos, despachos de aduana y toda la documentacion en origen y destino.",
  },
  {
    title: "Entrega",
    text: "Seguimiento permanente hasta que su carga llega a destino en tiempo y forma.",
  },
];

export const TESTIMONIALS = [
  {
    name: "Laura Gimenez",
    role: "Mudanza Mendoza - Madrid",
    quote:
      "Nos mudamos con toda la casa a Espana y no tuvimos una sola sorpresa. Explicaron cada paso y todo llego en perfecto estado.",
  },
  {
    name: "Ricardo Peralta",
    role: "Bodega exportadora, Lujan de Cuyo",
    quote:
      "Exportamos de forma regular y su equipo resuelve la parte aduanera con una prolijidad que no habiamos encontrado antes.",
  },
  {
    name: "Sofia Andrade",
    role: "Importacion de maquinaria",
    quote:
      "Trato personal, respuestas rapidas y precios claros. Se nota la experiencia de una empresa familiar con decadas en el rubro.",
  },
];