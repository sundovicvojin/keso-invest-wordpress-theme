<?php
/**
 * Template Name: Sprat (pregled stanova)
 *
 * Sprat se određuje po slugu stranice (prvi-sprat, drugi-sprat, treci-sprat, cetvrti-sprat).
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$keso_map   = keso_floor_map();
$keso_slug  = get_post_field( 'post_name', get_queried_object_id() );
$keso_floor = $keso_map[ $keso_slug ] ?? 'prvi';
$keso_apts  = keso_get_apartments( $keso_floor );
$keso_count = array( 'slobodan' => 0, 'rezervisan' => 0, 'prodat' => 0 );
$keso_luma  = get_page_by_path( 'luma-residence' );
foreach ( $keso_apts as $keso_a ) {
	$keso_st = get_post_meta( $keso_a->ID, 'status', true ) ?: 'slobodan';
	if ( isset( $keso_count[ $keso_st ] ) ) {
		++$keso_count[ $keso_st ];
	}
}
$keso_total = count( $keso_apts );

keso_page_hero(
	get_the_title(),
	/* translators: %s: naziv sprata */
	sprintf( __( 'Pregled stanova i njihovog statusa. Luma Residence, %s.', 'keso-invest' ), mb_strtolower( get_the_title() ) ),
	array( array( 'Luma Residence', $keso_luma ? get_permalink( $keso_luma ) : home_url( '/luma-residence/' ) ), array( get_the_title(), '' ) )
);
?>

