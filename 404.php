<?php
/**
 * Stranica nije pronađena.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<section class="not-found section">
	<div class="wrap">
		<p class="not-found__code" aria-hidden="true">404</p>
		<h1 class="page-hero__title"><?php esc_html_e( 'Stranica nije pronađena', 'keso-invest' ); ?></h1>
		<p class="lead"><?php esc_html_e( 'Adresa ne postoji ili je premeštena. Pređite na početnu stranu ili pretražite sajt.', 'keso-invest' ); ?></p>
		<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Početna strana', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></a></p>
		<?php get_search_form(); ?>
	</div>
</section>
<?php
get_footer();
