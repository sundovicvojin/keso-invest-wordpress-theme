<?php
/**
 * Kako gradimo: lokacija, projekat, gradnja, predaja.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$keso_steps = array(
	array( __( 'Lokacija', 'keso-invest' ), __( 'Lokacije biramo pažljivo.', 'keso-invest' ) ),
	array( __( 'Projekat', 'keso-invest' ), __( 'Od idejnog rešenja primenjujemo savremene arhitektonske koncepte.', 'keso-invest' ) ),
	array( __( 'Gradnja', 'keso-invest' ), __( 'Kvalitet gradnje je isti u svakoj fazi, do završne.', 'keso-invest' ) ),
	array( __( 'Predaja', 'keso-invest' ), __( 'Prema kupcima imamo odgovoran odnos u svakom koraku.', 'keso-invest' ) ),
);
?>
<section class="process section band band--ink" aria-labelledby="process-title" data-process>
	<div class="wrap process__grid">
		<div class="process__head" data-process-head>
			<h2 id="process-title" class="section__title" data-split><?php esc_html_e( 'Kako gradimo', 'keso-invest' ); ?></h2>
			<div class="process__bar" aria-hidden="true"><span data-process-bar></span></div>
		</div>
		<ol class="process__steps">
			<?php foreach ( $keso_steps as $i => $s ) : ?>
				<li class="process__step" data-step>
					<span class="process__no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<div>
						<h3 class="process__name"><?php echo esc_html( $s[0] ); ?></h3>
						<p><?php echo esc_html( $s[1] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
