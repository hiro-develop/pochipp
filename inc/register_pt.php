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
		'pochipp_data',
		[
			'show_in_rest'   => true,
			'single'         => true,
			'type'           => 'string',
			'object_subtype' => 'pochipps',
		]
	);
}

/**
 * Register post type
 */
add_action( 'init', 'POCHIPP\register_pt_pochipp' );
function register_pt_pochipp() {
	register_post_type(
		\POCHIPP::POST_TYPE_SLUG,
		[
			'labels'                => [
				'name'          => '商品リンクP',
				'singular_name' => \POCHIPP::POST_TYPE_SLUG,
			],
			'public'                => false,
			'publicly_queryable'    => false,
			'capability_type'       => 'page', // 固定ページと同じ権限レベル
			'has_archive'           => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'exclude_from_search'   => true,
			'menu_position'         => 21,
			'menu_icon'             => 'dashicons-media-default',
			'show_in_rest'          => true,  // ブロックエディターに対応させる
			'supports'              => [ 'title', 'editor', 'custom-fields' ], // 'editor'
			// 'template'              => [
			// 	[ 'pochipp/setting' ],
			// ],
			// 'template_lock'         => 'insert',
		]
	);
}
