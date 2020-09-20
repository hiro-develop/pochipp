<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Amazon APIから検索
 */
// \POCHIPP::ACTION_NAME['amazon'] とかでアクション名 とれるようにする
add_action( 'wp_ajax_pochipp_search_amazon', '\POCHIPP\search_from_amazon_api' );


/**
 * AmazonAPIから商品データを取得する
 */
function search_from_amazon_api() {

	$keywords     = \POCHIPP::array_get( $_GET, 'keywords', '' );
	$search_index = \POCHIPP::array_get( $_GET, 'search_index', '' );

	// 使用可能な インデックスカテゴリーかどうか
	if ( ! isset( \POCHIPP::$search_indexes[ $search_index ] ) ) {
		$search_index = 'All';
	}

	// 登録済み商品
	$registerd_items = \POCHIPP::get_registerd_items( [
		'keywords' => $keywords,
		'count'    => 2, // memo: ２個まで取得。<- 少ない？
	] );

	// 検索結果
	$searched_items = \POCHIPP\generate_amazon_datas_from_json( $keywords, $search_index );

	wp_die( json_encode( [
		'registerd_items' => $registerd_items ?: [],
		'searched_items'  => $searched_items ?: [],
	] ) );
}


/**
 * PA-APIv5に対応
 * AmazonAPIから商品データを取得
 */
function generate_amazon_datas_from_json( $keywords = '', $search_index = 'All' ) {

	// 空白の場合
	if ( 0 === strlen( trim( $keywords ) ) ) {
		// yyi: wp_die 使う
		wp_die( json_encode( [
			'error' => [
				'code'       => '',
				'message_jp' => 'キーワードを入力してください',
			],
		] ) );
	}

	$searchItemRequest = new \SearchItemsRequest();

	$searchItemRequest->Keywords    = $keywords;
	$searchItemRequest->SearchIndex = $search_index;

	$datas = \POCHIPP\get_json_from_amazon_api( 'SearchItems', $searchItemRequest );

	if ( is_array( $datas ) && isset( $datas['error'] ) ) {
		return $datas;
	} else {

		return \POCHIPP\set_data_for_amazon( $datas, $keywords );
	}
}


/**
 * for PA-APIv5
 * jsonからapiのデータを取得する
 *
 * @param $operation 'SearchItems' or 'GetItems' を受け取る
 * @throws Exception
 */
