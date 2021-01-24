<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Amazon APIから検索
 */
add_action( 'wp_ajax_pochipp_search_registerd', '\POCHIPP\search_registerd_items' );


/**
 * AmazonAPIから商品データを取得する
 */
function search_registerd_items() {

	$keywords = \POCHIPP::array_get( $_GET, 'keywords', '' );
	$term_id  = \POCHIPP::array_get( $_GET, 'term_id', 0 );
	$count    = \POCHIPP::array_get( $_GET, 'count', 10 );
	$sort     = \POCHIPP::array_get( $_GET, 'sort', 'new' );

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
