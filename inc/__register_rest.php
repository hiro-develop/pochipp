<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register REST
 */
add_action( 'rest_api_init', function() {
	register_rest_route( 'pochipp', '/data', [
		'methods'             => 'POST',
		'callback'            => function( $req ) {

			if ( ! isset( $req['pid'] ) ) return '';

			$pid = $req['pid'];

			return get_post_meta( $pid, 'pochipp_data', true ) ?: '';
		},
		'permission_callback' => function () {
			return true;
		},
	] );
} );
