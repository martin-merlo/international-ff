// El sitio se sirve desde una subcarpeta (ver `base` en vite.config.ts), así que
// ningún archivo de public/ puede referenciarse con una ruta absoluta escrita a
// mano: /images/x.png apuntaría a la raíz del dominio, donde viven otros sitios.
//
// import.meta.env.BASE_URL trae el prefijo con barra final ya incluida
// ("/new/lovable/dist/" en build, "/" en dev), por eso el path se normaliza sin
// barra inicial antes de concatenar.
export function assetUrl(path: string): string {
  return `${import.meta.env.BASE_URL}${path.replace(/^\/+/, "")}`;
}
