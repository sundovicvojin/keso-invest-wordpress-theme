<?php
/**
 * Obrazac za pretragu.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$keso_uid = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $keso_uid ); ?>"><?php esc_html_e( 'Pretraga', 'keso-invest' ); ?></label>
	<div class="search-form__row">
		<input id="<?php echo esc_attr( $keso_uid ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
		<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Traži', 'keso-invest' ); ?></button>
	</div>
</form>
