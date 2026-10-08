<?php
/**
 * Kontakt forma (radi bez JS-a i sa JS-om).
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$keso_tag  = isset( $args['tag'] ) && 'h2' === $args['tag'] ? 'h2' : 'h3';
$keso_res  = keso_form_flash();
$keso_val  = $keso_res['values'] ?? array( 'name' => '', 'email' => '', 'phone' => '', 'message' => '', 'consent' => false );
$keso_err  = $keso_res['errors'] ?? array();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$keso_sent = isset( $_GET['poslato'] ) && '1' === $_GET['poslato'];
$keso_priv = get_page_by_path( 'politika-privatnosti' );

/** Pomoćnik: atributi greške polja. */
$keso_f = static function ( $name ) use ( $keso_err ) {
	return isset( $keso_err[ $name ] ) ? ' aria-invalid="true" aria-describedby="' . esc_attr( $name ) . '-err"' : '';
};
$keso_e = static function ( $name ) use ( $keso_err ) {
	if ( isset( $keso_err[ $name ] ) ) {
		echo '<p class="field__err" id="' . esc_attr( $name ) . '-err" role="alert">' . esc_html( $keso_err[ $name ] ) . '</p>';
	} else {
		echo '<p class="field__err" id="' . esc_attr( $name ) . '-err" role="alert" hidden></p>';
	}
};
?>
<form class="form" id="poruka" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-form>
	<<?php echo esc_attr( $keso_tag ); ?> class="form__title"><?php esc_html_e( 'Pošaljite upit', 'keso-invest' ); ?></<?php echo esc_attr( $keso_tag ); ?>>

	<div class="form__status" role="status" aria-live="polite" data-form-status tabindex="-1">
		<?php if ( $keso_sent ) : ?>
			<p class="notice notice--ok"><?php esc_html_e( 'Hvala, vaša poruka je poslata. Javićemo se uskoro.', 'keso-invest' ); ?></p>
		<?php elseif ( $keso_res && ! empty( $keso_res['message'] ) ) : ?>
			<p class="notice notice--err"><?php echo esc_html( $keso_res['message'] ); ?></p>
		<?php endif; ?>
	</div>

	<div class="field">
		<label for="kf_name"><?php esc_html_e( 'Ime i prezime', 'keso-invest' ); ?></label>
		<input id="kf_name" name="kf_name" type="text" autocomplete="name" required value="<?php echo esc_attr( $keso_val['name'] ); ?>"<?php echo $keso_f( 'kf_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php $keso_e( 'kf_name' ); ?>
	</div>
	<div class="form__row">
		<div class="field">
			<label for="kf_email"><?php esc_html_e( 'Email', 'keso-invest' ); ?></label>
			<input id="kf_email" name="kf_email" type="email" autocomplete="email" required value="<?php echo esc_attr( $keso_val['email'] ); ?>"<?php echo $keso_f( 'kf_email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php $keso_e( 'kf_email' ); ?>
		</div>
		<div class="field">
			<label for="kf_phone"><?php esc_html_e( 'Telefon (opciono)', 'keso-invest' ); ?></label>
			<input id="kf_phone" name="kf_phone" type="tel" autocomplete="tel" value="<?php echo esc_attr( $keso_val['phone'] ); ?>"<?php echo $keso_f( 'kf_phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php $keso_e( 'kf_phone' ); ?>
		</div>
	</div>
	<div class="field">
		<label for="kf_message"><?php esc_html_e( 'Poruka', 'keso-invest' ); ?></label>
		<textarea id="kf_message" name="kf_message" rows="5" required<?php echo $keso_f( 'kf_message' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea( $keso_val['message'] ); ?></textarea>
		<?php $keso_e( 'kf_message' ); ?>
	</div>
	<div class="field field--check">
		<input id="kf_consent" name="kf_consent" type="checkbox" value="1" required<?php checked( ! empty( $keso_val['consent'] ) ); ?><?php echo $keso_f( 'kf_consent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<label for="kf_consent">
			<?php esc_html_e( 'Saglasan/saglasna sam da KESO INVEST doo obrađuje moje podatke radi odgovora na upit.', 'keso-invest' ); ?>
			<?php if ( $keso_priv && 'publish' === $keso_priv->post_status ) : ?>
				<a href="<?php echo esc_url( get_permalink( $keso_priv ) ); ?>"><?php esc_html_e( 'Politika privatnosti', 'keso-invest' ); ?></a>
			<?php endif; ?>
		</label>
		<?php $keso_e( 'kf_consent' ); ?>
	</div>

	<div class="form__hp" aria-hidden="true">
		<label for="kf_website"><?php esc_html_e( 'Ne popunjavajte ovo polje', 'keso-invest' ); ?></label>
		<input id="kf_website" name="kf_website" type="text" tabindex="-1" autocomplete="off">
	</div>
	<?php if ( keso_opt( 'turnstile_site' ) ) : ?>
		<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( keso_opt( 'turnstile_site' ) ); ?>"></div>
	<?php endif; ?>
	<input type="hidden" name="action" value="keso_contact">
	<input type="hidden" name="kf_page" value="<?php echo esc_url( get_permalink() ?: home_url( '/contact/' ) ); ?>">
	<input type="hidden" name="kf_ts" value="<?php echo esc_attr( keso_form_stamp() ); ?>">
	<input type="hidden" name="kf_nonce" value="<?php echo esc_attr( wp_create_nonce( 'keso_contact' ) ); ?>">

	<button class="btn btn--primary" type="submit" data-form-submit><?php esc_html_e( 'Pošalji poruku', 'keso-invest' ); ?><?php keso_icon( 'arrow' ); ?></button>
</form>
