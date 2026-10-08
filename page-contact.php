<?php
/**
 * Kontakt.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

keso_page_hero( get_the_title(), __( 'Pozovite nas, pišite nam ili posetite kancelariju u Novom Sadu.', 'keso-invest' ), array( array( get_the_title(), '' ) ) );
?>

<section class="section section--tight contact">
	<div class="wrap contact__grid">
		<div class="contact__info">
			<?php get_template_part( 'template-parts/contact-info' ); ?>
		</div>
		<div class="contact__form">
			<?php get_template_part( 'template-parts/contact-form', null, array( 'tag' => 'h2' ) ); ?>
		</div>
	</div>
	<div class="wrap"><?php get_template_part( 'template-parts/map' ); ?></div>
</section>

<?php
get_footer();
