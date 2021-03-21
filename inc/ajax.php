<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AJAXのNonceチェック
 */
function check_ajax_nonce( $request_key = 'nonce', $nonce_key = '' ) {

	if ( isset( $_POST[ $request_key ] ) ) {
		$nonce = $_POST[ $request_key ];
	} elseif ( isset( $_GET[ $request_key ] ) ) {
		$nonce = $_GET[ $request_key ];
	} else {
		return false;
	}

	$nonce_key = $nonce_key ?: \POCHIPP::NONCE_KEY;

	if ( wp_verify_nonce( $nonce, $nonce_key ) ) {
		return true;
	}

	return false;
}


require_once POCHIPP_PATH . 'inc/ajax/search_amazon.php';
require_once POCHIPP_PATH . 'inc/ajax/search_rakuten.php';
require_once POCHIPP_PATH . 'inc/ajax/search_yahoo.php';
require_once POCHIPP_PATH . 'inc/ajax/search_registerd.php';


/**
 * 商品データを更新する
 */
add_action( 'wp_ajax_pochipp_update_data', '\POCHIPP\update_data' );
function update_data() {

	if ( ! \POCHIPP\check_ajax_nonce() ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'nonce error',
				'message' => '不正なアクセスです。',
			],
		] ) );
	};

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

	} elseif ( 'yahoo' === $searched_at ) {

		// do: yahooの情報更新処理

	}

	if ( isset( $datas['error'] ) ) {
		wp_die( json_encode( [
			'error' => $datas['error'],
		] ) );
	}

	wp_die( json_encode( [
		'data' => $datas[0],
	] ) );
}


/**
 * ブロックから商品データを登録する
 */
add_action( 'wp_ajax_pochipp_registerd_by_block', '\POCHIPP\registerd_by_block' );
function registerd_by_block() {

	if ( ! \POCHIPP\check_ajax_nonce() ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'nonce error',
				'message' => '不正なアクセスです。',
			],
		] ) );
	};

	$datas     = [];
	$attrs     = \POCHIPP::array_get( $_POST, 'attributes', '' );
	$client_id = \POCHIPP::array_get( $_POST, 'clientId', '' );

	$attrs = str_replace( '\\"', '"', $attrs );
	$attrs = json_decode( $attrs, true );

	if ( empty( $attrs ) || ! is_array( $attrs ) ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'decode error',
				'message' => '商品データのデコードに失敗しました。',
			],
		] ) );
	}

	$pid = $attrs['pid'] ?? 0;
	if ( $pid ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'Already registered',
				'message' => 'すでに登録済みの商品です。',
			],
		] ) );
	}

	$title = $attrs['title'] ?? '不明なタイトル - ' . $client_id; // 初期値

	if ( isset( $attrs['title'] ) ) {
		$title = $attrs['title'];
		unset( $attrs['title'] );
	}

	// メタに保存しない項目を削除
	unset( $attrs['title'] );
	unset( $attrs['className'] );
	unset( $attrs['pid'] );
	unset( $attrs['hideInfo'] );
	unset( $attrs['hidePrice'] );
	unset( $attrs['hideAmazon'] );
	unset( $attrs['hideRakuten'] );
	unset( $attrs['hideYahoo'] );
	unset( $attrs['hideCustom'] );

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

	update_post_meta( $new_id, \POCHIPP::META_SLUG, json_encode( $attrs, JSON_UNESCAPED_UNICODE ) );

	wp_die( json_encode( [
		'pid' => $new_id,
	] ) );
}
