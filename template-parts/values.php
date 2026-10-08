<?php
/**
 * Četiri vrednosti, numerisane, bez ikonica.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$keso_values = array(
	array( __( 'Pouzdanost u realizaciji', 'keso-invest' ), __( 'Od idejnog rešenja do završne faze radimo po istim principima kvaliteta gradnje.', 'keso-invest' ) ),
	array( __( 'Transparentna prodaja', 'keso-invest' ), __( 'Kupcima obezbeđujemo potpunu pravnu sigurnost.', 'keso-invest' ) ),
	array( __( 'Savremeni koncepti', 'keso-invest' ), __( 'Projekte oblikujemo savremenim arhitektonskim konceptima.', 'keso-invest' ) ),
	array( __( 'Dugoročna vrednost doma', 'keso-invest' ), __( 'Fokus je na komforu i vrednosti koja traje.', 'keso-invest' ) ),
);
?>
<section class="values section" aria-labelledby="values-title">
	<div class="wrap">
		<h2 id="values-title" class="section__title" data-split><?php esc_html_e( 'Na čemu gradimo', 'keso-invest' ); ?></h2>
		<ol class="values__list" data-stagger>
			<?php foreach ( $keso_values as $i => $v ) : ?>
				<li class="values__item">
					<span class="values__no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<h3 class="values__name"><?php echo esc_html( $v[0] ); ?></h3>
					<p><?php echo esc_html( $v[1] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
