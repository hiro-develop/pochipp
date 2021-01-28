<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * POCHIPPクラスに継承させるデータ
 */
class Data {

	// 設定データを保持する変数
	public static $setting_data = [];

	// 設定のデフォルト値
	public static $default_data = [
		// 'box_style'        => 'default',
		'img_position'     => 'l',
		'box_layout_pc'    => 'dflt',
		'box_layout_mb'    => 'vrtcl',
		'btn_style'        => 'dflt',
		'btn_radius'       => 'off',
		'max_column_pc'    => 'fit',
		'max_column_mb'    => '1',
		// 'sale_position_pc' => 'top',
		// 'sale_position_mb' => 'inner',
		'amazon_btn_text'  => 'Amazon',
		'rakuten_btn_text' => '楽天市場',
		'yahoo_btn_text'   => 'Yahooショッピング',
		'custom_btn_color' => '#63a958',
		'sale_text_effect' => 'flash',
	];

	// DB名
	const DB_NAME = 'pochipp_settings';

	// 設定グループ名
	const SETTING_GROUP = 'pochipp_settings';

	// 設定ページ名用のプレフィックス
	const MENU_PAGE_PREFIX = 'pochipp_menu';

	// post type slug
	const POST_TYPE_SLUG = 'pochipps';

	// taxonomy slug
	const TAXONOMY_SLUG = 'pochipp_cat';

	// metadata slug
	const META_SLUG = 'pochipp_data';

	// nonce
	const NONCE_KEY = 'pchpp-nonce';

	// タブ名 -> ajax の アクション名と同じ
	const TABKEY_AMAZON    = 'pochipp_search_amazon';
	const TABKEY_RAKUTEN   = 'pochipp_search_rakuten';
	const TABKEY_REGISTERD = 'pochipp_search_registerd';

	// ライセンス関連で使用するURL
	// const IS_VALID_LICENSE_URL = 'https://asia-northeast1-pochipp-84843.cloudfunctions.net/isValidLicense';

	// Amazonカテゴリー
	// public static $amazon_indexs = [
	// 	'All'            => 'すべて',
	// 	// ...
	// ];

	// 楽天APIに指定できるソートパラメータ
	// public static $rakuten_sorts = [
	// 	'standard'       => '楽天標準ソート順',
	// 	'+itemPrice'     => '価格順（昇順）',
	// 	'-itemPrice'     => '価格順（降順）',
	// 	'+affiliateRate' => 'アフィリエイト料率順（昇順）',
	// 	'-affiliateRate' => 'アフィリエイト料率順（降順）',
	// 	'+reviewAverage' => 'レビュー平均順（昇順）',
	// 	'-reviewAverage' => 'レビュー平均順（降順）',
	// ];

	// インスタンス化させない
	private function __construct() {}
}
