<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 楽天APIから商品データを取得する
 */
add_action( 'wp_ajax_pochipp_search_rakuten', '\POCHIPP\search_from_rakuten_api' );
function search_from_rakuten_api() {

	if ( ! \POCHIPP\check_ajax_nonce() ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'nonce error',
				'message' => '不正なアクセスです。',
			],
		] ) );
	};

	$keywords = \POCHIPP::get_sanitized_data( $_GET, 'keywords', 'text', '' );
	$sort     = \POCHIPP::get_sanitized_data( $_GET, 'sort', 'text', 'standard' );
	$only     = \POCHIPP::get_sanitized_data( $_GET, 'only', 'text', '' );

	// ページ
	// $page     = \POCHIPP::get_sanitized_data( $_GET, 'page', 'int', 1 );
	// $page = intval( $page ) > 1 ? $page : '1';

	// 登録済み商品
	$registerd_items = [];
	if ( ! $only ) {
		$registerd_items = \POCHIPP::get_registerd_items( [
			'keywords' => $keywords,
			'count'    => 2, // memo: ２個は少ない？
		] );
	}

	// 検索結果を取得
	$searched_items = \POCHIPP\get_searched_data_from_rakuten_api( $keywords, $sort );

	wp_die( json_encode( [
		'registerd_items' => $registerd_items,
		'searched_items'  => $searched_items,
	] ) );
}


/**
 * キーワード検索
 */
function get_searched_data_from_rakuten_api( $keywords, $sort = 'standard' ) {

	if ( ! trim( $keywords ) ) {
		return [
			'error' => [
				'code'    => 'null',
				'message' => '検索キーワードが空です。',
			],
		];
	}

	// 検索API用のurlに付与するクエリ情報を生成
	$api_query  = '&hits=10'; // 検索数: amazonの数と揃える
	$api_query .= '&page=1&sort=' . rawurlencode( $sort );
	$api_query .= '&availability=1&keyword=' . rawurlencode( $keywords );

	return \POCHIPP\get_data_from_rakuten_api( $api_query, $keywords );
}


/**
 * 単体検索
 */
function get_item_data_from_rakuten_api( $itemcode ) {

	// 検索API用のurlに付与するクエリ情報を生成
	$api_query = '&availability=0&itemCode=' . rawurlencode( $itemcode );
	return \POCHIPP\get_data_from_rakuten_api( $api_query );
}


/**
 * 楽天APIから商品データを取得
 */
function get_data_from_rakuten_api( $api_query, $keywords = '' ) {

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
	$request_url .= '?applicationId=' . \POCHIPP::get_setting( 'rakuten_app_id' ); // アプリID情報
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

	// OK
	return \POCHIPP\set_item_data_by_rakuten_api( $response_arr['Items'], $keywords );
}


/**
 * 商品データを整形
 */
function set_item_data_by_rakuten_api( $items_data, $keywords = '' ) {

	$items = [];

	foreach ( $items_data as $item ) {

		$data = [];

		// キーワード検索の時だけ取得するデータ群
		if ( $keywords ) {
			$data = [
				'keywords'    => $keywords,
				'searched_at' => 'rakuten',
			];

			$data['title']    = $item['Item']['itemName'] ?? '';
			$data['itemcode'] = $item['Item']['itemCode'] ?? '';
			$data['info']     = $item['Item']['shopName'] ?? '';
		}

		// 詳細URL
		$data['rakuten_detail_url'] = $item['Item']['itemUrl'] ?? '';

		// 商品画像
		$imageFlag = (string) $item['Item']['imageFlag'];
		if ( '1' === $imageFlag ) {
			$image_url_s = $item['Item']['smallImageUrls'][0]['imageUrl'] ?? '';
			// $image_url_m = $item['Item']['mediumImageUrls'][0]['imageUrl'] ?? '';
			$image_url         = substr( $image_url_s, 0, strcspn( $image_url_s, '?' ) );
			$data['image_url'] = $image_url;
		} else {
			$data['image_url'] = '';
		}

		// 価格
		$data['price']    = $item['Item']['itemPrice'] ?? '';
		$data['price_at'] = wp_date( 'Y/m/d H:i' );

		// memo: とりあえず不要
		// $data['affi_rate']    = $item['Item']['affiliateRate'] ?? '';
		// $data['review_score'] = $item['Item']['reviewAverage'] ?? '';

		$items[] = $data;
	}

	return $items;
}


/**
 * 楽天APIのエラーメッセージをできるだけ日本語化して返す
 * see: https://webservice.rakuten.co.jp/api/ichibaitemsearch/
 */
function get_rakuten_api_error_text( $code = '', $description = '' ) {
	switch ( $code ) {
		// パラメーターエラー
		case 'wrong_parameter':
			switch ( $description ) {
				case 'specify valid applicationId':
					$message = 'アプリIDが指定されていません。';
					break;
				case 'keyword parameter is not valid':
					$message = 'キーワードを正しく設定してください。';
					break;
				default:
					$message = 'パラメーターが不足しています。(' . $description . ')';
					break;
			}
			break;
		case 'not_found':
			$message = 'リクエストに該当するデータが見つかりませんでした。';
			break;
		case 'too_many_requests':
			$message = 'リクエスト回数が多すぎます。しばらく時間を空けてから再度お試しください。';
			break;
		case 'system_error':
			$message = '楽天ウェブサービス内のシステムエラーです。';
			break;
		case 'service_unavailable':
			$message = '楽天ウェブサービスのメンテナンス中です。';
			break;
		default:
			$message = $description;
			break;
	}
	return $message;
}
