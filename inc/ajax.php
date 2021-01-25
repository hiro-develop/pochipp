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


/**
 * 商品データを更新する
 */
add_action( 'wp_ajax_pochipp_update_data', '\POCHIPP\update_data' );
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

	if ( isset( $datas['error'] ) ) {
		wp_die( json_encode( [
			'error' => $datas['error'],
		] ) );
	}

	wp_die( json_encode( [
		'datas' => $datas,
	] ) );
}


/**
 * ブロックから商品データを登録する
 */
add_action( 'wp_ajax_pochipp_registerd_by_block', '\POCHIPP\registerd_by_block' );
function registerd_by_block() {

	// if ( ! \POCHIPP\check_ajax_nonce() ) {
	// };

	$datas      = [];
	$attributes = \POCHIPP::array_get( $_POST, 'attributes', '' );
	$client_id  = \POCHIPP::array_get( $_POST, 'clientId', '' );

	$attributes = str_replace( '\\"', '"', $attributes );
	$attributes = json_decode( $attributes, true );

	if ( empty( $attributes ) || ! is_array( $attributes ) ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'decode error',
				'message' => '商品データのデコードに失敗しました。',
			],
		] ) );
	}

	$pid = $attributes['pid'] ?? 0;
	if ( $pid ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'Already registered',
				'message' => 'すでに登録済みの商品です。',
			],
		] ) );
	}

	$title = $attributes['title'] ?? '';
	$title = $title ?: '不明なタイトル - ' . $client_id;

	$meta = [
		'searched_at'        => $attributes['searched_at'] ?? '',
		'asin'               => $attributes['asin'] ?? '',
		'itemcode'           => $attributes['itemcode'] ?? '',
		'image_url'          => $attributes['image_url'] ?? '',
		'info'               => $attributes['info'] ?? '',
		'keywords'           => $attributes['keywords'] ?? '',
		'price'              => $attributes['price'] ?? '',
		'price_at'           => $attributes['price_at'] ?? '',
		'amazon_affi_url'    => $attributes['amazon_affi_url'] ?? '',
		'rakuten_detail_url' => $attributes['rakuten_detail_url'] ?? '',
		'custom_btn_text'    => $attributes['custom_btn_text'] ?? '',
		'custom_btn_url'     => $attributes['custom_btn_url'] ?? '',
	];

	$new_id = wp_insert_post( [
		'post_type'      => \POCHIPP::POST_TYPE_SLUG,
		'post_title'     => $title,
		'post_content'   => '<!-- wp:pochipp/setting /-->',
		'post_status'    => 'publish',
	] );

	if ( 0 === $new_id ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'insert post error',
				'message' => '商品データの登録に失敗しました。',
			],
		] ) );
	}

	update_post_meta( $new_id, \POCHIPP::META_SLUG, json_encode( $meta, JSON_UNESCAPED_UNICODE ) );

	wp_die( json_encode( [
		'pid'        => $new_id,
		// 'attributes' => $attributes,
		// 'meta'       => json_encode( $meta, JSON_UNESCAPED_UNICODE ),
	] ) );
}
