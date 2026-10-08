<?php
/**
 * Interaktivna mapa (Leaflet + MapTiler, OSM rezerva bez ključa). Inicijalizuje je assets/js/map.js kad sekcija uđe u viewport.
 * Bez JS-a ostaju placeholder sa mrežom i link "Otvori u Google mapama".
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Podaci mape na jednom mestu. Adresa i naslov dolaze iz Customizera (keso_opt('address')).
 * TODO: koordinate su privremeno okvirne za Novi Sad; vlasnik ih ručno proverava (desni klik na pin u Google mapama → lat, lng).
 */
$keso_map = array(
	'lat'  => 45.2580,
	'lng'  => 19.8400,
	'zoom' => 16,
);
$keso_addr = (string) keso_opt( 'address' );
$keso_gurl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $keso_addr );
?>
<div class="map" data-map role="region" aria-label="<?php echo esc_attr( $keso_addr ); ?>"
	data-lat="<?php echo esc_attr( $keso_map['lat'] ); ?>"
	data-lng="<?php echo esc_attr( $keso_map['lng'] ); ?>"
	data-zoom="<?php echo esc_attr( $keso_map['zoom'] ); ?>"
	data-maptiler-key="<?php echo esc_attr( defined( 'KESO_MAPTILER_KEY' ) ? KESO_MAPTILER_KEY : '' ); ?>"
	data-title="<?php echo esc_attr( $keso_addr ); ?>"
	data-hint="<?php echo esc_attr__( 'Koristi dva prsta za pomeranje mape', 'keso-invest' ); ?>">
	<div class="map__canvas" data-map-canvas></div>
	<div class="map__facade">
		<p class="map__addr"><?php echo esc_html( $keso_addr ); ?></p>
		<a class="link-arrow" href="<?php echo esc_url( $keso_gurl ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Otvori u Google mapama', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></a>
	</div>
</div>
