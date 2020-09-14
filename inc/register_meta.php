<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * カスタムフィールドの登録
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
