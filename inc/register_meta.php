<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register meta
 */
add_action( 'init', '\POCHIPP\register_metas' );
function register_metas() {
	register_meta(
		'post',
		\POCHIPP::META_SLUG,
		[
			'show_in_rest'   => true,
			'single'         => true,
			'type'           => 'string',
			'object_subtype' => \POCHIPP::POST_TYPE_SLUG,
		]
	);
}


/**
 * Save
 */
add_action( 'save_post', function ( $the_id ) {

	// $_POST なければ return
	if ( empty( $_POST ) ) return;

	$meta_key = \POCHIPP::META_SLUG;

	// メタデータなければ return
	if ( ! isset( $_POST[ $meta_key ] ) ) return;

	$meta_val = $_POST[ $meta_key ];

	update_post_meta( $the_id, $meta_key, $meta_val );

});