function get_json_from_amazon_api( $operation, $request ) {

	// 「memo: 設定はあとで追加。とりあえず動くように自分の キー を直代入
	$access_key   = \POCHIPP::get_setting( 'amazon_access_key' );
	$secret_key   = \POCHIPP::get_setting( 'amazon_secret_key' );
	$traccking_id = \POCHIPP::get_setting( 'amazon_traccking_id' );
	// amazon_traccking_id
	// $traccking_id = get_option( 'pochipp_amazon_traccking_id' ) ?: 'irepos-22';

	$request->PartnerType = 'Associates';
	$request->PartnerTag  = $traccking_id;
	$request->Resources   = [
		'Images.Primary.Small',
		'Images.Primary.Medium',
		'Images.Primary.Large',
		'Images.Variants.Small',
		'Images.Variants.Medium',
		'Images.Variants.Large',
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

	$header_string = '';
	$curl_headers  = [];
	foreach ( $headers as $key => $value ) {
		$curl_headers[] = $key . ': ' . $value;
		$header_string .= $key . ': ' . $value . "\r\n";
	}

	// cURLがインストールされていれば利用する
	if ( function_exists( 'curl_version' ) ) {
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, 'https://' . $host . $path );
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $curl_headers );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, true );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

		$response = curl_exec( $curl );
		$info     = curl_getinfo( $curl );
		$error_no = curl_errno( $curl );

		if ( $error_no === CURLE_OPERATION_TIMEDOUT ) {
			return [
				'error' => [
					'code'       => 'タイムアウトしました',
					'message'    => 'タイムアウトしました',
					'message_jp' => 'タイムアウトしました',
				],
			];
		}
		if ( $error_no !== CURLE_OK ) {
			return [
				'error' => [
					'code'       => 'cURLエラー',
					'message'    => intval( $error_no ) . ':cURLエラー',
					'message_jp' => intval( $error_no ) . ':cURLエラー',
				],
			];
		}

		$status_code = $info['http_code'];
		$header_size = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
		$res         = substr( $response, $header_size );
		curl_close( $curl );

	} elseif ( ini_get( 'allow_url_fopen' ) == '1' ) {

		$params = [
			'http' => [
				'header'        => $header_string,
				'method'        => 'POST',
				'content'       => $payload,
				'ignore_errors' => true,
			],
		];

		$stream = stream_context_create( $params );
		$res    = @file_get_contents( 'https://' . $host . $path, false, $stream );
		preg_match( '/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches );
		$status_code = $matches[1];

	} else {

		return [
			'error' => [
				'code'       => '環境エラー',
				'message'    => 'php.iniのallow_url_fopenをONにするかcURLインストールしてください',
				'message_jp' => 'php.iniのallow_url_fopenをONにするかcURLインストールしてください',
			],
		];
	}

	// レスポンスなしの場合
	if ( ! $res ) {
		return [
			'error' => [
				'code'       => '外部のファイルが読み込めません',
				'message'    => '【parser Error】XMLが正しくありません',
				'message_jp' => '【parser Error】XMLが正しくありません',
			],
		];
	}

	$json_datas = json_decode( $res );

	// データの形式がおかしい場合
	if ( ! $json_datas && is_array( $json_datas ) ) {
		return [
			'error' => [
				'code'       => 'データ取得不可',
				'message'    => 'APIから正しいデータが返ってきません',
				'message_jp' => 'APIから正しいデータが返ってきません',
			],
		];
	}

	// その他、エラーが返ってきた場合
	if ( isset( $json_datas->Errors[0] ) ) {
		$code       = $json_datas->Errors[0]->Code;
		$en_message = $json_datas->Errors[0]->Message;
		$message_ip = \POCHIPP\amazon_api_json_errors( $code, $en_message );
		return [
			'error' => [
				'code'       => $code,
				'message'    => $en_message,
				'message_jp' => $message_ip,
			],
		];
	}

	// AmazonAPI ステータスが 200 以外の場合（何か問題が発生している時）
	$status_code = intval( $status_code );
	if ( $status_code !== 200 ) {
		return [
			'error' => [
				'code'       => 'AmazonAPIのステータスエラー',
				'message'    => $status_code . ':AmazonAPIのステータスエラー',
				'message_jp' => $status_code . ':AmazonAPIのステータスエラー',
			],
		];
	}

	// OK.
	return $json_datas;
}



/**
 * for PA-APIv5
 * jsonのデータを整形
 *
 * @param $json_datas
 * @param bool       $is_new
 * @return array
 */
