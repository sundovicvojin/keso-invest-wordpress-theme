<?php
/**
 * Keso Invest tema: učitava module iz inc/.
 *
 * @package Keso_Invest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KESO_VERSION', '1.0.0' );

foreach ( array( 'setup', 'template-tags', 'cpt', 'meta', 'customizer', 'enqueue', 'contact-form', 'seo', 'redirects', 'demo' ) as $keso_module ) {
	require_once get_template_directory() . '/inc/' . $keso_module . '.php';
}
unset( $keso_module );
