<?php
/**
 * Kontakt forma bez plugina.
 *
 * Slanje: fetch() na admin-ajax.php (keso_contact), a bez JS-a običan POST na admin-post.php.
 * Zaštita: nonce (svež preko keso_token), honeypot, vremenski potpis, ograničenje po IP adresi, opciono Turnstile.
 * Konstante (wp-config.php): KESO_CONTACT_TO, KESO_MAIL_FROM, KESO_SMTP_HOST/PORT/USER/PASS/SECURE.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const KESO_FORM_MIN_SECONDS = 3;
const KESO_FORM_MAX_SECONDS = 172800; // 48 h (stranica može biti keširana).
const KESO_FORM_RATE_LIMIT  = 3;      // poruka po IP adresi na sat.

/** Potpisan vremenski žig za polje "kf_ts". */
function keso_form_stamp() {
	$t = time();
	return $t . '.' . substr( wp_hash( 'keso-form|' . $t ), 0, 16 );
}

function keso_form_stamp_age( $stamp ) {
	if ( ! is_string( $stamp ) || ! preg_match( '/^(\d{9,11})\.([a-f0-9]{16})$/', $stamp, $m ) ) {
		return false;
	}
	if ( ! hash_equals( substr( wp_hash( 'keso-form|' . $m[1] ), 0, 16 ), $m[2] ) ) {
		return false;
	}
	return time() - (int) $m[1];
}

function keso_client_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
}

/** Primalac: konstanta iz wp-config.php, pa Customizer, pa podrazumevana adresa. */
function keso_contact_to() {
	if ( defined( 'KESO_CONTACT_TO' ) && is_email( KESO_CONTACT_TO ) ) {
		return KESO_CONTACT_TO;
	}
	return 'kesoinvestdoo@gmail.com';
}

function keso_mail_from() {
	return defined( 'KESO_MAIL_FROM' ) && is_email( KESO_MAIL_FROM ) ? KESO_MAIL_FROM : 'forma@kesoinvest.com';
}

/** Prava adresa stranice sa koje je poslato (samo isti domen). */
function keso_form_source_url( $raw ) {
	$url = esc_url_raw( (string) $raw );
	$host = wp_parse_url( $url, PHP_URL_HOST );
	$own  = wp_parse_url( home_url(), PHP_URL_HOST );
	return $url && $host && $own && strtolower( $host ) === strtolower( $own ) ? $url : home_url( '/contact/' );
}

/**
 * Obrada poruke. Vraća array( 'ok' => bool, 'errors' => array, 'message' => string, 'values' => array ).
 *
 * @param array $in Ulaz sa uklonjenim slashes.
 */
