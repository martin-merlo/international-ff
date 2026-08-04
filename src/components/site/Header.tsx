import { useEffect, useState } from "react";
import { Menu, X, Ship } from "lucide-react";
import { CONTACT } from "./data";

const LINKS = [
  { href: "#inicio", label: "Inicio" },
  { href: "#servicios", label: "Servicios" },
  { href: "#nosotros", label: "Nosotros" },
  { href: "#testimonios", label: "Testimonios" },
  { href: "#contacto", label: "Contacto" },
];

export function Header() {
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <header
      className={`fixed inset-x-0 top-0 z-50 transition-all duration-300 ${
        scrolled
          ? "bg-background/95 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.5)] backdrop-blur"
          : "bg-navy-deep/70 backdrop-blur-sm"
      }`}
    >
      <div className="container-x grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-4">
        <a href="#inicio" className="flex min-w-0 items-center gap-3">
          <span className="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gold text-navy-deep">
            <Ship className="h-6 w-6" strokeWidth={2.2} />
          </span>
          <span className="min-w-0">
            <span
              className={`block truncate font-display text-base font-extrabold leading-tight sm:text-lg ${
                scrolled ? "text-foreground" : "text-primary-foreground"
              }`}
            >
              International Freight Forwarder
            </span>
            <span
              className={`block truncate text-xs ${
                scrolled ? "text-muted-foreground" : "text-primary-foreground/75"
              }`}
            >
              Mendoza, Argentina
            </span>
          </span>
        </a>

        <nav className="hidden items-center gap-8 lg:flex">
          {LINKS.map((l) => (
            <a
              key={l.href}
              href={l.href}
              className={`text-sm font-semibold transition-colors hover:text-gold ${
                scrolled ? "text-foreground" : "text-primary-foreground"
              }`}
            >
              {l.label}
            </a>
          ))}
          <a
            href="#contacto"
            className="rounded-full bg-gold px-5 py-2.5 text-sm font-bold text-navy-deep shadow-[0_8px_24px_-10px_rgba(180,140,40,0.9)] transition-transform hover:-translate-y-0.5"
          >
            Solicitar presupuesto
          </a>
        </nav>

        <button
          type="button"
          aria-label="Abrir menú"
          onClick={() => setOpen((v) => !v)}
          className={`grid h-11 w-11 place-items-center rounded-xl border lg:hidden ${
            scrolled
              ? "border-border text-foreground"
              : "border-primary-foreground/30 text-primary-foreground"
          }`}
        >
          {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
        </button>
      </div>

      {open && (
        <div className="border-t border-border bg-background lg:hidden">
          <div className="container-x flex flex-col gap-1 py-4">
            {LINKS.map((l) => (
              <a
                key={l.href}
                href={l.href}
                onClick={() => setOpen(false)}
                className="rounded-lg px-2 py-3 text-sm font-semibold text-foreground hover:bg-secondary"
              >
                {l.label}
              </a>
            ))}
            <a
              href={CONTACT.whatsapp}
              target="_blank"
              rel="noreferrer"
              className="mt-2 rounded-full bg-gold px-5 py-3 text-center text-sm font-bold text-navy-deep"
            >
              Solicitar presupuesto
            </a>
          </div>
        </div>
      )}
    </header>
  );
}