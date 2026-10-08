<?php
/**
 * Početna strana.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$keso_refs   = keso_references();
$keso_tot    = keso_reference_totals();
?>

<section class="hero" data-hero aria-label="<?php esc_attr_e( 'Uvod', 'keso-invest' ); ?>">
	<div class="hero__media" data-hero-media>
		<?php keso_media( (int) keso_opt( 'img_hero' ), 'keso-hero', __( 'Luma Residence: glavna fotografija ili render', 'keso-invest' ), array( 'ratio' => '16/9', 'eager' => true, 'sizes' => '100vw', 'class' => 'media--fill', 'default' => 'hero', 'alt' => __( 'Luma Residence: 3D prikaz fasade u zalasku sunca', 'keso-invest' ) ) ); ?>
	</div>
	<div class="hero__scrim" aria-hidden="true"></div>
	<div class="wrap hero__inner">
		<h1 class="hero__title" data-split><?php echo esc_html( keso_opt( 'hero_title' ) ); ?></h1>
		<p class="hero__sub"><?php echo esc_html( keso_opt( 'hero_sub' ) ); ?></p>
		<div class="hero__cta">
			<a class="btn btn--accent" href="<?php echo esc_url( keso_luma_url() ); ?>"<?php echo keso_luma_is_external() ? ' target="_blank" rel="noopener"' : ''; ?>><?php esc_html_e( 'Ponuda stanova', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></a>
			<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/o-investitoru/' ) ); ?>"><?php esc_html_e( 'O investitoru', 'keso-invest' ); ?></a>
		</div>
	</div>
	<dl class="wrap hero__facts">
		<div><dt><?php esc_html_e( 'Stanovi', 'keso-invest' ); ?></dt><dd><?php echo esc_html( keso_opt( 'luma_apt_range' ) ); ?></dd></div>
		<div><dt><?php esc_html_e( 'Poslovni prostori', 'keso-invest' ); ?></dt><dd><?php echo esc_html( keso_opt( 'luma_shop_range' ) ); ?></dd></div>
		<div><dt><?php esc_html_e( 'Završetak I faze', 'keso-invest' ); ?></dt><dd><?php echo esc_html( keso_opt( 'luma_phase1_end' ) ); ?></dd></div>
	</dl>
</section>

<!-- TODO: brojke su zbir iz referenci (134 stanova, 7 lokala); stari sajt navodi i 130+ i 300+. Vlasnik potvrđuje. -->
<section class="numbers section" aria-labelledby="numbers-title">
	<div class="wrap">
		<h2 id="numbers-title" class="label"><span class="label__no">01</span> <?php esc_html_e( 'Kompanija u brojkama', 'keso-invest' ); ?></h2>
		<?php keso_render_stats(); ?>
	</div>
</section>

<section class="about section" aria-labelledby="about-title">
	<div class="wrap about__grid">
		<div class="about__text">
			<h2 id="about-title" class="section__title" data-split>
				<?php
				/* translators: %d: broj godina iskustva */
				echo esc_html( sprintf( __( 'Više od %d godina u gradnji stambenih projekata visoke vrednosti', 'keso-invest' ), (int) keso_opt( 'stat_years' ) ) );
				?>
			</h2>
			<p class="lead" data-reveal><?php esc_html_e( 'KESO INVEST doo iz Novog Sada razvija stambene projekte visoke vrednosti. Od idejnog rešenja do završne faze primenjujemo iste principe: kvalitet gradnje, pažljivo birane lokacije i odgovoran odnos prema kupcima.', 'keso-invest' ); ?></p>
			<a class="link-arrow" href="<?php echo esc_url( home_url( '/o-investitoru/' ) ); ?>" data-reveal><?php esc_html_e( 'O investitoru', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></a>
		</div>
		<div class="about__media" data-reveal-img>
			<?php keso_media( (int) keso_opt( 'img_about' ), 'keso-card', __( 'Fotografija: realizovani objekat ili tim', 'keso-invest' ), array( 'ratio' => '4/5', 'sizes' => '(min-width:1024px) 40vw, 100vw', 'parallax' => true, 'default' => 'about', 'alt' => __( 'Luma Residence: 3D prikaz balkona i fasade', 'keso-invest' ) ) ); ?>
		</div>
	</div>
</section>

<section class="luma band band--dark" aria-labelledby="luma-title">
	<div class="luma__media" data-reveal-img>
		<?php keso_media( (int) keso_opt( 'img_luma' ), 'keso-hero', __( 'Fotografija ili render: Luma Residence', 'keso-invest' ), array( 'ratio' => '21/9', 'sizes' => '100vw', 'parallax' => true, 'class' => 'media--wide', 'default' => 'luma', 'alt' => __( 'Luma Residence: 3D prikaz kompleksa i platoa iz vazduha', 'keso-invest' ) ) ); ?>
	</div>
	<div class="wrap luma__grid section">
		<div>
			<h2 id="luma-title" class="section__title" data-split>Luma Residence</h2>
			<p class="lead" data-reveal><?php echo esc_html( keso_opt( 'luma_location' ) ); ?>. <?php esc_html_e( 'Stambeno-poslovni kompleks.', 'keso-invest' ); ?> <?php esc_html_e( 'Status:', 'keso-invest' ); ?> <?php echo esc_html( mb_strtolower( keso_opt( 'luma_status' ) ) ); ?>.</p>
			<ul class="phases" data-stagger>
				<li class="phases__item">
					<span class="phases__tag"><?php esc_html_e( 'I faza', 'keso-invest' ); ?></span>
					<strong><?php echo esc_html( keso_opt( 'luma_phase1' ) ); ?></strong>
					<span><?php esc_html_e( 'Završetak:', 'keso-invest' ); ?> <?php echo esc_html( mb_strtolower( keso_opt( 'luma_phase1_end' ) ) ); ?></span>
				</li>
				<li class="phases__item">
					<span class="phases__tag"><?php esc_html_e( 'II faza', 'keso-invest' ); ?></span>
					<strong><?php echo esc_html( keso_opt( 'luma_phase2' ) ); ?></strong>
					<span><?php echo esc_html( keso_opt( 'luma_phase2_status' ) ); ?></span>
				</li>
			</ul>
		</div>
		<div class="floors">
			<p class="lead" data-reveal><?php esc_html_e( 'Planovi, ponuda i sve o projektu na posebnom sajtu Luma Residence.', 'keso-invest' ); ?></p>
			<a class="btn btn--accent" href="<?php echo esc_url( keso_luma_url() ); ?>"<?php echo keso_luma_is_external() ? ' target="_blank" rel="noopener"' : ''; ?>><?php esc_html_e( 'Pogledaj sajt projekta', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></a>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/values' ); ?>

<?php if ( $keso_refs ) : ?>
<section class="refs section" aria-labelledby="refs-title" data-refs>
	<div class="wrap">
		<h2 id="refs-title" class="label"><span class="label__no">02</span> <?php esc_html_e( 'Reference', 'keso-invest' ); ?></h2>
		<div class="refs__grid">
			<ul class="refs__list" data-stagger>
				<?php
				foreach ( $keso_refs as $keso_i => $keso_r ) :
					$keso_st = (int) get_post_meta( $keso_r->ID, 'stanova', true );
					$keso_lo = (int) get_post_meta( $keso_r->ID, 'lokala', true );
					/*
					 * SLIKE OBJEKATA: postavljaju se u adminu, Reference > izaberi objekat > "Istaknuta slika"
					 * (preporuka 800x600 ili veća, 4:3). Dok slike nema, data-image je prazan i ostaje placeholder.
					 */
					$keso_img = (string) get_the_post_thumbnail_url( $keso_r, 'keso-card' );
					$keso_cap = sprintf(
						/* translators: 1: adresa objekta, 2: broj stanova, 3: broj lokala */
						__( '%1$s: %2$d stanova, %3$d lokala', 'keso-invest' ),
						get_the_title( $keso_r ),
						$keso_st,
						$keso_lo
					);
					?>
					<li class="refs__row ref-item<?php echo 0 === $keso_i ? ' is-active' : ''; ?>" tabindex="0" data-image="<?php echo esc_url( $keso_img ); ?>" data-caption="<?php echo esc_attr( $keso_cap ); ?>">
						<span class="refs__addr"><?php echo esc_html( get_the_title( $keso_r ) ); ?></span>
						<span class="refs__nums">
							<span><b><?php echo esc_html( $keso_st ); ?></b> <?php esc_html_e( 'stanova', 'keso-invest' ); ?></span>
							<span><b><?php echo esc_html( $keso_lo ); ?></b> <?php esc_html_e( 'lokala', 'keso-invest' ); ?></span>
						</span>
					</li>
				<?php endforeach; ?>
				<li class="refs__total">
					<span><?php esc_html_e( 'Ukupno', 'keso-invest' ); ?></span>
					<span class="refs__nums"><span><b><?php echo esc_html( $keso_tot['stanova'] ); ?></b> <?php esc_html_e( 'stanova', 'keso-invest' ); ?></span><span><b><?php echo esc_html( $keso_tot['lokala'] ); ?></b> <?php esc_html_e( 'lokala', 'keso-invest' ); ?></span></span>
				</li>
			</ul>
			<div class="refs__preview" data-refs-preview aria-hidden="true">
				<?php keso_media( 0, 'keso-card', __( 'Fotografija objekta (po adresi)', 'keso-invest' ), array( 'ratio' => '4/3' ) ); ?>
				<p class="refs__preview-cap" data-refs-cap><?php echo esc_html( sprintf( __( '%1$s: %2$d stanova, %3$d lokala', 'keso-invest' ), get_the_title( $keso_refs[0] ), (int) get_post_meta( $keso_refs[0]->ID, 'stanova', true ), (int) get_post_meta( $keso_refs[0]->ID, 'lokala', true ) ) ); ?></p>
			</div>
		</div>
		<a class="link-arrow" href="<?php echo esc_url( home_url( '/reference/' ) ); ?>"><?php esc_html_e( 'Sve reference', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></a>
	</div>
</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/process' ); ?>

<?php get_template_part( 'template-parts/cta' ); ?>

<section class="contact section" id="kontakt" aria-labelledby="contact-title">
	<div class="wrap contact__grid">
		<div class="contact__info">
			<h2 id="contact-title" class="section__title" data-split><?php esc_html_e( 'Kontakt', 'keso-invest' ); ?></h2>
			<?php get_template_part( 'template-parts/contact-info' ); ?>
		</div>
		<div class="contact__form">
			<?php get_template_part( 'template-parts/contact-form' ); ?>
		</div>
	</div>
	<div class="wrap"><?php get_template_part( 'template-parts/map' ); ?></div>
</section>

<?php
get_footer();
