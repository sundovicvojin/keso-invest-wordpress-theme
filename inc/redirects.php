<?php
/**
 * Usmeravanje šablona i 301 preusmeravanja sa starih adresa.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Stare šablonske stranice => novo odredište. Menja se filterom `keso_redirects`. */
function keso_redirect_map() {
	return apply_filters(
		'keso_redirects',
		array(
			'about-us'     => '/o-investitoru/',
			'amenities'    => '/luma-residence/',
			'apartment'    => '/luma-residence/',
			'availability' => '/luma-residence/',
			'building'     => '/luma-residence/',
			'gallery'      => '/luma-residence/',
			'neighborhood' => '/luma-residence/',
			'residences'   => '/luma-residence/',
			'blog'         => '/vesti/',
			'faqs'         => '/contact/',
			'icons'        => '/',
			'sample-page'  => '/',
		)
	);
}

/** 301 samo kada stranica ne postoji (404), da ne pregazi ono što vlasnik napravi. */
function keso_old_url_redirects() {
	if ( ! is_404() ) {
		return;
	}
	$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	if ( $home && 0 === strpos( $path, $home ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}
	$map = keso_redirect_map();
	if ( isset( $map[ $path ] ) ) {
		wp_safe_redirect( home_url( $map[ $path ] ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'keso_old_url_redirects', 1 );

/**
 * Stranice sa poznatim slugovima uvek koriste šablone teme, čak i ako je stara tema
 * dodelila svoj šablon (npr. Elementor ili Pavo canvas).
 */
function keso_template_include( $template ) {
	if ( ! is_page() ) {
		return $template;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	if ( isset( keso_floor_map()[ $slug ] ) ) {
		$t = locate_template( 'page-sprat.php' );
		return $t ?: $template;
	}
	foreach ( array( 'luma-residence', 'reference', 'o-investitoru', 'contact' ) as $s ) {
		if ( $s === $slug ) {
			$t = locate_template( 'page-' . $s . '.php' );
			return $t ?: $template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'keso_template_include', 20 );
