<?php
/**
 * Kontakt podaci.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<dl class="info">
	<div><dt><?php esc_html_e( 'Adresa', 'keso-invest' ); ?></dt><dd>KESO INVEST doo<br><?php echo esc_html( keso_opt( 'address' ) ); ?></dd></div>
	<div><dt><?php esc_html_e( 'Telefon', 'keso-invest' ); ?></dt><dd>
		<a href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_1' ) ) ); ?>"><?php echo esc_html( keso_opt( 'phone_1' ) ); ?></a>
		<?php if ( keso_opt( 'phone_2' ) ) : ?><br><a href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_2' ) ) ); ?>"><?php echo esc_html( keso_opt( 'phone_2' ) ); ?></a><?php endif; ?>
	</dd></div>
	<div><dt><?php esc_html_e( 'Email', 'keso-invest' ); ?></dt><dd><a href="mailto:<?php echo esc_attr( keso_opt( 'email' ) ); ?>"><?php echo esc_html( keso_opt( 'email' ) ); ?></a></dd></div>
</dl>
