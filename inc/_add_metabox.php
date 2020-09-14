<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add metabox
 */
// add_action( 'admin_menu', array( $this, 'add_meta_boxes' ) );
add_action( 'add_meta_boxes', '\POCHIPP\add_meta_boxes' );
function add_meta_boxes() {
	// var_dump('aaa');
	add_meta_box(
		'pochipp_metabox',
		'商品リンク設定',
		'POCHIPP\insert_metaboxs',
		[ \POCHIPP::POST_TYPE_SLUG ],
		'normal',
		null
	);
}

function insert_metaboxs() {
	include POCHIPP_PATH . 'inc/metabox/custom_fields.php';
}


// yyi: ショートコード表示するためだけのやつ
// add_action('add_meta_boxes', 'POCHIPP\add_meta_box_shortcode' );
// function add_meta_box_shortcode() {
// 	global $pagenow;
// 	if ( $pagenow !== 'post-new.php') {
// 		add_meta_box(
// 			$this->add_prefix('shortcode_side_meta_box'),
// 			'ショートコード',
// 			'POCHIPP\insert_side_meta_fields',
// 			'pochipp',
// 			'side'
// 		);
// 	}
// }
// function insert_side_meta_fields ( $post ) {
// 	echo '<textarea readonly="readonly" class="yyi-rinker-list-shortcode">[itemlink post_id="' . esc_html( $post->ID ) . '"]</textarea>';
// 	echo '<p class="description">(クリックでコピー)</p>';
// }
