<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * APIから検索
 */
add_action( 'wp_ajax_pochipp_search_amazon', '\POCHIPP\search_from_amazon_api' );


/**
 * AmazonAPIから商品データを取得する
 */
function search_from_amazon_api() {

	if ( ! \POCHIPP\check_ajax_nonce() ) {
		wp_die( json_encode( [
			'error' => [
				'code'    => 'nonce error',
				'message' => '不正なアクセスです。',
			],
		] ) );
	};

	$keywords     = \POCHIPP::array_get( $_GET, 'keywords', '' );
	$only         = \POCHIPP::array_get( $_GET, 'only', '' );
	$search_index = 'All'; // \POCHIPP::array_get( $_GET, 'search_index', 'All' );

	// 登録済み商品
	$registerd_items = [];
	if ( ! $only ) {
		$registerd_items = \POCHIPP::get_registerd_items( [
			'keywords' => $keywords,
			'count'    => 2, // memo: ２個まで取得。<- 少ない？
		] );
	}

	// 検索結果
	$searched_items = \POCHIPP\get_item_data_from_amazon_api( $keywords, $search_index );

	wp_die( json_encode( [
		'registerd_items' => $registerd_items ?: [],
		'searched_items'  => $searched_items ?: [],
	] ) );
}


/**
 * AmazonAPI (PA-APIv5) から商品データを取得
 */
function get_item_data_from_amazon_api( $keywords = '', $search_index = 'All' ) {

	// 空白の場合
	if ( ! trim( $keywords ) ) {
		return [
			'error' => [
				'code'    => 'null',
				'message' => '検索キーワードが空です。',
			],
		];
	}

	$request              = new \SearchItemsRequest();
	$request->SearchIndex = $search_index;
	$request->Keywords    = $keywords;
	return \POCHIPP\get_json_from_amazon_api( 'SearchItems', $request, $keywords );
}


/**
 * for PA-APIv5
 * apiからデータを取得する
 *
 * @param string $operation 'SearchItems' or 'GetItems' を受け取る
 */
