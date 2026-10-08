<?php
/**
 * Meta polja i jednostavni meta-boxovi (bez ACF-a).
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function keso_statuses() {
	return array(
		'slobodan'   => __( 'Slobodan', 'keso-invest' ),
		'rezervisan' => __( 'Rezervisan', 'keso-invest' ),
		'prodat'     => __( 'Prodat', 'keso-invest' ),
	);
}

/** Definicija polja po tipu sadržaja. */
function keso_meta_fields( $type ) {
	if ( 'stan' === $type ) {
		return array(
			'tip'         => array(
				'label'   => __( 'Tip', 'keso-invest' ),
				'type'    => 'select',
				'options' => array(
					'stan'  => __( 'Stan', 'keso-invest' ),
					'lokal' => __( 'Poslovni prostor (lokal)', 'keso-invest' ),
				),
			),
			'status'      => array(
				'label'   => __( 'Status', 'keso-invest' ),
				'type'    => 'select',
				'options' => keso_statuses(),
			),
			'povrsina'    => array(
				'label' => __( 'Površina (m²)', 'keso-invest' ),
				'type'  => 'number',
				'desc'  => __( 'Na primer 54.5', 'keso-invest' ),
			),
			'sobe'        => array(
				'label' => __( 'Broj soba', 'keso-invest' ),
				'type'  => 'text',
				'desc'  => __( 'Na primer 2.5 (ostavite prazno za lokal)', 'keso-invest' ),
			),
			'orijentacija' => array(
				'label' => __( 'Orijentacija', 'keso-invest' ),
				'type'  => 'text',
				'desc'  => __( 'Na primer jug-zapad', 'keso-invest' ),
			),
			'cena'        => array(
				'label' => __( 'Cena (EUR, bez PDV-a ili kako želite da stoji)', 'keso-invest' ),
				'type'  => 'text',
				'desc'  => __( 'Prikazuje se samo ako je u Customizer-u uključeno „Prikaži cene“.', 'keso-invest' ),
			),
			'sema'        => array(
				'label' => __( 'Šema / PDF stana', 'keso-invest' ),
				'type'  => 'file',
			),
			'redosled'    => array(
				'label' => __( 'Redni broj (za sortiranje)', 'keso-invest' ),
				'type'  => 'number',
				'step'  => '1',
			),
		);
	}
	if ( 'referenca' === $type ) {
		return array(
			'stanova' => array(
				'label' => __( 'Broj stanova', 'keso-invest' ),
				'type'  => 'number',
				'step'  => '1',
			),
			'lokala'  => array(
				'label' => __( 'Broj lokala', 'keso-invest' ),
				'type'  => 'number',
				'step'  => '1',
			),
			'godina'  => array(
				'label' => __( 'Godina završetka (opciono)', 'keso-invest' ),
				'type'  => 'text',
			),
		);
	}
	if ( 'page' === $type ) {
		return array(
			'meta_opis' => array(
				'label' => __( 'Meta opis za pretraživače (do 160 znakova)', 'keso-invest' ),
				'type'  => 'textarea',
			),
		);
	}
	return array();
}

function keso_sanitize_field( $value, $field ) {
	$value = is_string( $value ) ? wp_unslash( $value ) : '';
	switch ( $field['type'] ) {
		case 'number':
			$value = str_replace( ',', '.', trim( $value ) );
			if ( '' === $value || ! is_numeric( $value ) ) {
				return '';
			}
			return isset( $field['step'] ) && '1' === $field['step'] ? (string) absint( $value ) : (string) abs( (float) $value );
		case 'select':
			return isset( $field['options'][ $value ] ) ? $value : '';
		case 'file':
			return esc_url_raw( $value );
		case 'textarea':
			return sanitize_textarea_field( $value );
		default:
			return sanitize_text_field( $value );
	}
}

function keso_register_meta() {
	foreach ( array( 'stan', 'referenca', 'page' ) as $type ) {
		foreach ( keso_meta_fields( $type ) as $key => $field ) {
			register_post_meta(
				$type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => false,
					'sanitize_callback' => static function ( $v ) use ( $field ) {
						return keso_sanitize_field( wp_slash( (string) $v ), $field );
					},
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'keso_register_meta' );

function keso_add_meta_boxes() {
	add_meta_box( 'keso_stan', __( 'Podaci o stanu', 'keso-invest' ), 'keso_render_meta_box', 'stan', 'normal', 'high' );
	add_meta_box( 'keso_referenca', __( 'Podaci o referenci', 'keso-invest' ), 'keso_render_meta_box', 'referenca', 'normal', 'high' );
	add_meta_box( 'keso_page', __( 'SEO opis', 'keso-invest' ), 'keso_render_meta_box', 'page', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'keso_add_meta_boxes' );

function keso_render_meta_box( $post ) {
	wp_nonce_field( 'keso_meta_save', 'keso_meta_nonce' );
	$fields = keso_meta_fields( $post->post_type );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields as $key => $f ) {
		$val = get_post_meta( $post->ID, $key, true );
		if ( '' === $val && 'status' === $key ) {
			$val = 'slobodan';
		}
		$id = 'keso_' . $key;
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $f['label'] ) . '</label></th><td>';
		switch ( $f['type'] ) {
			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '">';
				foreach ( $f['options'] as $ov => $ol ) {
					printf( '<option value="%s"%s>%s</option>', esc_attr( $ov ), selected( $val, $ov, false ), esc_html( $ol ) );
				}
				echo '</select>';
				break;
			case 'textarea':
				echo '<textarea class="widefat" rows="3" maxlength="200" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '">' . esc_textarea( $val ) . '</textarea>';
				break;
			case 'file':
				echo '<input type="url" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $val ) . '" /> ';
				echo '<button type="button" class="button keso-pick-file" data-target="' . esc_attr( $id ) . '">' . esc_html__( 'Izaberi fajl', 'keso-invest' ) . '</button>';
				break;
			case 'number':
				echo '<input type="number" min="0" step="' . esc_attr( $f['step'] ?? 'any' ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $val ) . '" />';
				break;
			default:
				echo '<input type="text" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $val ) . '" />';
		}
		if ( ! empty( $f['desc'] ) ) {
			echo '<p class="description">' . esc_html( $f['desc'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	if ( 'stan' === $post->post_type ) {
		echo '<p class="description">' . esc_html__( 'Naslov je oznaka stana (npr. G-12). Sprat i lamelu birate u poljima sa desne strane.', 'keso-invest' ) . '</p>';
	}
}

function keso_save_meta( $post_id, $post ) {
	if ( ! isset( $_POST['keso_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['keso_meta_nonce'] ) ), 'keso_meta_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( keso_meta_fields( $post->post_type ) as $key => $field ) {
		$raw = isset( $_POST[ 'keso_' . $key ] ) ? wp_unslash( $_POST[ 'keso_' . $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitizuje keso_sanitize_field().
		$val = keso_sanitize_field( wp_slash( $raw ), $field );
		if ( '' === $val ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $val );
		}
	}
}
add_action( 'save_post', 'keso_save_meta', 10, 2 );

/** Biranje fajla (šema stana) preko medija biblioteke. */
function keso_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'stan' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script(
		'media-editor',
		'document.addEventListener("click",function(e){var b=e.target.closest(".keso-pick-file");if(!b)return;e.preventDefault();var f=wp.media({multiple:false});f.on("select",function(){document.getElementById(b.dataset.target).value=f.state().get("selection").first().toJSON().url;});f.open();});'
	);
}
add_action( 'admin_enqueue_scripts', 'keso_admin_assets' );