function keso_process_contact( $in ) {
	$raw_name  = is_string( $in['kf_name'] ?? '' ) ? $in['kf_name'] : '';
	$raw_email = is_string( $in['kf_email'] ?? '' ) ? $in['kf_email'] : '';
	$values    = array(
		'name'    => sanitize_text_field( $raw_name ),
		'email'   => sanitize_email( $raw_email ),
		'phone'   => sanitize_text_field( is_string( $in['kf_phone'] ?? '' ) ? $in['kf_phone'] : '' ),
		'message' => sanitize_textarea_field( is_string( $in['kf_message'] ?? '' ) ? $in['kf_message'] : '' ),
		'consent' => ! empty( $in['kf_consent'] ),
	);
	$res       = array( 'ok' => false, 'errors' => array(), 'message' => '', 'values' => $values );

	// Honeypot: tihi "uspeh" (bot ne dobija informaciju).
	if ( ! empty( $in['kf_website'] ) ) {
		$res['ok']      = true;
		$res['message'] = __( 'Hvala, vaša poruka je poslata. Javićemo se uskoro.', 'keso-invest' );
		$res['values']  = array();
		return $res;
	}
	if ( ! isset( $in['kf_nonce'] ) || ! is_string( $in['kf_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $in['kf_nonce'] ), 'keso_contact' ) ) {
		$res['message'] = __( 'Sesija je istekla. Osvežite stranicu i pokušajte ponovo.', 'keso-invest' );
		return $res;
	}
	$age = keso_form_stamp_age( $in['kf_ts'] ?? '' );
	if ( false === $age || $age > KESO_FORM_MAX_SECONDS ) {
		$res['message'] = __( 'Obrazac je istekao. Osvežite stranicu i pokušajte ponovo.', 'keso-invest' );
		return $res;
	}
	if ( $age < KESO_FORM_MIN_SECONDS ) {
		$res['message'] = __( 'Poruka je poslata prebrzo. Sačekajte trenutak i pokušajte ponovo.', 'keso-invest' );
		return $res;
	}

	// Validacija.
	if ( mb_strlen( $values['name'] ) < 2 || preg_match( '/[\r\n]/', $raw_name ) ) {
		$res['errors']['kf_name'] = __( 'Unesite ime i prezime (najmanje 2 znaka).', 'keso-invest' );
	}
	if ( preg_match( '/[\r\n]/', $raw_email ) || ! is_email( $values['email'] ) ) {
		$res['errors']['kf_email'] = __( 'Unesite ispravnu email adresu.', 'keso-invest' );
	}
	if ( '' !== $values['phone'] && ! preg_match( '/^[0-9 +()\-]{5,25}$/', $values['phone'] ) ) {
		$res['errors']['kf_phone'] = __( 'Telefon sme da sadrži samo cifre, razmake i znakove + - ( ).', 'keso-invest' );
	}
	$len = mb_strlen( $values['message'] );
	if ( $len < 10 ) {
		$res['errors']['kf_message'] = __( 'Poruka mora imati najmanje 10 znakova.', 'keso-invest' );
	} elseif ( $len > 3000 ) {
		$res['errors']['kf_message'] = __( 'Poruka je predugačka (najviše 3000 znakova).', 'keso-invest' );
	}
	if ( ! $values['consent'] ) {
		$res['errors']['kf_consent'] = __( 'Potrebna je saglasnost za obradu podataka da bismo odgovorili na upit.', 'keso-invest' );
	}
	if ( $res['errors'] ) {
		$res['message'] = __( 'Proverite označena polja.', 'keso-invest' );
		return $res;
	}

	// Turnstile (opciono).
	$secret = keso_opt( 'turnstile_secret' );
	if ( $secret && keso_opt( 'turnstile_site' ) ) {
		$token = sanitize_text_field( $in['cf-turnstile-response'] ?? '' );
		$resp  = $token ? wp_remote_post(
			'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			array( 'timeout' => 8, 'body' => array( 'secret' => $secret, 'response' => $token, 'remoteip' => keso_client_ip() ) )
		) : null;
		$ok    = $resp && ! is_wp_error( $resp ) && ! empty( json_decode( wp_remote_retrieve_body( $resp ), true )['success'] );
		if ( ! $ok ) {
			$res['message'] = __( 'Provera zaštite od spama nije uspela. Pokušajte ponovo.', 'keso-invest' );
			return $res;
		}
	}

	// Ograničenje učestalosti po IP adresi.
	$rl_key = 'keso_rl_' . md5( keso_client_ip() );
	$count  = (int) get_transient( $rl_key );
	if ( $count >= KESO_FORM_RATE_LIMIT ) {
		$res['message'] = __( 'Poslali ste previše poruka. Pokušajte ponovo za sat vremena ili nas pozovite.', 'keso-invest' );
		return $res;
	}
	set_transient( $rl_key, $count + 1, HOUR_IN_SECONDS );

	if ( ! keso_send_contact_mail( $values, keso_form_source_url( $in['kf_page'] ?? '' ) ) ) {
		$res['message'] = __( 'Poruka nije mogla da se pošalje. Pozovite nas ili pišite direktno na email.', 'keso-invest' );
		return $res;
	}
	$res['ok']      = true;
	$res['message'] = __( 'Hvala, vaša poruka je poslata. Javićemo se uskoro.', 'keso-invest' );
	$res['values']  = array();
	return $res;
}

/** Plain-text alternativa za trenutni mejl (postavlja se u phpmailer_init). */
$GLOBALS['keso_mail_alt'] = '';

function keso_send_contact_mail( $v, $page ) {
	$name   = trim( preg_replace( '/\s+/', ' ', $v['name'] ) );
	$time   = wp_date( 'd.m.Y. H:i:s' );
	$ip     = keso_client_ip();
	$phone  = '' !== $v['phone'] ? $v['phone'] : '-';
	$labels = array(
		'name'    => __( 'Ime i prezime', 'keso-invest' ),
		'email'   => __( 'Email', 'keso-invest' ),
		'phone'   => __( 'Telefon', 'keso-invest' ),
		'message' => __( 'Poruka', 'keso-invest' ),
		'time'    => __( 'Vreme', 'keso-invest' ),
		'ip'      => __( 'IP adresa', 'keso-invest' ),
		'page'    => __( 'Stranica', 'keso-invest' ),
	);
	$data   = array(
		'name'    => $name,
		'email'   => $v['email'],
		'phone'   => $phone,
		'message' => $v['message'],
		'time'    => $time,
		'ip'      => $ip,
		'page'    => $page,
	);

	$plain = '';
	$html  = '<table role="presentation" cellpadding="6" cellspacing="0" style="font-family:Arial,sans-serif;font-size:15px;color:#111;border-collapse:collapse">';
	foreach ( $data as $k => $val ) {
		$plain .= $labels[ $k ] . ': ' . ( 'message' === $k ? "\n" : '' ) . $val . "\n";
		$html  .= '<tr><td valign="top" style="font-weight:bold;white-space:nowrap">' . esc_html( $labels[ $k ] ) . '</td><td>' . nl2br( esc_html( $val ) ) . '</td></tr>';
	}
	$html .= '</table>';

	$subject = sprintf( /* translators: %s: ime pošiljaoca */ __( 'Novi upit sa sajta: %s', 'keso-invest' ), $name );
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: KESO INVEST <' . keso_mail_from() . '>',
		'Reply-To: ' . $v['email'],
	);

	$GLOBALS['keso_mail_alt'] = $plain;
	$sent                     = wp_mail( keso_contact_to(), $subject, $html, $headers );
	$GLOBALS['keso_mail_alt'] = '';
	return (bool) $sent;
}

/** SMTP samo ako su konstante definisane u wp-config.php; inače podrazumevani wp_mail. */
function keso_phpmailer_init( $phpmailer ) {
	if ( ! empty( $GLOBALS['keso_mail_alt'] ) ) {
		$phpmailer->AltBody = $GLOBALS['keso_mail_alt'];
	}
	if ( ! defined( 'KESO_SMTP_HOST' ) || ! defined( 'KESO_SMTP_USER' ) || ! defined( 'KESO_SMTP_PASS' ) || ! KESO_SMTP_HOST || ! KESO_SMTP_USER || ! KESO_SMTP_PASS ) {
		return;
	}
	$secure = defined( 'KESO_SMTP_SECURE' ) ? strtolower( (string) KESO_SMTP_SECURE ) : 'ssl';
	$phpmailer->isSMTP();
	$phpmailer->Host       = KESO_SMTP_HOST;
	$phpmailer->Port       = defined( 'KESO_SMTP_PORT' ) ? (int) KESO_SMTP_PORT : 465;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = KESO_SMTP_USER;
	$phpmailer->Password   = KESO_SMTP_PASS;
	$phpmailer->SMTPSecure = in_array( $secure, array( 'ssl', 'tls' ), true ) ? $secure : '';
	if ( '' === $phpmailer->SMTPSecure ) {
		$phpmailer->SMTPAutoTLS = false;
	}
}
add_action( 'phpmailer_init', 'keso_phpmailer_init' );

/** Greške slanja idu u error_log (bez lozinke), da se vidi zašto mejl nije stigao. */
function keso_log_mail_failure( $error ) {
	error_log( 'KESO kontakt forma, wp_mail greška: ' . $error->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}
add_action( 'wp_mail_failed', 'keso_log_mail_failure' );

/** Svež nonce + žig za JS (stranica može biti keširana, ovaj odgovor nikad). */
function keso_ajax_token() {
	nocache_headers();
	wp_send_json_success( array( 'nonce' => wp_create_nonce( 'keso_contact' ), 'ts' => keso_form_stamp() ) );
}
add_action( 'wp_ajax_nopriv_keso_token', 'keso_ajax_token' );
add_action( 'wp_ajax_keso_token', 'keso_ajax_token' );

/** AJAX slanje. */
function keso_ajax_contact() {
	nocache_headers();
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce proverava keso_process_contact().
	$res = keso_process_contact( wp_unslash( $_POST ) );
	unset( $res['values'] );
	wp_send_json( $res, $res['ok'] ? 200 : 422 );
}
add_action( 'wp_ajax_nopriv_keso_contact', 'keso_ajax_contact' );
add_action( 'wp_ajax_keso_contact', 'keso_ajax_contact' );

/** Rezerva bez JS-a: običan POST na admin-post.php, pa preusmerenje nazad (PRG). */
function keso_admin_post_contact() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce proverava keso_process_contact().
	$in   = wp_unslash( $_POST );
	$res  = keso_process_contact( $in );
	$back = keso_form_source_url( $in['kf_page'] ?? '' );
	if ( $res['ok'] ) {
		wp_safe_redirect( add_query_arg( 'poslato', '1', $back ) . '#poruka' );
		exit;
	}
	$id = wp_generate_password( 16, false );
	set_transient( 'keso_fr_' . $id, $res, 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'kf', $id, $back ) . '#poruka' );
	exit;
}
add_action( 'admin_post_nopriv_keso_contact', 'keso_admin_post_contact' );
add_action( 'admin_post_keso_contact', 'keso_admin_post_contact' );

/** Rezultat fallback slanja za prikaz u šablonu (jednokratno). */
function keso_form_flash() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$id = isset( $_GET['kf'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', wp_unslash( $_GET['kf'] ) ) : '';
	if ( '' === $id ) {
		return null;
	}
	$res = get_transient( 'keso_fr_' . $id );
	delete_transient( 'keso_fr_' . $id );
	return is_array( $res ) ? $res : null;
}
