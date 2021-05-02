<?php
namespace POCHIPP;

// see: https://developer.yahoo.co.jp/webapi/shopping/shopping/v3/itemsearch.html

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * YahooAPIから商品データを取得する
 */
add_action( 'wp_ajax_pochipp_search_yahoo', '\POCHIPP\search_from_yahoo_api' );
function search_from_yahoo_api() {

	if ( ! \POCHIPP\check_ajax_nonce() ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'nonce error',
				'message' => '不正なアクセスです。',
			],
		] ) );
	};

	$keywords = \POCHIPP::get_sanitized_data( $_GET, 'keywords', 'text', '' );
	$only     = \POCHIPP::get_sanitized_data( $_GET, 'only', 'text', '' );

	// 登録済み商品
	$registerd_items = [];
	if ( ! $only ) {
		$registerd_items = \POCHIPP::get_registerd_items( [
			'keywords' => $keywords,
			'count'    => 2, // memo: ２個まで取得。<- 少ない？
		] );
	}

	// 検索結果を取得
	$searched_items = \POCHIPP\get_searched_data_from_yahoo_api( $keywords );

	wp_die( json_encode( [
		'registerd_items' => $registerd_items ?: [],
		'searched_items'  => $searched_items ?: [],
	] ) );
}



/**
 * キーワード検索
 */
function get_searched_data_from_yahoo_api( $keywords ) {

	if ( ! trim( $keywords ) ) {
		return [
			'error' => [
				'code'    => 'null',
				'message' => '検索キーワードが空です。',
			],
		];
	}

	// 検索API用のurlに付与するクエリ情報を生成
	$api_query  = '&results=10'; // 検索数: amazonの数と揃える
	$api_query .= '&query=' . rawurlencode( $keywords );
	// $api_query .= '&page=1&sort=' . rawurlencode( $sort ); // ソート

	return \POCHIPP\get_data_from_yahoo_api( $api_query, $keywords );
}


/**
 * 単体検索
 */
// function get_item_data_from_yahoo_api( $itemcode ) {
// 	return \POCHIPP\get_data_from_yahoo_api( $api_query );
// }


/**
 * Yahoo APIから商品データを取得
 */
function get_data_from_yahoo_api( $api_query, $keywords = '' ) {

	// クエリが不正な場合
	if ( ! $api_query ) {
		return [
			'error' => [
				'code'    => 'no query',
				'message' => '検索条件が不明です。',
			],
		];
	}

	$request_url  = 'https://shopping.yahooapis.jp/ShoppingWebService/V3/itemSearch';
	$request_url .= '?appid=' . \POCHIPP::get_setting( 'yahoo_app_id' ); // アプリID情報
	$request_url .= $api_query .= '&image_size=600'; // その他の条件

	$response = wp_remote_get( $request_url, [
		// 'method'      => 'GET',
		'timeout' => 30,
	] );

	// エラーがあれば
	if ( is_wp_error( $response ) ) {
		return [
			'error' => [
				'code'    => 'is_wp_error',
				'message' => $response->get_error_message(),
			],
		];
	}

	// レスポンスをデコード
	$response_arr = json_decode( $response['body'], true );

	// decode失敗時
	if ( ! $response_arr || ! is_array( $response_arr ) ) {
		return [
			'error' => [
				'code'    => 'decode error',
				'message' => 'APIから正しいデータが返ってきませんでした。',
			],
		];
	}

	// APIからエラーが返ってきた場合
	if ( isset( $response_arr['Error'] ) ) {
		return [
			'error' => [
				'code'      => 'APIエラー',
				'message'   => $response_arr['Error']['Message'] ?? '不明なエラー',
			],
		];
	}

	if ( 0 === (int) $response_arr['totalResultsAvailable'] ) {
		return [
			'error' => [
				'code'    => 'no item',
				'message' => '商品が見つかりませんでした。',
			],
		];
	}

	// OK
	return \POCHIPP\set_item_data_by_yahoo_api( $response_arr['hits'], $keywords );
}


/**
 * 商品データを整形
 */
function set_item_data_by_yahoo_api( $items_data, $keywords = '' ) {

	$items = [];

	foreach ( $items_data as $data ) {

		$item = [
			'keywords'    => $keywords,
			'searched_at' => 'yahoo',
		];

		$item['title']          = $data['name'] ?? '';
		$item['yahoo_itemcode'] = $data['code'] ?? '';
		$item['seller_id']      = $data['seller']['sellerId'] ?? '';

		// $item['isbn']           = $data['isbn'] ?? ''; // -> ほぼ全部空
		// $item['janCode']        = $data['janCode'] ?? ''; // -> ほぼ全部空

		$item['yahoo_detail_url'] = $data['url'] ?? '';

		// 商品画像
		$item['image_url'] = $data['exImage']['url'] ?? ''; // 「/i/l/」 -> 「/i/g/」で小さい画像

		// 商品情報
		$item['info']     = $data['brand']['name'] ?? $data['seller']['name'] ?? '';
		$item['price']    = $data['price'] ?? '';
		$item['price_at'] = wp_date( 'Y/m/d H:i' );

		$is_taxable = $data['priceLabel']['taxable'] ?? '';

		if ( ! $is_taxable ) {
			$item['price'] = ''; // 総額表示違反を防ぐ
		}
		// else {
			// $item['price'] .= '(税込み)';
		// }

		$isPMallSeller     = $data['seller']['isPMallSeller'] ?? false;
		$item['is_paypay'] = $isPMallSeller ? '1' : '';

		// レビュー平均
		// hits/seller/review/rate

		$items[] = $item;
	}

	return $items;
}
