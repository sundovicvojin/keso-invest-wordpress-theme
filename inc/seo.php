<?php
/**
 * Osnovni SEO: naslovi, meta opis, Open Graph, JSON-LD. Povlači se ako je aktivan SEO plugin.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function keso_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

function keso_title_parts( $parts ) {
	if ( is_front_page() ) {
		$parts['title']   = 'Keso Invest';
		$parts['tagline'] = __( 'Stambeni projekti u Novom Sadu', 'keso-invest' );
		unset( $parts['site'] );
	} else {
		$parts['site'] = 'Keso Invest';
	}
	return $parts;
}
add_filter( 'document_title_parts', 'keso_title_parts' );

function keso_meta_description() {
	if ( is_singular() ) {
		$p    = get_post();
		$desc = get_post_meta( $p->ID, 'meta_opis', true );
		if ( ! $desc && has_excerpt( $p ) ) {
			$desc = get_the_excerpt( $p );
		}
	} elseif ( is_front_page() ) {
		$desc = '';
	} elseif ( is_home() || is_archive() ) {
		$desc = __( 'Vesti i obaveštenja kompanije Keso Invest iz Novog Sada.', 'keso-invest' );
	} else {
		$desc = '';
	}
	if ( ! $desc && is_front_page() ) {
		$desc = __( 'Keso Invest doo iz Novog Sada gradi stambene projekte visoke vrednosti. Aktuelni projekat: Luma Residence.', 'keso-invest' );
	}
	return wp_strip_all_tags( (string) $desc );
}

function keso_head_meta() {
	if ( keso_seo_plugin_active() ) {
		return;
	}
	$desc  = keso_meta_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : ( is_front_page() ? home_url( '/' ) : '' );
	$img   = (int) keso_opt( 'img_og' );
	if ( is_singular() && has_post_thumbnail() ) {
		$img = (int) get_post_thumbnail_id();
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	}
	echo '<meta property="og:site_name" content="Keso Invest">' . "\n";
	echo '<meta property="og:locale" content="sr_RS">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	$src = $img ? wp_get_attachment_image_src( $img, 'full' ) : false;
	$og  = $src ? $src[0] : get_template_directory_uri() . '/assets/img/luma/og.jpg';
	echo '<meta property="og:image" content="' . esc_url( $og ) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="theme-color" content="#0e1424">' . "\n";
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() . '/assets/img/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
		echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() . '/assets/img/mark-64.png' ) . '" type="image/png" sizes="64x64">' . "\n";
	}
}
add_action( 'wp_head', 'keso_head_meta', 3 );

/** Trasa stranice (za BreadcrumbList). */
function keso_breadcrumb_items() {
	$items = array( array( 'name' => __( 'Početna', 'keso-invest' ), 'url' => home_url( '/' ) ) );
	if ( is_page() ) {
		$p = get_post();
		if ( in_array( $p->post_name, array_keys( keso_floor_map() ), true ) ) {
			$luma = get_page_by_path( 'luma-residence' );
			if ( $luma ) {
				$items[] = array( 'name' => get_the_title( $luma ), 'url' => get_permalink( $luma ) );
			}
		}
		if ( ! is_front_page() ) {
			$items[] = array( 'name' => get_the_title( $p ), 'url' => get_permalink( $p ) );
		}
	} elseif ( is_singular( 'post' ) ) {
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$items[] = array( 'name' => get_the_title( $blog ), 'url' => get_permalink( $blog ) );
		}
		$items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	}
	return $items;
}

function keso_json_ld() {
	if ( keso_seo_plugin_active() ) {
		return;
	}
	$graph = array();
	$logo  = has_custom_logo() ? wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' ) : '';
	$same  = array_values( keso_social_links() );

	$org = array(
		'@type'     => array( 'Organization', 'GeneralContractor' ),
		'@id'       => home_url( '/#organization' ),
		'name'      => 'KESO INVEST doo',
		'url'       => home_url( '/' ),
		'telephone' => keso_opt( 'phone_1' ),
		'email'     => keso_opt( 'email' ),
		'address'   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Danila Kiša 14a',
			'addressLocality' => 'Novi Sad',
			'addressCountry'  => 'RS',
		),
	);
	if ( $logo ) {
		$org['logo'] = $logo;
	}
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	$graph[] = $org;
	$graph[] = array(
		'@type'      => 'WebSite',
		'@id'        => home_url( '/#website' ),
		'url'        => home_url( '/' ),
		'name'       => 'Keso Invest',
		'inLanguage' => 'sr-Latn',
		'publisher'  => array( '@id' => home_url( '/#organization' ) ),
	);

	$crumbs = keso_breadcrumb_items();
	if ( count( $crumbs ) > 1 ) {
		$list = array();
		foreach ( $crumbs as $i => $c ) {
			$list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name'], 'item' => $c['url'] );
		}
		$graph[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $list );
	}

	// Stanovi na stranicama spratova: samo stvarni podaci, bez cena.
	if ( is_page() ) {
		$slug = get_post()->post_name;
		$map  = keso_floor_map();
		if ( isset( $map[ $slug ] ) ) {
			foreach ( array_slice( keso_get_apartments( $map[ $slug ] ), 0, 60 ) as $apt ) {
				$area = get_post_meta( $apt->ID, 'povrsina', true );
				if ( '' === $area || 'lokal' === get_post_meta( $apt->ID, 'tip', true ) ) {
					continue;
				}
				$a = array(
					'@type'     => 'Apartment',
					'name'      => $apt->post_title,
					'floorSize' => array( '@type' => 'QuantitativeValue', 'value' => (float) $area, 'unitCode' => 'MTK' ),
				);
				$rooms = get_post_meta( $apt->ID, 'sobe', true );
				if ( is_numeric( $rooms ) ) {
					$a['numberOfRooms'] = (float) $rooms;
				}
				$graph[] = $a;
			}
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'keso_json_ld', 20 );
