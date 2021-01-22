<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'manage_posts_columns', '\POCHIPP\add_custom_post_columns' );
add_action( 'manage_posts_custom_column', '\POCHIPP\output_custom_post_columns', 10, 2 );

/**
 * 投稿一覧テーブルに アイキャッチ画像などの列を追加。
 */
function add_custom_post_columns( $columns ) {
	global $post_type;

	if ( \POCHIPP::POST_TYPE_SLUG === $post_type ) {

		$columns['searched_at'] = '検索元';
		$columns['used_at']     = '使用ページ';

	}

	return $columns;
}

/**
 * 表示内容
 */
function output_custom_post_columns( $column_name, $post_id ) {
	global $post_type;

	if ( \POCHIPP::POST_TYPE_SLUG !== $post_type ) return;

	if ( 'searched_at' === $column_name ) {
		$pchpp_metas = get_post_meta( $post_id, \POCHIPP::META_SLUG, true );
		$pchpp_metas = json_decode( $pchpp_metas, true ) ?: [];

		echo esc_html( $pchpp_metas['searched_at'] ?? '-' );

	} elseif ( 'used_at' === $column_name ) {
		echo '使用中のページをここに表示';
	}

}
