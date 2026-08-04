import { createFileRoute } from "@tanstack/react-router";
import { Header } from "@/components/site/Header";
import { Footer } from "@/components/site/Footer";
import { WhatsAppButton } from "@/components/site/WhatsAppButton";
import {
  About,
  Contact,
  CtaBand,
  Hero,
  Process,
  Services,
  Stats,
  Testimonials,
  WhyUs,
} from "@/components/site/Sections";

const TITLE = "Mudanzas Internacionales y Comercio Exterior | Mendoza";
const DESC =
  "Mudanzas internacionales puerta a puerta, logística de cargas, importación, exportación y despachos de aduana. Mas de 30 años de experiencia en Mendoza, Argentina.";

export const Route = createFileRoute("/")({
  head: () => ({
    meta: [
      { title: TITLE },
      { name: "description", content: DESC },
      { property: "og:title", content: TITLE },
      { property: "og:description", content: DESC },
    ],
  }),
  component: Index,
});

function Index() {
  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main>
        <Hero />
        <Stats />
        <Services />
        <About />
        <Process />
        <WhyUs />
        <Testimonials />
        <CtaBand />
        <Contact />
      </main>
      <Footer />
      <WhatsAppButton />
    </div>
  );
}
