<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'save_post', '\POCHIPP\hook_save_post' );

/**
 * 保存処理
 */
function hook_save_post( $post_id ) {

	// $_POST || nonce がなければ return
	if ( empty( $_POST ) ) {
		return;
	}

	// nonceキーチェック
	// if ( ! wp_verify_nonce( $_POST[ SWELL_NONCE_NAME . '_meta_code' ], SWELL_NONCE_ACTION . '_meta_code' ) ) {
	// 	return;
	// }

	// 自動保存時には保存しないように
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// 現在のユーザーに投稿の編集権限があるかのチェック （投稿 : 'edit_post' / 固定ページ & LP : 'edit_page')
	$check_can = ( isset( $_POST['post_type'] ) && 'post' === $_POST['post_type'] ) ? 'edit_post' : 'edit_page';
	if ( ! current_user_can( $check_can, $post_id ) ) {
		return;
	}

	$meta_keys = [
		'pochipp_data',
	];

	foreach ( $meta_keys as $key ) {
		// 保存したい情報が渡ってきていれば更新作業に入る
		if ( isset( $_POST[ $key ] ) ) {

			$meta_val = $_POST[ $key ];
			$meta_val = sanitize_text_field( $meta_val );

			// DBアップデート
			update_post_meta( $post_id, $key, $meta_val );

		}
	}

}


return;

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
