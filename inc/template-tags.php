<?php
/**
 * Pomoćne funkcije za šablone.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function keso_tel_href( $phone ) {
	return 'tel:' . preg_replace( '/[^\d+]/', '', (string) $phone );
}

function keso_status_label( $status ) {
	$all = keso_statuses();
	return $all[ $status ] ?? $all['slobodan'];
}

function keso_format_area( $v ) {
	$n = (float) $v;
	$s = rtrim( rtrim( number_format( $n, 2, ',', '.' ), '0' ), ',' );
	return $s . ' m²';
}

/** Wordmark kao inline SVG (nasleđuje boju teksta). */
function keso_wordmark( $class = '' ) {
	static $svg = null, $printed = false;
	if ( null === $svg ) {
		$path = get_template_directory() . '/assets/img/wordmark.svg';
		$svg  = file_exists( $path ) ? (string) file_get_contents( $path ) : '';
	}
	if ( $printed ) {
		// Ponovni prikaz (podnožje) koristi putanju iz zaglavlja: manje HTML-a.
		$vb = preg_match( '/viewBox="([^"]+)"/', $svg, $m ) ? $m[1] : '0 0 100 20';
		echo '<svg class="' . esc_attr( $class ) . '" viewBox="' . esc_attr( $vb ) . '" focusable="false" aria-hidden="true"><use href="#keso-wm"/></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	$printed = true;
	$svg     = str_replace( '<path ', '<path id="keso-wm" ', $svg );
	$svg_out = preg_replace( '/<svg /', '<svg class="' . esc_attr( $class ) . '" focusable="false" ', $svg, 1 );
	echo $svg_out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fajl iz teme.
}

/** Ikone: samo geometrijski primitivi (strelica, plus, zatvaranje). */
function keso_icon( $name ) {
	$icons = array(
		'arrow' => '<svg class="i" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M4 12h15M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>',
		'down'  => '<svg class="i" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M12 4v15M6 13l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>',
		'plus'  => '<svg class="i" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>',
		'phone' => '<svg class="i" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M6 3h4l1.5 5-2.5 1.5a11 11 0 0 0 5.5 5.5L16 12.5l5 1.5v4a2 2 0 0 1-2 2A16 16 0 0 1 4 5a2 2 0 0 1 2-2z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
	);
	echo $icons[ $name ] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- statički SVG.
}

/**
 * Slika iz biblioteke ili označeno mesto za fotografiju.
 *
 * @param int    $id      ID priloga (0 = placeholder).
 * @param string $size    Registrovana veličina.
 * @param string $label   Opis mesta za sliku (vidi se samo na placeholderu).
 * @param array  $args    ratio (npr. '16/9'), sizes, eager (bool), class, alt, parallax (bool).
 */
function keso_media( $id, $size, $label, $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'ratio'    => '4/3',
			'sizes'    => '100vw',
			'eager'    => false,
			'class'    => '',
			'alt'      => '',
			'parallax' => false,
			'default'  => '', // ključ priložene slike iz assets/img/luma/ (koristi se dok vlasnik ne izabere svoju).
		)
	);
	$attrs = ' class="media ' . esc_attr( $args['class'] ) . '" style="--ratio:' . esc_attr( $args['ratio'] ) . '"';
	$data  = $args['parallax'] ? ' data-parallax' : '';
	if ( $id && wp_attachment_is_image( $id ) ) {
		$img_attr = array(
			'sizes'    => $args['sizes'],
			'alt'      => $args['alt'],
			'decoding' => 'async',
			'class'    => 'media__img',
		);
		if ( $args['eager'] ) {
			$img_attr['loading']       = 'eager';
			$img_attr['fetchpriority'] = 'high';
		} else {
			$img_attr['loading'] = 'lazy';
		}
		echo '<div' . $attrs . $data . '>' . wp_get_attachment_image( $id, $size, false, $img_attr ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	if ( $args['default'] && keso_default_image( $args['default'] ) ) {
		$d    = keso_default_image( $args['default'] );
		$lazy = $args['eager'] ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"';
		echo '<div' . $attrs . $data . '><picture><source type="image/webp" srcset="' . esc_attr( $d['srcset'] ) . '" sizes="' . esc_attr( $args['sizes'] ) . '"><img class="media__img" src="' . esc_url( $d['jpg'] ) . '" width="1024" height="576" alt="' . esc_attr( $args['alt'] ) . '" decoding="async" ' . $lazy . '></picture></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	echo '<div' . $attrs . $data . ' role="img" aria-label="' . esc_attr( $args['alt'] ?: $label ) . '"><span class="media__ph"><span class="media__ph-tag">' . esc_html__( 'Fotografija', 'keso-invest' ) . '</span><span class="media__ph-text">' . esc_html( $label ) . '</span></span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/** Priložene slike (3D prikazi Luma Residence) kao podrazumevane; vlasnik ih menja u Customizer-u. */
function keso_default_image( $key ) {
	$base = get_template_directory() . '/assets/img/luma/';
	if ( ! preg_match( '/^[a-z0-9]+$/', $key ) || ! file_exists( $base . $key . '-1024.jpg' ) ) {
		return null;
	}
	$uri = get_template_directory_uri() . '/assets/img/luma/';
	return array(
		'jpg'    => $uri . $key . '-1024.jpg',
		'srcset' => $uri . $key . '-480.webp 480w, ' . $uri . $key . '-800.webp 800w, ' . $uri . $key . '-1280.webp 1280w, ' . $uri . $key . '-1600.webp 1600w',
	);
}

/** Slika iz podešavanja teme ili priložena podrazumevana; vraća argumente za keso_media(). */
function keso_img( $opt, $default ) {
	return array( (int) keso_opt( $opt ), $default );
}

/** Zbirovi iz referenci. */
function keso_reference_totals() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$q     = new WP_Query(
		array(
			'post_type'      => 'referenca',
			'posts_per_page' => 100,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'fields'         => 'ids',
		)
	);
	$cache = array( 'stanova' => 0, 'lokala' => 0, 'lokacija' => 0 );
	foreach ( $q->posts as $id ) {
		$cache['stanova']  += (int) get_post_meta( $id, 'stanova', true );
		$cache['lokala']   += (int) get_post_meta( $id, 'lokala', true );
		$cache['lokacija'] ++;
	}
	return $cache;
}

function keso_references() {
	return get_posts(
		array(
			'post_type'      => 'referenca',
			'posts_per_page' => 100,
			'post_status'    => 'publish',
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Brojke za sekciju sa brojačima.
 *
 * @return array[] Svaka stavka: value, suffix, label, todo (bool, vidljivo samo administratoru).
 */
function keso_stats() {
	$manual = (bool) keso_opt( 'stats_manual' );
	$tot    = keso_reference_totals();
	$have   = $tot['lokacija'] > 0;
	$units  = $manual || ! $have ? (int) keso_opt( 'stat_units' ) : $tot['stanova'];
	$shops  = $manual || ! $have ? (int) keso_opt( 'stat_shops' ) : $tot['lokala'];
	$locs   = $manual || ! $have ? (int) keso_opt( 'stat_locations' ) : $tot['lokacija'];
	$stats  = array(
		array( 'value' => $units, 'suffix' => '', 'label' => __( 'izgrađenih stanova', 'keso-invest' ), 'todo' => false ),
		array( 'value' => $shops, 'suffix' => '', 'label' => __( 'poslovnih prostora', 'keso-invest' ), 'todo' => false ),
		array( 'value' => $locs, 'suffix' => '', 'label' => __( 'lokacija u Novom Sadu', 'keso-invest' ), 'todo' => false ),
		array( 'value' => (int) keso_opt( 'stat_years' ), 'suffix' => '+', 'label' => __( 'godina iskustva', 'keso-invest' ), 'todo' => false ),
	);
	$ontime = keso_opt( 'stat_ontime' );
	if ( '' !== $ontime && null !== $ontime ) {
		$stats[] = array( 'value' => (int) $ontime, 'suffix' => '%', 'label' => __( 'projekata završeno u planiranim rokovima', 'keso-invest' ), 'todo' => false );
	} elseif ( current_user_can( 'edit_theme_options' ) ) {
		$stats[] = array( 'value' => 0, 'suffix' => '%', 'label' => __( 'TODO (vidi samo administrator): unesite % projekata završenih u rokovima u Customizer-u', 'keso-invest' ), 'todo' => true );
	}
	return array_values( array_filter( $stats, static fn( $s ) => $s['todo'] || $s['value'] > 0 ) );
}

function keso_render_stats( $class = '' ) {
	$stats = keso_stats();
	if ( ! $stats ) {
		return;
	}
	echo '<ul class="stats ' . esc_attr( $class ) . '" data-stagger>';
	foreach ( $stats as $s ) {
		echo '<li class="stats__item' . ( $s['todo'] ? ' is-todo' : '' ) . '">';
		echo '<span class="stats__num" data-count="' . esc_attr( $s['value'] ) . '" data-suffix="' . esc_attr( $s['suffix'] ) . '">' . esc_html( $s['value'] . $s['suffix'] ) . '</span>';
		echo '<span class="stats__label">' . esc_html( $s['label'] ) . '</span></li>';
	}
	echo '</ul>';
}

/** Stanovi za sprat (slug termina), sortirani po rednom broju pa prirodno po oznaci. */
function keso_get_apartments( $floor ) {
	$q     = new WP_Query(
		array(
			'post_type'      => 'stan',
			'posts_per_page' => 300,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'tax_query'      => array( array( 'taxonomy' => 'sprat', 'field' => 'slug', 'terms' => $floor ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		)
	);
	$posts = $q->posts;
	usort(
		$posts,
		static function ( $a, $b ) {
			$ra = (string) get_post_meta( $a->ID, 'redosled', true );
			$rb = (string) get_post_meta( $b->ID, 'redosled', true );
			$ra = '' === $ra ? PHP_INT_MAX : (int) $ra;
			$rb = '' === $rb ? PHP_INT_MAX : (int) $rb;
			return $ra === $rb ? strnatcasecmp( $a->post_title, $b->post_title ) : $ra <=> $rb;
		}
	);
	return $posts;
}

function keso_term_name( $post_id, $tax ) {
	$t = get_the_terms( $post_id, $tax );
	return ( $t && ! is_wp_error( $t ) ) ? $t[0]->name : '';
}

/** Naslov i uvod stranice koji se ponavljaju u unutrašnjim šablonima. */
function keso_page_hero( $title, $lead = '', $crumbs = array() ) {
	echo '<header class="page-hero"><div class="wrap">';
	if ( $crumbs ) {
		echo '<nav class="crumbs" aria-label="' . esc_attr__( 'Putanja', 'keso-invest' ) . '"><ol>';
		echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Početna', 'keso-invest' ) . '</a></li>';
		foreach ( $crumbs as $c ) {
			echo '<li>' . ( ! empty( $c[1] ) ? '<a href="' . esc_url( $c[1] ) . '">' . esc_html( $c[0] ) . '</a>' : '<span aria-current="page">' . esc_html( $c[0] ) . '</span>' ) . '</li>';
		}
		echo '</ol></nav>';
	}
	echo '<h1 class="page-hero__title" data-split>' . esc_html( $title ) . '</h1>';
	if ( $lead ) {
		echo '<p class="page-hero__lead">' . esc_html( $lead ) . '</p>';
	}
	echo '</div></header>';
}

function keso_social_links() {
	$out   = array();
	$items = array(
		'social_facebook'  => 'Facebook',
		'social_instagram' => 'Instagram',
		'social_linkedin'  => 'LinkedIn',
	);
	foreach ( $items as $k => $name ) {
		$url = keso_opt( $k );
		if ( $url ) {
			$out[ $name ] = $url;
		}
	}
	return $out;
}
