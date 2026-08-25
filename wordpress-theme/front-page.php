<?php
/**
 * Home: la landing completa.
 *
 * Se arma con las nueve secciones en el mismo orden que el <App /> original.
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
