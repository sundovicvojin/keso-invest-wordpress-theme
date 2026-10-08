<?php
/**
 * Kartica objave (arhiva i pretraga).
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class( 'post-card' ); ?> data-reveal>
	<a class="post-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'keso-card', array( 'class' => 'post-card__img', 'loading' => 'lazy', 'sizes' => '(min-width:1024px) 30vw, 100vw' ) ); ?>
		<?php endif; ?>
		<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j. n. Y.' ) ); ?></time>
		<h2 class="post-card__title"><?php the_title(); ?></h2>
		<p class="post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
	</a>
</article>
