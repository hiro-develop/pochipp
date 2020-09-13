<?php
/**
 * User: yayoi
 * Date: 2018/04/30
 * Time: 22:22
 */

class Yyi_Rinker_Abstract_Base {
	const APP_PREFIX		= 'yyi_rinker';
	const VERSION 			= '1.7.6';
	// jsとcssのファイルバージョン　本体と別のVERSIONにした
	const FILE_VERSION 		= '1.1.0';
	const LINK_POST_TYPE	= 'yyi_rinker';
	const LINK_TERM_NAME	= 'yyi_rinker_cat';

	//タブ
	const TAB_AMAZON 		= 'yyi_rinker_search_amazon';
	const TAB_RAKUTEN		= 'yyi_rinker_search_rakuten';
	const TAB_ITEMLIST		= 'yyi_rinker_search_itemlist';

	//画像と価格の保存期間
	const EXPIRED_TIME		= 24 * 60 * 60;

	const RAKUTEN_APPLICATION_ID = '1022852054992484221';

	const AMAZON_ID_INSERT_TAG		= '{{@amazon_id}}';
	const ASIN_INSERT_TAG 			= '{{@asin}}';
	const RAKUTEN_ID_INSERT_TAG		= '{{@rakuten_id}}';
	const RAKUTEN_CODE_INSERT_TAG	= '{{@rakuten_code}}';

	public $prefix					= self::APP_PREFIX;
	public $media_type				= self::APP_PREFIX;
	public $_admin_referer_column	= self::APP_PREFIX;

	//const化しているものはDBに格納しているもの
	public $shortcode_params = [
		1	=> 'asin',
		2	=> 'rakuten_itemcode',
		5	=> 'search_shop_value',
		8	=> 'free_title_url',
		10	=> 'title',
		20	=> 'post_id',
		22	=> 'rakuten_title_url',
		23	=> 'free_url_label_1_column',
		24	=> 'free_url_1',
		25	=> 'free_url_label_2_column',
		26	=> 'free_url_2',
		27	=> 'amazon_title_url',
		28	=> 'amazon_kindle_url',
		30	=> 'amazon_url',
		40	=> 'rakuten_url',
		45	=> 'yahoo_url',
		46	=> 'free_url_label_3_column',
		47	=> 'free_url_3',
		48	=> 'free_url_label_4_column',
		49	=> 'free_url_4',
		50	=> 'size',
		51	=> 'sizesw',
		52	=> 'sizesh',
		61	=> 'sizemw',
		62	=> 'sizemh',
		71	=> 'sizelw',
		72	=> 'sizelh',
		60	=> 'brand',
		70	=> 'price',
		80	=> 'price_at',
		90	=> 'alabel',
		91	=> 'klabel',
		92	=> 'rlabel',
		94	=> 'ylabel',
		210 => 'aomt',
		211 => 'aimt',
		212 => 'romt',
		213 => 'rimt',
		214 => 'yomt',
		215 => 'yimt',
		216 => 'komt',
		217 => 'kimt',
	];

	public $tabs = [
		self::TAB_AMAZON		=> 'Amazonから商品検索',
		self::TAB_RAKUTEN		=> '楽天市場から商品検索',
		self::TAB_ITEMLIST		=> '登録済み商品リンクから検索',
	];

	//商品リンクフォーム

	// const IMAGE_S_SIZE_W_COLUMN					= 'image_s_size_w_column';
	// const IMAGE_S_SIZE_H_COLUMN					= 'image_s_size_h_column';
	// const IMAGE_M_SIZE_W_COLUMN					= 'image_m_size_w_column';
	// const IMAGE_M_SIZE_H_COLUMN					= 'image_m_size_h_column';
	// const IMAGE_L_SIZE_W_COLUMN					= 'image_l_size_w_column';
	// const IMAGE_L_SIZE_H_COLUMN					= 'image_l_size_h_column';

