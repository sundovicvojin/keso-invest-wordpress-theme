<?php
/**
 * Podešavanja koja vlasnik menja bez koda (Izgled → Prilagodi).
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Podrazumevane vrednosti. Sve vrednosti dolaze sa postojećeg sajta kesoinvest.com.
 * Stavke označene TODO vlasnik mora da potvrdi (videti README).
 */
function keso_defaults() {
	return array(
		// Kontakt.
		'address'        => 'Danila Kiša 14a, Novi Sad',
		'phone_1'        => '+381 21 30 10 250',
		'phone_2'        => '+381 64 657 9655',
		'email'          => 'kesoinvestdoo@gmail.com',
		'form_to'        => '',
		'social_facebook'  => '',
		'social_instagram' => '',
		'social_linkedin'  => '',
		// Brojke. TODO: potvrditi (izvor se protivreči: 130+/300+ naspram 134 u tabeli referenci).
		'stats_manual'   => false,
		'stat_units'     => 134,
		'stat_shops'     => 7,
		'stat_locations' => 5,
		'stat_years'     => 10,
		'stat_ontime'    => '', // TODO: procenat završenih projekata u planiranim rokovima, vrednost nije pročitana sa sajta.
		'naselja'        => 'Grbavica, Novo Naselje, Telep', // TODO: potvrditi kontekst.
		// Luma Residence.
		'luma_location'  => 'Ugao ulica Oblačića Rada i Boška Vrebalova',
		'luma_status'    => 'U izgradnji',
		'luma_phase1'    => 'Lamela G i Lamela A',
		'luma_phase1_end' => 'Kraj 2027.',
		'luma_phase2'    => 'Lamela B',
		'luma_phase2_status' => 'U pripremi',
		'luma_apt_range' => '27–93 m²',
		'luma_shop_range' => '90–560 m²',
		'luma_site_url'  => 'https://lumaresidence.kesoinvest.com',
		// Početna.
		'hero_title'     => 'Stambeni projekti visoke vrednosti u Novom Sadu',
		'hero_sub'       => 'Luma Residence je u izgradnji. Završetak prve faze planiran je za kraj 2027. godine.',
		// Slike (prazno = označeno mesto za fotografiju).
		'img_hero'       => 0,
		'img_about'      => 0,
		'img_luma'       => 0,
		'img_og'         => 0,
		'img_gal1'       => 0,
		'img_gal2'       => 0,
		'img_gal3'       => 0,
		// Ostalo.
		'show_prices'    => false,
		'turnstile_site' => '',
		'turnstile_secret' => '',
	);
}

function keso_opt( $key ) {
	$d = keso_defaults();
	return get_theme_mod( $key, $d[ $key ] ?? '' );
}

/** Adresa posebnog sajta projekta Luma Residence; dok nije uneta, vodi na internu stranicu projekta. */
function keso_luma_url() {
	$url = trim( (string) keso_opt( 'luma_site_url' ) );
	return $url ? $url : home_url( '/luma-residence/' );
}

function keso_luma_is_external() {
	return '' !== trim( (string) keso_opt( 'luma_site_url' ) );
}

function keso_sanitize_checkbox( $v ) {
	return (bool) $v;
}

function keso_sanitize_int( $v ) {
	return '' === $v ? '' : absint( $v );
}

