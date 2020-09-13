<?php
/**
 * User: yayoi
 * Date: 2018/02/11
 * Time: 1:57
 */
require_once dirname( __FILE__ ) . '/yyi_rinker_abstract.php';
require_once dirname( __FILE__ ) . '/lib/paapiv5.php';

class Yyi_Rinker_Plugin extends Yyi_Rinker_Abstract_Base {

	public $admin_style_css_url		= '';
	public $style_css_ur			= '';
	public $loading_img_url			= '';

	public $script_event_tracking_url = '';

	public $moshimo_shops_check = null;

	public $is_tracking = false;

	public $amazon_traccking_id = '';
	public $rakuten_affiliate_id = '';

	public $yahoo_linkswitch = '';
	public $yahoo_pid = '';
	public $yahoo_sid = '';

	public $is_yahoo_id = false;
	public $style_css_url = '';

	//フリーHTMLのデフォルト
	public $freelink_free_comment = '';
	public $amazon_free_comment = '';
	public $rakuten_free_comment = '';

	//価格を非表示にするかどうか
	public $is_no_price_disp_column = false;

	//再取得するかどうか
	public $is_no_reapi_column = false;

	//target blankにするかどうか
	public $is_target_blank = false;

	public $design_type_val = self::DESIGN_TYPE_NORMAL;

