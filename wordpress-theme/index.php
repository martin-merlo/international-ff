<?php
/**
 * Fallback obligatorio de WordPress.
 *
 * El sitio es una landing de una sola página: cualquier request que no sea la
 * home cae acá y se le muestra la landing, que es el comportamiento que tenía la
 * SPA con su fallback a index.html.
 *
 * @package International_FF
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main>
	<?php
	$iff_sections = array(
		'hero',
		'stats',
		'services',
		'about',
		'process',
		'why-us',
		'testimonials',
		'cta-band',
		'contact',
	);

	foreach ( $iff_sections as $iff_section ) {
		get_template_part( 'template-parts/sections/' . $iff_section );
	}
	?>
</main>
<?php
get_footer();
