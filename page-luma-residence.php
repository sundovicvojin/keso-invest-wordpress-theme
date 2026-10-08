<?php
/**
 * Luma Residence: projekat, faze, spratovi.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>

<section class="hero hero--inner" data-hero aria-label="<?php esc_attr_e( 'Luma Residence', 'keso-invest' ); ?>">
	<div class="hero__media" data-hero-media>
		<?php keso_media( (int) keso_opt( 'img_luma' ), 'keso-hero', __( 'Luma Residence: fotografija ili render', 'keso-invest' ), array( 'ratio' => '16/9', 'eager' => true, 'class' => 'media--fill', 'default' => 'luma', 'alt' => __( 'Luma Residence: 3D prikaz kompleksa i platoa iz vazduha', 'keso-invest' ) ) ); ?>
	</div>
	<div class="hero__scrim" aria-hidden="true"></div>
	<div class="wrap hero__inner">
		<h1 class="hero__title" data-split>Luma Residence</h1>
		<p class="hero__sub"><?php echo esc_html( keso_opt( 'luma_location' ) ); ?>. <?php echo esc_html( keso_opt( 'luma_status' ) ); ?>.</p>
	</div>
	<dl class="wrap hero__facts">
		<div><dt><?php esc_html_e( 'Stanovi', 'keso-invest' ); ?></dt><dd><?php echo esc_html( keso_opt( 'luma_apt_range' ) ); ?></dd></div>
		<div><dt><?php esc_html_e( 'Poslovni prostori', 'keso-invest' ); ?></dt><dd><?php echo esc_html( keso_opt( 'luma_shop_range' ) ); ?></dd></div>
		<div><dt><?php esc_html_e( 'Završetak I faze', 'keso-invest' ); ?></dt><dd><?php echo esc_html( keso_opt( 'luma_phase1_end' ) ); ?></dd></div>
	</dl>
</section>

<section class="section phases-sec" aria-labelledby="phases-title">
	<div class="wrap">
		<h2 id="phases-title" class="section__title" data-split><?php esc_html_e( 'Faze gradnje', 'keso-invest' ); ?></h2>
		<ol class="timeline" data-stagger>
			<li class="timeline__item">
				<span class="timeline__tag"><?php esc_html_e( 'I faza', 'keso-invest' ); ?></span>
				<h3><?php echo esc_html( keso_opt( 'luma_phase1' ) ); ?></h3>
				<p><?php esc_html_e( 'Planirani završetak:', 'keso-invest' ); ?> <?php echo esc_html( mb_strtolower( keso_opt( 'luma_phase1_end' ) ) ); ?></p>
			</li>
			<li class="timeline__item">
				<span class="timeline__tag"><?php esc_html_e( 'II faza', 'keso-invest' ); ?></span>
				<h3><?php echo esc_html( keso_opt( 'luma_phase2' ) ); ?></h3>
				<p><?php echo esc_html( keso_opt( 'luma_phase2_status' ) ); ?></p>
			</li>
		</ol>
	</div>
</section>

<section class="section floors-sec band band--ink" aria-labelledby="floors-title">
	<div class="wrap">
		<h2 id="floors-title" class="section__title" data-split><?php esc_html_e( 'Ponuda i planovi stanova', 'keso-invest' ); ?></h2>
		<p class="lead" data-reveal><?php esc_html_e( 'Kompletnu ponudu, planove i status stanova pogledajte na posebnom sajtu projekta.', 'keso-invest' ); ?></p>
		<a class="btn btn--accent" href="<?php echo esc_url( keso_luma_url() ); ?>"<?php echo keso_luma_is_external() ? ' target="_blank" rel="noopener"' : ''; ?>><?php esc_html_e( 'Pogledaj sajt projekta', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></a>
	</div>
</section>

<section class="section gallery" aria-labelledby="gal-title">
	<div class="wrap">
		<h2 id="gal-title" class="section__title" data-split><?php esc_html_e( 'Galerija', 'keso-invest' ); ?></h2>
		<div class="gallery__grid" data-stagger>
			<?php keso_media( (int) keso_opt( 'img_gal1' ), 'keso-card', __( 'Spoljašnji izgled (fotografija ili render)', 'keso-invest' ), array( 'ratio' => '4/3', 'class' => 'gallery__a', 'sizes' => '(min-width:800px) 58vw, 100vw', 'default' => 'gal1', 'alt' => __( 'Luma Residence: 3D prikaz fasade sa ulice', 'keso-invest' ) ) ); ?>
			<?php keso_media( (int) keso_opt( 'img_gal2' ), 'keso-card', __( 'Enterijer (fotografija ili render)', 'keso-invest' ), array( 'ratio' => '4/3', 'class' => 'gallery__b', 'sizes' => '(min-width:800px) 40vw, 100vw', 'default' => 'gal2', 'alt' => __( 'Luma Residence: 3D prikaz ugla zgrade sa poslovnim prostorima u prizemlju', 'keso-invest' ) ) ); ?>
			<?php keso_media( (int) keso_opt( 'img_gal3' ), 'keso-card', __( 'Lokacija i okruženje', 'keso-invest' ), array( 'ratio' => '4/3', 'class' => 'gallery__c', 'sizes' => '(min-width:800px) 40vw, 100vw', 'default' => 'gal3', 'alt' => __( 'Luma Residence: 3D prikaz balkona sa strane', 'keso-invest' ) ) ); ?>
		</div>
		<!-- TODO: 3D prikazi su privremeni; zameniti pravim fotografijama u Customizer-u kad budu dostupne. -->
	</div>
</section>

<?php
if ( trim( get_the_content() ) ) :
	?>
	<section class="section"><div class="wrap prose"><?php the_content(); ?></div></section>
<?php endif; ?>

<?php
get_template_part( 'template-parts/cta' );
get_footer();