<section class="section section--tight floor" data-floor>
	<div class="wrap">
		<nav class="floor-nav" aria-label="<?php esc_attr_e( 'Spratovi', 'keso-invest' ); ?>">
			<ul>
				<?php
				foreach ( $keso_map as $keso_s => $keso_t ) {
					$keso_p = get_page_by_path( $keso_s );
					if ( $keso_p ) {
						printf( '<li><a href="%s"%s>%s</a></li>', esc_url( get_permalink( $keso_p ) ), $keso_s === $keso_slug ? ' aria-current="page"' : '', esc_html( get_the_title( $keso_p ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
				?>
			</ul>
		</nav>

		<?php if ( $keso_total ) : ?>
			<div class="summary" aria-label="<?php esc_attr_e( 'Pregled statusa', 'keso-invest' ); ?>">
				<div class="summary__bar" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: 1-3 brojevi */ __( 'Slobodnih %1$d, rezervisanih %2$d, prodatih %3$d', 'keso-invest' ), $keso_count['slobodan'], $keso_count['rezervisan'], $keso_count['prodat'] ) ); ?>">
					<?php foreach ( $keso_count as $keso_k => $keso_n ) : ?>
						<?php if ( $keso_n ) : ?><span class="seg seg--<?php echo esc_attr( $keso_k ); ?>" style="flex-grow:<?php echo (int) $keso_n; ?>"></span><?php endif; ?>
					<?php endforeach; ?>
				</div>
				<ul class="summary__legend">
					<?php foreach ( keso_statuses() as $keso_k => $keso_l ) : ?>
						<li><span class="dot dot--<?php echo esc_attr( $keso_k ); ?>" aria-hidden="true"></span><?php echo esc_html( $keso_l ); ?>: <b><?php echo (int) $keso_count[ $keso_k ]; ?></b></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<form class="filters" data-filters aria-label="<?php esc_attr_e( 'Filteri', 'keso-invest' ); ?>">
				<fieldset>
					<legend><?php esc_html_e( 'Status', 'keso-invest' ); ?></legend>
					<label class="chip"><input type="radio" name="st" value="" checked><span><?php esc_html_e( 'Svi', 'keso-invest' ); ?></span></label>
					<?php foreach ( keso_statuses() as $keso_k => $keso_l ) : ?>
						<label class="chip"><input type="radio" name="st" value="<?php echo esc_attr( $keso_k ); ?>"><span><?php echo esc_html( $keso_l ); ?></span></label>
					<?php endforeach; ?>
				</fieldset>
				<div class="filters__area">
					<label for="area-min"><?php esc_html_e( 'Površina od (m²)', 'keso-invest' ); ?></label>
					<input id="area-min" name="min" type="number" inputmode="decimal" min="0" step="1" placeholder="0">
				</div>
				<p class="filters__count" role="status" aria-live="polite" data-filters-count></p>
			</form>

			<table class="apts">
				<caption class="sr-only"><?php echo esc_html( get_the_title() ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Oznaka', 'keso-invest' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Lamela', 'keso-invest' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Sobe', 'keso-invest' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Površina', 'keso-invest' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Orijentacija', 'keso-invest' ); ?></th>
						<?php if ( keso_opt( 'show_prices' ) ) : ?><th scope="col"><?php esc_html_e( 'Cena', 'keso-invest' ); ?></th><?php endif; ?>
						<th scope="col"><?php esc_html_e( 'Status', 'keso-invest' ); ?></th>
						<th scope="col"><span class="sr-only"><?php esc_html_e( 'Šema', 'keso-invest' ); ?></span></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $keso_apts as $keso_a ) :
						$keso_id   = $keso_a->ID;
						$keso_st   = get_post_meta( $keso_id, 'status', true ) ?: 'slobodan';
						$keso_area = get_post_meta( $keso_id, 'povrsina', true );
						$keso_type = get_post_meta( $keso_id, 'tip', true );
						$keso_sema = get_post_meta( $keso_id, 'sema', true );
						$keso_price = get_post_meta( $keso_id, 'cena', true );
						?>
						<tr class="apts__row" data-status="<?php echo esc_attr( $keso_st ); ?>" data-area="<?php echo esc_attr( $keso_area ); ?>">
							<th scope="row" data-label="<?php esc_attr_e( 'Oznaka', 'keso-invest' ); ?>"><?php echo esc_html( $keso_a->post_title ); ?><?php echo 'lokal' === $keso_type ? ' <span class="apts__type">' . esc_html__( 'lokal', 'keso-invest' ) . '</span>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td data-label="<?php esc_attr_e( 'Lamela', 'keso-invest' ); ?>"><?php echo esc_html( str_replace( 'Lamela ', '', keso_term_name( $keso_id, 'lamela' ) ) ?: '–' ); ?></td>
							<td data-label="<?php esc_attr_e( 'Sobe', 'keso-invest' ); ?>"><?php echo esc_html( str_replace( '.', ',', (string) get_post_meta( $keso_id, 'sobe', true ) ) ?: '–' ); ?></td>
							<td data-label="<?php esc_attr_e( 'Površina', 'keso-invest' ); ?>"><?php echo '' !== $keso_area ? esc_html( keso_format_area( $keso_area ) ) : '–'; ?></td>
							<td data-label="<?php esc_attr_e( 'Orijentacija', 'keso-invest' ); ?>"><?php echo esc_html( get_post_meta( $keso_id, 'orijentacija', true ) ?: '–' ); ?></td>
							<?php if ( keso_opt( 'show_prices' ) ) : ?>
								<td data-label="<?php esc_attr_e( 'Cena', 'keso-invest' ); ?>"><?php echo $keso_price ? esc_html( $keso_price ) : '–'; ?></td>
							<?php endif; ?>
							<td data-label="<?php esc_attr_e( 'Status', 'keso-invest' ); ?>"><span class="status status--<?php echo esc_attr( $keso_st ); ?>"><span class="dot dot--<?php echo esc_attr( $keso_st ); ?>" aria-hidden="true"></span><?php echo esc_html( keso_status_label( $keso_st ) ); ?></span></td>
							<td class="apts__act"><?php if ( $keso_sema ) : ?><a class="link-arrow" href="<?php echo esc_url( $keso_sema ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Šema', 'keso-invest' ); ?><span class="sr-only"> <?php echo esc_html( $keso_a->post_title ); ?></span></a><?php endif; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="floor__empty" data-filters-empty hidden><?php esc_html_e( 'Nijedan stan ne odgovara izabranim filterima.', 'keso-invest' ); ?></p>
		<?php else : ?>
			<div class="empty">
				<h2 class="empty__title"><?php esc_html_e( 'Pregled stanova za ovaj sprat uskoro stiže', 'keso-invest' ); ?></h2>
				<p><?php esc_html_e( 'Za informacije o raspoloživosti pozovite nas ili pošaljite upit.', 'keso-invest' ); ?></p>
				<p><a class="btn btn--primary" href="<?php echo esc_attr( keso_tel_href( keso_opt( 'phone_1' ) ) ); ?>"><?php echo esc_html( keso_opt( 'phone_1' ) ); ?></a></p>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<p class="empty__admin"><?php esc_html_e( 'Samo administrator vidi: dodajte stanove u administraciji (Stanovi → Dodaj stan) i dodelite im sprat.', 'keso-invest' ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_template_part( 'template-parts/cta' );
get_footer();
