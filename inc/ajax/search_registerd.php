<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 登録済み商品の検索
 */
add_action( 'wp_ajax_pochipp_search_registerd', '\POCHIPP\search_registerd_items' );
function search_registerd_items() {

	if ( ! \POCHIPP\check_ajax_nonce() ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'nonce error',
				'message' => '不正なアクセスです。',
			],
		] ) );
	};

	$keywords = \POCHIPP::get_sanitized_data( $_GET, 'keywords', 'text', '' );
	$term_id  = \POCHIPP::get_sanitized_data( $_GET, 'term_id', 'int', 0 );
	$count    = \POCHIPP::get_sanitized_data( $_GET, 'count', 'int', 10 );
	$sort     = \POCHIPP::get_sanitized_data( $_GET, 'sort', 'text', 'new' );

	// 登録済み商品
	$registerd_items = \POCHIPP::get_registerd_items( [
		'keywords' => $keywords,
		'term_id'  => $term_id,
		'count'    => $count,
		'sort'     => $sort,
	] );

	// 検索結果
	wp_die( json_encode( [
		'registerd_items' => $registerd_items ?: [],
		'searched_items'  => [],
	] ) );
}
