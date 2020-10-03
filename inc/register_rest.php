<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register REST routr
 */
add_action( 'rest_api_init', function() {

	register_rest_route( 'pochipp', '/data', [

		'methods'             => 'POST',
		'callback'            => function( $req ) {

			return ['req' => $req['pid'] ];

			// if ( ! isset( $request['pid'] ) ) return '';
			// $pid = $request['pid'];
			// return get_post_meta( $pid, 'pochipp_data', true ) ?: '';
		},
		'permission_callback' => function () {
			return true;
		},

	] );
} );
