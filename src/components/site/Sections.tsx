import { useEffect, useRef, useState } from "react";
import {
  ArrowRight,
  Award,
  Check,
  CheckCircle2,
  Clock,
  FileText,
  Globe2,
  Handshake,
  Mail,
  MapPin,
  MessageSquare,
  Package,
  Phone,
  Quote,
  ShieldCheck,
  Star,
  Users,
} from "lucide-react";
import hero from "@/assets/hero-port.jpg";
import warehouse from "@/assets/about-warehouse.jpg";
import terminal from "@/assets/cta-terminal.jpg";
import { CONTACT, SERVICES, STATS, STEPS, TESTIMONIALS } from "./data";

function useInView<T extends HTMLElement>() {
  const ref = useRef<T | null>(null);
  const [seen, setSeen] = useState(false);
  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const io = new IntersectionObserver(
      (entries) => {
        if (entries[0]?.isIntersecting) {
          setSeen(true);
          io.disconnect();
        }
      },
      { threshold: 0.3 },
    );
    io.observe(el);
    return () => io.disconnect();
  }, []);
  return { ref, seen };
}

function Counter({ value, suffix, run }: { value: number; suffix: string; run: boolean }) {
  const [n, setN] = useState(0);
  useEffect(() => {
    if (!run) return;
    const start = performance.now();
    const dur = 1600;
    let raf = 0;
    const tick = (t: number) => {
      const p = Math.min((t - start) / dur, 1);
      setN(Math.round(value * (1 - Math.pow(1 - p, 3))));
      if (p < 1) raf = requestAnimationFrame(tick);
    };
    raf = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(raf);
  }, [run, value]);
  return (
    <span>
      {n.toLocaleString("es-AR")}
      {suffix}
    </span>
  );
}

export function Hero() {
  return (
    <section id="inicio" className="relative isolate min-h-[92vh] overflow-hidden">
      <img
        src={hero}
        alt="Buque portacontenedores en puerto internacional"
        width={1920}
        height={1088}
        className="absolute inset-0 -z-20 h-full w-full object-cover"
      />
      <div className="absolute inset-0 -z-10 bg-[linear-gradient(100deg,oklch(0.21_0.062_258/0.94)_0%,oklch(0.21_0.062_258/0.82)_45%,oklch(0.21_0.062_258/0.55)_100%)]" />
      <div className="container-x flex min-h-[92vh] flex-col justify-center pb-24 pt-36">
        <div className="max-w-3xl animate-rise">
          <p className="eyebrow text-gold">Mudanzas internacionales · Comercio exterior</p>
          <h1 className="mt-5 text-4xl font-extrabold leading-[1.05] text-primary-foreground sm:text-6xl lg:text-7xl">
            Servicio puerta a puerta a cualquier lugar del mundo
          </h1>
          <p className="mt-6 max-w-2xl text-lg leading-relaxed text-primary-foreground/90">
            30 años de servicio avalan nuestra capacidad y honestidad para que su mudanza
            internacional sea sin sorpresas ni sobresaltos. Logística de cargas nacional e
            internacional, importación y exportación de mercaderías.
          </p>
          <div className="mt-9 flex flex-wrap gap-4">
            <a
              href="#contacto"
              className="inline-flex items-center gap-2 rounded-full bg-gold px-7 py-4 text-sm font-bold text-navy-deep shadow-[0_18px_40px_-18px_rgba(0,0,0,0.8)] transition-transform hover:-translate-y-0.5"
            >
              Solicitar presupuesto <ArrowRight className="h-4 w-4" />
            </a>
            <a
              href={CONTACT.whatsapp}
              target="_blank"
              rel="noreferrer"
              className="inline-flex items-center gap-2 rounded-full border border-primary-foreground/40 bg-primary-foreground/10 px-7 py-4 text-sm font-bold text-primary-foreground backdrop-blur transition-colors hover:bg-primary-foreground/20"
            >
              <MessageSquare className="h-4 w-4" /> WhatsApp
            </a>
          </div>
          <div className="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-primary-foreground/85">
            {["Aéreo, marítimo y terrestre", "Despachantes de aduana", "Red global de agentes"].map(
              (t) => (
                <span key={t} className="inline-flex items-center gap-2">
                  <CheckCircle2 className="h-4 w-4 text-gold" /> {t}
                </span>
              ),
            )}
          </div>
        </div>
      </div>
    </section>
  );
}

export function Stats() {
  const { ref, seen } = useInView<HTMLDivElement>();
  return (
    <section className="bg-navy-deep py-16">
      <div ref={ref} className="container-x grid grid-cols-2 gap-8 lg:grid-cols-4">
        {STATS.map((s) => (
          <div key={s.label} className="text-center">
            <p className="font-display text-4xl font-extrabold text-gold sm:text-5xl">
              <Counter value={s.value} suffix={s.suffix} run={seen} />
            </p>
            <p className="mt-2 text-sm font-semibold text-primary-foreground/85">{s.label}</p>
          </div>
        ))}
      </div>
    </section>
  );
}

