<?php
/**
 * Tipovi sadržaja: stan (sa spratom i lamelom) i referenca.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Stranica => sprat (slug termina). Koristi se za šablon i upit. */
function keso_floor_map() {
	return array(
		'prvi-sprat'   => 'prvi',
		'drugi-sprat'  => 'drugi',
		'treci-sprat'  => 'treci',
		'cetvrti-sprat' => 'cetvrti',
	);
}

function keso_register_content_types() {
	register_post_type(
		'stan',
		array(
			'labels'              => array(
				'name'               => __( 'Stanovi', 'keso-invest' ),
				'singular_name'      => __( 'Stan', 'keso-invest' ),
				'add_new'            => __( 'Dodaj stan', 'keso-invest' ),
				'add_new_item'       => __( 'Dodaj novi stan ili lokal', 'keso-invest' ),
				'edit_item'          => __( 'Izmeni stan', 'keso-invest' ),
				'new_item'           => __( 'Novi stan', 'keso-invest' ),
				'view_item'          => __( 'Pogledaj stan', 'keso-invest' ),
				'search_items'       => __( 'Pretraži stanove', 'keso-invest' ),
				'not_found'          => __( 'Nema pronađenih stanova.', 'keso-invest' ),
				'not_found_in_trash' => __( 'Nema stanova u korpi.', 'keso-invest' ),
				'all_items'          => __( 'Svi stanovi', 'keso-invest' ),
				'menu_name'          => __( 'Stanovi', 'keso-invest' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-building',
			'supports'            => array( 'title' ),
		)
	);

	register_post_type(
		'referenca',
		array(
			'labels'              => array(
				'name'               => __( 'Reference', 'keso-invest' ),
				'singular_name'      => __( 'Referenca', 'keso-invest' ),
				'add_new'            => __( 'Dodaj referencu', 'keso-invest' ),
				'add_new_item'       => __( 'Dodaj novu referencu', 'keso-invest' ),
				'edit_item'          => __( 'Izmeni referencu', 'keso-invest' ),
				'new_item'           => __( 'Nova referenca', 'keso-invest' ),
				'view_item'          => __( 'Pogledaj referencu', 'keso-invest' ),
				'search_items'       => __( 'Pretraži reference', 'keso-invest' ),
				'not_found'          => __( 'Nema pronađenih referenci.', 'keso-invest' ),
				'not_found_in_trash' => __( 'Nema referenci u korpi.', 'keso-invest' ),
				'all_items'          => __( 'Sve reference', 'keso-invest' ),
				'menu_name'          => __( 'Reference', 'keso-invest' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'menu_position'       => 22,
			'menu_icon'           => 'dashicons-location-alt',
			'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
		)
	);

	register_taxonomy(
		'sprat',
		'stan',
		array(
			'labels'            => array(
				'name'          => __( 'Spratovi', 'keso-invest' ),
				'singular_name' => __( 'Sprat', 'keso-invest' ),
				'menu_name'     => __( 'Spratovi', 'keso-invest' ),
				'all_items'     => __( 'Svi spratovi', 'keso-invest' ),
				'edit_item'     => __( 'Izmeni sprat', 'keso-invest' ),
				'add_new_item'  => __( 'Dodaj sprat', 'keso-invest' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => false,
			'hierarchical'      => true,
			'rewrite'           => false,
		)
	);

	register_taxonomy(
		'lamela',
		'stan',
		array(
			'labels'            => array(
				'name'          => __( 'Lamele', 'keso-invest' ),
				'singular_name' => __( 'Lamela', 'keso-invest' ),
				'menu_name'     => __( 'Lamele', 'keso-invest' ),
				'all_items'     => __( 'Sve lamele', 'keso-invest' ),
				'edit_item'     => __( 'Izmeni lamelu', 'keso-invest' ),
				'add_new_item'  => __( 'Dodaj lamelu', 'keso-invest' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => false,
			'hierarchical'      => true,
			'rewrite'           => false,
		)
	);
}
add_action( 'init', 'keso_register_content_types' );

/** Osnovni termini (sprat, lamela); kreiraju se jednom. */
function keso_seed_terms() {
	if ( get_option( 'keso_terms_seeded' ) ) {
		return;
	}
	$floors = array(
		'prvi'    => __( 'Prvi sprat', 'keso-invest' ),
		'drugi'   => __( 'Drugi sprat', 'keso-invest' ),
		'treci'   => __( 'Treći sprat', 'keso-invest' ),
		'cetvrti' => __( 'Četvrti sprat', 'keso-invest' ),
	);
	foreach ( $floors as $slug => $name ) {
		if ( ! term_exists( $slug, 'sprat' ) ) {
			wp_insert_term( $name, 'sprat', array( 'slug' => $slug ) );
		}
	}
	foreach ( array( 'G', 'A', 'B' ) as $l ) {
		if ( ! term_exists( 'lamela-' . strtolower( $l ), 'lamela' ) ) {
			wp_insert_term(
				/* translators: %s: oznaka lamele (G, A, B) */
				sprintf( __( 'Lamela %s', 'keso-invest' ), $l ),
				'lamela',
				array( 'slug' => 'lamela-' . strtolower( $l ) )
			);
		}
	}
	update_option( 'keso_terms_seeded', 1, false );
}
add_action( 'init', 'keso_seed_terms', 20 );

/** Podrazumevano sortiranje u administraciji: po redosledu pa po naslovu. */
function keso_admin_columns_stan( $cols ) {
	return array(
		'cb'          => $cols['cb'],
		'title'       => __( 'Oznaka stana', 'keso-invest' ),
		'taxonomy-sprat'  => __( 'Sprat', 'keso-invest' ),
		'taxonomy-lamela' => __( 'Lamela', 'keso-invest' ),
		'keso_tip'    => __( 'Tip', 'keso-invest' ),
		'keso_povrsina' => __( 'Površina', 'keso-invest' ),
		'keso_status' => __( 'Status', 'keso-invest' ),
	);
}
add_filter( 'manage_stan_posts_columns', 'keso_admin_columns_stan' );

function keso_admin_column_content_stan( $col, $id ) {
	if ( 'keso_tip' === $col ) {
		echo esc_html( 'lokal' === get_post_meta( $id, 'tip', true ) ? __( 'Lokal', 'keso-invest' ) : __( 'Stan', 'keso-invest' ) );
	} elseif ( 'keso_povrsina' === $col ) {
		$p = get_post_meta( $id, 'povrsina', true );
		echo '' !== $p ? esc_html( keso_format_area( $p ) ) : '&mdash;';
	} elseif ( 'keso_status' === $col ) {
		echo esc_html( keso_status_label( get_post_meta( $id, 'status', true ) ) );
	}
}
add_action( 'manage_stan_posts_custom_column', 'keso_admin_column_content_stan', 10, 2 );

function keso_admin_columns_referenca( $cols ) {
	return array(
		'cb'      => $cols['cb'],
		'title'   => __( 'Adresa', 'keso-invest' ),
		'keso_st' => __( 'Stanova', 'keso-invest' ),
		'keso_lo' => __( 'Lokala', 'keso-invest' ),
		'keso_go' => __( 'Godina', 'keso-invest' ),
	);
}
add_filter( 'manage_referenca_posts_columns', 'keso_admin_columns_referenca' );

function keso_admin_column_content_referenca( $col, $id ) {
	$map = array(
		'keso_st' => 'stanova',
		'keso_lo' => 'lokala',
		'keso_go' => 'godina',
	);
	if ( isset( $map[ $col ] ) ) {
		$v = get_post_meta( $id, $map[ $col ], true );
		echo '' !== $v ? esc_html( $v ) : '&mdash;';
	}
}
add_action( 'manage_referenca_posts_custom_column', 'keso_admin_column_content_referenca', 10, 2 );

/** Admin upit za stanove: filter po statusu. */
function keso_admin_status_filter() {
	global $typenow;
	if ( 'stan' !== $typenow ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$cur = isset( $_GET['keso_status'] ) ? sanitize_key( wp_unslash( $_GET['keso_status'] ) ) : '';
	echo '<select name="keso_status"><option value="">' . esc_html__( 'Svi statusi', 'keso-invest' ) . '</option>';
	foreach ( keso_statuses() as $key => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $cur, $key, false ), esc_html( $label ) );
	}
	echo '</select>';
}
add_action( 'restrict_manage_posts', 'keso_admin_status_filter' );

function keso_admin_status_filter_query( $q ) {
	global $pagenow;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( is_admin() && $q->is_main_query() && 'edit.php' === $pagenow && 'stan' === $q->get( 'post_type' ) && ! empty( $_GET['keso_status'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$s = sanitize_key( wp_unslash( $_GET['keso_status'] ) );
		if ( isset( keso_statuses()[ $s ] ) ) {
			$q->set( 'meta_key', 'status' );
			$q->set( 'meta_value', $s );
		}
	}
}
add_action( 'pre_get_posts', 'keso_admin_status_filter_query' );
