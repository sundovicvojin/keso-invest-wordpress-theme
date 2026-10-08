<?php
/**
 * Učitavanje stilova i skripti, gate za animacije Nivoa 2.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Putanja do fajla; koristi .min varijantu kada postoji i nije SCRIPT_DEBUG. */
function keso_asset( $rel ) {
	$dir = get_template_directory();
	if ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
		$min = preg_replace( '/\.(css|js)$/', '.min.$1', $rel );
		if ( $min !== $rel && file_exists( $dir . '/' . $min ) ) {
			$rel = $min;
		}
	}
	return array(
		'url'  => get_template_directory_uri() . '/' . $rel,
		'path' => $dir . '/' . $rel,
		'ver'  => file_exists( $dir . '/' . $rel ) ? (string) filemtime( $dir . '/' . $rel ) : KESO_VERSION,
	);
}

/**
 * Inline skript u <head>: klase .js, .anim-ready (Nivo 1) i .motion-ok (Nivo 2 gate).
 * Sadržaj je vidljiv podrazumevano; sakrivanje postoji samo uz ove klase.
 * Rezervni tajmeri skidaju klase ako se skripte ne učitaju.
 */
function keso_head_boot() {
	?>
<script>
(function(d){var h=d.documentElement,m=function(q){return matchMedia(q).matches},c=navigator.connection||{},rm=m('(prefers-reduced-motion:reduce)');h.className=h.className.replace('no-js','js');
if(!rm){h.classList.add('anim-ready')}
if(m('(min-width:1024px)')&&m('(hover:hover) and (pointer:fine)')&&!rm&&!c.saveData&&(navigator.deviceMemory===undefined||navigator.deviceMemory>=4)&&(navigator.hardwareConcurrency===undefined||navigator.hardwareConcurrency>=4)){h.classList.add('motion-ok')}
setTimeout(function(){if(!h.classList.contains('is-ready'))h.classList.remove('anim-ready','motion-ok');if(!h.classList.contains('motion-on'))h.classList.remove('motion-ok')},4500)})(document)
</script>
	<?php
}
add_action( 'wp_head', 'keso_head_boot', 1 );

/** Kritični CSS inline + preload glavnog fonta. */
function keso_critical_css() {
	$f   = keso_asset( 'assets/css/critical.css' );
	$css = file_exists( $f['path'] ) ? (string) file_get_contents( $f['path'] ) : '';
	if ( false === strpos( $f['path'], '.min.' ) ) {
		$css = preg_replace( array( '!/\*.*?\*/!s', '/\s*([{};:,>])\s*/', '/\s{2,}/' ), array( '', '$1', ' ' ), $css );
	}
	$css = str_replace( array( '{{FONTS}}', '{{IMG}}' ), array( get_template_directory_uri() . '/assets/fonts', get_template_directory_uri() . '/assets/img' ), (string) $css );
	echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/fonts/bricolage.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<style id="keso-critical">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS iz teme.
	// Preload LCP slike početne ako je uneta.
	// Hero slika nema preload: <img fetchpriority="high"> je prvi element u dokumentu, a preload sa srcset-om je birao pogrešnu veličinu.
}
add_action( 'wp_head', 'keso_critical_css', 2 );

function keso_enqueue() {
	$css = keso_asset( 'assets/css/main.css' );
	wp_enqueue_style( 'keso-main', $css['url'], array(), $css['ver'] );

	$js = keso_asset( 'assets/js/core.js' );
	wp_enqueue_script( 'keso-core', $js['url'], array(), $js['ver'], array( 'in_footer' => true, 'strategy' => 'defer' ) );

	// Pregled referenci (hover/fokus/tap): samo na početnoj, gde se lista renderuje.
	if ( is_front_page() ) {
		$refs = keso_asset( 'assets/js/refs.js' );
		wp_enqueue_script( 'keso-refs', $refs['url'], array(), $refs['ver'], array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}

	// Kontakt forma (fetch na admin-ajax): samo gde se forma renderuje.
	if ( is_front_page() || is_page( 'contact' ) ) {
		$cf = keso_asset( 'assets/js/contact.js' );
		wp_enqueue_script( 'keso-contact', $cf['url'], array(), $cf['ver'], array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_add_inline_script(
			'keso-contact',
			'window.KESO_FORM=' . wp_json_encode(
				array(
					'ajax' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
					'i18n' => array(
						'sending' => __( 'Šaljem…', 'keso-invest' ),
						'error'   => __( 'Slanje nije uspelo. Pokušajte ponovo ili nas pozovite.', 'keso-invest' ),
					),
				)
			) . ';',
			'before'
		);
	}

	// Mapa (Leaflet, lokalna kopija): samo gde se renderuje template-parts/map.php.
	if ( is_front_page() || is_page( 'contact' ) ) {
		$lf = get_template_directory_uri() . '/assets/vendor/leaflet/';
		$mp = keso_asset( 'assets/js/map.js' );
		wp_enqueue_style( 'keso-leaflet', $lf . 'leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'keso-leaflet', $lf . 'leaflet.js', array(), '1.9.4', array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_enqueue_script( 'keso-map', $mp['url'], array( 'keso-leaflet' ), $mp['ver'], array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}

	$motion = keso_asset( 'assets/js/motion.js' );
	$base   = get_template_directory_uri() . '/assets/js/';
	wp_add_inline_script(
		'keso-core',
		'window.KESO=' . wp_json_encode(
			array(
				'motion' => array(
					$base . 'vendor/gsap.min.js?ver=3.12.7',
					$base . 'vendor/ScrollTrigger.min.js?ver=3.12.7',
					$base . 'vendor/lenis.min.js?ver=1.1.20',
					$motion['url'] . '?ver=' . $motion['ver'],
				),
				'i18n'   => array(
					'close'   => __( 'Zatvori meni', 'keso-invest' ),
					'open'    => __( 'Otvori meni', 'keso-invest' ),
				),
			)
		) . ';',
		'before'
	);

	// Blok stilovi samo gde sadržaj zaista koristi blokove (npr. stranice koje vlasnik napravi u editoru).
	$needs_blocks = is_singular() && has_blocks( get_post() );
	if ( ! $needs_blocks ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
		remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
		remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	}
	wp_dequeue_script( 'wp-embed' );
	wp_deregister_script( 'wp-embed' );

	if ( is_page( 'contact' ) && keso_opt( 'turnstile_site' ) ) {
		wp_enqueue_script( 'keso-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'keso_enqueue' );

/** Preconnect samo na MapTiler pločice, samo gde mapa postoji. */
function keso_map_preconnect() {
	if ( is_front_page() || is_page( 'contact' ) ) {
		echo '<link rel="preconnect" href="https://api.maptiler.com" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'keso_map_preconnect', 3 );

/** Glavni CSS ne blokira iscrtavanje (kritični je inline); noscript rezerva postoji. */
function keso_style_loader_tag( $html, $handle, $href ) {
	if ( 'keso-main' !== $handle ) {
		return $html;
	}
	$href = esc_url( $href );
	return '<link rel="preload" as="style" id="keso-main-css" href="' . $href . '" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
		. '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>' . "\n";
}
add_filter( 'style_loader_tag', 'keso_style_loader_tag', 10, 3 );

/** Gutenberg: stilovi bloka kroz glavni CSS ne pokrivaju editor; editor.css je poseban. */
function keso_dequeue_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
	}
}
add_action( 'wp_default_scripts', 'keso_dequeue_jquery_migrate' );

/** Skrivene pomoćne klase kad nema CSS-a ne treba; ništa dodatno. */
