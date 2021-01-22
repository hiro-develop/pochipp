<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Amazon APIから検索
 */
// \POCHIPP::ACTION_NAME['rakuten'] とかでアクション名 とれるようにする
add_action( 'wp_ajax_pochipp_search_rakuten', '\POCHIPP\search_from_rakuten_api' );


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
 *  楽天APIから商品データを取得する for ajax
 */
function search_from_rakuten_api() {

	$keywords = \POCHIPP::array_get( $_GET, 'keywords', '' );
	$page     = \POCHIPP::array_get( $_GET, 'page', 1 );
	$sort     = \POCHIPP::array_get( $_GET, 'sort', 1 );

	// 整理
	$page = intval( $page ) > 1 ? $page : '1';
	$sort = \POCHIPP\rakuten_sort( $sort );

	// 登録済み商品
	$registerd_items = \POCHIPP::get_registerd_items( [
		'keywords' => $keywords,
		'count'    => 2, // memo: ２個まで取得。<- 少ない？
	] );

	// 検索API用のurlに付与するクエリ情報を生成
	$api_query  = '&hits=10'; // 検索数
	$api_query .= '&page=' . $page . '&sort=' . rawurlencode( $sort );
	$api_query .= '&availability=1&keyword=' . rawurlencode( $keywords );

	// 検索結果を取得
	$searched_items = \POCHIPP\get_item_data_from_rakuten_api( $api_query, $keywords );

	wp_die( json_encode( [
		'registerd_items' => $registerd_items,
		'searched_items'  => $searched_items,
	] ) );
}

/**
 * 楽天APIから商品データを取得
 */
function get_item_data_from_rakuten_api( $api_query, $keywords, $itemcode = '' ) {

	// 空白の場合
	if ( ! trim( $keywords ) && ! $itemcode ) {
		return [
			'error' => [
				'code'    => 'null',
				'message' => '検索キーワードが空です。',
			],
		];
	}

	// クエリが不正な場合
	if ( ! $api_query ) {
		return [
			'error' => [
				'code'    => 'no query',
				'message' => '検索条件が不明です。',
			],
		];
	}

	$request_url  = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706';
	$request_url .= '?applicationId=' . \POCHIPP::RAKUTEN_APP_ID; // アプリID情報を付与
	$request_url .= $api_query; // その他の条件

	// 楽天アフィID // memo: itemUrl もアフィURLになってしまう。
	// $rakuten_affi_id = \POCHIPP::get_setting( 'rakuten_affiliate_id' );
	// if ( $rakuten_affi_id ) { $api_query .= '&affiliateId=' . $rakuten_affi_id; }

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
	if ( isset( $response_arr['error'] ) ) {
		return [
			'error' => [
				'code'      => $response_arr['error'],
				'message'   => \POCHIPP\get_rakuten_api_error_text( $response_arr['error'], $response_arr['error_description'] ),
			],
		];
	}

	if ( ! isset( $response_arr['Items'] ) ) {
		return [
			'error' => [
				'code'    => 'no item',
				'message' => '商品が見つかりませんでした。',
			],
		];
	}

	// if ( $itemcode && isset( $response_arr['hits'] ) && intval( $response_arr['hits'] ) === 0 ) {
	// 	return [
	// 		'error' => [
	// 			'code'    => 'no item',
	// 			'message' => '指定の商品コードの商品がありません',
	// 		],
	// 	];
	// }

	// OK
	return \POCHIPP\set_item_data_by_rakuten_api( $response_arr['Items'], $keywords, $itemcode );
}


/**
 * 商品データを整形
 */
function set_item_data_by_rakuten_api( $items_data, $keywords = '', $itemcode ) {

	$items = [];

	foreach ( $items_data as $data ) {

		$item = [
			'keywords'    => $keywords,
			'searched_at' => 'rakuten',
		];

		// itemcode で商品取得するときは必要な部分だけ取得
		// if ( $itemcode ) {
		// 	$items[] = $item;
		// 	break;
		// }

		$item['title']              = $data['Item']['itemName'] ?? '';
		$item['itemcode']           = $data['Item']['itemCode'] ?? '';
		$item['rakuten_detail_url'] = $data['Item']['itemUrl'] ?? '';

		// 商品画像
		$item['s_image_url'] = $data['Item']['smallImageUrls'][0]['imageUrl'] ?? '';
		$item['m_image_url'] = $data['Item']['mediumImageUrls'][0]['imageUrl'] ?? '';
		$item['l_image_url'] = '';

		// 商品情報
		$item['info']     = $data['Item']['shopName'] ?? '';
		$item['price']    = $data['Item']['itemPrice'] ?? '';
		$item['price_at'] = date_i18n( 'Y/m/d H:i' );

		// 楽天市場のみ memo: いる？
		$item['affi_rate']    = $data['Item']['affiliateRate'] ?? '';
		$item['review_score'] = $data['Item']['reviewAverage'] ?? '';

		$items[] = $item;
	}

	return $items;
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