	public function __construct(){
		$this->admin_style_css_url		= plugins_url( 'css/admin_style.css', __FILE__ ) . '?v=' . self::FILE_VERSION;
		$this->style_css_url			= plugins_url( 'css/style.css', __FILE__ ). '?v=' . self::FILE_VERSION;
		$this->script_admin_rinker_url	= plugins_url( 'js/admin-rinker.js', __FILE__ ). '?v=' . self::FILE_VERSION;
		$this->loading_img_url			= plugins_url( 'img/loading.gif', __FILE__ );

		//基本設定
		$this->is_no_price_disp_column		= !!( get_option( $this->option_column_name( 'is_no_price_disp_column' ), false ) );
		$this->is_no_reapi_column			= !!( get_option( $this->option_column_name( 'is_no_reapi' ), false ) );
		//もしもの設定
		$this->moshimo_amazon_id	= trim( get_option( $this->option_column_name( 'moshimo_amazon_id' ), '' ) );
		$this->moshimo_yahoo_id		= trim( get_option( $this->option_column_name( 'moshimo_yahoo_id' ), '' ) );
		$this->moshimo_rakuten_id	= trim( get_option( $this->option_column_name( 'moshimo_rakuten_id' ), '' ) );
		//もしもにするかどうか
		$this->moshimo_shops_check	= trim( get_option( $this->option_column_name( 'moshimo_shops_check' ) , 0 ) );

		//トラッキング設定
		$this->is_tracking = !!get_option( $this->option_column_name( 'is_tracking' ) , false );
		$this->script_event_tracking_url	= plugins_url( 'js/event-tracking.js', __FILE__ ) . '?v=' . self::FILE_VERSION;

		//AmazonトラッキングID
		$this->amazon_traccking_id	= get_option( $this->option_column_name( 'amazon_traccking_id' ) );
		//楽天アフィリエイトID
		$this->rakuten_affiliate_id	= get_option( $this->option_column_name( 'rakuten_affiliate_id' ) );

		//Yahoo
		$this->yahoo_linkswitch = get_option( $this->option_column_name( 'valuecommerce_linkswitch_tag' ) );
		$this->yahoo_pid = get_option( $this->option_column_name( 'yahoo_pid' ) );
		$this->yahoo_sid = get_option( $this->option_column_name( 'yahoo_sid' ) );

		//フリーHTML
		$this->amazon_free_comment = get_option( $this->option_column_name( 'amazon_free_comment' ) );
		$this->rakuten_free_comment = get_option( $this->option_column_name( 'rakuten_free_comment' ) );

		

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
			
			add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
			add_action( 'admin_head', array( $this, 'add_thickbox' ) );

			add_action( 'save_post_'. self::LINK_POST_TYPE, array( $this, 'save_links_fields' ) );

			add_action( 'media_upload_' . $this->media_type,  array($this, 'media_upload_iframe') );
			

			// add_filter( $this->add_prefix( 'media_upload_tabs' ), array( $this, 'add_item_list_search_tab' ) );

			//ダッシュボード
			add_action( 'wp_dashboard_setup', array( $this, 'info_dashboard_widgets' ) );

			//商品リンク一覧にショートコードと利用記事を表示
			add_filter( 'manage_' . self::LINK_POST_TYPE . '_posts_columns', array( $this, 'manage_linkinfo_posts_columns' ));
			add_action( 'manage_' . self::LINK_POST_TYPE . '_posts_custom_column', array( $this, 'manage_linkinfo_post_custom_column' ), 10, 2 );

			add_filter( 'manage_edit-' . self::LINK_TERM_NAME . '_columns', array( $this, 'manage_term_linkinfo_posts_columns' ));
			add_filter( 'manage_' . self::LINK_TERM_NAME . '_custom_column', array( $this, 'manage_term_linkinfo_post_custom_column' ), 10, 3);

			add_action( 'restrict_manage_posts', array( $this, 'add_search_term_dropdown' ), 10, 2 );

			
		} else {
			
			add_action( 'wp_head', array( $this, 'add_linkswitch_tag' ) );
			add_action( 'wp_head', array( $this, 'switch_design_type' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_front_styles' ) );
		}

		add_action( 'init', array( $this, 'set_esign_types' ) );
		add_shortcode( 'itemlink', array( $this, 'shotcode' ) );
		add_shortcode( 'itemlinks', array( $this, 'linksshotcode' ) );

		add_filter( $this->add_prefix( 'meta_data_update_init' ), array( $this, 'check_id_amazon_detail' ), 10 );
		add_filter( $this->add_prefix( 'meta_data_update_init' ), array( $this, 'check_id_rakuten_detail' ), 11 );
		add_filter( $this->add_prefix( 'meta_data_update' ), array( $this, 'is_no_price_disp' ), 9, 2 );
		add_filter( $this->add_prefix( 'meta_data_update' ), array( $this, 'upate_html_data' ), 10, 2 );

		//デザイン追加用フィルター
		add_filter('yyi_rinker_custom_design_types', array( $this, 'add_rinker_design'), 8);


		//shortcode_paramsの設定
		$this->custom_shortcode_params();

		//カスタムフィールドの設定
		$this->custom_field_params();

		//target blankの設定
		$this->set_is_target_blank();

		//shop_typ 設定
		$this->shop_types();

	}

	public function add_rinker_design( $design_types ) {
		//labelは設定selectボックスに表示されます
		//funcはCSSを返す関数名を記載します
		$design_types[10] = ['label' => 'スタイルアップ', 'func' => 'yyi_rinker_style_up_design'];
		return $design_types;
	}


	/**
	 * ダッシュボードにRinker情報を追加
	 **/
	function info_dashboard_widgets() {
		wp_add_dashboard_widget( 'custom_info_widget', 'Rinker', array( $this, 'dashboard_detail' ) );
	}

	/**
	 * ダッシュボードにメッセージを表示
	 * @return string
	 */
	function dashboard_detail() {
		$info_dashboard_path = dirname( __FILE__ ) . '/parts/info-dashboard.php';
		$info_dashboard_path = apply_filters( $this->add_prefix( 'load_info_dashboard' ),  $info_dashboard_path );

		if ( validate_file( $info_dashboard_path ) !== 0) {
			return '';
		}

		ob_start();
		include( $info_dashboard_path );
		$info_dashboard = ob_get_contents();
		ob_end_clean();
		echo $info_dashboard;
	}

	/**
	 * ショップタイプ設定
	 */
	public function shop_types() {
		foreach ($this->shop_types as $key => $value ) {
			$this->shop_types[ $key ]['a_id'] =  trim( get_option( $this->option_column_name( $value[ 'column' ] ), '' ) );
		}
		$shop_types = apply_filters( $this->add_prefix( 'custom_shop_types' ), $this->shop_types );
		$this->shop_types = $shop_types;
	}

	/**
	 * デザインタイプ設定
	 */
	public function set_esign_types() {
		$design_types = apply_filters( $this->add_prefix( 'custom_design_types' ), $this->design_types );
		$this->design_types = $design_types;
	}

	/**
	 * ショートコードパラメーターを変更
	 */
	public function custom_shortcode_params() {
		$shortcode = apply_filters( $this->add_prefix( 'cusrom_shortcode_params' ), $this->shortcode_params );
		ksort( $shortcode );
		$this->shortcode_params = $shortcode;
	}

	/**
	 * カスタムフィールドの種類を変更
	 */
	public function custom_field_params() {
		$fields = $this->custom_field_params = apply_filters( $this->add_prefix( 'custom_field_params' ), $this->custom_field_params );
		ksort( $fields );
		$this->custom_field_params = $fields;
	}

	/**
	 * target="_blank"にするかどうか
	 */
	public function set_is_target_blank() {
		$this->is_target_blank = apply_filters( $this->add_prefix( 'is_target_blank' ), $this->is_target_blank );
		return $this->is_target_blank;
	}

	public function get_rel_target_text() {
		if ( $this->is_target_blank ) {
			$rel_target_text = 'rel="nofollow noopener" target="_blank"';
		} else {
			$rel_target_text = 'rel="nofollow"';
		}
		$rel_target_text =  apply_filters( $this->add_prefix( 'rel_target_text' ), $rel_target_text, $this->is_target_blank );
		return $rel_target_text;
	}

	

	public function remove_meta_boxes() {
		remove_meta_box( 'slugdiv', self::LINK_POST_TYPE, 'normal' ); // スラッグ
		remove_meta_box( 'postdivrich', self::LINK_POST_TYPE, 'normal' );
	}


	/**
	 * カスタム投稿で本文がなくてもthickboxを使えるようにする
	 */
	function add_thickbox() {
		add_thickbox();
	}




	/**
	 * デザインを変更する
	 */
	public function switch_design_type() {
		if ($this->design_type_val !== self::DESIGN_TYPE_NORMAL) {
			if ( isset($this->design_types[$this->design_type_val]) ) {
				$func = $this->design_types[$this->design_type_val]['func'];
				if (function_exists($func)) {
					echo '<style>';
					echo htmlspecialchars($func(), ENT_NOQUOTES);
					echo '</style>';
				}
			}
		}
	}

	/**
	 * 商品リンクの一覧にショートコードを表示
	 * @param $posts_columns
	 *
	 * @return mixed
	 */
	public function manage_linkinfo_posts_columns( $posts_columns ) {
		$posts_columns['shorcode'] = 'ショートコード<span class="yyi-rinker-small">(クリックでコピー)</span>';
		$posts_columns['used_post_id'] = '利用記事';
		return $posts_columns;
	}

	/**
	 * 記事へのリンクを表示
	 * @param $column_name
	 * @param $post_id
	 */
	function manage_linkinfo_post_custom_column( $column_name, $post_id ) {
		global $wpdb;
		if ( $column_name == 'shorcode' ) {
			echo '<textarea readonly="readonly" class="yyi-rinker-list-shortcode">[itemlink post_id="' . esc_html( $post_id ) . '"]</textarea>';
		}
		if ( $column_name == 'used_post_id' ) {
			$shortcode =  '%[itemlink post_id="' . $post_id . '"%';
			$post_types = [ 'post', 'page' ];
			$post_types = apply_filters( $this->add_prefix( 'manage_linkinfo_post_types' ), $post_types );
			if (!is_array($post_types) || count( $post_types ) === 0 ) {
				return '';
			}
			$sql_post_type_text = implode(',', array_fill(0, count( $post_types ) ,  '%s') );
			$values = [$shortcode, $shortcode, $shortcode];
			$values = array_merge($values, $post_types);

			$results = $wpdb->get_results($wpdb->prepare("
SELECT SQL_CALC_FOUND_ROWS {$wpdb->posts}.ID
FROM {$wpdb->posts}
WHERE 1=1 AND ((({$wpdb->posts}.post_title
LIKE '%s') OR ({$wpdb->posts}.post_excerpt 
LIKE '%s') OR ({$wpdb->posts}.post_content 
LIKE '%s'))) AND {$wpdb->posts}.post_type IN ({$sql_post_type_text}) AND ({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'future' OR {$wpdb->posts}.post_status = 'draft' OR {$wpdb->posts}.post_status = 'pending' OR {$wpdb->posts}.post_author = 1 AND {$wpdb->posts}.post_status = 'private') 
ORDER BY {$wpdb->posts}.ID ASC", $values));


			foreach ($results AS $value) {
				$post_id = $value->ID;
				$title = get_the_title( $post_id );
				$title = strlen($title) === 0 ? '(タイトルなし)' : $title;
				$src = admin_url() . 'post.php?post=' . esc_attr( $post_id ) . '&action=edit';
				echo '<a href="' . $src . '" target="_blank" data-title="' . $title . '">[' . esc_html( $post_id ) . ']</a>';
			}
		}
	}

	/**
	 * 商品リンクカスタム投稿にタクソノミーでの絞り込みをつけました
	 * @param $post_type
	 */
	public function add_search_term_dropdown( $post_type ) {
		if ( self::LINK_POST_TYPE === $post_type ) {
			$slug = get_query_var( self::LINK_TERM_NAME );
			wp_dropdown_categories( array(
				'show_option_all'	=> '商品リンクカテゴリー',
				'selected'			=> $slug,
				'name'				=> self::LINK_TERM_NAME,
				'taxonomy'			=> self::LINK_TERM_NAME,
				'value_field'		=> 'slug',
			));
		}
	}

	/**
	 * 商品リンクのカテゴリ一覧にショートコードを表示
	 * @param $posts_columns
	 *
	 * @return mixed
	 */
	public function manage_term_linkinfo_posts_columns( $posts_columns ) {
		$posts_columns['shorcode'] = '<div>テキストリンク</div><span class="yyi-rinker-small">(クリックでコピー)</span>';
		return $posts_columns;
	}

	/**
	 * カテゴリのショートコードを表示
	 * @param $column_name
	 * @param $post_id
	 */
	function manage_term_linkinfo_post_custom_column($string,  $column_name, $tag_id ) {
		if ( $column_name == 'shorcode' ) {
			echo '<textarea readonly="readonly" class="yyi-rinker-term-list-shortcode">[itemlinks tag_id="' . esc_html( $tag_id ) . '"]</textarea>';
		}
	}

	

	/**
	 * 	カスタム投稿　self::LINK_POST_TYP　の
	 *  カスタムフィールド情報を更新
	 * @param $post_id
	 */
	function save_links_fields( $post_id ) {
		$this->save_post_meta_links($post_id, $_POST, false, false);
	}

	/**
	 * カスタム投稿　self::LINK_POST_TYP　のカスタムフィールドに値を保存します
	 * @param $post_id
	 * @param $params
	 */
	function save_post_meta_links($post_id, $params, $is_create = true, $is_force_update = true) {
		//更新時にはキャッシュ削除
		delete_transient( $this->add_prefix( 'itemlink_' . $post_id ) );
		//メインのページだけ更新させる
		if ( $this->array_get($_POST, 'yyi_rinker_from_page', '') === 'main' || $is_force_update) {
			$new_datas = [];
			foreach ($this->custom_field_params AS $index => $values) {
				if ( $value = $this->array_get($params, $values['key'], false ) ) {
					if ( $index === 'is_amazon_no_exist' || $index === 'is_rakuten_no_exist' ) {
						$new_datas[ $values[ 'key' ] ] = !!$value ? 1 : '';
					} else {
						$new_datas[ $values[ 'key' ] ] = $value;
					}

					if ($index === 'search_shop_value' ) {
						$new_datas[ $values[ 'key' ] ] = intval( $value );
					}
					update_post_meta($post_id, $this->custom_field_column_name( $values['key'] ), $value );
				} else {
					$new_datas[$values['key']] = null;
					delete_post_meta($post_id, $this->custom_field_column_name( $values['key'] ) );
				}
			}

			$meta_datas = $this->get_tansient_meta_data( $post_id, [], $new_datas, false );

			if ( $is_create ) {
				//キャッシュにいれる
				set_transient($this->add_prefix( 'itemlink_' . $post_id), $meta_datas, self::EXPIRED_TIME );
			}
		}
	}


	

	public function links_shortcode_keys() {
		$values = [];
		foreach( $this->links_shortcode_params AS $key => $val) {
			$values[$val] = '';
		}
		return $values;
	}

	/**
	 * ショートコード itemlinks　からリンクHTMLを作成
	 * @param $att
	 *
	 * @return array|mixed|null|string|void
	 */
	public function linksshotcode( $att )
	{
		$shortcodes = $this->links_shortcode_keys();
		$atts = shortcode_atts( $shortcodes, $att );

		$arg = array(
			'post_type' => self::LINK_POST_TYPE,
			'tax_query' => array(
				array(
					'taxonomy' => self::LINK_TERM_NAME,
					'terms' => array( $atts[ 'tag_id' ] ),
					'field'=>'term_id',
				)
			),
			'orderby'	=> 'date',
			'order'		=> 'ASC',
			'nopaging'	=> true,
		);

		$template_path = dirname( __FILE__ ) . '/template/links-template-default.php';

		if ( validate_file( $template_path ) !== 0) {
			return '';
		}

		ob_start();
		include( $template_path );
		$template = ob_get_contents();
		ob_end_clean();
		return $template;
	}

	public function shortcode_keys() {
		$values = [];
		foreach( $this->shortcode_params AS $key => $val) {
			$values[$val] = '';
		}
		return $values;
	}

	/**
	 * ショートコード itemlin　からリンクHTMLを作成
	 * @param $att
	 *
	 * @return array|mixed|null|string|void
	 */
	public function shotcode( $att ) {
		$shortcodes = $this->shortcode_keys();

		$atts = shortcode_atts( $shortcodes, $att );
		$atts = apply_filters( $this->add_prefix( 'update_attribute' ), $atts );

		if (get_post_type() === self::LINK_POST_TYPE) {
			$post_id = get_the_ID();
		} else {
			$post_id = $this->array_get($atts, 'post_id', 0);
		}
		$category_classes = [];
		foreach( get_the_category() AS $category ) {
			$category_classes[] = 'yyi-rinker-catid-' . intval( $category->cat_ID );
		}
		$status = get_post_status( $post_id );
		//存在しない　か　プレビューではないのにpublish以外は表示しない （存在して かつ　publishかプレビューのときは表示するということ）
		if (!$status || ( !is_preview() && $status !== 'publish' ) ) {
			return '';
		}

		//delete_transient( $this->add_prefix( 'itemlink_' . $post_id ) );

		$search_shop_value = $this->get_search_shop_value( $post_id );
		$is_search_from_amazon = $this->is_search_from_amazon( $search_shop_value );
		$is_search_from_rakuten = $this->is_search_from_rakuten( $search_shop_value );
		$is_search_from_freelink = $this->is_search_from_freelink( $search_shop_value );

		//キャッシュデータを取得
		$meta_datas = get_transient( $this->add_prefix( 'itemlink_' . $post_id ) );

		//再取得をしない場合かフリーリンクはDBからデータ取得
		if ( $this->is_no_reapi_column || $is_search_from_freelink ) {
			$is_api_data = false;
			$new_data = [];
			//表示データに整形
			if ( !$meta_datas ) {
				$meta_datas = $this->get_tansient_meta_data( $post_id,  $atts, $new_data, $is_api_data );
			}

			//キャッシュに入れる
			set_transient($this->add_prefix( 'itemlink_' . $post_id), $meta_datas, self::EXPIRED_TIME );

			//フリーリンク以外は価格は消す
			if ( !$is_search_from_freelink ) {
				$meta_datas['price'] = '';
			}
			//ショートコードで上書き
			$meta_datas = $this->set_shortcode_data( $meta_datas, $atts );

		//キャッシュがあればキャッシュを利用
		} elseif ( $meta_datas ) {
			//ショートコードで上書き
			$meta_datas = $this->set_shortcode_data( $meta_datas, $atts );
			//Amazonから検索でさらにキャッシュの中にASINがなければ再取得
			if ( $is_search_from_amazon && $this->array_get( $meta_datas, 'asin', '' ) === '' ) {
				$meta_datas[ 'asin' ] = get_post_meta(
					$post_id,
					$this->custom_field_column_name('asin' ),
					true
				);
			}
		//apiで再取得
		} else {
			if ( $is_search_from_rakuten ) {
				$itemcode = get_post_meta( $post_id, $this->custom_field_column_name( 'rakuten_itemcode' ), true );
				//楽天 apiから情報を再取得
				$new_datas = $this->get_rakuten_data_from_itemcode( $itemcode );
			} elseif ( $is_search_from_amazon ) {
				$asin = get_post_meta( $post_id, $this->custom_field_column_name( 'asin' ), true );
				//amazon apiから情報を再取得
				$new_datas = $this->get_amazon_data_from_asin( $asin );
				$new_datas[ 'asin' ] = $asin;
			}

			$errors = $this->array_get( $new_datas, 'error', false);

			if ( isset( $new_datas[0] ) && !$errors ) {
				$new_data = $new_datas[0];
				$is_api_data = true;
				//データをAPIから取得できた場合、DBを上書き
				if ( $is_search_from_rakuten ) {
					foreach ($new_data AS $key => $value) {
						if ( $key !== 'availability' ) {
							update_post_meta( $post_id, $this->custom_field_column_name( $key ), $value );
							//存在するならflgを戻す
							update_post_meta( $post_id, $this->custom_field_column_name( 'is_rakuten_no_exist' ), 0 );
						}
					}
				} elseif ( $is_search_from_amazon ) {
					foreach ($new_data AS $key => $value) {
						if ( $key !== 'asin' ) {
							update_post_meta( $post_id, $this->custom_field_column_name( $key ), $value );
							//存在するならflgを戻す
							update_post_meta( $post_id, $this->custom_field_column_name( 'is_amazon_no_exist' ), 0 );
						}
					}
				}
				//商品のasin自体がない場合 Amazonのみ
			} elseif( !!$errors && $errors['code'] === 'InvalidParameterValue' ) {
				update_post_meta( $post_id, $this->custom_field_column_name( 'is_amazon_no_exist' ), 1 );
				return $this->return_no_template( $post_id, $atts );
				//商品のitemcode自体がない場合 楽天のみ
			} elseif( !!$errors && $errors['code'] === 'rakuten_noitem' ) {
				update_post_meta( $post_id, $this->custom_field_column_name( 'is_rakuten_no_exist' ), 1 );
				return $this->return_no_template( $post_id, $atts );
				//商品のasinかitemCodeはあるが在庫切れ
			} else {
				$is_api_data = false;
				//データをAPIから取得できなかった場合価格は消す
				update_post_meta( $post_id, $this->custom_field_column_name( 'price' ), '' );
				update_post_meta( $post_id, $this->custom_field_column_name( 'price_at' ), '' );
				$new_data = [];
			}

			//表示データに整形
			$meta_datas = $this->get_tansient_meta_data( $post_id,  $atts, $new_data, $is_api_data );

			//データをAPIから取得できた場合、キャッシュにいれる
			if ( $is_api_data ) {
				set_transient($this->add_prefix('itemlink_' . $post_id), $meta_datas, self::EXPIRED_TIME);
			}
		}

		if ( $is_search_from_rakuten ) {
			//楽天に存在しているか確認して、なかったら商品を消す
			$is_rakuten_no_exist = !!get_post_meta($post_id, $this->custom_field_column_name('is_rakuten_no_exist'), true);
			if ($is_rakuten_no_exist) {
				return $this->return_no_template($post_id, $atts);
			}
		} elseif ( $is_search_from_amazon ) {
			//Amazonに存在しているか確認してなければ商品を消す
			$is_amazon_no_exist = !!get_post_meta( $post_id, $this->custom_field_column_name( 'is_amazon_no_exist' ), true);
			if ( $is_amazon_no_exist  ) {
				return $this->return_no_template( $post_id, $atts );
			}
		}


		//AmazonでASINがありリンクがもしも設定のときkindleリンクはRinkerで作ったリンクを使用する
		if ( $is_search_from_amazon &&
			strlen( $this->array_get( $meta_datas,  'asin' , '') ) > 0  &&
			$this->is_moshimo( self::SHOP_TYPE_AMAZON_KINDLE ) &&
			$this->array_get($meta_datas, 'original_amazon_title_url') ===$this->array_get($meta_datas, 'original_amazon_kindle_url') ) {
			$meta_datas[ 'original_amazon_kindle_url' ] =  $this->generate_amazon_title_original_link( $meta_datas[ 'asin' ] );
		}
		//AmazonでASINがありリンクがもしも設定になっている場合はRinkerで作ったリンクを使用する
		if ( $is_search_from_amazon &&
			strlen( $this->array_get( $meta_datas,  'asin' , '') ) > 0  &&
			$this->is_moshimo( self::SHOP_TYPE_AMAZON ) ) {
			$meta_datas[ 'original_amazon_title_url' ] =  $this->generate_amazon_title_original_link( $meta_datas[ 'asin' ] );
		}

		$meta_datas = apply_filters( $this->add_prefix( 'meta_data_update_init' ), $meta_datas );

		$meta_datas = apply_filters( $this->add_prefix( 'meta_data_update' ), $meta_datas, $atts );

		extract($meta_datas);

		if ( $is_search_from_freelink ) {
			$template_path = dirname( __FILE__ ) . '/template/template-freelink.php';
			$template_path = apply_filters( $this->add_prefix( 'load_freelink_template_path' ),  $template_path );
		} else {
			$template_path = dirname( __FILE__ ) . '/template/template-default.php';
			$template_path = apply_filters( $this->add_prefix( 'load_template_path' ),  $template_path );
		}

		if ( validate_file( $template_path ) !== 0) {
			return '';
		}

		ob_start();
		include( $template_path );
		$template = ob_get_contents();
		ob_end_clean();
		return $template;
	}

	/**
	 * 現在取り扱いがありません表示を追加
	 * @return string
	 */
	public function return_no_template( $post_id, $atts ) {
		$meta_datas = $this->get_tansient_meta_data($post_id, $atts, [], false);

		//Amazonボタン用URL
		$amazon_original_url = $this->array_get( $meta_datas, 'original_amazon_url', '' );
		if ( $amazon_original_url !== '' ) {
			$meta_datas[ 'amazon_url' ] = $this->generate_amazon_link_with_aid( $amazon_original_url, $post_id );
		}

		//AmazonKindleボタン用URL
		$amazon_kindle_original_url = $this->array_get( $meta_datas, 'original_amazon_kindle_url', '' );
		if ( $amazon_original_url !== '' ) {
			$meta_datas[ 'amazon_kindle_url' ] = $this->generate_amazon_kindle_link_with_aid( $amazon_kindle_original_url, $post_id );
		}

		//楽天ボタン用URL
		$rakuten_original_url = $this->array_get( $meta_datas, 'original_rakuten_url', '' );
		if ( $rakuten_original_url !== '' ) {
			$meta_datas[ 'rakuten_url' ] = $this->generate_rakuten_link_with_aid( $rakuten_original_url, $post_id );
		}

		//Yahooボタン用URL
		$yahoo_original_url = $this->array_get( $meta_datas, 'original_yahoo_url', '' );
		if ( $yahoo_original_url !== '' ) {
			$meta_datas[ 'yahoo_url' ] = $this->generate_yahoo_link_with_aid( $yahoo_original_url, $post_id );
		}

		//リンク作成
		foreach($this->shop_types AS $key => $values) {
			$meta_datas[ $key . '_link' ] = $this->link_html( $meta_datas, $key, $values, $atts );
		}

		foreach ([ 1, 2, 3, 4 ] as $num) {
			$meta_datas = $this->free_link_html( $num, $meta_datas );
		}

		$no_template_path = dirname( __FILE__ ) . '/template/no-template-default.php';
		$no_template_path = apply_filters( $this->add_prefix( 'load_no_template_path' ),  $no_template_path );

		if ( validate_file( $no_template_path ) !== 0) {
			return '';
		}

		ob_start();
		include( $no_template_path );
		$template = ob_get_contents();
		ob_end_clean();
		return $template;
	}

	/**
	 * 価格を表示するかどうか
	 * @param $meta_datas
	 * @param $atts
	 *
	 * @return mixed
	 */
	public function is_no_price_disp( $meta_datas, $atts ) {
		if ( $this->is_no_price_disp_column ) {
			if ( isset($meta_datas[ 'price_at' ]) ) {
				$meta_datas[ 'price_at' ] = '';
			}

			if ( isset($meta_datas[ 'price' ]) ) {
				$meta_datas[ 'price' ] = '';
			}
		}
		return $meta_datas;
	}

	public function set_shortcode_data( $meta_datas, $atts ) {
		$shortcodes = $this->shortcode_keys();
		foreach ( $shortcodes AS $key => $val ) {
			$att_value = trim( $this->array_get( $atts, $key, '' ) );
			$index = $key;
			if ( $key === 'size' ) {
				$data =  $this->default_image_size( $att_value );
			} else {
				//ショートコードに設定があればそれを優先
				if ( strlen( $att_value ) > 0 ) {
					$data = $att_value;
				} else {
					$data = $this->array_get( $meta_datas, $key, '' );
				}
			}
			if ( strlen( $data ) > 0 ) {
				$meta_datas[ $index ] = $data;
			}
		}
		return $meta_datas;
	}

	

	/**
	 * キャッシュになかった場合、新しく取得したデータから画像と値段データを読む
	 * データで取得できなかった場合はDBのデータを読む
	 * @param $post_idavailability
	 * @param $atts
	 * @param $new_data
	 * @param bool|true $is_api_data
	 *
	 * @return array
	 */
	public function get_tansient_meta_data( $post_id,  $atts, $new_data, $is_api_data = true ) {
		$shortcodes = $this->shortcode_keys();
		$meta_datas = [];
		foreach ( $shortcodes AS $key => $val ) {
			$att_value = trim ($this->array_get( $atts, $key, '' ) );
			$index = $key;
			unset( $data );
			//ショートコードに設定があればそれを優先
			if ( strlen( $att_value ) > 0 ) {
				$data = $att_value;
			} else {
				switch ( $key ) {
					case 'title':
						$data = trim( get_the_title( $post_id ) );
						break;
					case 'amazon_title_url':
					case 'amazon_url':
					case 'amazon_kindle_url':
					case 'rakuten_title_url':
					case 'rakuten_url':
					case 'yahoo_url':
						$type = $this->custom_field_column_name( $key );
						$data = get_post_meta( $post_id, $type, true );
						if (strlen( $data ) > 0) {
							$meta_datas[ 'original_' . $key ] = $data;
						}
						break;
					case 'sizesw':
					case 'sizesh':
					case 'sizemw':
					case 'sizemh':
					case 'sizelw':
					case 'sizelh':
						$size_column = $this->getImageSizeColumnFromShortcode( $key );
						if ( $is_api_data ) {
							$data = $this->array_get($new_data, $size_column, '');
						} else {
							$data = get_post_meta($post_id, $this->custom_field_column_name($size_column), true);
						}
						break;
					case 'price':
					case 'price_at':
						if ( $is_api_data ) {
							$data = $this->array_get($new_data, $key, '');
							break;
						} else {
							$data = get_post_meta( $post_id, $this->custom_field_column_name( $key ), true );
							break;
						}
					default:
						$data = get_post_meta( $post_id, $this->custom_field_column_name( $key ), true );
						break;
				}
			}
			if (isset( $data )) {
				$meta_datas[ $index ] = $data;
			}
		}

		//画像系の処理　Amazonの再取得時は新しいデータを利用する
		if ( $is_api_data && intval( $this->array_get( $meta_datas,  'search_shop_value' ) ) === self::SEARCH_SHOP_AMAZON ) {
			$meta_datas['s_image_url'] = $this->array_get($new_data, 's_image_url', '');
			$meta_datas['m_image_url'] = $this->array_get($new_data, 'm_image_url', '');
			$meta_datas['l_image_url'] = $this->array_get($new_data, 'l_image_url', '');
		} else {
			$meta_datas['s_image_url'] = get_post_meta( $post_id, $this->custom_field_column_name( 's_image_url' ), true );
			$meta_datas['m_image_url'] = get_post_meta( $post_id, $this->custom_field_column_name( 'm_image_url' ), true );
			$meta_datas['l_image_url'] = get_post_meta( $post_id, $this->custom_field_column_name( 'l_image_url' ), true );
		}


		$free_comment = get_post_meta( $post_id, $this->custom_field_column_name( 'free_comment' ), true );
		if ( strlen($free_comment) > 0) {
			$origin_free_comment = $free_comment;
		} else {
			if ( $this->is_search_from_rakuten( $this->array_get( $meta_datas,  'search_shop_value' ) ) ) {
				$origin_free_comment = $this->rakuten_free_comment;
			} elseif ( $this->is_search_from_freelink( $this->array_get( $meta_datas,  'search_shop_value' ) ) ) {
				$origin_free_comment = $this->freelink_free_comment;
			} else {
				$origin_free_comment = $this->amazon_free_comment;
			}
		}

		$meta_datas[ 'free_comment' ] = $this->replace_free_comment( $meta_datas, $origin_free_comment );

		$meta_datas = apply_filters( $this->add_prefix( 'get_tansient_meta_data' ), $meta_datas,  $new_data);

		return $meta_datas;
	}

	/**
	 * 画像のサイズショートコードから実際のカラム名を取得
	 * @param $shortcode
	 * @return string
	 */
	public function getImageSizeColumnFromShortcode( $shortcode ) {
		$column = '';
		switch ($shortcode) {
			case 'sizesw':
				$column = self::IMAGE_S_SIZE_W_COLUMN;
				break;
			case 'sizesh':
				$column = self::IMAGE_S_SIZE_H_COLUMN;
				break;
			case 'sizemw':
				$column = self::IMAGE_M_SIZE_W_COLUMN;
				break;
			case 'sizemh':
				$column = self::IMAGE_M_SIZE_H_COLUMN;
				break;
			case 'sizelw':
				$column = self::IMAGE_L_SIZE_W_COLUMN;
				break;
			case 'sizelh':
				$column = self::IMAGE_L_SIZE_H_COLUMN;
				break;
		}
		return $column;
	}

	/**
	 * 画像のサイズカラム名からショートコード名を取得
	 * @param $shortcode
	 * @return string
	 */
	public function getImageSizeColumnFromColumn( $column ) {
		$shortcode = '';
		switch ($column) {
			case self::IMAGE_S_SIZE_W_COLUMN:
				$shortcode ='sizesw';
				break;
			case self::IMAGE_S_SIZE_H_COLUMN:
				$shortcode ='sizesh';
				break;
			case self::IMAGE_M_SIZE_W_COLUMN:
				$shortcode ='sizemw';
				break;
			case self::IMAGE_M_SIZE_H_COLUMN:
				$shortcode ='sizemh';
				break;
			case self::IMAGE_L_SIZE_W_COLUMN:
				$shortcode ='sizelw';
				break;
			case self::IMAGE_L_SIZE_H_COLUMN:
				$shortcode ='sizelh';
				break;
		}
		return $shortcode;
	}

	/**
	 * 利用画像のカラムから画像の幅を取得
	 * @param $image_column
	 * @param $meta_datas
	 * @return mixed|null
	 */
	public function getImageWidth( $image_column, $meta_datas ) {
		$width = null;
		switch ($image_column) {
			case 's_image_url':
			case $this->add_prefix( 's_image_url' ):
				$width = $this->array_get( $meta_datas, $this->getImageSizeColumnFromColumn(self::IMAGE_S_SIZE_W_COLUMN ) ) ;
				break;
			case 'm_image_url':
			case $this->add_prefix( 'm_image_url' ):
				$width = $this->array_get( $meta_datas, $this->getImageSizeColumnFromColumn( self::IMAGE_M_SIZE_W_COLUMN ) ) ;
				break;
			case 'l_image_url':
			case $this->add_prefix( 'l_image_url' ):
				$width = $this->array_get( $meta_datas, $this->getImageSizeColumnFromColumn(self::IMAGE_L_SIZE_W_COLUMN ) ) ;
				break;
		}
		return $width;
	}

	/**
	 * 利用画像のカラムから画像の高さを取得
	 * @param $image_column
	 * @param $meta_datas
	 * @return mixed|null
	 */
	public function getImageHeight( $image_column, $meta_datas ) {
		$height = null;
		switch ($image_column) {
			case 's_image_url':
			case $this->add_prefix( 's_image_url' ):
				$height = $this->array_get( $meta_datas, $this->getImageSizeColumnFromColumn(self::IMAGE_S_SIZE_H_COLUMN ) );
				break;
			case 'm_image_url':
			case $this->add_prefix( 'm_image_url' ):
				$height = $this->array_get( $meta_datas, $this->getImageSizeColumnFromColumn(self::IMAGE_M_SIZE_H_COLUMN ) );
				break;
			case 'l_image_url':
			case $this->add_prefix( 'l_image_url' ):
				$height = $this->array_get( $meta_datas, $this->getImageSizeColumnFromColumn(self::IMAGE_L_SIZE_H_COLUMN ) );
				break;
		}
		return $height;
	}

	/**
	 * フリーHTMLの変換
	 */
	public function replace_free_comment( $meta_datas, $free_comment ) {
		$free_comment = str_replace( self::AMAZON_ID_INSERT_TAG, $this->amazon_traccking_id, $free_comment );
		$free_comment = str_replace( self::RAKUTEN_ID_INSERT_TAG, $this->rakuten_affiliate_id, $free_comment );
		$free_comment = str_replace( self::ASIN_INSERT_TAG, $meta_datas[ 'asin' ], $free_comment );
		$free_comment = str_replace( self::RAKUTEN_CODE_INSERT_TAG, $meta_datas[ 'rakuten_itemcode' ], $free_comment );
		$free_comment = do_shortcode( stripslashes( $free_comment ) );
		return $free_comment;
	}

	/**
	 * アマゾンボタンリンクに商品詳細リンクをはるかどうか確認してこのメソッドで変更
	 * タイトルURLの設定がなければ検索ページへのリンクのまま
	 * @param $meta_data
	 * @return mixed
	 */
	public function check_id_amazon_detail( $meta_data ) {
		if ( $this->is_amazon_detail_url() && isset( $meta_data[ 'original_amazon_title_url' ] )) {
			$meta_data['original_amazon_url'] = $meta_data[ 'original_amazon_title_url' ];
		}
		return $meta_data;
	}

	public function is_amazon_detail_url() {
		$is_amazon_detail_url = get_option( $this->option_column_name( 'is_detail_amazon_url' ) );
		return intval( $is_amazon_detail_url ) === 1;
	}


	/**
	 * 楽天ボタンリンクに商品詳細リンクをはるかどうか確認してこのメソッドで変更
	 * タイトルURLの設定がなければ検索ページへのリンクのまま
	 * @param $meta_data
	 * @return mixed
	 */
	public function check_id_rakuten_detail( $meta_data ) {
		if ( $this->is_rakuten_detail_url() && isset( $meta_data[ 'original_rakuten_title_url' ] )) {
			$meta_data['original_rakuten_url'] = $meta_data[ 'original_rakuten_title_url' ];
		}
		return $meta_data;
	}

	/**
	 * 楽天ボタンを商品詳細にするかどうか
	 * もしもの時は必ず検索へ
	 * @return bool
	 */
	public function is_rakuten_detail_url() {
		$is_rakuten_detail_url = get_option( $this->option_column_name( 'is_detail_rakuten_url' ) );
		return intval( $is_rakuten_detail_url ) === 1 &&  !$this->is_moshimo(self::SHOP_TYPE_RAKUTEN );
	}

	/**
	 * ショートコードがから画像のサイズを取得する
	 * @param null $size
	 *
	 * @return string
	 */
	public function getImageColumn($size = null, $is_prefix = true) {
		$default_size =  $this->default_image_size( $size );
		switch ( $default_size ) {
			case 'S':
			case 's':
				$column = 's_image_url';
				break;
			case 'M':
			case 'm':
				$column = 'm_image_url';
				break;
			case 'L':
			case 'l':
				$column = 'l_image_url';
				break;
			default:
				$column = 'm_image_url';
				break;
		}
		if ( $is_prefix ){
			return $this->custom_field_column_name( $column );
		} else {
			return $column;
		}
	}

	/**
	 * ショートコードがから画像のclassを取得する
	 * @param null $size
	 *
	 * @return string
	 */
	public function getImageClass( $size = null ) {
		switch ($size) {
			case 'S':
			case 's':
				$class = 'yyi-rinker-img-s';
				break;
			case 'L':
			case 'l':
				$class = 'yyi-rinker-img-l';
				break;
			case 'M':
			case 'm':
				$class = 'yyi-rinker-img-m';
				break;
			default:
				$class = $this->default_image_class( $size );
				break;
		}
		return $class;
	}

	/*
	 * デフォルトの画像class
	 */
	public function default_image_class( $size ) {
		$default_size =  $this->default_image_size( $size );
		switch ($default_size) {
			case 'S':
			case 's':
				$image_class = 'yyi-rinker-img-s';
				break;
			case 'L':
			case 'l':
				$image_class = 'yyi-rinker-img-l';
				break;
			case 'M':
			case 'm':
				$image_class = 'yyi-rinker-img-m';
				break;
			default:
				$image_class = 'yyi-rinker-img-m';
				break;
		}
		return $image_class;
	}

	/**
	 * ショートコードで指定があればそれを返し、なければデフォルトの画像サイズを返す
	 * @param $size
	 * @return string
	 */
	public function default_image_size( $size ) {
		if (is_null($size) || $size === '') {
			$s = 'm';
			$s = apply_filters($this->add_prefix('default_image_size'), $s);
			return $s;
		} else {
			return $size;
		}
	}



	public function add_menu_page() {
		add_submenu_page( 'options-general.php', 'Rinker設定', 'Rinker設定', 'manage_options', YYIRINKER_PLUGIN_DIR . 'yyi-rinker.php', array($this, 'option_page'), 6 );
	}



	public function updated_message() {
		echo '<div id="message" class="updated notice notice-success is-dismissible"><p>設定を更新しました。</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
	}

	/**
	 * 商品リンクをカスタムフィールドに追加する for ajax
	 */
	public function add_item() {
		global $post_id;

		if ( !check_ajax_referer(  $this->add_prefix( 'add_itemlinks' ), '_wpnonce', false ) ) {
			wp_die( 'リファラエラー' );
		}

		if ( !current_user_can( 'edit_posts' ) ) {
			wp_die( '権限がありません' );
		}

		$params = [
			'post_title'	=> $this->array_get($_POST, 'title'),
			'post_name'		=> $this->array_get($_POST, 'title'),
			'post_status'	=> 'publish',
			'post_type'		=> self::LINK_POST_TYPE,
			'ping_status'	=> 'closed',
		];
		$post_id = wp_insert_post( $params );

		if ( intval($post_id) > 0) {
			$this->save_post_meta_links( $post_id, $_POST, true, true );
			wp_die( $post_id );
		} else {
			wp_die( 0 );
		}
	}

	/**
	 * Amazon検索画面から商品リンクを取得返す
	 * @param $keywords
	 *
	 * @return string
	 */
	public function generate_amazon_link_from_keyword( $keywords ) {
		$traccking_id	= $this->amazon_tracking_id;
		$url = 'https://www.amazon.co.jp/gp/search?ie=UTF8&keywords=' . urlencode( $keywords ) . '&tag=' . $traccking_id . '&index=blended&linkCode=ure&creative=6339';
		return $url;
	}

	/**
	 * Amazon 詳細ページのオリジナルリンクを取得します
	 * @param $keywords
	 *
	 * @return string
	 */
	public function generate_amazon_title_original_link( $asin ) {
		if (strlen( $asin ) > 0) {
			$url = 'https://www.amazon.co.jp/dp/' . $asin;
		} else {
			$url = '';
		}

		return $url;
	}

	/**
	 * Amazon 詳細ページのアソシエイトID付きで返します
	 * @param $keywords
	 *
	 * @return string
	 */
	public function generate_amazon_title_link_with_aid( $original_url, $post_id ) {
		if ( $this->is_moshimo( self::SHOP_TYPE_AMAZON ) ) {
			$url = $this->generate_moshimo_link( self::SHOP_TYPE_AMAZON, $original_url );
		} else {
			if ( strpos( $original_url, 'tag=' . $this->amazon_traccking_id ) === false ) {
				$url = $original_url . '?tag=' . $this->amazon_traccking_id . '&linkCode=as1&creative=6339';
			} else {
				$url = $original_url;
			}
		}
		$url = apply_filters( $this->add_prefix( 'generate_amazon_title_link_with_aid' ), $url, $original_url );
		return $url;
	}



	/**
	 * Amazon アソシエイトID付きで返します
	 * @param $keywords
	 *
	 * @return string
	 */
	public function generate_amazon_link_with_aid( $original_url, $post_id ) {

		if ( $this->is_moshimo( self::SHOP_TYPE_AMAZON ) ) {
			$url = $this->generate_moshimo_link( self::SHOP_TYPE_AMAZON, $original_url );
		} else {
			$traccking_id	= $this->amazon_traccking_id;
			$url = $original_url . '&tag=' . $traccking_id . '&index=blended&linkCode=ure&creative=6339';
		}

		$url = apply_filters( $this->add_prefix( 'generate_amazon_link_with_aid' ), $url, $original_url );
		return $url;
	}

	/**
	 * Amazon Kindle アソシエイトID付きで返します
	 * @param $keywords
	 *
	 * @return string
	 */
	public function generate_amazon_kindle_link_with_aid( $original_url, $post_id ) {

		if ( $this->is_moshimo( self::SHOP_TYPE_AMAZON_KINDLE ) ) {
			$url = $this->generate_moshimo_link( self::SHOP_TYPE_AMAZON_KINDLE, $original_url );
		} else {
			if ( strpos( $original_url, 'tag=' . $this->amazon_traccking_id ) === false ) {
				$traccking_id	= $this->amazon_traccking_id;
				$url = $original_url . '&tag=' . $traccking_id . '&index=blended&linkCode=ure&creative=6339';
			} else {
				$url = $original_url;
			}
		}

		$url = apply_filters( $this->add_prefix( 'generate_amazon_kindle_link_with_aid' ), $url, $original_url );
		return $url;
	}



	/**
	 * PA-APIv5に対応
	 * AmazonApiから商品データを取得して再保存する
	 * @param $asin
	 */
	public function get_amazon_data_from_asin( $asin ) {
		$request = new GetItemsRequest();
		$request->ItemIds = [ $asin ];
		$datas = $this->get_json_from_amazon_api( 'GetItems', $request);
		if ( is_array($datas) && isset( $datas['error' ]) ) {
			return $datas;
		} else {
			return $this->set_data_for_amazon_resave( $datas );
		}
	}




	/**
	 * for PA-APIv5
	 * jsonのデータを整形
	 * @param $json_datas
	 * @param bool $is_new
	 * @return array
	 */
	private function set_data_for_amazon_resave( $json_datas ) {
		$items = [];
		foreach ( $json_datas->ItemsResult->Items as $item ) {
			$data = [];
			if ( isset( $item->ItemInfo->Classifications->ProductGroup->DisplayValue ) ) {
				$group = (string)$item->ItemInfo->Classifications->ProductGroup->DisplayValue;
				$data[ 'product_group' ] = $group;
				if ($group === 'Digital Ebook Purchas') {
					$data[ 'amazon_kindle_url' ] = (string)$item->DetailPageURL;
				}
			}

			if ( isset( $item->DetailPageURL ) ) {
				$data[ 'amazon_title_url' ]	= (string)$item->DetailPageURL;
			}

			if ( isset( $item->Offers->Listings[0]->Price->Amount ) ) {
				$price = $item->Offers->Listings[0]->Price->Amount;
				$data[ 'price' ] = (string)$price;
				$data[ 'price_at' ] = date_i18n('Y/m/d H:i:s');
			} else {
				$data[ 'price' ] = '';
				$data[ 'price_at' ] = '';
			}

			if ( isset($item->Images->Primary)) {
				$data['s_image_url'] = (string)$item->Images->Primary->Small->URL;
				$data['m_image_url'] = (string)$item->Images->Primary->Medium->URL;
				$data['l_image_url'] = (string)$item->Images->Primary->Large->URL;

				$data[self::IMAGE_S_SIZE_W_COLUMN] = (string)$item->Images->Primary->Small->Width;
				$data[self::IMAGE_S_SIZE_H_COLUMN] = (string)$item->Images->Primary->Small->Height;
				$data[self::IMAGE_M_SIZE_W_COLUMN] = (string)$item->Images->Primary->Medium->Width;
				$data[self::IMAGE_M_SIZE_H_COLUMN] = (string)$item->Images->Primary->Medium->Height;
				$data[self::IMAGE_L_SIZE_W_COLUMN] = (string)$item->Images->Primary->Large->Width;
				$data[self::IMAGE_L_SIZE_H_COLUMN] = (string)$item->Images->Primary->Large->Height;
			} else {
				$data['s_image_url'] = '';
				$data['m_image_url'] = '';
				$data['l_image_url'] = '';

				$data[self::IMAGE_S_SIZE_W_COLUMN] = '';
				$data[self::IMAGE_S_SIZE_H_COLUMN] = '';
				$data[self::IMAGE_M_SIZE_W_COLUMN] = '';
				$data[self::IMAGE_M_SIZE_H_COLUMN] = '';
				$data[self::IMAGE_L_SIZE_W_COLUMN] = '';
				$data[self::IMAGE_L_SIZE_H_COLUMN] = '';
			}

			$items[] = $data;
		}
		return $items;
	}



	/**
	 * PA-APIv4 廃止予定
	 * @param $endpoint
	 * @param $infos
	 * @param bool $is_new
	 * @return array
	 */
	private function get_xml_from_amazon_api( $endpoint, $infos, $is_new = true ) {
		$uri = '/onca/xml';

		$access_key_id	= get_option( $this->option_column_name( 'amazon_access_key' ) );
		$traccking_id	= $this->amazon_traccking_id;
		$secret_key		= get_option( $this->option_column_name( 'amazon_secret_key' ) );

		$params = [
			'Service'			=> 'AWSECommerceService',
			'AWSAccessKeyId'	=> $access_key_id,
			'Timestamp'			=> gmdate( 'Y-m-d\TH:i:s\Z' ),
			'AssociateTag'		=> $traccking_id,
			'ResponseGroup'		=> "Images,ItemAttributes,Offers,AlternateVersions",
		];

		$params = $params + $infos;

		ksort($params);

		$pairs = array();

		foreach ($params as $key => $value) {
			array_push( $pairs, rawurlencode( $key ) . '=' . rawurlencode( $value ) );
		}

		$canonical_query_string = join( "&", $pairs );

		$string_to_sign = "GET\n" . $endpoint . "\n" . $uri . "\n" . $canonical_query_string;

		$signature = base64_encode( hash_hmac( "sha256", $string_to_sign, $secret_key, true ) );

		$request_url = 'http://' . $endpoint . $uri . '?' . $canonical_query_string . '&Signature=' . rawurlencode($signature);

		$response = wp_remote_request(
			$request_url,
			array(
				'timeout' => 30,
			)
		);

		set_error_handler( function( $errno, $errstr, $errfile, $errline ) {
			throw new Exception( $errstr, $errno );
		});

		$body = wp_remote_retrieve_body( $response );
		try {
			$xml = simplexml_load_string( $body );
			restore_error_handler();
		} catch( Exception $e ){
			restore_error_handler();
			return [ 'error' => [
				'code' => 'XML parser Error',
				'message' => '【parser Error】XMLが正しくありません',
				'message_jp' => '【parser Error】XMLが正しくありません'
			] ] ;
		}

		if ( $xml ) {
			if ( $xml->Items && $xml->Items->Request && 'True' == (string)$xml->Items->Request->IsValid ) {

				if ( $xml->Items->Request->Errors->Error ) {
					$error = $xml->Items->Request->Errors->Error;
					$errors = [ 'error' => [] ];
					$errors[ 'error' ][ 'code' ]		= (string)$error->Code;
					$errors[ 'error' ][ 'message' ]		= (string)$error->Message;
					$errors[ 'error' ][ 'message_jp' ]	= $this->amazon_api_errors( $errors[ 'error' ][ 'code' ], $errors[ 'error' ][ 'message' ] );
					return $errors;
				}

				$items = [];
				foreach ( $xml->Items->Item as $item ) {
					$data = [];
					//新規の時だけ登録する部分
					if ( $is_new ) {
						$data[ 'asin' ]				= ( string ) $item->ASIN;
						$data[ 'amazon_url' ]		= \POCHIPP\generate_amazon_original_link( $infos['Keywords'] );
						$data[ 'rakuten_url' ]		= \POCHIPP\generate_rakuten_original_link( $infos['Keywords'] );
						$data[ 'yahoo_url' ]			= \POCHIPP\generate_yahoo_original_link( $infos['Keywords'] );

						if ( isset( $item->ItemAttributes ) ) {
							$data[ 'title' ] = (string)$item->ItemAttributes->Title;
							$data[ 'brand' ] = (string)$item->ItemAttributes->Brand;
						} else {
							$data[ 'title' ] = '';
							$data[ 'brand' ] = '';
						}
					}

					if ( isset( $item->ItemAttributes ) ) {
						$group = (string)$item->ItemAttributes->ProductGroup;

						$data[ 'product_group' ] = $group;
						if ( $group === 'Book' ) {
							$data[ 'amazon_kindle_url' ] = $this->setKindleUrl( $item );
						} elseif ($group === 'eBooks') {
							$data[ 'amazon_kindle_url' ] = (string)$item->DetailPageURL;
						}
					}

					$data[ 'amazon_title_url' ]	= (string)$item->DetailPageURL;

					if ( isset( $item->OfferSummary->LowestNewPrice ) ) {
						$price = $item->OfferSummary->LowestNewPrice->Amount;
						$data[ 'price' ] = (string)$price;
						$data[ 'price_at' ] = date_i18n('Y/m/d H:i:s');
					} else {
						$data[ 'price' ] = '';
						$data[ 'price_at' ] = '';
					}

					$data[ 's_image_url' ]	= (string) $item->SmallImage->URL;
					$data[ 'm_image_url' ]	= (string) $item->MediumImage->URL;
					$data[ 'l_image_url' ]	= (string) $item->LargeImage->URL;

					$data[ self::IMAGE_S_SIZE_W_COLUMN ]	= (string) $item->SmallImage->Width;
					$data[ self::IMAGE_S_SIZE_H_COLUMN ]	= (string) $item->SmallImage->Height;
					$data[ self::IMAGE_M_SIZE_W_COLUMN ]	= (string) $item->MediumImage->Width;
					$data[ self::IMAGE_M_SIZE_H_COLUMN ]	= (string) $item->MediumImage->Height;
					$data[ self::IMAGE_L_SIZE_W_COLUMN ]	= (string) $item->LargeImage->Width;
					$data[ self::IMAGE_L_SIZE_H_COLUMN ]	= (string) $item->LargeImage->Height;

					$items[] = $data;
				}
				return $items;
			} else {
				$errors = [];
				if ( $xml->Error ) {
					$error = $xml->Error;
					$errors[ 'code' ]			= (string)$error->Code;
					$errors[ 'message' ]		= (string)$error->Message;
				} elseif ( $xml->Items->Request->Errors->Error ) {
					$error = $xml->Items->Request->Errors->Error;
					$errors[ 'code' ]			= (string)$error->Code;
					$errors[ 'message' ]		= (string)$error->Message;
				} else {
					$errors[ 'code' ]			= 'API ERROR';
					$errors[ 'message' ]		= 'APIのレスポンス内にエラーがあります';
				}
				$errors['message_jp']	= $this->amazon_api_errors( $errors['code'], $errors['message'] );
				return [ 'error' => $errors ] ;
			}
		}
	}

	/**
	 * 廃止予定
	 * AmazonのxmlからkindleのURLを抜き出す
	 * @param $item
	 * @return string
	 */
	public function setKindleUrl($item) {
		$json	= json_encode( $item );
		$items	= json_decode( $json, true );

		if ( isset( $items[ 'AlternateVersions' ][ 'AlternateVersion' ] ) ) {
			$versions = $items[ 'AlternateVersions' ][ 'AlternateVersion' ];
			if ( isset( $versions[ 'Binding' ] ) && $versions[ 'Binding' ] === 'Kindle版' ) {
				$kindle_asin = $this->array_get( $versions, 'ASIN', '' );
			} elseif ( isset( $versions[ 0 ] ) && is_array( $versions[ 0 ] ) ) {
				foreach( $versions AS $version ) {
					$binding = $this->array_get( $version, 'Binding', '' );
					if ( $binding === 'Kindle版' ) {
						$kindle_asin = $this->array_get( $version, 'ASIN', '' );
					}
				}
			}
		}

		if ( isset( $kindle_asin ) ) {
			return $this->generate_amazon_title_original_link( $kindle_asin );
		} else {
			return '';
		}
	}

	/**
	 * 廃止予定
	 * AmazonAPIから商品データを取得
	 */
	public function generate_amazon_datas( $keywords, $search_index ) {

		if ( strlen( trim( $keywords ) ) === 0 ) {
			die( json_encode( ['error' => [ 'code' => '', 'message_jp' => 'キーワードを入力してください' ] ] ) );
		}

		$endpoint = 'webservices.amazon.co.jp';
		$infos = [
			'Operation'			=>'ItemSearch',
			'Keywords'			=> $keywords,
			'SearchIndex'		=> $search_index,
		];

		$datas = $this->get_xml_from_amazon_api( $endpoint, $infos );

		wp_send_json( $datas );

	}

	/**
	 * 廃止予定
	 * kindle以外の書籍を検索
	 * @param $keywords
	 */
	public function generate_amazon_book_datas( $keywords ) {

		if ( strlen( trim( $keywords ) ) === 0 ) {
			die( json_encode( ['error' => [ 'code' => '', 'message_jp' => 'キーワードを入力してください' ] ] ) );
		}

		$endpoint = 'webservices.amazon.co.jp';
		$infos = [
			'Operation'			=>'ItemSearch',
			'Keywords'			=> $keywords,
			'SearchIndex'		=> 'Books',
			'Power'				=> 'binding:not kindle',
		];

		$datas = $this->get_xml_from_amazon_api( $endpoint, $infos );

		wp_send_json( $datas );

	}

	/**
	 * 廃止予定
	 * Amazon APIのエラーメッセージを返します
	 * @param $code
	 * @param $en_message
	 *
	 * @return string
	 */
	public function amazon_api_errors( $code , $en_message ) {
		switch( $code ) {
			case 'SignatureDoesNotMatch':
				$message = '認証情報が合いません。[設定]-[[Rinker設定]-[Amazon][API][シークレットキー]を正しいものに設定してください。';
				break;
			case 'InvalidClientTokenId':
				$message = 'アクセスキーIDが合いません。[設定]-[[Rinker設定]-[Amazon][API][アクセスキーID]を正しいものに設定してください。';
				break;
			case 'MissingClientTokenId':
				$message = 'アクセスキーIDが存在しません。[設定]-[[Rinker設定]-[Amazon][API][アクセスキーID]を設定してください。';
				break;
			case 'RequestThrottled':
				$message = 'リクエスト回数が多すぎます。';
				break;
			case 'AWS.MinimumParameterRequirement':
				$message = 'キーワードを入力してください';
				break;
			case 'AWS.ECommerceService.NoExactMatches':
				$message = '該当する商品がありません';
				break;
			case 'AWS.InvalidParameterValue':
				$message = 'このASINに該当する商品がありません';
				break;
			default:
				$message = $en_message;
				break;
		}
		return $message;
	}

	/**
	 * 楽天商品コードから商品データを取得する
	 * @param $itemcode
	 */
	public function get_rakuten_data_from_itemcode( $itemcode ) {
		$datas = $this->generate_rakuten_datas( $itemcode, 1, '', true );
		return $datas;
	}


	/**
	 * 楽天 詳細ページのアソシエイトID付きで返します
	 * @param $keywords
	 *
	 * @return string
	 */
	public function generate_rakuten_title_link_with_aid( $original_url, $post_id, $place = 'title' ) {
		if ( $this->is_moshimo( self::SHOP_TYPE_RAKUTEN ) ) {
			$url = $this->generate_moshimo_link( self::SHOP_TYPE_RAKUTEN, $original_url );
		} else {
			$timestamp		= esc_attr( get_the_date('YmdHis', $post_id) );
			if ( $place === 'image' ) {
				$mark = 'Rinker_i_' . $timestamp;
			} else {
				$mark = 'Rinker_t_' . $timestamp;
			}
			$url = preg_replace('/\?pc=/', $mark . '?pc=', $original_url, 1);
		}
		$url = apply_filters( $this->add_prefix( 'generate_rakuten_title_link_with_aid' ), $url, $original_url, $place );
		return $url;
	}



	/**
	 * 楽天アフィリエイトURLを返す
	 * @param $url
	 *
	 * @return string
	 */
	public function generate_rakuten_link_with_aid( $original_url , $post_id ) {
		if ( $this->is_moshimo( self::SHOP_TYPE_RAKUTEN ) ) {
			$url = $this->generate_moshimo_link( self::SHOP_TYPE_RAKUTEN, $original_url );
		} else {
			$timestamp		= esc_attr( get_the_date('YmdHis', $post_id) );
			$url = 'https://hb.afl.rakuten.co.jp/hgc/' .  $this->rakuten_affiliate_id . '/Rinker_o_' . $timestamp . '?pc=' . urlencode( $original_url ) . '&m=' . urlencode( $original_url );
		}
		$url = apply_filters( $this->add_prefix( 'generate_rakuten_link_with_aid' ), $url, $original_url );
		return $url;
	}

	/**
	 * YahooアフィリエイトURLを返すフィルター
	 * @param $url
	 *
	 * @return string
	 */
	public function generate_yahoo_link_with_aid( $original_url, $post_id ) {

		if ( $this->is_moshimo( self::SHOP_TYPE_YAHOO ) ) {
			$url = $this->generate_moshimo_link( self::SHOP_TYPE_YAHOO, $original_url );
		} else {
			if ( $this->is_yahoo_id() ) {
				$url = 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=' . $this->yahoo_sid . '&pid=' . $this->yahoo_pid . '&vc_url=' . urlencode( $original_url );
			} else {
				$url = $original_url;
			}
		}
		return $url;
	}

	/**
	 * もしもリンクを作成します
	 * @param $shop_type
	 * @param $original_url
	 *
	 * @return string
	 */
	public function generate_moshimo_link( $shop_type, $original_url ){
		$a_id	= esc_attr( $this->shop_types[ $shop_type ][ 'a_id' ] );
		$p_id	= $this->shop_types[ $shop_type ][ 'p_id' ];
		$pc_id	= $this->shop_types[ $shop_type ][ 'pc_id' ];
		$pl_id	= $this->shop_types[ $shop_type ][ 'pl_id' ];
		return 'https://af.moshimo.com/af/c/click?a_id=' . $a_id . '&p_id=' . $p_id .'&pc_id='. $pc_id . '&pl_id=' . $pl_id . '&url=' . urlencode( $original_url );
	}


	/**
	 * そのShopはもしもリンクをはるかどうかを返す
	 * @param $shop_type_val
	 *
	 * @return bool
	 */
	public function is_moshimo( $shop_type ) {
		$shop_type_val = $this->shop_types[ $shop_type ][ 'val' ];
		return !!isset( $this->shop_types[ $shop_type ][ 'a_id' ] ) && strlen($this->shop_types[ $shop_type ][ 'a_id' ]) > 0  && ( $this->moshimo_shops_check & $shop_type_val );
	}

	/**
	 * Yahooのpidとsidが入力されているか判断する
	 */
	public function is_yahoo_id() {
		return intval( $this->yahoo_pid ) > 0 && intval( $this->yahoo_sid );
	}

	public function get_search_shop_value( $post_id ) {
		return intval( get_post_meta( $post_id, $this->custom_field_column_name( 'search_shop_value' ), true ) );
	}

	/**
	 * post_idから
	 * タイトルURLを楽天にするかどうかチェックをする
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function is_search_rakuten_from( $post_id ) {
		$value = $this->get_search_shop_value( $post_id );
		return $this->is_search_from_rakuten( $value );
	}

	/**
	 * Amazonからの検索かどうかチェックする
	 * @param $value
	 *
	 * @return bool
	 */
	public function is_search_from_amazon( $value ) {
		return intval( $value ) === self::SEARCH_SHOP_AMAZON || intval( $value ) === 0 ? true : false;
	}

	/**
	 * 楽天からの検索かどうかチェックする
	 * @param $value
	 *
	 * @return bool
	 */
	public function is_search_from_rakuten( $value ) {
		return intval( $value ) === self::SEARCH_SHOP_RAKUTEN ? true : false;
	}

	/**
	 * フリーリンクの商品リンクかチェックする
	 * @param $value
	 *
	 * @return bool
	 */
	public function is_search_from_freelink( $value ) {
		return intval( $value ) === self::SEARCH_SHOP_FREE ? true : false;
	}

	/**
	 * タイトルのURLをDBから返します
	 */
	public function get_title_url( $post_id , $is_title_url_rakuten = '') {
		if ( $is_title_url_rakuten === '' ) {
			$is_title_url_rakuten = $this->is_search_rakuten_from( $post_id );
		}
		if ( $is_title_url_rakuten ) {
			return get_post_meta( $post_id, $this->custom_field_column_name( 'rakuten_title_url' ), true );
		} else {
			return get_post_meta( $post_id, $this->custom_field_column_name( 'amazon_title_url' ), true );
		}
	}

	/**
	 * リンク更新ボタン押下で呼ばれる for ajax
	 */
	public function relink_from_api() {
		$keywords = $this->array_get( $_GET, 'keywords', '' );
		$sites = [
			'amazon_url'  => \POCHIPP\generate_amazon_original_link( $keywords ),
			'rakuten_url' => \POCHIPP\generate_rakuten_original_link( $keywords ),
			'yahoo_url'   => \POCHIPP\generate_yahoo_original_link( $keywords ),
		];
		wp_send_json( $sites );
	}

	/**
	 * キャッシュを一括削除する
	 * for ajax
	 */
	public function delete_all_cache() {
		global $post_id;

		if ( !check_ajax_referer(  $this->add_prefix( 'delete_all_cache' ), '_wpnonce', false ) ) {
			wp_die( 'ページの更新期限がきれています' );
		}

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( 'このユーザーに操作権限がありません' );
		}

		$transient_key = '_transient_' . $this->add_prefix( 'itemlink_' ) . '%';
		$transient_key_timeout = '_transient_timeout_' . $this->add_prefix( 'itemlink_' ) . '%';

		global $wpdb;
		$result = $wpdb->query('DELETE FROM `' . $wpdb->options . '` WHERE (`option_name` LIKE "' . $transient_key  . '" OR `option_name` LIKE "' . $transient_key_timeout . '")');
		wp_die($result);
	}

	/**
	 * 登録済みの商品リンクデータを取得する　for ajax
	 * 登録済み商品リンクから検索タブの検索で使用
	 */
	public function search_itemlist() {
		$term_id = $this->array_get( $_GET, 'term_id', 0);
		$keywords = $this->array_get( $_GET, 'keywords', '' );
		$datas = $this->get_search_itemlist( $term_id, $keywords );
		wp_send_json($datas);
	}




	/**
	 * テンプレートに表示するためにリンクを整形
	 * @param $meta_datas
	 *
	 * @return mixed
	 */
	function upate_html_data( $meta_datas, $atts ) {
		$post_id = $meta_datas[ 'post_id' ];

		//Amazonボタン用URL
		$original_url = $this->array_get( $meta_datas, 'original_amazon_url', '' );
		if ( $original_url !== '' ) {
			if ( $this->is_amazon_detail_url() && isset( $meta_datas[ 'original_amazon_title_url' ] ) ) {
				$meta_datas[ 'amazon_url' ] = $this->generate_amazon_title_link_with_aid( $original_url, $post_id);
			} else {
				$meta_datas[ 'amazon_url' ] = $this->generate_amazon_link_with_aid( $original_url, $post_id );
			}
		}

		//AmazonKindleボタン用URL
		$original_url = $this->array_get( $meta_datas, 'original_amazon_kindle_url', '' );
		if ( $original_url !== '' ) {
			if (strpos($original_url, '?') === false ) {
				$original_url .= '?';
			}
			$meta_datas[ 'amazon_kindle_url' ] = $this->generate_amazon_kindle_link_with_aid( $original_url, $post_id );
		}

		//楽天ボタン用URL
		$original_url = $this->array_get( $meta_datas, 'original_rakuten_url', '' );
		if ( $original_url !== '' ) {
			if ( $this->is_rakuten_detail_url() && isset( $meta_datas[ 'original_rakuten_title_url' ] ) ) {
				$meta_datas[ 'rakuten_url' ] = $this->generate_rakuten_title_link_with_aid( $original_url, $post_id );
			} else {
				$meta_datas[ 'rakuten_url' ] = $this->generate_rakuten_link_with_aid( $original_url, $post_id );
			}

		}
		//Yahooボタン用URL
		$original_url = $this->array_get( $meta_datas, 'original_yahoo_url', '' );
		if ( $original_url !== '' ) {
			$meta_datas[ 'yahoo_url' ] = $this->generate_yahoo_link_with_aid( $original_url, $post_id );
		}

		foreach($this->shop_types AS $key => $values) {
			$meta_datas[ $key . '_link' ] = $this->link_html( $meta_datas, $key, $values, $atts );
		}


		//楽天タイトルリンク
		//もしもの時は必ず検索ボタンにする
		if ( $this->is_moshimo(self::SHOP_TYPE_RAKUTEN ) ) {
			$original_url = $this->array_get( $meta_datas, 'original_rakuten_url', '' );
		} else {
			$original_url = $this->array_get( $meta_datas, 'original_rakuten_title_url', '' );
		}
		if ( $original_url !== '' ) {
			$meta_datas[ 'rakuten_title_url' ] = $this->generate_rakuten_title_link_with_aid( $original_url, $post_id );
			$meta_datas[ 'rakuten_image_url' ] = $this->generate_rakuten_title_link_with_aid( $original_url, $post_id, 'image' );
		}
		$meta_datas[ 'rakuten_title_link' ] =$this->title_html( $meta_datas, self::SHOP_TYPE_RAKUTEN );
		$meta_datas[ 'rakuten_image_link' ]	 = $this->image_html( $meta_datas, self::SHOP_TYPE_RAKUTEN );

		//Amazonタイトルリンク
		$original_url = $this->array_get( $meta_datas, 'original_amazon_title_url', '' );
		if ( $original_url !== '' ) {
			$meta_datas[ 'amazon_title_url' ] = $this->generate_amazon_title_link_with_aid( $original_url, $post_id );
		}
		$meta_datas[ 'amazon_title_link' ] = $this->title_html( $meta_datas, self::SHOP_TYPE_AMAZON );
		$meta_datas[ 'amazon_image_link' ]	 = $this->image_html( $meta_datas, self::SHOP_TYPE_AMAZON);

		foreach ([ 1, 2, 3, 4 ] as $num) {
			$meta_datas = $this->free_link_html( $num, $meta_datas );
		}

		$meta_datas[ 'credit' ] = 'created by&nbsp;<a href="https://oyakosodate.com/rinker/" rel="nofollow noopener" target="_blank" >Rinker</a>';

		return $meta_datas;
	}

	public function image_html( $meta_datas, $shop_type ) {
		if ( $shop_type === self::SHOP_TYPE_RAKUTEN ) {
			$title_image_url =  $this->array_get($meta_datas, 'rakuten_image_url', '');
		} else {
			$title_image_url =  $this->array_get($meta_datas, 'amazon_title_url', '');
		}

		$image_size = $this->array_get( $meta_datas, 'size', '');
		$image_column = $this->getImageColumn( $image_size, false );
		$image_path = $this->array_get( $meta_datas, $image_column, '' );
		$size = $this->image_size_html( $image_column, $meta_datas );

		$html = '';
		if ( strlen( $title_image_url ) > 0) {
			$rel_target_text = $this->get_rel_target_text();

			if ( $this->is_tracking ) {
				$click_tracking_data = esc_attr( $shop_type ) . '_img '  . esc_attr( $meta_datas[ 'post_id' ] ). ' ' . esc_attr( $meta_datas[ 'title' ] );
				$html .= '<a href="' . esc_url( $title_image_url ) . '" ' . $rel_target_text . ' class="yyi-rinker-tracking"  data-click-tracking="' . $click_tracking_data . '" data-vars-click-id="' . $click_tracking_data . '">';
				$html .= '<img src="' . esc_url( $image_path ) . '" ' . $size . ' class="yyi-rinker-main-img" style="border: none;"></a>';
			} else {
				$html .= '<a href="' . esc_url( $title_image_url ) . '" ' . $rel_target_text . '><img src="' . esc_url( $image_path ) . '"' .  $size . ' class="yyi-rinker-main-img" style="border: none;"></a>';
			}
			if ( $this->is_moshimo( $shop_type ) ) {
				$html .= $this->add_tracking_img( $shop_type );
			}
		} else {
			$html .= '<img src="' . esc_url( $image_path ) . '" ' . $size . ' style="border: none;" class="yyi-rinker-main-img">';
		}
		return $html;
	}

	public function image_size_html($image_column, $meta_datas) {
		$width = $this->getImageWidth( $image_column, $meta_datas );
		$height = $this->getImageHeight( $image_column, $meta_datas );
		$size = '';
		if ( strlen( $width ) > 0 ) {
			$size .=  ' width="' .  esc_attr( $width ) . '"';
		}
		if ( strlen( $height) > 0) {
			$size .=  ' height="' .  esc_attr( $height ) . '"';
		}
		return $size;
	}

	public function title_html( $meta_datas, $shop_type ) {
		if ( $shop_type === self::SHOP_TYPE_RAKUTEN ) {
			$title_url = $this->array_get( $meta_datas, 'rakuten_title_url', '');
		} else {
			$title_url = $this->array_get( $meta_datas, 'amazon_title_url', '');
		}

		$html = '';
		if ( strlen( $title_url ) > 0) {
			$rel_target_text = $this->get_rel_target_text();
			if ( $this->is_tracking ) {
				$click_tracking_data = esc_attr( $shop_type ) . '_title ' . esc_attr( $meta_datas[ 'post_id' ] ). ' ' . esc_attr( $meta_datas[ 'title' ] );
				$html .= '<a href="' . esc_url( $title_url ) . '" ' . $rel_target_text . ' class="yyi-rinker-tracking" data-click-tracking="' . $click_tracking_data . '" data-vars-amp-click-id="' . $click_tracking_data . '" >' . esc_html( $meta_datas[ 'title' ] ) . '</a>';
			} else {
				$html .= '<a href="' . esc_url( $title_url ) . '" ' . $rel_target_text . '>' . esc_html( $meta_datas[ 'title' ] ) . '</a>';
			}
			if ( $this->is_moshimo( $shop_type ) ) {
				$html .= $this->add_tracking_img( $shop_type );
			}
		} else {
			$html .= esc_html( $meta_datas[ 'title' ] );
		}
		return $html;
	}

	/**
	 * フリーURLのリンクを作成します
	 * ここでエスケープをする
	 * @param $num
	 * @param $meta_datas
	 *
	 * @return mixed
	 */
	public function free_link_html( $num , $meta_datas ) {
		$key = 'free_url_' . $num;
		$label = $this->array_get( $meta_datas, 'free_url_label_' . $num . '_column', '' );
		$free_url_column = $this->array_get( $meta_datas, $key, '' );

		if ( $free_url_column  !== '' ) {
			$rel_target_text = $this->get_rel_target_text();
			if ( $this->is_tracking ) {
				$click_tracking_data = 'free_' . esc_attr( $num )  . ' ' . esc_attr( $meta_datas[ 'post_id' ] ). ' ' . esc_attr( $meta_datas[ 'title' ] );
				$html = '<a href="' . esc_attr( $free_url_column ) . '" ' . $rel_target_text . ' class="yyi-rinker-link yyi-rinker-tracking" data-click-tracking="' . $click_tracking_data . '" data-vars-amp-click-id="' . $click_tracking_data . '">' . esc_html( $label ) . '</a>';
			} else {
				$html = '<a href="' . esc_attr( $free_url_column ) . '" ' . $rel_target_text . ' class="yyi-rinker-link">' . esc_html( $label ) . '</a>';
			}
			$meta_datas[ $key ] = $html;
		}
		return $meta_datas;
	}


	/**
	 * リンク部分のHTMLを作成
	 * ここでエスケープをする
	 * @param $meta_datas
	 * @param $shop_type
	 *
	 * @return string
	 */
	public function link_html( $meta_datas, $shop_type, $values, $atts ) {
		//urlの設定がない場合は非表示
		if ( !isset($meta_datas[$shop_type . '_url'] ) ) {
			return '';
		}

		switch( $shop_type ) {
			case self::SHOP_TYPE_AMAZON:
				if ( strlen( $this->amazon_traccking_id ) === 0 && !$this->is_moshimo( $shop_type )  ) {
					return '';
				}
				$label = $this->array_get( $atts, 'alabel', '' );

				break;
			case self::SHOP_TYPE_RAKUTEN:
				if ( strlen( $this->rakuten_affiliate_id ) === 0 && !$this->is_moshimo( $shop_type ) ) {
					return '';
				}
				$label = $this->array_get( $atts, 'rlabel' );
				break;
			case self::SHOP_TYPE_YAHOO:
				if ( !( ( strlen( $this->yahoo_pid ) > 0 && strlen( $this->yahoo_pid ) > 0 ) || strlen( $this->yahoo_linkswitch ) > 0 ) && !$this->is_moshimo( $shop_type ) ) {
					return '';
				}
				$label = $this->array_get( $atts, 'ylabel', '' );
				break;
			case self::SHOP_TYPE_AMAZON_KINDLE:
				if ( strlen( $this->amazon_traccking_id ) === 0 && !$this->is_moshimo( $shop_type )  ) {
					return '';
				}
				$label = $this->array_get( $atts, 'klabel', '' );
				break;
			default:
				$label = '';
				break;
		}

		if ($label === '') {
			$label = $values[ 'label' ];
		}

		$rel_target_text = $this->get_rel_target_text();

		if ( $this->is_tracking ) {
			$click_tracking_data = $shop_type . ' ' . esc_attr( $meta_datas[ 'post_id' ] ). ' ' . esc_attr( $meta_datas[ 'title' ] );
			$html = '<a href="' . esc_attr( $meta_datas[$shop_type . '_url'] ) . '" ' . $rel_target_text . ' class="yyi-rinker-link yyi-rinker-tracking"  data-click-tracking="' . $click_tracking_data . '"  data-vars-amp-click-id="' . $click_tracking_data .'">';
		} else {
			$html = '<a href="' . esc_attr( $meta_datas[$shop_type . '_url'] ) . '" ' . $rel_target_text . ' class="yyi-rinker-link">';
		}

		$label = apply_filters( $this->add_prefix( 'button_label_text' ), $label, $shop_type);
		$html = $html . $this->allow_tags( $label );

		$html = $html . '</a>';

		if ( $shop_type === self::SHOP_TYPE_YAHOO && $this->is_yahoo_id() && !$this->is_moshimo( $shop_type ) ) {
			$html = $html . '<img src="https://ad.jp.ap.valuecommerce.com/servlet/gifbanner?sid=' . esc_attr( $this->yahoo_sid ) . '&pid=' . esc_attr( $this->yahoo_pid ) . '" height="1" width="1" border="0">';
		}

		if ( $this->is_moshimo( $shop_type ) ) {
			$html .= $this->add_tracking_img( $shop_type );
		}
		return $html;
	}

	/**
	 * 安全なタグだけ利用させるボタン名に使用
	 */
	public function allow_tags( $text ) {
		$allowed_html = [
			'div' => ['class' => [], 'id' => []],
			'span' => ['class' => [], 'id' => []],
			'br' => [],
			'i' => ['class' => [], 'id' => []],
			'strong' => [],
		];
		return  wp_kses( $text, $allowed_html );
	}

	public function add_tracking_img( $shop_type ) {
		$a_id	= $this->shop_types[ $shop_type ][ 'a_id' ];
		$p_id	= $this->shop_types[ $shop_type ][ 'p_id' ];
		$pc_id	= $this->shop_types[ $shop_type ][ 'pc_id' ];
		$pl_id	= $this->shop_types[ $shop_type ][ 'pl_id' ];
		$url = 'https://i.moshimo.com/af/i/impression?a_id=' . $a_id . '&p_id=' . $p_id . '&pc_id=' . $pc_id . '&pl_id=' . $pl_id;
		return '<img src="' . esc_attr( $url ) . '" width="1" height="1" style="border:none;">';
	}


	/**
	 * 商品追加ボタンを追加します
	 * yyi: 使用されてない？
	 */
	public function media_buttons() {
		global $post_ID;
		add_thickbox();
		$src = 'media-upload.php?post_id=' . intval($post_ID) . '&amp;type=' . $this->media_type . '&amp;tab=' . self::TAB_AMAZON;
		echo '<a id="yyirinker-media-button" href="' .  esc_attr( $src . '&TB_iframe=true' ) . '" type="button" class="button thickbox add_media" title="商品リンク追加"><span class="yyirinker-buttons-icon"></span>商品リンク追加</a>';
	}


	/**
	 * Rinker設定ページを更新
	 */
	function option_page() {
		$params = [];
		$this->option_params = apply_filters( $this->add_prefix( 'update_option_params' ), $this->option_params );

		if ( !current_user_can( 'manage_options' )) {
			include_once 'parts/cannot_user.php';
			exit;
		}

		if ( isset( $_POST['_wp_http_referer'] ) ) {
			check_admin_referer( $this->_admin_referer_column );
			foreach( $this->option_params AS $key => $v ) {
				if ( $v[ 'is_digit' ] ) {
					$values = $this->array_get($_POST, $key, []);
					$value = 0;
					if (is_array($values)) {
						foreach ($values AS $index => $val) {
							$value += intval($val);
						}
					}
				} elseif ( $v[ 'is_bool' ] ) {
					$value = $this->array_get( $_POST, $key, null);
					$value = !!$value ? 1 : 0;

				} elseif ( isset( $v[ 'is_check' ]) && !!$v[ 'is_check' ] ) {
					$value = $this->array_get( $_POST, $key, null);
					$value = intval( $value );
				} else {
					$value = $this->array_get( $_POST, $key, null);
				}
				$params[ $key ] = $value;
				$this->option_params[ $key ][ 'value' ] = $value;
			}

			foreach( $params AS $key => $value ) {
				update_option( $this->option_column_name( $key ), $value);
			}
			add_action( $this->add_prefix( 'admin_notices' ), array( $this, 'updated_message' ) );

		} else {
			foreach($this->option_params AS $key => $v) {
				$value = get_option( $this->option_column_name( $key ) );

				if ( $value ) {
					$params[$key] = $value;
					$this->option_params[$key]['value'] = $value;
				}
			}
		}
		do_action( $this->add_prefix( 'admin_notices' ) );
		include_once 'parts/setting-form.php';
	}


	public static function get_object() {
		static $instance;

		if ( NULL === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LinkSwitchタグ設置
	 */
	public function add_linkswitch_tag() {
		$tag = $this->yahoo_linkswitch;
		if ( strlen( $tag ) > 0 ) {
			echo stripslashes($tag);
		}
	}

	/*
	 * Rinker [商品リンク]のカスタムフィールドに接頭語をつける
	 */
	public function custom_field_column_name( $key_name ) {
		return $this->add_prefix( $key_name );
	}

	/*
	 * Rinker固有の設定項目に接頭語をつける
	 */
	public function option_column_name( $key_name ) {
		return $this->add_prefix( $key_name );
	}

	//add prefix text
	public function add_prefix($text) {
		return self::APP_PREFIX . '_' . $text;
	}

	/**
	 * image info from amazon api
	 * @param object $image
	 *
	 * @return array
	 */
	static public function set_image_info($image) {
		return [
			'url'		=>  $image->URL,
			'width'		=>  $image->WIDTH,
			'height'	=>  $image->HEIGHT,
		];
	}

	public function init() {

	}
}

/**
 * for Gutenberg
 */

class Yyi_Rinker_Plugin_Gutenberg extends Yyi_Rinker_Abstract_Base
{
	function __construct()
	{
		//Gutenbergが非アクティブのときは何もしない
		if (!function_exists('register_block_type')) {
			return;
		}
		add_action('init', array($this, 'gutenberg_rinker_register_block'));
	}

	function gutenberg_rinker_register_block()
	{
		if (is_admin()) {
			wp_register_script(
				'gutenberg-rinker',
				plugins_url('js/block.js', __FILE__),
				['wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'underscore'],
				getlastmod()
			);

			wp_register_style(
				'gutenberg-rinker',
				plugins_url('css/style.css', __FILE__) . '?v=1.0.0',
				[],
				getlastmod()
			);
		}

		register_block_type('rinkerg/gutenberg-rinker', [
			'style' => 'gutenberg-rinker',
			'script' => 'gutenberg-rinker',
			'editor_script' => 'gutenberg-rinker',
			'attributes' => [
				'content' => ['type' => 'array'],
				'content_text' => ['type' => 'string'],
				'alignment' => ['type' => 'string'],
				'post_id' => ['type' => 'string'],
				'title' => ['type' => 'string'],
				'size' => ['type' => 'string'],
				'alabel' => ['type' => 'string'],
				'rlabel' => ['type' => 'string'],
				'ylabel' => ['type' => 'string'],
				'klabel' => ['type' => 'string'],
			],
			'render_callback' => array( $this, 'gutenberg_rinker_render' ),
		]);

		wp_add_inline_script(
			'gutenberg-rinker',
			sprintf(
				'var gutenberg_rinker = { localeData: %s };',
				json_encode('rinker-gutenberg-1.0.0')
			),
			'before'
		);
	}

	function gutenberg_rinker_render( $attributes, $value )
	{
		//エディタで編集した時
		if ( isset( $attributes['content_text'] ) ) {
			$shortcode = $attributes['content_text'];
			$new_shortcode = $this->shortcode_attribute_relpace( $shortcode, $attributes );
			$html = do_shortcode( $new_shortcode );
			if ($html === '') {
				return '';
			} else {
				return $html;
			}
		} elseif( strlen( $value ) > 0 ) {
			$html = $this->shortcode_attribute_relpace( $value, $attributes );
			return $html;
		} else {
			return '';
		}
	}

	public function shortcode_attribute_relpace( $shortcode, $attributes ) {
		$new_shortcode = $shortcode;
		foreach ( $this->shortcode_params as $i => $key ) {
			if ( isset( $attributes[ $key ] ) && strlen( trim( $attributes[ $key ] ) ) > 0 ) {
				$result = preg_match("/{$key}=\"\S+\"/", $new_shortcode, $m);
				$new_attr = $key . '="' . esc_html( trim( $attributes[ $key ] ) ) . '"';
				if ( $result === 1 ){
					$new_shortcode = str_replace( $m[0], $new_attr, $new_shortcode );
				} elseif ( $result === 0 ) {
					$new_shortcode = preg_replace("/\](<\/p>)?$/", ' ' . $new_attr . ']', $new_shortcode, 1);
				}
			}
		}
		return $new_shortcode;
	}

	public static function init() {
		$instance = new self;
		return $instance;
	}
}

Yyi_Rinker_Plugin_Gutenberg::init();

/**
 * Rinker用CSSを記述
 * @return string
 */
function yyi_rinker_style_up_design() {
	return '
div.yyi-rinker-contents div.yyi-rinker-box ul.yyi-rinker-links li {
    list-style: none;
}
div.yyi-rinker-contents ul.yyi-rinker-links {
	border: none;
}
div.yyi-rinker-contents ul.yyi-rinker-links li a {
	text-decoration: none;
}
div.yyi-rinker-contents {
    margin: 2em 0;
}
div.yyi-rinker-contents div.yyi-rinker-box {
    display: flex;
    padding: 26px 26px 0;
    border: 3px solid #f5f5f5;
    box-sizing: border-box;
}
@media (min-width: 768px) {
    div.yyi-rinker-contents div.yyi-rinker-box {
        padding: 26px 26px 0;
    }
}
@media (max-width: 767px) {
    div.yyi-rinker-contents div.yyi-rinker-box {
        flex-direction: column;
        padding: 26px 14px 0;
    }
}
div.yyi-rinker-box div.yyi-rinker-image {
    display: flex;
    flex: none;
    justify-content: center;
}
div.yyi-rinker-box div.yyi-rinker-image a {
    display: inline-block;
    height: fit-content;
    margin-bottom: 26px;
}
div.yyi-rinker-image img.yyi-rinker-main-img {
    display: block;
    max-width: 100%;
    height: auto;
}
div.yyi-rinker-img-s img.yyi-rinker-main-img {
    width: 56px;
}
div.yyi-rinker-img-m img.yyi-rinker-main-img {
    width: 120px;
}
div.yyi-rinker-img-l img.yyi-rinker-main-img {
    width: 200px;
}
div.yyi-rinker-box div.yyi-rinker-info {
    display: flex;
    width: 100%;
    flex-direction: column;
}
@media (min-width: 768px) {
    div.yyi-rinker-box div.yyi-rinker-info {
        padding-left: 26px;
    }
}
@media (max-width: 767px) {
    div.yyi-rinker-box div.yyi-rinker-info {
        text-align: center;
    }
}
div.yyi-rinker-info div.yyi-rinker-title a {
    color: #333;
    font-weight: 600;
    font-size: 18px;
    text-decoration: none;
}
div.yyi-rinker-info div.yyi-rinker-detail {
    display: flex;
    flex-direction: column;
    padding: 8px 0 12px;
}
div.yyi-rinker-detail div:not(:last-child) {
    padding-bottom: 8px;
}
div.yyi-rinker-detail div.credit-box {
    font-size: 12px;
}
div.yyi-rinker-detail div.credit-box a {
    text-decoration: underline;
}
div.yyi-rinker-detail div.brand,
div.yyi-rinker-detail div.price-box {
    font-size: 14px;
}
@media (max-width: 767px) {
    div.price-box span.price {
        display: block;
    }
}
div.yyi-rinker-info div.free-text {
    order: 2;
    padding-top: 8px;
    font-size: 16px;
}
div.yyi-rinker-info ul.yyi-rinker-links {
    display: flex;
    flex-wrap: wrap;
    margin: 0 0 14px;
    padding: 0;
    list-style-type: none;
}
div.yyi-rinker-info ul.yyi-rinker-links li {
    display: inherit;
    flex-direction: column;
    align-self: flex-end;
    text-align: center;
}
@media (min-width: 768px) {
    div.yyi-rinker-info ul.yyi-rinker-links li:not(:last-child){
        margin-right: 8px;
    }
    div.yyi-rinker-info ul.yyi-rinker-links li {
        margin-bottom: 12px;
    }
}
@media (max-width: 767px) {
    div.yyi-rinker-info ul.yyi-rinker-links li {
        width: 100%;
        margin-bottom: 10px;
    }
}
ul.yyi-rinker-links li.amazonkindlelink a {
    background-color: #37475a;
}
ul.yyi-rinker-links li.amazonlink a {
    background-color: #f9bf51;
}
ul.yyi-rinker-links li.rakutenlink a {
    background-color: #d53a3a;
}
ul.yyi-rinker-links li.yahoolink a {
    background-color: #76c2f3;
}
ul.yyi-rinker-links li.freelink1 a {
    background-color: #5db49f;
}
ul.yyi-rinker-links li.freelink2 a {
    background-color: #7e77c1;
}
ul.yyi-rinker-links li.freelink3 a {
    background-color: #3974be;
}
ul.yyi-rinker-links li.freelink4 a {
    background-color: #333;
}
ul.yyi-rinker-links a.yyi-rinker-link {
    display: flex;
    position: relative;
    width: 100%;
    min-height: 38px;
    overflow-x: hidden;
    flex-wrap: wrap-reverse;
    justify-content: center;
    align-items: center;
    border-radius: 2px;
    box-shadow: 0 1px 6px 0 rgba(0,0,0,0.12);
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    transition: 0.3s ease-out;
    box-sizing: border-box;
}
ul.yyi-rinker-links a.yyi-rinker-link:after {
    position: absolute;
    right: 12px;
    width: 6px;
    height: 6px;
    border-top: 2px solid;
    border-right: 2px solid;
    content: "";
    transform: rotate(45deg);
    box-sizing: border-box;
}
ul.yyi-rinker-links a.yyi-rinker-link:hover {
    box-shadow: 0 4px 6px 2px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}
@media (min-width: 768px) {
    ul.yyi-rinker-links a.yyi-rinker-link {
        padding: 6px 24px;
    }
}
@media (max-width: 767px) {
    ul.yyi-rinker-links a.yyi-rinker-link {
        padding: 10px 24px;
    }
}';
}

