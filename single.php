<?php
/**
 * Pojedinačna objava.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) :
	the_post();
	$keso_blog = (int) get_option( 'page_for_posts' );
	?>
	<article <?php post_class( 'single' ); ?>>
		<header class="page-hero">
			<div class="wrap wrap--narrow">
				<nav class="crumbs" aria-label="<?php esc_attr_e( 'Putanja', 'keso-invest' ); ?>"><ol>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Početna', 'keso-invest' ); ?></a></li>
					<li><a href="<?php echo esc_url( $keso_blog ? get_permalink( $keso_blog ) : home_url( '/vesti/' ) ); ?>"><?php esc_html_e( 'Vesti', 'keso-invest' ); ?></a></li>
					<li><span aria-current="page"><?php the_title(); ?></span></li>
				</ol></nav>
				<time class="single__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j. n. Y.' ) ); ?></time>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
			</div>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="wrap single__img"><?php the_post_thumbnail( 'keso-wide', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(min-width:1440px) 1344px, 100vw' ) ); ?></div>
		<?php endif; ?>
		<section class="section section--tight">
			<div class="wrap wrap--narrow prose"><?php the_content(); ?></div>
		</section>
		<nav class="wrap wrap--narrow post-nav" aria-label="<?php esc_attr_e( 'Druge objave', 'keso-invest' ); ?>">
			<?php previous_post_link( '<span class="post-nav__prev">%link</span>', '&larr; %title' ); ?>
			<?php next_post_link( '<span class="post-nav__next">%link</span>', '%title &rarr;' ); ?>
		</nav>
	</article>
	<?php
endwhile;
get_template_part( 'template-parts/cta' );
get_footer();
