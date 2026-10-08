<?php
/**
 * Arhive (kategorije, oznake, datumi).
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$keso_title = get_the_archive_title();
keso_page_hero( $keso_title, __( 'Obaveštenja i novosti iz kompanije.', 'keso-invest' ), array( array( $keso_title, '' ) ) );
?>
<section class="section section--tight">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card-post' );
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Prethodna', 'keso-invest' ),
					'next_text' => __( 'Sledeća', 'keso-invest' ),
					'screen_reader_text' => __( 'Navigacija kroz objave', 'keso-invest' ),
				)
			);
			?>
		<?php else : ?>
			<div class="empty">
				<h2 class="empty__title"><?php esc_html_e( 'Još nema objava', 'keso-invest' ); ?></h2>
				<p><?php esc_html_e( 'Vesti će se pojaviti ovde čim budu objavljene.', 'keso-invest' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
