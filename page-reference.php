<?php
/**
 * Reference: lista završenih objekata sa zbirovima.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$keso_refs = keso_references();
$keso_tot  = keso_reference_totals();
$keso_nas  = keso_opt( 'naselja' );

keso_page_hero( get_the_title(), __( 'Završeni stambeni objekti u Novom Sadu.', 'keso-invest' ), array( array( get_the_title(), '' ) ) );
?>

<section class="section section--tight">
	<div class="wrap">
		<?php if ( $keso_refs ) : ?>
			<table class="refs-table">
				<caption class="sr-only"><?php esc_html_e( 'Reference', 'keso-invest' ); ?></caption>
				<thead><tr>
					<th scope="col"><?php esc_html_e( 'Lokacija', 'keso-invest' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Stanova', 'keso-invest' ); ?></th>
					<th scope="col" class="num"><?php esc_html_e( 'Lokala', 'keso-invest' ); ?></th>
				</tr></thead>
				<tbody>
					<?php foreach ( $keso_refs as $keso_r ) : ?>
						<tr data-reveal>
							<th scope="row" data-label="<?php esc_attr_e( 'Lokacija', 'keso-invest' ); ?>">
								<?php echo esc_html( get_the_title( $keso_r ) ); ?>
								<?php $keso_y = get_post_meta( $keso_r->ID, 'godina', true ); ?>
								<?php if ( $keso_y ) : ?><span class="refs-table__year"><?php echo esc_html( $keso_y ); ?></span><?php endif; ?>
							</th>
							<td class="num" data-label="<?php esc_attr_e( 'Stanova', 'keso-invest' ); ?>"><?php echo (int) get_post_meta( $keso_r->ID, 'stanova', true ); ?></td>
							<td class="num" data-label="<?php esc_attr_e( 'Lokala', 'keso-invest' ); ?>"><?php echo (int) get_post_meta( $keso_r->ID, 'lokala', true ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot><tr>
					<th scope="row"><?php esc_html_e( 'Ukupno', 'keso-invest' ); ?></th>
					<td class="num" data-label="<?php esc_attr_e( 'Stanova', 'keso-invest' ); ?>"><?php echo (int) $keso_tot['stanova']; ?></td>
					<td class="num" data-label="<?php esc_attr_e( 'Lokala', 'keso-invest' ); ?>"><?php echo (int) $keso_tot['lokala']; ?></td>
				</tr></tfoot>
			</table>
			<?php if ( $keso_nas ) : ?>
				<p class="refs-note"><?php echo esc_html( sprintf( /* translators: %s: spisak naselja */ __( 'Naselja: %s.', 'keso-invest' ), $keso_nas ) ); ?></p><!-- TODO: potvrditi kontekst naselja sa starog sajta. -->
			<?php endif; ?>
		<?php else : ?>
			<div class="empty"><h2 class="empty__title"><?php esc_html_e( 'Reference još nisu unete', 'keso-invest' ); ?></h2></div>
		<?php endif; ?>
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
