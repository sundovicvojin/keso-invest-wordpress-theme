<?php
/**
 * Zaglavlje: wordmark, meni, telefon, dugme Kontakt.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$keso_over = is_front_page() || is_page( 'luma-residence' );
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#sadrzaj"><?php esc_html_e( 'Pređi na sadržaj', 'keso-invest' ); ?></a>

<header class="site-header<?php echo $keso_over ? ' is-over' : ''; ?>" data-header>
	<div class="site-header__bar wrap">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Keso Invest, početna strana', 'keso-invest' ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<span class="brand__logo" aria-hidden="true"></span><span class="sr-only">KESO INVEST</span>';
			}
			?>
		</a>

		<nav id="site-nav" class="nav" aria-label="<?php esc_attr_e( 'Glavni meni', 'keso-invest' ); ?>" data-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav__list',
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
			?>
			<div class="nav__foot">
				<a class="nav__tel" href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_1' ) ) ); ?>"><?php echo esc_html( keso_opt( 'phone_1' ) ); ?></a>
				<a class="nav__mail" href="mailto:<?php echo esc_attr( keso_opt( 'email' ) ); ?>"><?php echo esc_html( keso_opt( 'email' ) ); ?></a>
			</div>
		</nav>

		<div class="site-header__actions">
			<a class="tel" href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_1' ) ) ); ?>">
				<?php keso_icon( 'phone' ); ?>
				<span><?php echo esc_html( keso_opt( 'phone_1' ) ); ?></span>
			</a>
			<a class="btn btn--sm btn--header" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Kontakt', 'keso-invest' ); ?></a>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
				<span class="nav-toggle__bars" aria-hidden="true"><i></i><i></i></span>
				<span class="sr-only" data-nav-toggle-label><?php esc_html_e( 'Otvori meni', 'keso-invest' ); ?></span>
			</button>
		</div>
	</div>
</header>
<div class="header-sentinel" data-sentinel aria-hidden="true"></div>

<main id="sadrzaj" class="site-main" tabindex="-1">
