<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Amazon APIから検索
 */
// \POCHIPP::ACTION_NAME['rakuten'] とかでアクション名 とれるようにする
add_action( 'wp_ajax_pochipp_search_rakuten', '\POCHIPP\search_from_rakuten_api' );


/**
 *  楽天APIから商品データを取得する　for ajax
 */
function search_from_rakuten_api() {

	$keywords = \POCHIPP::array_get( $_GET, 'keywords', '' );
	$page     = \POCHIPP::array_get( $_GET, 'page', 1 );
	$sort     = \POCHIPP::array_get( $_GET, 'sort', 1 );

	// 登録済み商品
	$registerd_items = \POCHIPP::get_registerd_items( [
		'keywords' => $keywords,
		'count'    => 2, // memo: ２個まで取得。<- 少ない？
	] );

	// 検索結果
	$searched_items = \POCHIPP\get_item_data_from_rakuten_api( $keywords, $page, $sort, false );

	wp_die( json_encode( [
		'registerd_items' => $registerd_items,
		'searched_items'  => $searched_items,
	] ) );
}

/**
 * 楽天APIから商品データを取得
 */
function get_item_data_from_rakuten_api( $keywords, $page, $sort, $is_itemcode = false ) {

	// 空白の場合
	if ( 0 === strlen( trim( $keywords ) ) ) {
		return [
			'error' => [
				'code'    => 'null',
				'message' => '検索キーワードが空です。',
			],
		];
	}

	// memo: hits は検索数
	$request_url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706?hits=10';

	// アプリID
	$request_url .= '&applicationId=' . \POCHIPP::RAKUTEN_APP_ID;

	// 楽天アフィID
	// $rakuten_affi_id = \POCHIPP::get_setting( 'rakuten_affiliate_id' );

	// memo: アフィID投げなけない。（ itemUrl で普通のURL取れる ）
	// if ( $rakuten_affi_id ) { $request_url .= '&affiliateId=' . $rakuten_affi_id; }

	$page        = intval( $page );
	$page        = $page > 1 ? $page : 1;
	$request_url = $request_url . '&page=' . $page;

	// 並び順を変更
	$sort = \POCHIPP\rakuten_sort( $sort );

	$request_url = $request_url . '&sort=' . urlencode( $sort );

	if ( $is_itemcode ) {
		$request_url .= '&availability=0&itemCode=' . rawurlencode( $keywords );
	} else {
		$request_url .= '&availability=1&keyword=' . rawurlencode( $keywords );
	}

	$response = wp_remote_get(
		$request_url,
		[
			// 'method'      => 'GET',
			'timeout' => 30,
		]
	);

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
	if ( isset( $response_arr['error'] ) ) {

		return [
			'error' => [
				'code'      => $response_arr['error'],
				'message'   => \POCHIPP\get_rakuten_api_error_text( $response_arr['error'], $response_arr['error_description'] ),
			],
		];

	}

	// OK
	return \POCHIPP\set_item_data_by_rakuten_api( $response_arr, $keywords, $is_itemcode );
}


/**
 * 商品データを整形
 */
function set_item_data_by_rakuten_api( $response_data = [], $keywords = '', $is_itemcode ) {

	$items = [];

	if ( $is_itemcode && isset( $response_data['hits'] ) && intval( $response_data['hits'] ) === 0 ) {
		return [
			'error' => [
				'code'    => 'no item',
				'message' => '指定の商品コードの商品がありません',
			],
		];
	}

	if ( ! isset( $response_data['Items'] ) ) {
		return [
			'error' => [
				'code'    => 'no item',
				'message' => '商品が見つかりませんでした。',
			],
		];
	}

	foreach ( $response_data['Items'] as $data ) {

		$item = [
			'keywords'    => $keywords,
			'searched_at' => 'rakuten',
		];

		// itemcode で商品取得するとき
		if ( $is_itemcode ) {
			$item['price']              = $data['Item']['itemPrice'] ?? '';
			$item['price_at']           = date_i18n( 'Y/m/d H:i' );
			$item['rakuten_detail_url'] = $data['Item']['itemUrl'] ?? '';

			$items[] = $item;
			break;
		}

		$item['title']              = $data['Item']['itemName'] ?? '';
		$item['itemcode']           = $data['Item']['itemCode'] ?? '';
		$item['rakuten_detail_url'] = $data['Item']['itemUrl'] ?? '';

		// 商品画像
		$item['s_image_url'] = $data['Item']['smallImageUrls'][0]['imageUrl'] ?? '';
		$item['m_image_url'] = $data['Item']['mediumImageUrls'][0]['imageUrl'] ?? '';
		$item['l_image_url'] = '';

		$item['shop_name'] = $data['Item']['shopName'] ?? '';
		$item['price']     = $data['Item']['itemPrice'] ?? '';
		$item['price_at']  = date_i18n( 'Y/m/d H:i' );

		// 楽天市場のみ
		$item['affiliateRate'] = $data['Item']['affiliateRate'] ?? '';
		$item['reviewAverage'] = $data['Item']['reviewAverage'] ?? '';

		$items[] = $item;
	}

	return $items;
}

/**
 * 楽天APIの並び順設定
 */
function rakuten_sort( $sort ) {
	$sort_info = \POCHIPP::array_get( \POCHIPP::$rakuten_sorts, intval( $sort ), false );
	if ( ! $sort_info ) {
		$sort_info = \POCHIPP::$rakuten_sorts[5];
	}
	return $sort_info['value'];
}


/**
 * 楽天APIのエラーメッセージをできるだけ日本語化して返す
 */
function get_rakuten_api_error_text( $code = '', $description = '' ) {
	switch ( $code ) {
		case 'wrong_parameter':
			switch ( $description ) {
				case 'keyword is not valid':
					$message = 'キーワードを正しく設定してください';
					break;
				case 'specify valid applicationId':
				case 'client_id or access_token':
					$message = 'アプリケーションIDが登録されていません。開発者に問い合わせてください。';
					break;
				case 'itemCode is not valid':
					$message = '商品コードが存在しません';
					break;
				default:
					$message = 'パラメーターエラーです';
					break;
			}
			break;
		case 'not_found':
			$message = 'データが存在しません';
			break;
		case 'too_many_requests':
			$message = 'リクエスト回数が多すぎます。しばらく時間を空けてからご利用ください。';
			break;

		case 'system_error':
			$message = '楽天ウェブサービスのシステムエラーです。長時間続くようであれば楽天ウェブサービスヘルプページよりごお問い合わせください。';
			break;
		case 'service_unavailable':
			$message = '楽天ウェブサービスメンテナンス中です。' . $description;
			break;
		default:
			$message = $description;
			break;
	}
	return $message;
}
