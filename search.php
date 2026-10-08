<?php
/**
 * Rezultati pretrage.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
/* translators: %s: izraz pretrage */
keso_page_hero( sprintf( __( 'Pretraga: %s', 'keso-invest' ), get_search_query() ), '', array( array( __( 'Pretraga', 'keso-invest' ), '' ) ) );
?>
<section class="section section--tight">
	<div class="wrap">
		<?php get_search_form(); ?>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card-post' );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'prev_text' => __( 'Prethodna', 'keso-invest' ), 'next_text' => __( 'Sledeća', 'keso-invest' ), 'screen_reader_text' => __( 'Navigacija kroz rezultate', 'keso-invest' ) ) ); ?>
		<?php else : ?>
			<div class="empty">
				<h2 class="empty__title"><?php esc_html_e( 'Nema rezultata', 'keso-invest' ); ?></h2>
				<p><?php esc_html_e( 'Pokušajte sa drugim izrazom ili pregledajte glavne stranice sajta.', 'keso-invest' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