export function Services() {
  return (
    <section id="servicios" className="bg-background py-24">
      <div className="container-x">
        <p className="eyebrow text-navy-soft">Nuestros servicios</p>
        <h2 className="mt-4 max-w-2xl text-3xl font-extrabold text-foreground sm:text-5xl">
          Soluciones completas de logística internacional
        </h2>
        <p className="mt-5 max-w-2xl text-lg text-muted-foreground">
          Confíe sus bienes en nosotros por cualquier medio: aéreo, marítimo o terrestre. Mas de
          30 años de experiencia avalan nuestra trayectoria.
        </p>

        <div className="mt-14 grid gap-8 lg:grid-cols-3">
          {SERVICES.map((s) => (
            <article
              key={s.title}
              className="group overflow-hidden rounded-3xl border border-border bg-card shadow-[0_24px_60px_-40px_rgba(15,23,42,0.6)] transition-transform duration-300 hover:-translate-y-1.5"
            >
              <div className="relative h-56 overflow-hidden">
                <img
                  src={s.image}
                  alt={s.title}
                  loading="lazy"
                  width={1024}
                  height={768}
                  className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
              </div>
              <div className="p-8">
                <h3 className="text-xl font-bold text-card-foreground">{s.title}</h3>
                <p className="mt-3 text-sm leading-relaxed text-muted-foreground">{s.text}</p>
                <ul className="mt-6 space-y-2.5">
                  {s.points.map((p) => (
                    <li key={p} className="flex items-start gap-2.5 text-sm text-foreground">
                      <Check className="mt-0.5 h-4 w-4 shrink-0 text-navy" />
                      {p}
                    </li>
                  ))}
                </ul>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}

export function About() {
  return (
    <section id="nosotros" className="bg-secondary py-24">
      <div className="container-x grid items-center gap-14 lg:grid-cols-2">
        <div className="overflow-hidden rounded-3xl shadow-[0_40px_80px_-50px_rgba(15,23,42,0.8)]">
          <img
            src={warehouse}
            alt="Operación logística en depósito"
            loading="lazy"
            width={1400}
            height={900}
            className="h-full w-full object-cover"
          />
        </div>
        <div>
          <p className="eyebrow text-navy-soft">Quiénes somos</p>
          <h2 className="mt-4 text-3xl font-extrabold text-foreground sm:text-4xl">
            Una empresa familiar a su servicio
          </h2>
          <p className="mt-5 text-lg leading-relaxed text-muted-foreground">
            Somos una empresa con vasta experiencia en todo tipo de cargas y con interés total por
            la satisfacción de nuestros clientes. Nuestra trayectoria se remonta a la primera
            generación de la familia: Don Juan Alfredo Motta, junto a la Compañía Sudamericana de
            Vapores, fue pionero en la salida de cargas vía Océano Pacifico.
          </p>
          <p className="mt-4 text-lg leading-relaxed text-muted-foreground">
            Entendemos que cada carga implica la posibilidad de nuevos negocios y la realización de
            anhelos y sueños. Por eso ofrecemos un servicio cómodo, seguro, eficaz y ágil, con
            trato personal en cada tema concerniente a su carga.
          </p>
          <div className="mt-8 grid gap-4 sm:grid-cols-3">
            {[
              { icon: Clock, t: "Nuestra historia", d: "Mas de 30 años de trayectoria." },
              { icon: Handshake, t: "Nuestra filosofía", d: "Ganarnos su confianza cada día." },
              { icon: Package, t: "Nuestro compromiso", d: "Su carga, en tiempo y forma." },
            ].map((c) => (
              <div key={c.t} className="rounded-2xl border border-border bg-card p-5">
                <c.icon className="h-6 w-6 text-navy" />
                <p className="mt-3 text-sm font-bold text-card-foreground">{c.t}</p>
                <p className="mt-1 text-sm text-muted-foreground">{c.d}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

export function Process() {
  return (
    <section className="bg-background py-24">
      <div className="container-x">
        <p className="eyebrow text-navy-soft">Cómo trabajamos</p>
        <h2 className="mt-4 text-3xl font-extrabold text-foreground sm:text-5xl">
          Cuatro pasos, cero sorpresas
        </h2>
        <div className="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
          {STEPS.map((s, i) => (
            <div key={s.title} className="relative rounded-3xl bg-secondary p-8">
              <span className="grid h-12 w-12 place-items-center rounded-2xl bg-navy font-display text-lg font-extrabold text-primary-foreground">
                {i + 1}
              </span>
              <h3 className="mt-5 text-lg font-bold text-foreground">{s.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-muted-foreground">{s.text}</p>
              {i < STEPS.length - 1 && (
                <span className="absolute right-6 top-12 hidden text-gold lg:block">
                  <ArrowRight className="h-5 w-5" />
                </span>
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

export function WhyUs() {
  const items = [
    { icon: Award, t: "30 años de trayectoria", d: "Miles de mudanzas internacionales realizadas." },
    { icon: Globe2, t: "Red internacional", d: "Oficina central en Mendoza y agentes asociados en el resto del mundo." },
    { icon: ShieldCheck, t: "Marco aduanero", d: "Asesoramiento técnico, operativo y jurídico en todo el proceso." },
    { icon: Users, t: "Trato personal", d: "Empresa familiar con dedicación especial en cada carga." },
    { icon: FileText, t: "Sin cargos imprevistos", d: "Presupuestos claros y planificación anticipada." },
    { icon: Package, t: "Multimodal", d: "Aire, tierra y mar, cargas full y parciales LCL." },
  ];
  return (
    <section className="bg-navy py-24">
      <div className="container-x">
        <p className="eyebrow text-gold">Por qué elegirnos</p>
        <h2 className="mt-4 max-w-2xl text-3xl font-extrabold text-primary-foreground sm:text-5xl">
          Experiencia comprobable en cada envío
        </h2>
        <div className="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {items.map((i) => (
            <div
              key={i.t}
              className="rounded-3xl border border-primary-foreground/15 bg-primary-foreground/5 p-8 backdrop-blur transition-colors hover:border-gold/50"
            >
              <i.icon className="h-7 w-7 text-gold" />
              <h3 className="mt-5 text-lg font-bold text-primary-foreground">{i.t}</h3>
              <p className="mt-2 text-sm leading-relaxed text-primary-foreground/80">{i.d}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

export function Testimonials() {
  return (
    <section id="testimonios" className="bg-background py-24">
      <div className="container-x">
        <p className="eyebrow text-navy-soft">Testimonios</p>
        <h2 className="mt-4 text-3xl font-extrabold text-foreground sm:text-5xl">
          Lo que dicen nuestros clientes
        </h2>
        <div className="mt-14 grid gap-8 lg:grid-cols-3">
          {TESTIMONIALS.map((t) => (
            <figure
              key={t.name}
              className="rounded-3xl border border-border bg-card p-8 shadow-[0_24px_60px_-45px_rgba(15,23,42,0.7)]"
            >
              <Quote className="h-8 w-8 text-gold" />
              <blockquote className="mt-5 text-base leading-relaxed text-card-foreground">
                {t.quote}
              </blockquote>
              <div className="mt-6 flex gap-1">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Star key={i} className="h-4 w-4 fill-gold text-gold" />
                ))}
              </div>
              <figcaption className="mt-4">
                <p className="font-bold text-foreground">{t.name}</p>
                <p className="text-sm text-muted-foreground">{t.role}</p>
              </figcaption>
            </figure>
          ))}
        </div>
      </div>
    </section>
  );
}

export function CtaBand() {
  return (
    <section className="relative isolate overflow-hidden">
      <img
        src={terminal}
        alt="Terminal de contenedores de noche"
        loading="lazy"
        width={1600}
        height={900}
        className="absolute inset-0 -z-20 h-full w-full object-cover"
      />
      <div className="absolute inset-0 -z-10 bg-[oklch(0.21_0.062_258/0.9)]" />
      <div className="container-x py-24 text-center">
        <h2 className="mx-auto max-w-3xl text-3xl font-extrabold text-primary-foreground sm:text-5xl">
          Contáctenos y le responderemos a la brevedad posible
        </h2>
        <p className="mx-auto mt-5 max-w-2xl text-lg text-primary-foreground/85">
          Pida un presupuesto para su mudanza internacional u operación de comercio exterior y lo
          asesoraremos con gusto.
        </p>
        <div className="mt-9 flex flex-wrap justify-center gap-4">
          <a
            href="#contacto"
            className="inline-flex items-center gap-2 rounded-full bg-gold px-7 py-4 text-sm font-bold text-navy-deep transition-transform hover:-translate-y-0.5"
          >
            Solicitar presupuesto <ArrowRight className="h-4 w-4" />
          </a>
          <a
            href={`tel:${CONTACT.phoneHref}`}
            className="inline-flex items-center gap-2 rounded-full border border-primary-foreground/40 px-7 py-4 text-sm font-bold text-primary-foreground hover:bg-primary-foreground/10"
          >
            <Phone className="h-4 w-4" /> {CONTACT.phone}
          </a>
        </div>
      </div>
    </section>
  );
}

export function Contact() {
  const [sent, setSent] = useState(false);
  return (
    <section id="contacto" className="bg-secondary py-24">
      <div className="container-x grid gap-12 lg:grid-cols-2">
        <div>
          <p className="eyebrow text-navy-soft">Contacto</p>
          <h2 className="mt-4 text-3xl font-extrabold text-foreground sm:text-4xl">Contáctenos</h2>
          <p className="mt-5 text-lg text-muted-foreground">
            Escríbanos por cualquier necesidad de servicio de mudanzas o comercio exterior y
            responderemos a la brevedad posible.
          </p>

          <div className="mt-8 space-y-4">
            <div className="flex items-start gap-4 rounded-2xl bg-card p-5">
              <MapPin className="mt-0.5 h-5 w-5 shrink-0 text-navy" />
              <p className="text-sm text-card-foreground">{CONTACT.address}</p>
            </div>
            <div className="grid gap-4 sm:grid-cols-2">
              {[
                { name: "Juan Motta", tel: CONTACT.phone, mail: CONTACT.email },
                { name: "Martin Ruggeri", tel: CONTACT.phone2, mail: CONTACT.email2 },
              ].map((p) => (
                <div key={p.name} className="rounded-2xl bg-card p-5">
                  <p className="font-bold text-card-foreground">{p.name}</p>
                  <a
                    href={`tel:${p.tel.replace(/[^+\d]/g, "")}`}
                    className="mt-3 flex items-center gap-2 text-sm text-muted-foreground hover:text-navy"
                  >
                    <Phone className="h-4 w-4" /> {p.tel}
                  </a>
                  <a
                    href={`mailto:${p.mail}`}
                    className="mt-2 flex items-center gap-2 break-all text-sm text-muted-foreground hover:text-navy"
                  >
                    <Mail className="h-4 w-4 shrink-0" /> {p.mail}
                  </a>
                </div>
              ))}
            </div>
          </div>

          <div className="mt-6 overflow-hidden rounded-2xl border border-border">
            <iframe
              title="Ubicación de la oficina en Mendoza"
              src="https://www.google.com/maps?q=Moreno%203350%20Mendoza%20Argentina&output=embed"
              className="h-72 w-full"
              loading="lazy"
            />
          </div>
        </div>

        <form
          onSubmit={(e) => {
            e.preventDefault();
            setSent(true);
          }}
          className="h-fit rounded-3xl bg-card p-8 shadow-[0_30px_70px_-50px_rgba(15,23,42,0.8)]"
        >
          <h3 className="text-xl font-bold text-card-foreground">Solicitar presupuesto</h3>
          <div className="mt-6 space-y-4">
            {[
              { id: "nombre", label: "Nombre y apellido", type: "text" },
              { id: "email", label: "Email", type: "email" },
              { id: "telefono", label: "Teléfono", type: "tel" },
            ].map((f) => (
              <div key={f.id}>
                <label htmlFor={f.id} className="text-sm font-semibold text-foreground">
                  {f.label}
                </label>
                <input
                  id={f.id}
                  type={f.type}
                  required={f.id !== "telefono"}
                  className="mt-2 w-full rounded-xl border border-input bg-background px-4 py-3 text-sm text-foreground outline-none focus:border-navy focus:ring-2 focus:ring-navy/20"
                />
              </div>
            ))}
            <div>
              <label htmlFor="servicio" className="text-sm font-semibold text-foreground">
                Servicio
              </label>
              <select
                id="servicio"
                className="mt-2 w-full rounded-xl border border-input bg-background px-4 py-3 text-sm text-foreground outline-none focus:border-navy focus:ring-2 focus:ring-navy/20"
              >
                {SERVICES.map((s) => (
                  <option key={s.title}>{s.title}</option>
                ))}
              </select>
            </div>
            <div>
              <label htmlFor="mensaje" className="text-sm font-semibold text-foreground">
                Mensaje
              </label>
              <textarea
                id="mensaje"
                rows={4}
                required
                className="mt-2 w-full rounded-xl border border-input bg-background px-4 py-3 text-sm text-foreground outline-none focus:border-navy focus:ring-2 focus:ring-navy/20"
              />
            </div>
          </div>
          <button
            type="submit"
            className="mt-6 w-full rounded-full bg-navy px-6 py-4 text-sm font-bold text-primary-foreground transition-colors hover:bg-navy-deep"
          >
            Enviar consulta
          </button>
          {sent && (
            <p className="mt-4 text-center text-sm font-semibold text-navy">
              Gracias por su consulta. Nos comunicaremos a la brevedad.
            </p>
          )}
          <a
            href={CONTACT.whatsapp}
            target="_blank"
            rel="noreferrer"
            className="mt-3 flex w-full items-center justify-center gap-2 rounded-full border border-border px-6 py-4 text-sm font-bold text-foreground hover:bg-secondary"
          >
            <MessageSquare className="h-4 w-4" /> Escribir por WhatsApp
          </a>
        </form>
      </div>
    </section>
  );
}