function get_json_from_amazon_api( $operation, $request, $keywords ) {

	// 設定取得
	$access_key   = \POCHIPP::get_setting( 'amazon_access_key' );
	$secret_key   = \POCHIPP::get_setting( 'amazon_secret_key' );
	$traccking_id = \POCHIPP::get_setting( 'amazon_traccking_id' );

	$request->PartnerType = 'Associates';
	$request->PartnerTag  = $traccking_id;
	$request->Resources   = [
		'Images.Primary.Small',
		// 'Images.Primary.Medium',
		'Images.Primary.Large',
		// 'Images.Variants.Small',
		// 'Images.Variants.Medium',
		// 'Images.Variants.Large',
		'ItemInfo.ByLineInfo',
		'ItemInfo.Title',
		'ItemInfo.ByLineInfo',
		'ItemInfo.Classifications',
		'ItemInfo.ProductInfo',
		'Offers.Listings.Price',
		'ParentASIN',
	];

	$host = 'webservices.amazon.co.jp';
	$path = '/paapi5/' . mb_strtolower( $operation );

	$payload = json_encode( $request );

	$awsv4 = new \AwsV4( $access_key, $secret_key );
	$awsv4->setRegionName( 'us-west-2' );
	$awsv4->setServiceName( 'ProductAdvertisingAPI' );
	$awsv4->setPath( $path );
	$awsv4->setPayload( $payload );
	$awsv4->setRequestMethod( 'POST' );
	$awsv4->addHeader( 'content-encoding', 'amz-1.0' );
	$awsv4->addHeader( 'content-type', 'application/json; charset=utf-8' );
	$awsv4->addHeader( 'host', $host );
	$awsv4->addHeader( 'x-amz-target', 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.' . $operation );

	$headers = $awsv4->getHeaders();

	// API接続
	$response = wp_remote_post(
		'https://' . $host . $path,
		[
			// 'method'      => 'POST',
			'timeout'     => 10,
			'redirection' => 5,
			'sslverify'   => true,
			'headers'     => $headers,
			'body'        => $payload,
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
	$response_obj = json_decode( $response['body'] );

	// decode失敗時
	if ( ! $response_obj || ! is_object( $response_obj ) ) {
		return [
			'error' => [
				'code'    => 'decode error',
				'message' => 'APIから正しいデータが返ってきませんでした。',
			],
		];
	}

	// APIからエラーが返ってきた場合
	if ( isset( $response_obj->Errors[0] ) ) {
		$error_data = $response_obj->Errors[0];
		$error_code = $error_data->Code;
		return [
			'error' => [
				'code'       => $error_code,
				'message'    => \POCHIPP\get_amazon_api_error_text( $error_code, $error_data->Message ),
			],
		];
	}

	$result_data = 'SearchItems' === $operation ? $response_obj->SearchResult : $response_obj->ItemsResult;
	if ( empty( $result_data ) ) {
		return [
			'error' => [
				'code'       => 'no result',
				'message'    => '商品データが見つかりませんでした。',
			],
		];
	}

	// エラーがなければ、必要な商品データを取得
	return \POCHIPP\set_item_data_by_amazon_api( $result_data, $keywords );
}



/**
 * 商品データを整形
 */
function set_item_data_by_amazon_api( $result_data, $keywords ) {

	$items = [];
	foreach ( $result_data->Items as $item ) {

		// if ( $only ) {
		// 	$items[] = [
		// 		'title'           => $item->ItemInfo->Title->DisplayValue ?? '',
		// 		'asin'            => $item->ASIN ?? '',
		// 		'amazon_affi_url' => $item->DetailPageURL ?? '',
		// 		// 'add_searched_at' => 'amazon',
		// 	];
		// 	continue;
		// }

		$data = [
			'keywords'    => $keywords,
			'searched_at' => 'amazon',
		];

		$data['asin'] = $item->ASIN ?? '';

		// 商品名
		$data['title'] = $item->ItemInfo->Title->DisplayValue ?? '';

		// ブランド名
		$brand        = $item->ItemInfo->ByLineInfo->Brand->DisplayValue ?? '';
		$data['info'] = $brand;

		// ブランド名なければ、著者名などの情報
		if ( ! $brand ) {
			$contributors      = '';
			$contributors_data = $item->ItemInfo->ByLineInfo->Contributors ?? [];
			foreach ( $contributors_data as $obj ) {
				if ( '' !== $contributors ) {
					$contributors .= ', ';
				}
				$contributors .= $obj->Role . ':' . $obj->Name;
			}
			$data['info'] = $contributors;
		}

		// 商品詳細 アフィURL
		$data['amazon_affi_url'] = $item->DetailPageURL ?? '';

		// 商品詳細URL memo: アフィ用のクエリが付いていないURL
		// $data['amazon_detail_url'] = 'https://www.amazon.co.jp/dp/' . $asin;

		// 価格
		$price            = $item->Offers->Listings[0]->Price->Amount ?? '';
		$data['price']    = (string) $price;
		$data['price_at'] = wp_date( 'Y/m/d H:i' );

		// 画像URL
		$data['image_url'] = $item->Images->Primary->Large->URL ?? '';

		$items[] = $data;
	}
	return $items;
}


/**
 * Amazon APIのエラーメッセージをできるだけ日本語化して返す
 * see: https://webservices.amazon.com/paapi5/documentation/troubleshooting/error-messages.html
 */
function get_amazon_api_error_text( $code, $description ) {
	switch ( $code ) {
		case 'AccessDenied':
		case 'AccessDeniedAwsUsers':
			$message = '無効なアクセスキーです。';
			break;
		case 'InvalidAssociate':
			$message = 'アクセスキーがAmazonアソシエイトに登録されていません。';
			break;
		case 'IncompleteSignature':
			$message = 'リクエストの署名情報が欠落しています。';
			break;
		case 'InvalidPartnerTag':
			$message = 'パートナータグ（トラッキングID）の認証に失敗しました。';
			break;
		case 'InvalidSignature':
			$message = 'リクエストが正しく署名されていません。';
			break;
		case 'TooManyRequests':
			$message = 'リクエスト回数が多すぎます。（PA-APIの利用制限についても確認してみてください。）';
			break;
		case 'RequestExpired':
			$message = 'リクエストの有効期限が過ぎています。';
			break;
		case 'InvalidParameterValue':
		case 'MissingParameter':
			$message = 'キーワードを入力してください';
			break;
		case 'UnknownOperation':
			$message = '要求された操作が無効なものです。';
			break;
		case 'UnrecognizedClient':
			$message = 'アクセスキーまたはシークレットキーが無効です。';
			break;
		case 'NoResults':
			$message = 'リクエストに該当する商品が見つかりませんでした。';
			break;
		default:
			$message = $description;
			break;
	}
	return $message;
}