	//Rinker設定のパラメーター
	public $option_params = [
		'is_no_reapi' => [
			'value'		=> NULL,
			'is_bool'	=> true,
			'is_digit'	=> false,
		],
		'is_no_price_disp_column' => [
			'value'		=> NULL,
			'is_bool'	=> true,
			'is_digit'	=> false,
		],
		'amazon_access_key' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'amazon_secret_key' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'amazon_traccking_id' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'rakuten_affiliate_id' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'is_detail_rakuten_url' => [
			'value'		=> NULL,
			'is_bool'	=> true,
			'is_digit'	=> false,
		],
		'is_detail_amazon_url' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'valuecommerce_linkswitch_tag' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'yahoo_pid' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'yahoo_sid' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'moshimo_amazon_id' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'moshimo_rakuten_id' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'moshimo_yahoo_id' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'moshimo_shops_check' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> true,
		],
		'is_tracking' => [
			'value'		=> NULL,
			'is_bool'	=> true,
			'is_digit'	=> false,
		],
		'design_type' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
			'is_check'	=> true,
		],
		'amazon_free_comment' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
		'rakuten_free_comment' => [
			'value'		=> NULL,
			'is_bool'	=> false,
			'is_digit'	=> false,
		],
	];

	const SEARCH_SHOP_FREE      = 6;
	const SEARCH_SHOP_AMAZON	= 10;
	const SEARCH_SHOP_RAKUTEN	= 21;



	//商品リンクカスタムフィールドの値
	public $custom_field_params = [
		5 => [
			'key'		=>  'search_shop_value',
			'label'		=> 'リンクの種類',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		8 => [
			'key'		=>  'free_title_url',
			'label'		=> 'タイトルリンクURL',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		self::SEARCH_SHOP_AMAZON => [
			'key'		=>  'asin',
			'label'		=> 'ASIN',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		11 => [
			'key'		=> 'amazon_title_url',
			'label'		=> 'Amazon商品詳細URL',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		19 => [
			'key'		=> 'amazon_kindle_url',
			'label'		=> 'AmazonKindle用URL',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		self::SEARCH_SHOP_RAKUTEN => [
			'key'		=> 'rakuten_itemcode',
			'label'		=> '楽天市場商品コード',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		22 => [
			'key'		=> 'rakuten_title_url',
			'label'		=> '楽天市場商品詳細URL',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		23	=> [
			'key'		=> 'free_url_label_1_column',
			'label'		=> '自由URLボタン名1',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		24	=> [
			'key'		=> 'free_url_1',
			'label'		=> '自由URL1',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		25	=> [
			'key'		=> 'free_url_label_3_column',
			'label'		=> '自由URL3ボタン名',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		26	=> [
			'key'		=> 'free_url_3',
			'label'		=> '自由URL3',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		29 => [
			'key'		=> 'keyword',
			'label'		=> '検索キーワード',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		30 => [
			'key'		=> 'amazon_url',
			'label'		=> 'Amazonボタン用URL',
			'is_link'	=> true,
			'is_relink'	=> true,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		40 => [
			'key'		=> 'rakuten_url',
			'label'		=> '楽天ボタン用URL',
			'is_link'	=> true,
			'is_relink'	=> true,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		45	=> [
			'key'		=> 'yahoo_url',
			'label'		=> 'Yahooボタン用商品URL',
			'is_link'	=> true,
			'is_relink'	=> true,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		46	=> [
			'key'		=> 'free_url_label_2_column',
			'label'		=> '自由URL2ボタン名',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		47	=> [
			'key'		=> 'free_url_2',
			'label'		=> '自由URL2',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		48	=> [
			'key'		=> 'free_url_label_4_column',
			'label'		=> '自由URL4ボタン名',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		49	=> [
			'key'		=> 'free_url_4',
			'label'		=> '自由URL4',
			'is_link'	=> true,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		50 => [
			'key'		=> 's_image_url',
			'label'		=> '画像（小）',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> true,
		],
		51 => [
			'key'		=> self::IMAGE_S_SIZE_W_COLUMN,
			'label'		=> '画像（小）の幅',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> true,
			'is_img'	=> false,
		],
		52 => [
			'key'		=> self::IMAGE_S_SIZE_H_COLUMN,
			'label'		=> '画像（小）の高さ',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> true,
			'is_img'	=> false,
		],
		60 => [
			'key'		=> 'm_image_url',
			'label'		=> '画像（中）',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> true,
		],
		61 => [
			'key'		=> self::IMAGE_M_SIZE_W_COLUMN,
			'label'		=> '画像（中）の幅',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> true,
			'is_img'	=> false,
		],
		62 => [
			'key'		=> self::IMAGE_M_SIZE_H_COLUMN,
			'label'		=> '画像（中）の高さ',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> true,
			'is_img'	=> false,
		],
		70 => [
			'key'		=> 'l_image_url',
			'label'		=> '画像（大）',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> true,
		],
		71 => [
			'key'		=> self::IMAGE_L_SIZE_W_COLUMN,
			'label'		=> '画像（大）の幅',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> true,
			'is_img'	=> false,
		],
		72 => [
			'key'		=> self::IMAGE_L_SIZE_H_COLUMN,
			'label'		=> '画像（大）の高さ',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> true,
			'is_img'	=> false,
		],
		80 => [
			'key'		=> 'brand',
			'label'		=> 'ブランド名',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		85 => [
			'key'		=> 'free_comment',
			'label'		=> 'フリーHTML',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> true,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		90 => [
			'key'		=> 'price',
			'label'		=> '値段',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		100 => [
			'key'		=> 'price_at',
			'label'		=> '値段取得日時',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> true,
			'is_text'	=> false,
			'is_free'	=> true,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		200 => [
			'key'		=> 'is_amazon_no_exist',
			'label'		=> 'Amazon取り扱い無し',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
		201 => [
			'key'		=> 'is_rakuten_no_exist',
			'label'		=> '楽天取り扱い無し',
			'is_link'	=> false,
			'is_relink'	=> false,
			'is_ajax'	=> false,
			'is_text'	=> false,
			'is_free'	=> false,
			'is_size'	=> false,
			'is_img'	=> false,
		],
	];

	//キーはASINと楽天商品コードのcustom_field_paramsのキーと同じにする
	public $search_shops = [
		self::SEARCH_SHOP_AMAZON	=> 'Amazon',
		self::SEARCH_SHOP_RAKUTEN	=> '楽天市場',
		self::SEARCH_SHOP_FREE		=> 'フリーリンク',
	];

	//itemlinks用パラメータ
	//const化しているものはDBに格納しているもの
	public $links_shortcode_params = [
		1	=> 'tag_id',
	];

	const SHOP_TYPE_AMAZON_KINDLE = 'amazon_kindle';
	const SHOP_TYPE_AMAZON	= 'amazon';
	const SHOP_TYPE_RAKUTEN	= 'rakuten';
	const SHOP_TYPE_YAHOO	= 'yahoo';

	const MOSHIMO_SHOP_AMAZON_VAL			= 1;
	const MOSHIMO_SHOP_RAKUTEN_VAL			= 2;
	const MOSHIMO_SHOP_YAHOO_VAL			= 4;
	const MOSHIMO_SHOP_AMAZON_KINDLE_VAL	= 8;

	public $shop_types = [
		self::SHOP_TYPE_AMAZON_KINDLE => [
			'column'	=> 'amazon_kindl_url',
			'label'		=> 'Kindle',
			'column'	=> 'moshimo_amazon_id',
			'val'		=> self::MOSHIMO_SHOP_AMAZON_KINDLE_VAL,
			'a_id'		=> '',
			'p_id'		=> 170,
			'pc_id'		=> 185,
			'pl_id'		=> 4062,
		],
		self::SHOP_TYPE_AMAZON => [
			'column'	=> 'amazon_url',
			'label'		=> 'Amazon',
			'column'	=> 'moshimo_amazon_id',
			'val'		=> self::MOSHIMO_SHOP_AMAZON_VAL,
			'a_id'		=> '',
			'p_id'		=> 170,
			'pc_id'		=> 185,
			'pl_id'		=> 4062,
		],
		self::SHOP_TYPE_RAKUTEN => [
			'column'	=> 'rakuten_url',
			'label'		=> '楽天市場',
			'column'	=> 'moshimo_rakuten_id',
			'val'		=> self::MOSHIMO_SHOP_RAKUTEN_VAL,
			'a_id'		=> '',
			'p_id'		=> 54,
			'pc_id'		=> 54,
			'pl_id'		=> 616,
		],
		self::SHOP_TYPE_YAHOO => [
			'column'	=> 'yahop_url',
			'label'		=> 'Yahooショッピング',
			'column'	=> 'moshimo_yahoo_id',
			'val'		=> self::MOSHIMO_SHOP_YAHOO_VAL,
			'a_id'		=> '',
			'p_id'		=> 1225,
			'pc_id'		=> 1925,
			'pl_id'		=> 18502,
		],
	];


	public $search_indexes = [
		'All'						=> 'すべて',
		'AmazonVideo'				=> 'Prime Video',
		'Apparel'					=> 'アパレル&ファッション雑貨',
		'Appliances'				=> '電化製品',
		'Automotive'				=> '車＆バイク',
		'Baby'						=> 'ベビー&マタニティ',
		'Beauty'					=> 'コスメ',
		'Books'						=> '書籍（Kindle含む）',
		'KindleStore'				=> 'Kindleのみ',
		'Classical'					=> 'クラシック音楽',
		'Computers'					=> 'コンピューター',
		'CreditCards'				=> 'クレジットカード',
		'DigitalMusic'				=> 'デジタルミュージック',
		'Electronics'				=> '家電&カメラ',
		//'EverythingElse'			=> 'ほかのすべて',
		'Fashion'					=> 'ファッション',
		'FashionBaby'				=> 'ファッション（キッズ&ベビー）',
		'FashionMen' 				=> 'ファッション（メンズ）',
		'FashionWomen' 				=> 'ファッション（レディース）',
		'ForeignBooks'				=> '洋書',
		'GiftCards'					=> 'ギフトカード',
		'GroceryAndGourmetFood'		=> '食料と飲料',
		'HealthPersonalCare'		=> 'ヘルス＆ビューティー',
		'Hobbies'					=> 'ホビー',
		'HomeAndKitchen'			=> 'ホーム&キッチン',
		'Industrial'				=> '産業・研究開発用品',
		'Jewelry'					=> 'ジュエリー',
		'MobileApps'				=> 'Android アプリ',
		'MoviesAndTV'				=> '映画とテレビ',
		'Music'						=> 'ミュージック',
		'MusicalInstruments'		=> '楽器',
		'OfficeProducts'			=> '文房具&オフィス用品',
		'PetSupplies'				=> 'ペット用品',
		'Shoes'						=> 'シューズ&バッグ',
		'Software'					=> 'PCソフト',
		'SportsAndOutdoors'			=> 'スポーツ&アウトドア',
		'ToolsAndHomeImprovement'	=> 'DIY&工具&ガーデン',
		'Toys'						=> 'おもちゃ',
		'VideoGames'				=> 'TVゲーム',
		'Watches'					=> '腕時計',
	];

	const DESIGN_TYPE_NORMAL = 0;
	const DESIGN_TYPE_NONE = 99;
	public $design_types = [
		self::DESIGN_TYPE_NONE => ['label' => 'デザインなし', 'func' => null],
		self::DESIGN_TYPE_NORMAL => ['label' => 'ノーマル', 'func' => null],
	];

	

	public function now() {
		return date('Y-m-d H:i:s');
	}
}
