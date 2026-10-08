<?php
/**
 * Podnožje: džinovski wordmark, kontakt, mapa sajta.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$keso_privacy = get_page_by_path( 'politika-privatnosti' );
?>
</main>

<footer class="site-footer">
	<div class="wrap site-footer__grid">
		<div class="site-footer__col">
			<h2 class="site-footer__h"><?php esc_html_e( 'Kontakt', 'keso-invest' ); ?></h2>
			<address class="site-footer__addr">
				KESO INVEST doo<br>
				<?php echo esc_html( keso_opt( 'address' ) ); ?>
			</address>
			<p>
				<a href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_1' ) ) ); ?>"><?php echo esc_html( keso_opt( 'phone_1' ) ); ?></a><br>
				<?php if ( keso_opt( 'phone_2' ) ) : ?>
					<a href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_2' ) ) ); ?>"><?php echo esc_html( keso_opt( 'phone_2' ) ); ?></a><br>
				<?php endif; ?>
				<a href="mailto:<?php echo esc_attr( keso_opt( 'email' ) ); ?>"><?php echo esc_html( keso_opt( 'email' ) ); ?></a>
			</p>
		</div>
		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Mapa sajta', 'keso-invest' ); ?>">
			<h2 class="site-footer__h"><?php esc_html_e( 'Sajt', 'keso-invest' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'site-footer__list',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
		<div class="site-footer__col">
			<h2 class="site-footer__h"><?php esc_html_e( 'Luma Residence', 'keso-invest' ); ?></h2>
			<ul class="site-footer__list">
				<li><a href="<?php echo esc_url( keso_luma_url() ); ?>"<?php echo keso_luma_is_external() ? ' target="_blank" rel="noopener"' : ''; ?>><?php esc_html_e( 'Pogledaj sajt projekta', 'keso-invest' ); ?></a></li>
			</ul>
			<?php $keso_social = keso_social_links(); ?>
			<?php if ( $keso_social ) : ?>
				<ul class="site-footer__list site-footer__social">
					<?php foreach ( $keso_social as $keso_n => $keso_u ) : ?>
						<li><a href="<?php echo esc_url( $keso_u ); ?>" rel="noopener" target="_blank"><?php echo esc_html( $keso_n ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>

	<div class="site-footer__word" aria-hidden="true"><?php keso_wordmark( 'site-footer__mark' ); ?></div>

	<div class="wrap site-footer__legal">
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> KESO INVEST doo, Novi Sad | Created by <a href="https://www.instagram.com/devbyvojin/">devbyvojin</a></span>
		<?php if ( $keso_privacy && 'publish' === $keso_privacy->post_status ) : ?>
			<a href="<?php echo esc_url( get_permalink( $keso_privacy ) ); ?>"><?php esc_html_e( 'Politika privatnosti', 'keso-invest' ); ?></a>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
