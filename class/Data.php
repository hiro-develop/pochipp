<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * POCHIPPクラスに継承させるデータ
 */
class Data {

	// constructor
	private function __construct() {}

	// 設定データを保持する変数
	public static $setting_data = [];

	// 設定のデフォルト値
	public static $default_data = [
		// 'box_style'        => 'default',
		'img_position'       => 'l',
		'box_layout_pc'      => 'dflt',
		'box_layout_mb'      => 'vrtcl',
		'btn_style'          => 'dflt',
		'btn_radius'         => 'off',
		'max_column_pc'      => 'fit',
		'max_column_mb'      => '1',
		'amazon_btn_text'    => 'Amazon',
		'rakuten_btn_text'   => '楽天市場',
		'yahoo_btn_text'     => 'Yahooショッピング',
		'paypay_btn_text'    => 'PayPayモール',
		'custom_btn_color'   => '#5ca250',
		'custom_btn_color_2' => '#8e59e4',
		'sale_text_effect'   => 'flash',
		// 'sale_position_pc' => 'top',
		// 'sale_position_mb' => 'inner',
	];

	// DB名
	const DB_NAME          = 'pochipp_settings'; // DB名
	const NONCE_KEY        = 'pchpp-nonce'; // NONCE名
	const SETTING_GROUP    = 'pochipp_settings'; // 設定グループ名
	const MENU_PAGE_PREFIX = 'pochipp_menu'; // 設定ページ名用のプレフィックス
	const POST_TYPE_SLUG   = 'pochipps'; // 投稿タイプスラッグ
	const TAXONOMY_SLUG    = 'pochipp_cat'; // タクソノミースラッグ
	const META_SLUG        = 'pochipp_data'; // metaスラッグ

	// タブ名 | memo: ajax の アクション名としても利用
	const TABKEYS = [
		'amazon'    => 'pochipp_search_amazon',
		'rakuten'   => 'pochipp_search_rakuten',
		'yahoo'     => 'pochipp_search_yahoo',
		'registerd' => 'pochipp_search_registerd',
	];

	// 各ボタン用のアフィ設定があるかどうか
	public static $has_affi = [
		'amazon'  => false,
		'rakuten' => false,
		'yahoo'   => false,
	];

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

}
