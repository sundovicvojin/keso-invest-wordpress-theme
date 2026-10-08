<?php
/**
 * Tamna traka sa telefonom i emailom.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="cta band band--dark" aria-labelledby="cta-title">
	<div class="wrap cta__inner">
		<div>
			<h2 id="cta-title" class="cta__title" data-reveal><?php esc_html_e( 'Zainteresovani ste za stan u Luma Residence?', 'keso-invest' ); ?></h2>
			<p class="cta__sub" data-reveal><?php esc_html_e( 'Pozovite nas ili pošaljite upit na email.', 'keso-invest' ); ?></p>
		</div>
		<div class="cta__links" data-reveal>
			<a class="cta__link" href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_1' ) ) ); ?>"><?php echo esc_html( keso_opt( 'phone_1' ) ); ?></a>
			<a class="cta__link cta__link--mail" href="mailto:<?php echo esc_attr( keso_opt( 'email' ) ); ?>"><?php echo esc_html( keso_opt( 'email' ) ); ?></a>
		</div>
	</div>
</section>
