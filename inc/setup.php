<?php
/**
 * Osnovna podešavanja teme.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function keso_setup() {
	load_theme_textdomain( 'keso-invest', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Glavni meni', 'keso-invest' ),
			'footer'  => __( 'Meni u podnožju', 'keso-invest' ),
		)
	);

	add_image_size( 'keso-hero', 1920, 1080, true );
	add_image_size( 'keso-wide', 1440, 810, true );
	add_image_size( 'keso-card', 800, 600, true );
}
add_action( 'after_setup_theme', 'keso_setup' );

/** Jezik dokumenta: srpski, latinica. */
function keso_language_attributes( $output ) {
	return preg_replace( '/lang="[^"]*"/', 'lang="sr-Latn"', $output );
}
add_filter( 'language_attributes', 'keso_language_attributes' );

/** Čist wp_head / wp_footer, bez kvarenja funkcionalnosti. */
function keso_clean_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'wp_resource_hints', 2 );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'keso_clean_head' );

/** Skip-link i telo dokumenta. */
function keso_body_classes( $classes ) {
	$classes[] = 'no-js-body';
	return $classes;
}
add_filter( 'body_class', 'keso_body_classes' );

/** Excerpt: kratko i bez [...]. */
add_filter(
	'excerpt_more',
	static function () {
		return '…';
	}
);
add_filter(
	'excerpt_length',
	static function () {
		return 28;
	}
);

/** Zaštita: gutenberg stranice ne smeju ostati bez razumnog "wide" ponašanja. */
add_filter( 'use_block_editor_for_post_type', static function ( $use, $type ) {
	return in_array( $type, array( 'stan', 'referenca' ), true ) ? false : $use;
}, 10, 2 );

/** Dugme za otvaranje podmenija (tastatura i dodir) uz stavke koje imaju podmeni. */
function keso_submenu_toggle( $item_output, $item, $depth, $args ) {
	if ( 'primary' === ( $args->theme_location ?? '' ) && 0 === $depth && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		$item_output .= '<button type="button" class="sub-toggle" aria-expanded="false"><span class="sr-only">' . esc_html( sprintf( /* translators: %s: naziv stavke menija */ __( 'Prikaži podmeni: %s', 'keso-invest' ), $item->title ) ) . '</span>' . '<svg class="i" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.5"/></svg></button>';
	}
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'keso_submenu_toggle', 10, 4 );

/**
 * Luma Residence u glavnom meniju je jedna stavka (bez podmenija spratova);
 * ako je unet sajt projekta, stavka vodi na njega.
 */
function keso_luma_menu_items( $items, $args ) {
	if ( 'primary' !== ( $args->theme_location ?? '' ) ) {
		return $items;
	}
	$luma_page = get_page_by_path( 'luma-residence' );
	if ( ! $luma_page ) {
		return $items;
	}
	$luma_ids = array();
	foreach ( $items as $item ) {
		if ( 'post_type' === $item->type && (int) $item->object_id === (int) $luma_page->ID ) {
			$luma_ids[] = (int) $item->ID;
		}
	}
	if ( ! $luma_ids ) {
		return $items;
	}
	foreach ( $items as $k => $item ) {
		if ( in_array( (int) $item->menu_item_parent, $luma_ids, true ) ) {
			unset( $items[ $k ] );
		}
	}
	foreach ( $items as $item ) {
		if ( in_array( (int) $item->ID, $luma_ids, true ) ) {
			$item->classes = array_diff( (array) $item->classes, array( 'menu-item-has-children' ) );
			if ( keso_luma_is_external() ) {
				$item->url    = keso_luma_url();
				$item->target = '_blank';
				$item->xfn    = 'noopener';
			}
		}
	}
	return array_values( $items );
}
add_filter( 'wp_nav_menu_objects', 'keso_luma_menu_items', 10, 2 );
