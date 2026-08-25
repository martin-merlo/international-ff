<?php
/**
 * Cabecera del sitio: nav fija con estado de scroll y menú mobile.
 *
 * El original de React alternaba clases con un useState (`scrolled`). Acá el JS
 * solo agrega/saca la clase `is-scrolled` en el <header> y las variantes
 * `[&.is-scrolled]:` / `[.is-scrolled_&]:` de Tailwind hacen el resto en CSS,
 * con la misma transición de 300ms.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$iff_contact   = iff_contact();
$iff_nav_links = iff_nav_links();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="min-h-screen bg-background">
	<header
		id="site-header"
		class="fixed inset-x-0 top-0 z-50 transition-all duration-300 bg-navy-deep/70 backdrop-blur-sm [&.is-scrolled]:bg-background/95 [&.is-scrolled]:shadow-[0_10px_30px_-18px_rgba(15,23,42,0.5)] [&.is-scrolled]:backdrop-blur"
	>
		<div class="container-x grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-4">
			<a href="#inicio" class="flex min-w-0 items-center gap-3">
				<img
					src="<?php echo esc_url( iff_img( 'logo-iff.png' ) ); ?>"
					alt="<?php echo esc_attr( iff_content( 'company_name' ) ); ?>"
					width="56"
					height="56"
					class="h-12 w-12 shrink-0 rounded-full object-contain sm:h-14 sm:w-14"
				/>
				<span class="min-w-0">
					<span class="block truncate font-display text-base font-extrabold leading-tight text-primary-foreground sm:text-lg [.is-scrolled_&]:text-foreground">
						<?php echo esc_html( iff_content( 'company_name' ) ); ?>
					</span>
					<span class="block truncate text-xs text-primary-foreground/75 [.is-scrolled_&]:text-muted-foreground">
						<?php echo esc_html( iff_content( 'company_tagline' ) ); ?>
					</span>
				</span>
			</a>

			<nav class="hidden items-center gap-8 lg:flex">
				<?php foreach ( $iff_nav_links as $iff_link ) : ?>
					<a
						href="<?php echo esc_attr( $iff_link['href'] ); ?>"
						class="text-sm font-semibold text-primary-foreground transition-colors hover:text-gold [.is-scrolled_&]:text-foreground"
					>
						<?php echo esc_html( $iff_link['label'] ); ?>
					</a>
				<?php endforeach; ?>
				<a
					href="#contacto"
					class="rounded-full bg-gold px-5 py-2.5 text-sm font-bold text-navy-deep shadow-[0_8px_24px_-10px_rgba(180,140,40,0.9)] transition-transform hover:-translate-y-0.5"
				>
					Solicitar presupuesto
				</a>
			</nav>

			<button
				type="button"
				id="iff-menu-toggle"
				aria-label="Abrir menú"
				aria-expanded="false"
				aria-controls="iff-mobile-menu"
				class="grid h-11 w-11 place-items-center rounded-xl border border-primary-foreground/30 text-primary-foreground lg:hidden [.is-scrolled_&]:border-border [.is-scrolled_&]:text-foreground"
			>
				<span data-menu-icon="open"><?php iff_icon( 'menu', 'h-5 w-5' ); ?></span>
				<span data-menu-icon="close" hidden><?php iff_icon( 'x', 'h-5 w-5' ); ?></span>
			</button>
		</div>

		<div id="iff-mobile-menu" class="hidden border-t border-border bg-background lg:hidden">
			<div class="container-x flex flex-col gap-1 py-4">
				<?php foreach ( $iff_nav_links as $iff_link ) : ?>
					<a
						href="<?php echo esc_attr( $iff_link['href'] ); ?>"
						data-mobile-link
						class="rounded-lg px-2 py-3 text-sm font-semibold text-foreground hover:bg-secondary"
					>
						<?php echo esc_html( $iff_link['label'] ); ?>
					</a>
				<?php endforeach; ?>
				<a
					href="<?php echo esc_url( $iff_contact['whatsapp'] ); ?>"
					target="_blank"
					rel="noreferrer"
					class="mt-2 rounded-full bg-gold px-5 py-3 text-center text-sm font-bold text-navy-deep"
				>
					Solicitar presupuesto
				</a>
			</div>
		</div>
	</header>
