<?php
/**
 * Opšta stranica (sadržaj iz editora).
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) :
	the_post();
	keso_page_hero( get_the_title(), '', array( array( get_the_title(), '' ) ) );
	?>
	<section class="section section--tight">
		<div class="wrap wrap--narrow prose"><?php the_content(); ?></div>
	</section>
	<?php
endwhile;
get_footer();
