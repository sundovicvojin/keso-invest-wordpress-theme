<?php
/**
 * Prvo podešavanje pri aktivaciji: stranice, meniji, reference iz tabele sa starog sajta.
 * Ništa se ne briše i ne prepisuje; postojeće stranice sa istim slugom ostaju.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function keso_seed_pages() {
	return array(
		'luma-residence' => array( 'Luma Residence', 'Luma Residence je stambeno-poslovni kompleks u Novom Sadu, na uglu ulica Oblačića Rada i Boška Vrebalova. Pogledajte faze gradnje i ponudu po spratovima.' ),
		'prvi-sprat'     => array( 'Prvi sprat', 'Pregled stanova na prvom spratu kompleksa Luma Residence i njihovog statusa.' ),
		'drugi-sprat'    => array( 'Drugi sprat', 'Pregled stanova na drugom spratu kompleksa Luma Residence i njihovog statusa.' ),
		'treci-sprat'    => array( 'Treći sprat', 'Pregled stanova na trećem spratu kompleksa Luma Residence i njihovog statusa.' ),
		'cetvrti-sprat'  => array( 'Četvrti sprat', 'Pregled stanova na četvrtom spratu kompleksa Luma Residence i njihovog statusa.' ),
		'reference'      => array( 'Reference', 'Završeni stambeni objekti kompanije Keso Invest u Novom Sadu: adrese, broj stanova i poslovnih prostora.' ),
		'o-investitoru'  => array( 'O investitoru', 'Keso Invest doo iz Novog Sada gradi stambene projekte visoke vrednosti, sa više od 10 godina iskustva.' ),
		'contact'        => array( 'Kontakt', 'Kontaktirajte Keso Invest: Danila Kiša 14a, Novi Sad. Telefon, email i kontakt forma.' ),
		'vesti'          => array( 'Vesti', 'Vesti i obaveštenja kompanije Keso Invest iz Novog Sada.' ),
	);
}

function keso_seed_references() {
	return array(
		array( 'Bele Njive 24', 36, 0 ),
		array( 'Bele Njive 24a', 38, 0 ),
		array( 'Braće Ribnikar 46', 12, 2 ),
		array( 'Đorđa Servickog 16', 16, 1 ),
		array( 'Dalmatinska 21', 32, 4 ),
	);
}

function keso_activate_theme() {
	keso_register_content_types();
	keso_seed_terms();

	$ids = array();
	foreach ( keso_seed_pages() as $slug => $d ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			if ( 'publish' !== $existing->post_status ) {
				wp_update_post( array( 'ID' => $existing->ID, 'post_status' => 'publish' ) );
			}
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $d[0],
				'post_name'   => $slug,
				'meta_input'  => array( 'meta_opis' => $d[1] ),
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			$ids[ $slug ] = $id;
		}
	}
	// Spratovi su podstranice Luma Residence (adrese ostaju /prvi-sprat/ jer se parent ne dodaje).

	// Politika privatnosti: skica; tekst mora da unese vlasnik.
	if ( ! get_page_by_path( 'politika-privatnosti' ) ) {
		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'draft',
				'post_title'   => 'Politika privatnosti',
				'post_name'    => 'politika-privatnosti',
				'post_content' => '<!-- wp:paragraph --><p>TODO: unesite tekst politike privatnosti i objavite stranicu. Dok je skica, link ka njoj se ne prikazuje.</p><!-- /wp:paragraph -->',
			)
		);
	}

	// Početna i stranica vesti.
	if ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) ) {
		$home = get_page_by_path( 'pocetna' );
		if ( ! $home ) {
			$hid = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => 'Početna',
					'post_name'   => 'pocetna',
				)
			);
		} else {
			$hid = $home->ID;
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $hid );
	}
	if ( ! get_option( 'page_for_posts' ) && isset( $ids['vesti'] ) ) {
		update_option( 'page_for_posts', $ids['vesti'] );
	}

	// Reference iz tabele sa starog sajta (TODO: vlasnik potvrđuje brojke).
	$have = get_posts( array( 'post_type' => 'referenca', 'posts_per_page' => 1, 'post_status' => 'any', 'fields' => 'ids' ) );
	if ( ! $have ) {
		foreach ( keso_seed_references() as $i => $r ) {
			wp_insert_post(
				array(
					'post_type'   => 'referenca',
					'post_status' => 'publish',
					'post_title'  => $r[0],
					'menu_order'  => $i,
					'meta_input'  => array( 'stanova' => (string) $r[1], 'lokala' => (string) $r[2] ),
				)
			);
		}
	}

	// Meniji.
	$locations = get_nav_menu_locations();
	if ( empty( $locations['primary'] ) ) {
		$menu_id = wp_create_nav_menu( 'Glavni meni' );
		if ( ! is_wp_error( $menu_id ) ) {
			$luma_item = 0;
			$add       = static function ( $title, $slug, $parent = 0 ) use ( $menu_id, $ids ) {
				if ( ! isset( $ids[ $slug ] ) ) {
					return 0;
				}
				return wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $title,
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $ids[ $slug ],
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-parent-id' => $parent,
					)
				);
			};
			$luma_item = $add( 'Luma Residence', 'luma-residence' );
			$add( 'Reference', 'reference' );
			$add( 'O investitoru', 'o-investitoru' );
			$add( 'Vesti', 'vesti' );
			$locations['primary'] = $menu_id;
		}
	}
	if ( empty( $locations['footer'] ) ) {
		$fm = wp_create_nav_menu( 'Podnožje' );
		if ( ! is_wp_error( $fm ) ) {
			foreach ( array( 'luma-residence' => 'Luma Residence', 'reference' => 'Reference', 'o-investitoru' => 'O investitoru', 'vesti' => 'Vesti', 'contact' => 'Kontakt' ) as $s => $t ) {
				if ( isset( $ids[ $s ] ) ) {
					wp_update_nav_menu_item(
						$fm,
						0,
						array(
							'menu-item-title'     => $t,
							'menu-item-object'    => 'page',
							'menu-item-object-id' => $ids[ $s ],
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						)
					);
				}
			}
			$locations['footer'] = $fm;
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	// Lepi permalinkovi (ako su obični), bez prepisivanja postojećeg izbora.
	if ( ! get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	flush_rewrite_rules( false );
}
add_action( 'after_switch_theme', 'keso_activate_theme' );