function set_data_for_amazon( $json_datas, $keyword, $is_new = true ) {

	$items = [];
	foreach ( $json_datas->SearchResult->Items as $item ) {
		$data = [];

		// 新規の時だけ登録する部分
		// memo: ↑ 「新規の時」、とは...？
		if ( $is_new ) {
			$asin         = $item->ASIN ?? '';
			$data['asin'] = (string) $asin;

			// 商品名
			$item_title    = $item->ItemInfo->Title->DisplayValue ?? '';
			$data['title'] = (string) $item_title;

			// ブランド名
			$brand         = $item->ItemInfo->ByLineInfo->Brand->DisplayValue ?? '';
			$data['brand'] = (string) $brand;

			// 検索結果URL
			$data['amazon_url']  = \POCHIPP::generate_amazon_original_link( $keyword );
			$data['rakuten_url'] = \POCHIPP::generate_rakuten_original_link( $keyword );
			$data['yahoo_url']   = \POCHIPP::generate_yahoo_original_link( $keyword );

			// 商品詳細URL memo: アフィ用のクエリが付いていないURL
			$data['amazon_detail_url'] = 'https://www.amazon.co.jp/dp/' . $asin;

		}

		// memo: たぶん商品カテゴリーの取得 & Kindleの場合は専用のkeyでデータ保存。
		//       現状だとKindleじゃない時 amazon_kindle_url は存在しないが、とりあえず空で持たせておいてもいい？
		if ( isset( $item->ItemInfo->Classifications->ProductGroup->DisplayValue ) ) {
			$group                 = (string) $item->ItemInfo->Classifications->ProductGroup->DisplayValue;
			$data['product_group'] = $group;

			// Kindle商品のURL（アフィ用のクエリ付き）
			if ( 'Digital Ebook Purchas' === $group ) {
				$data['amazon_kindle_url'] = (string) $item->DetailPageURL;
			}
		}

		// 商品詳細URL
		// memo: アフィ用のクエリが付いたURL
		$data['amazon_title_url'] = (string) $item->DetailPageURL;

		if ( isset( $item->Offers->Listings[0]->Price->Amount ) ) {
			$price            = $item->Offers->Listings[0]->Price->Amount;
			$data['price']    = (string) $price;
			$data['price_at'] = date_i18n( 'Y/m/d H:i:s' );
		} else {
			$data['price']    = '';
			$data['price_at'] = '';
		}

		if ( isset( $item->Images->Primary ) ) {
			$data['s_image_url'] = (string) $item->Images->Primary->Small->URL;
			$data['m_image_url'] = (string) $item->Images->Primary->Medium->URL;
			$data['l_image_url'] = (string) $item->Images->Primary->Large->URL;

			// $data[self::IMAGE_S_SIZE_W_COLUMN] = (string) $item->Images->Primary->Small->Width;
			// $data[self::IMAGE_S_SIZE_H_COLUMN] = (string) $item->Images->Primary->Small->Height;
			// $data[self::IMAGE_M_SIZE_W_COLUMN] = (string) $item->Images->Primary->Medium->Width;
			// $data[self::IMAGE_M_SIZE_H_COLUMN] = (string) $item->Images->Primary->Medium->Height;
			// $data[self::IMAGE_L_SIZE_W_COLUMN] = (string) $item->Images->Primary->Large->Width;
			// $data[self::IMAGE_L_SIZE_H_COLUMN] = (string) $item->Images->Primary->Large->Height;

		} else {

			$data['s_image_url'] = '';
			$data['m_image_url'] = '';
			$data['l_image_url'] = '';

			// $data[self::IMAGE_S_SIZE_W_COLUMN] = '';
			// $data[self::IMAGE_S_SIZE_H_COLUMN] = '';
			// $data[self::IMAGE_M_SIZE_W_COLUMN] = '';
			// $data[self::IMAGE_M_SIZE_H_COLUMN] = '';
			// $data[self::IMAGE_L_SIZE_W_COLUMN] = '';
			// $data[self::IMAGE_L_SIZE_H_COLUMN] = '';
		}

		$items[] = $data;
	}
	return $items;
}


/**
 * For PA-API v5
 * Amazon APIのエラーメッセージを返します
 *
 * @param $code
 * @param $en_message
 */
function amazon_api_json_errors( $code, $en_message ) {
	switch ( $code ) {
		case 'AccessDenied':
		case 'AccessDeniedAwsUsers':
			$message = 'このアクセスキーは、Product Advertising APIにアクセスするために有効になっていません。AWS認証情報を利用している場合はProduct Advertising APIで取得し直してください。';
			break;
		case 'InvalidAssociate':
			$message = 'アクセスキー[アクセスキー]は、承認されたアソシエイトストアのプライマリにマップされていません。';
			break;
		case 'IncompleteSignature':
			$message = '要求の署名には、必要なコンポーネントのすべてが含まれていませんでした。';
			break;
		case 'InvalidPartnerTag':
			$message = '認証情報が合いません。[設定]-[[Rinker設定]-[Amazon][アソシエイツのトラッキングID][トラッキングID]を正しいものに設定してください。';
			break;
		case 'InvalidSignature"':
			$message = 'アクセスキーIDが存在しません。[設定]-[[Rinker設定]-[Amazon][API][シークレットキー]を正しいものに設定してください。';
			break;

		case 'TooManyRequests':
			$message = 'リクエスト回数が多すぎます。';
			break;
		case 'RequestExpired':
			$message = 'リクエストの有効期限が過ぎています。';
			break;
		case 'InvalidParameterValue':
		case 'MissingParameter':
			$message = 'キーワードを入力してください';
			break;
		case 'UnrecognizedClient':
			$message = 'アクセスキーIDが合いません。[設定]-[[Rinker設定]-[Amazon][API][アクセスキーID]を正しいものに設定してください。';
			break;
		case 'UnknownOperationException':
			$message = '要求された操作は無効です。操作名が正しく入力されていることを確認してください。';
			break;
		case 'NoResults':
			$message = '該当する商品がありません';
			break;
		case 'UnrecognizedClientException':
			$message = 'アクセスキーまたはセキュリティトークンが無効です。';
			break;
		default:
			$message = $en_message;
			break;
	}
	return $message;
}
