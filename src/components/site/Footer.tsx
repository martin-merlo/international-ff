import { Facebook, Instagram, Linkedin, Mail, MapPin, Phone } from "lucide-react";
import { CONTACT } from "./data";
import logo from "@/assets/logo-iff.png.asset.json";
import opItl from "@/assets/op-itl.png.asset.json";
import opCcni from "@/assets/op-ccni.png.asset.json";
import opDhl from "@/assets/op-dhl.png.asset.json";
import opSomarco from "@/assets/op-somarco.png.asset.json";
import opUps from "@/assets/op-ups.png.asset.json";
import opMsc from "@/assets/op-msc.png.asset.json";

const OPERATORS = [
  { src: opItl.url, name: "International Trade Logistics" },
  { src: opCcni.url, name: "CCNI" },
  { src: opDhl.url, name: "DHL" },
  { src: opSomarco.url, name: "Somarco" },
  { src: opUps.url, name: "UPS" },
  { src: opMsc.url, name: "MSC" },
];

export function Footer() {
  return (
    <footer className="bg-navy-deep pb-10 pt-16">
      <div className="container-x grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr]">
        <div>
          <div className="flex items-center gap-3">
            <img
              src={logo.url}
              alt="International Freight Forwarder"
              width={56}
              height={56}
              loading="lazy"
              className="h-14 w-14 shrink-0 rounded-full object-contain"
            />
            <span className="font-display text-lg font-extrabold text-primary-foreground">
              International Freight Forwarder
            </span>
          </div>
          <p className="mt-5 max-w-md text-sm leading-relaxed text-primary-foreground/75">
            Mudanzas internacionales, comercio exterior y agente de cargas. Oficina central en
            Mendoza, Argentina, con oficinas asociadas en el resto del mundo.
          </p>
          <div className="mt-6 flex gap-3">
            {[Facebook, Instagram, Linkedin].map((Icon, i) => (
              <a
                key={i}
                href="#inicio"
                aria-label="Red social"
                className="grid h-10 w-10 place-items-center rounded-full border border-primary-foreground/25 text-primary-foreground transition-colors hover:border-gold hover:text-gold"
              >
                <Icon className="h-4 w-4" />
              </a>
            ))}
          </div>
        </div>

        <div>
          <h3 className="text-sm font-bold uppercase tracking-widest text-gold">
            Red de operadores
          </h3>
          <ul className="mt-5 grid grid-cols-3 gap-2">
            {OPERATORS.map((op) => (
              <li
                key={op.name}
                className="flex h-16 items-center justify-center overflow-hidden rounded-lg bg-primary-foreground p-2"
              >
                <img
                  src={op.src}
                  alt={`Logo ${op.name}`}
                  width={96}
                  height={96}
                  loading="lazy"
                  className="max-h-full max-w-full object-contain"
                />
              </li>
            ))}
          </ul>
        </div>

        <div>
          <h3 className="text-sm font-bold uppercase tracking-widest text-gold">Contacto</h3>
          <ul className="mt-5 space-y-3 text-sm text-primary-foreground/80">
            <li className="flex gap-3">
              <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-gold" />
              {CONTACT.address}
            </li>
            <li className="flex gap-3">
              <Phone className="h-4 w-4 shrink-0 text-gold" />
              <a href={`tel:${CONTACT.phoneHref}`} className="hover:text-gold">
                {CONTACT.phone}
              </a>
            </li>
            <li className="flex gap-3">
              <Mail className="mt-0.5 h-4 w-4 shrink-0 text-gold" />
              <a href={`mailto:${CONTACT.email}`} className="break-all hover:text-gold">
                {CONTACT.email}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div className="container-x mt-12 border-t border-primary-foreground/15 pt-6">
        <p className="text-xs text-primary-foreground/60">
          © {new Date().getFullYear()} International Freight Forwarder. Todos los derechos
          reservados.
        </p>
      </div>
    </footer>
  );
}