<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * AJAXのNonceチェック
 */
function check_ajax_nonce( $request_key = 'nonce', $nonce_key = '' ) {
	if ( ! isset( $_POST[ $request_key ] ) ) return false;

	$nonce     = $_POST[ $request_key ];
	$nonce_key = $nonce_key ?: \POCHIPP::NONCE_KEY;

	if ( wp_verify_nonce( $nonce, $nonce_key ) ) {
		return true;
	}

	return false;
}



require_once POCHIPP_PATH . 'inc/ajax/search_amazon.php';
require_once POCHIPP_PATH . 'inc/ajax/search_rakuten.php';
require_once POCHIPP_PATH . 'inc/ajax/search_registerd.php';

// add_action( 'wp_ajax_yyi_rinker_relink', '\POCHIPP\relink_from_api' );
// add_action( 'wp_ajax_yyi_rinker_add_item', '\POCHIPP\add_item' );

// add_action( 'wp_ajax_yyi_rinker_search_itemlist', '\POCHIPP\search_itemlist' );

// add_action( 'wp_ajax_yyi_rinker_delete_all_cache', '\POCHIPP\delete_all_cache' );

/**
 * Amazon APIから検索
 */
// \POCHIPP::ACTION_NAME['amazon'] とかでアクション名 とれるようにする
add_action( 'wp_ajax_pochipp_update_data', '\POCHIPP\update_data' );


/**
 * AmazonAPIから商品データを取得する
 */
function update_data() {

	// if ( ! \POCHIPP\check_ajax_nonce() ) {
	// };

	$datas       = [];
	$keywords    = \POCHIPP::array_get( $_POST, 'keywords', '' );
	$searched_at = \POCHIPP::array_get( $_POST, 'searched_at', '' );
	$itemcode    = \POCHIPP::array_get( $_POST, 'itemcode', '' );

	if ( 'amazon' === $searched_at ) {

		$request          = new \GetItemsRequest();
		$request->ItemIds = [ $itemcode ];
		$datas            = \POCHIPP\get_json_from_amazon_api( 'GetItems', $request, $keywords );

	} elseif ( 'rakuten' === $searched_at ) {

		$api_query = '&availability=0&itemCode=' . rawurlencode( $itemcode );
		$datas     = \POCHIPP\get_item_data_from_rakuten_api( $api_query, $keywords, $itemcode );

	}

	wp_die( json_encode( [
		'datas'    => $datas,
	] ) );
}