function keso_customize_register( $wp_customize ) {
	$d = keso_defaults();

	$add = static function ( $section, $key, $label, $type = 'text', $desc = '', $sanitize = 'sanitize_text_field', $extra = array() ) use ( $wp_customize, $d ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $d[ $key ] ?? '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array_merge(
				array(
					'section'     => $section,
					'label'       => $label,
					'description' => $desc,
					'type'        => $type,
				),
				$extra
			)
		);
	};
	$add_img = static function ( $section, $key, $label, $desc ) use ( $wp_customize ) {
		$wp_customize->add_setting( $key, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$key,
				array(
					'section'     => $section,
					'label'       => $label,
					'description' => $desc,
					'mime_type'   => 'image',
				)
			)
		);
	};

	$wp_customize->add_panel(
		'keso_panel',
		array(
			'title'    => __( 'Keso Invest podešavanja', 'keso-invest' ),
			'priority' => 30,
		)
	);

	// Kontakt.
	$wp_customize->add_section( 'keso_contact', array( 'title' => __( 'Kontakt podaci', 'keso-invest' ), 'panel' => 'keso_panel' ) );
	$add( 'keso_contact', 'address', __( 'Adresa', 'keso-invest' ) );
	$add( 'keso_contact', 'phone_1', __( 'Telefon 1 (glavni, prikazuje se u zaglavlju)', 'keso-invest' ) );
	$add( 'keso_contact', 'phone_2', __( 'Telefon 2', 'keso-invest' ) );
	$add( 'keso_contact', 'email', __( 'Email', 'keso-invest' ), 'text', '', 'sanitize_email' );
	$add( 'keso_contact', 'form_to', __( 'Email za poruke sa kontakt forme', 'keso-invest' ), 'text', __( 'Ako ostane prazno, koristi se gornji email.', 'keso-invest' ), 'sanitize_email' );
	$add( 'keso_contact', 'social_facebook', __( 'Facebook (pun URL)', 'keso-invest' ), 'url', __( 'Ikonice društvenih mreža se prikazuju samo ako je URL unet.', 'keso-invest' ), 'esc_url_raw' );
	$add( 'keso_contact', 'social_instagram', __( 'Instagram (pun URL)', 'keso-invest' ), 'url', '', 'esc_url_raw' );
	$add( 'keso_contact', 'social_linkedin', __( 'LinkedIn (pun URL)', 'keso-invest' ), 'url', '', 'esc_url_raw' );

	// Brojke.
	$wp_customize->add_section(
		'keso_stats',
		array(
			'title'       => __( 'Brojke na sajtu', 'keso-invest' ),
			'panel'       => 'keso_panel',
			'description' => __( 'Podrazumevano se brojevi stanova, lokala i lokacija sabiraju iz unetih referenci. TODO: potvrdite vrednosti, jer se prethodni sajt protivreči (130+, 300+, 134 u tabeli).', 'keso-invest' ),
		)
	);
	$add( 'keso_stats', 'stats_manual', __( 'Ručno unesi brojke (umesto zbira iz referenci)', 'keso-invest' ), 'checkbox', '', 'keso_sanitize_checkbox' );
	$add( 'keso_stats', 'stat_units', __( 'Izgrađeni stanovi (ručno)', 'keso-invest' ), 'number', '', 'keso_sanitize_int' );
	$add( 'keso_stats', 'stat_shops', __( 'Poslovni prostori (ručno)', 'keso-invest' ), 'number', '', 'keso_sanitize_int' );
	$add( 'keso_stats', 'stat_locations', __( 'Lokacije u Novom Sadu (ručno)', 'keso-invest' ), 'number', '', 'keso_sanitize_int' );
	$add( 'keso_stats', 'stat_years', __( 'Godine iskustva („više od“)', 'keso-invest' ), 'number', __( 'Prikazuje se kao „10+“.', 'keso-invest' ), 'keso_sanitize_int' );
	$add( 'keso_stats', 'stat_ontime', __( 'Projekti završeni u planiranim rokovima (%)', 'keso-invest' ), 'number', __( 'TODO: vrednost sa starog sajta nije pročitana. Dok je prazno, brojka se ne prikazuje posetiocima.', 'keso-invest' ), 'keso_sanitize_int', array( 'input_attrs' => array( 'min' => 0, 'max' => 100 ) ) );
	$add( 'keso_stats', 'naselja', __( 'Naselja koja se pominju na starom sajtu', 'keso-invest' ), 'text', __( 'TODO: potvrdite. Prazno polje sakriva red.', 'keso-invest' ) );

	// Luma Residence.
	$wp_customize->add_section( 'keso_luma', array( 'title' => __( 'Luma Residence', 'keso-invest' ), 'panel' => 'keso_panel' ) );
	$add( 'keso_luma', 'luma_location', __( 'Lokacija', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_status', __( 'Status projekta', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_phase1', __( 'I faza (šta obuhvata)', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_phase1_end', __( 'I faza: planirani završetak', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_phase2', __( 'II faza (šta obuhvata)', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_phase2_status', __( 'II faza: status', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_apt_range', __( 'Površine stanova', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_shop_range', __( 'Površine poslovnih prostora', 'keso-invest' ) );
	$add( 'keso_luma', 'luma_site_url', __( 'Adresa posebnog sajta projekta (CTA)', 'keso-invest' ), 'url', __( 'Kad je uneta, meni i dugmad „Pogledaj sajt projekta“ vode na taj sajt (novi tab). Dok je prazno, vode na internu stranicu Luma Residence.', 'keso-invest' ), 'esc_url_raw' );
	$add( 'keso_luma', 'show_prices', __( 'Prikaži cene stanova', 'keso-invest' ), 'checkbox', __( 'Cene se prikazuju samo ako su unete uz stan.', 'keso-invest' ), 'keso_sanitize_checkbox' );

	// Početna i slike.
	$wp_customize->add_section( 'keso_home', array( 'title' => __( 'Početna strana i fotografije', 'keso-invest' ), 'panel' => 'keso_panel' ) );
	$add( 'keso_home', 'hero_title', __( 'Naslov na početnoj', 'keso-invest' ) );
	$add( 'keso_home', 'hero_sub', __( 'Podnaslov (do 20 reči)', 'keso-invest' ), 'textarea' );
	$add_img( 'keso_home', 'img_hero', __( 'Glavna fotografija (hero)', 'keso-invest' ), __( 'Preporuka: 2400×1350 px. Ako ostane prazno, prikazuje se priložena slika (3D prikaz) teme.', 'keso-invest' ) );
	$add_img( 'keso_home', 'img_luma', __( 'Fotografija ili render: Luma Residence', 'keso-invest' ), '' );
	$add_img( 'keso_home', 'img_about', __( 'Fotografija: O investitoru', 'keso-invest' ), '' );
	$add_img( 'keso_home', 'img_og', __( 'Slika za deljenje na mrežama (1200×630)', 'keso-invest' ), '' );
	$add_img( 'keso_home', 'img_gal1', __( 'Galerija Luma Residence: velika slika', 'keso-invest' ), __( 'Ako ostane prazno, prikazuje se priložena slika (3D prikaz).', 'keso-invest' ) );
	$add_img( 'keso_home', 'img_gal2', __( 'Galerija Luma Residence: slika 2', 'keso-invest' ), '' );
	$add_img( 'keso_home', 'img_gal3', __( 'Galerija Luma Residence: slika 3', 'keso-invest' ), '' );

	// Zaštita forme.
	$wp_customize->add_section( 'keso_spam', array( 'title' => __( 'Zaštita kontakt forme', 'keso-invest' ), 'panel' => 'keso_panel', 'description' => __( 'Opciono: Cloudflare Turnstile. Ključeve dobijate besplatno u Cloudflare nalogu. Bez njih forma koristi honeypot, vremensku proveru i ograničenje učestalosti.', 'keso-invest' ) ) );
	$add( 'keso_spam', 'turnstile_site', __( 'Turnstile site key', 'keso-invest' ) );
	$add( 'keso_spam', 'turnstile_secret', __( 'Turnstile secret key', 'keso-invest' ), 'password' );
}
add_action( 'customize_register', 'keso_customize_register' );
