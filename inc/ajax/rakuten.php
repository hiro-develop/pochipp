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

	$keywords = \POCHIPP\array_get( $_GET, 'keywords', '' );
	$page     = \POCHIPP\array_get( $_GET, 'page', 1 );
	$sort     = \POCHIPP\array_get( $_GET, 'sort', 1 );

	$registerd_items = \POCHIPP\get_registerd_items( [
		'keywords' => $keywords,
		'count'    => 2,
	] );
	$searched_items  = \POCHIPP\generate_rakuten_datas( $keywords, $page, $sort, false );

	wp_die( json_encode( [
		'registerd_items' => $registerd_items,
		'searched_items'  => $searched_items,
	] ) );
}

/**
 * 楽天APIから商品データを取得
 *
 * @param $keywords
 * @param $page
 * @param bool     $is_itemcode itemcodeから検索（再取得時）
 * @return array
 */
function generate_rakuten_datas( $keywords, $page, $sort, $is_itemcode = false ) {

	if ( strlen( trim( $keywords ) ) === 0 ) {
		return [
			'error' => [
				'code'       => '',
				'message_jp' => '正しいキーワードを入力してください',
			],
		];
	}

	// memo: hits は検索数
	$request_url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706?hits=10';

	// アプリID
	$request_url .= '&applicationId=' . \POCHIPP::RAKUTEN_APP_ID;

	// 楽天アフィID   memo: あとで取得できるように
	$rakuten_affi_id = get_option( 'pochipp_rakuten_affiliate_id' ) ?: '1cf1501e.27808d00.1cf1501f.2f6529aa';

	// アフィID ( memo: これ検索の時にいる...？ ->  affiliateUrl が取れる & itemUrl = affiliateUrl になる。 )
	// アフィID投げなければ、 itemUrl で普通のURL取れる
	if ( $rakuten_affi_id ) {
		$request_url .= '&affiliateId=' . $rakuten_affi_id;
	}

	$page        = intval( $page ) > 1 ? intval( $page ) : 1;
	$request_url = $request_url . '&page=' . $page;

	// 並び順を変更
	$sort = \POCHIPP\rakuten_sort( $sort );

	$request_url = $request_url . '&sort=' . urlencode( $sort );

	if ( $is_itemcode ) {
		$request_url .= '&availability=0&itemCode=' . rawurlencode( $keywords );
	} else {
		$request_url .= '&availability=1&keyword=' . rawurlencode( $keywords );
	}
	$response = wp_remote_request(
		$request_url,
		[
			'timeout' => 30,
		]
	);

	if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
		return [
			'error' => [
				'code'          => 'XML parser error',
				'message'       => '【parser Error】XMLが正しくありません',
				'message_jp'    => '【parser Error】XMLが正しくありません',
			],
		];
	}

	$datas = json_decode( $response['body'], true );

	if ( is_array( $datas ) && isset( $datas['error'] ) ) {
		$errors          = [];
		$errors['error'] = [
			'code'      => $datas['error'],
			'message'   => $datas['error_description'],
		];

		$errors['error']['message_jp'] = \POCHIPP\rakuten_api_errors( $datas['error'], $datas['error_description'] );
		return $errors;
	} else {
		return \POCHIPP\set_data_for_rakuten( $datas, $keywords, $is_itemcode );
	}
}

function set_data_for_rakuten( $datas = [], $keywords = '', $is_itemcode ) {

	$items = [];

	if ( $is_itemcode && isset( $datas['hits'] ) && intval( $datas['hits'] ) === 0 ) {
		$errors          = [];
		$errors['error'] = [
			'code'          => 'rakuten_noitem',
			'message'       => '指定の商品コードの商品がありません',
			'message_jp'    => '指定の商品コードの商品がありません',
		];
		return $errors;
	}
	if ( isset( $datas['Items'] ) ) {
		$item = [];
		foreach ( $datas['Items'] as $data ) {

			if ( $is_itemcode ) {

				$item['price']             = $data['Item']['itemPrice'];
				$item['price_at']          = date_i18n( 'Y/m/d H:i:s' );
				$item['rakuten_title_url'] = $data['Item']['itemUrl'];
				$items[]                   = $item;
				break;
			}

			$item['title']             = $data['Item']['itemName'];
			$item['rakuten_itemcode']  = $data['Item']['itemCode'];
			$item['rakuten_title_url'] = $data['Item']['itemUrl'];
			if ( isset( $data['Item']['smallImageUrls'][0]['imageUrl'] ) ) {
				$item['s_image_url'] = $data['Item']['smallImageUrls'][0]['imageUrl'];
			} else {
				$item['s_image_url'] = '';
			}

			if ( isset( $data['Item']['mediumImageUrls'][0]['imageUrl'] ) ) {
				$item['m_image_url'] = $data['Item']['mediumImageUrls'][0]['imageUrl'];
			} else {
				$item['m_image_url'] = '';
			}
			$item['l_image_url'] = '';
			$item['brand']       = '';
			$item['price']       = $data['Item']['itemPrice'];
			$item['amazon_url']  = \POCHIPP\generate_amazon_original_link( $keywords );
			$item['rakuten_url'] = \POCHIPP\generate_rakuten_original_link( $keywords );
			$item['yahoo_url']   = \POCHIPP\generate_yahoo_original_link( $keywords );

			// $item[ self::IMAGE_S_SIZE_W_COLUMN ]	= 64;
			// $item[ self::IMAGE_S_SIZE_H_COLUMN ]	= 64;
			// $item[ self::IMAGE_M_SIZE_W_COLUMN ]	= 128;
			// $item[ self::IMAGE_M_SIZE_H_COLUMN ]	= 128;
			// $item[ self::IMAGE_L_SIZE_W_COLUMN ]	= '';
			// $item[ self::IMAGE_L_SIZE_H_COLUMN ]	= '';

			// 楽天市場のみ
			$item['affiliateRate'] = $data['Item']['affiliateRate'];
			$item['reviewAverage'] = $data['Item']['reviewAverage'];

			$items[] = $item;
		}
	}

	return $items;
}

/**
 * 楽天APIの並び順を変更
 *
 * @param $sort
 * @return mixed
 */
function rakuten_sort( $sort ) {
	$sort_info = \POCHIPP\array_get( \POCHIPP::$rakuten_sorts, intval( $sort ), false );
	if ( ! $sort_info ) {
		$sort_info = \POCHIPP::$rakuten_sorts[5];
	}
	return $sort_info['value'];
}

function rakuten_api_errors( $code = '', $en_message = '' ) {
	switch ( $code ) {
		case 'wrong_parameter':
			switch ( $en_message ) {
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
			$message = '楽天ウェブサービスメンテナンス中です。' . $en_message;
			break;
		default:
			$message = $en_message;
			break;
	}
	return $message;
}
