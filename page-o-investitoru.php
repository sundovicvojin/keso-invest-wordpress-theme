<?php
/**
 * O investitoru.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

keso_page_hero( get_the_title(), __( 'Stambeni projekti visoke vrednosti, od idejnog rešenja do završne faze.', 'keso-invest' ), array( array( get_the_title(), '' ) ) );
?>

<section class="section section--tight about about--page" aria-label="<?php esc_attr_e( 'Kompanija', 'keso-invest' ); ?>">
	<div class="wrap about__grid">
		<div class="about__text">
			<p class="lead" data-reveal><?php echo esc_html( sprintf( /* translators: %d: godine iskustva */ __( 'KESO INVEST doo iz Novog Sada razvija stambene projekte visoke vrednosti. Imamo više od %d godina iskustva.', 'keso-invest' ), (int) keso_opt( 'stat_years' ) ) ); ?></p>
			<p data-reveal><?php esc_html_e( 'Od idejnog rešenja do završne faze primenjujemo iste principe: kvalitet gradnje, pažljivo birane lokacije i odgovoran odnos prema kupcima. Prodaja je transparentna, uz potpunu pravnu sigurnost, a projekti se zasnivaju na savremenim arhitektonskim konceptima, sa fokusom na komfor i dugoročnu vrednost doma.', 'keso-invest' ); ?></p>
			<!-- TODO: stari sajt u tekstu "O investitoru" navodi "više od 300 stanova i 10 lokala". Brojke ostaju u Customizer-u dok vlasnik ne potvrdi. -->
		</div>
		<div class="about__media" data-reveal-img>
			<?php keso_media( (int) keso_opt( 'img_about' ), 'keso-card', __( 'Fotografija: realizovani objekat ili tim', 'keso-invest' ), array( 'ratio' => '4/5', 'sizes' => '(min-width:1024px) 40vw, 100vw', 'parallax' => true, 'default' => 'about', 'alt' => __( 'Luma Residence: 3D prikaz balkona i fasade', 'keso-invest' ) ) ); ?>
		</div>
	</div>
</section>

<section class="numbers section" aria-label="<?php esc_attr_e( 'Brojke', 'keso-invest' ); ?>">
	<div class="wrap"><?php keso_render_stats(); ?></div>
</section>

<?php
get_template_part( 'template-parts/values' );
get_template_part( 'template-parts/process' );
if ( trim( get_the_content() ) ) :
	?>
	<section class="section"><div class="wrap prose"><?php the_content(); ?></div></section>
<?php endif; ?>
<?php
get_template_part( 'template-parts/cta' );
get_footer();
