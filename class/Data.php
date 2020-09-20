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
		'amazon_btn_target'  => 'detail',
		'rakuten_btn_target' => 'detail',
	];

	// DB名
	const DB_NAME = 'pochipp_settings';

	// 設定グループ名
	const SETTING_GROUP = 'pochipp_settings';

	// 設定ページ名
	const MENU_PAGE = [
		'basic'  => 'pochipp_menu_basic',
		'design' => 'pochipp_menu_design',
	];

	// 楽天商品検索API の ID
	const RAKUTEN_APP_ID = '1098412079780620197';

	// post type slug
	const POST_TYPE_SLUG = 'pochipps';

	// taxonomy slug
	const TAXONOMY_SLUG = 'pochipp_cat';

	// metadata slug
	const META_SLUG = 'pochipp_data';

	// タブ名 -> ajax の アクション名と同じ
	const TABKEY_AMAZON    = 'pochipp_search_amazon';
	const TABKEY_RAKUTEN   = 'pochipp_search_rakuten';
	const TABKEY_REGISTERD = 'pochipp_search_registerd';


	// 国際化を考え、定数ではなく変数で ?
	// あとで amazon_indexs とかに改名する
	public static $search_indexes = [
		'All'                       => 'すべて',
		'AmazonVideo'               => 'Prime Video',
		'Apparel'                   => 'アパレル&ファッション雑貨',
		'Appliances'                => '電化製品',
		'Automotive'                => '車＆バイク',
		'Baby'                      => 'ベビー&マタニティ',
		'Beauty'                    => 'コスメ',
		'Books'                     => '書籍（Kindle含む）',
		'KindleStore'               => 'Kindleのみ',
		'Classical'                 => 'クラシック音楽',
		'Computers'                 => 'コンピューター',
		'CreditCards'               => 'クレジットカード',
		'DigitalMusic'              => 'デジタルミュージック',
		'Electronics'               => '家電&カメラ',
		// 'EverythingElse'          => 'ほかのすべて',
		'Fashion'                   => 'ファッション',
		'FashionBaby'               => 'ファッション（キッズ&ベビー）',
		'FashionMen'                => 'ファッション（メンズ）',
		'FashionWomen'              => 'ファッション（レディース）',
		'ForeignBooks'              => '洋書',
		'GiftCards'                 => 'ギフトカード',
		'GroceryAndGourmetFood'     => '食料と飲料',
		'HealthPersonalCare'        => 'ヘルス＆ビューティー',
		'Hobbies'                   => 'ホビー',
		'HomeAndKitchen'            => 'ホーム&キッチン',
		'Industrial'                => '産業・研究開発用品',
		'Jewelry'                   => 'ジュエリー',
		'MobileApps'                => 'Android アプリ',
		'MoviesAndTV'               => '映画とテレビ',
		'Music'                     => 'ミュージック',
		'MusicalInstruments'        => '楽器',
		'OfficeProducts'            => '文房具&オフィス用品',
		'PetSupplies'               => 'ペット用品',
		'Shoes'                     => 'シューズ&バッグ',
		'Software'                  => 'PCソフト',
		'SportsAndOutdoors'         => 'スポーツ&アウトドア',
		'ToolsAndHomeImprovement'   => 'DIY&工具&ガーデン',
		'Toys'                      => 'おもちゃ',
		'VideoGames'                => 'TVゲーム',
		'Watches'                   => '腕時計',
	];

	// memo: 数字なに？
	public static $rakuten_sorts = [
		5 => [
			'label' => '楽天標準ソート順',
			'value' => 'standard',
		],
		10 => [
			'label' => 'アフィリエイト料率順（昇順）',
			'value' => '+affiliateRate',
		],
		15 => [
			'label' => 'アフィリエイト料率順（降順）',
			'value' => '-affiliateRate',
		],
		30 => [
			'label' => 'レビュー平均順（昇順）',
			'value' => '+reviewAverage',
		],
		35 => [
			'label' => 'レビュー平均順（降順）',
			'value' => '-reviewAverage',
		],
		40 => [
			'label' => '価格順（昇順）',
			'value' => '+reviewCount',
		],
		45 => [
			'label' => '価格順（降順）',
			'value' => '-itemPrice',
		],
	];

	// インスタンス化させない
	private function __construct() {}
}
