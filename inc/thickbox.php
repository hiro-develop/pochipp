<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * media_upload_{$action} : iframeを読み込む
 */
add_action( 'media_upload_pochipp', function() {
	wp_enqueue_style( 'pochipp-iframe', POCHIPP_URL . 'dist/css/iframe.css', [], \POCHIPP::$version );
	wp_enqueue_script( 'pochipp-iframe', POCHIPP_URL . 'dist/js/search.js', [ 'jquery' ], \POCHIPP::$version, true );
	wp_iframe( '\POCHIPP\load_search_iframe' );
} );


/**
 * 商品選択 iframe
 */
function load_search_iframe() {

	// タブ
	add_filter( 'media_upload_tabs', '\POCHIPP\register_search_tab', 999 );

	// コンテンツ
	include POCHIPP_PATH . 'inc/thickbox/serach_items.php';
}


/**
 * 商品リンク追加画面のタブ設定
 */
function register_search_tab() {

	$at   = \POCHIPP::get_sanitized_data( $_GET, 'at', 'text', '' );
	$only = \POCHIPP::get_sanitized_data( $_GET, 'only', 'text', '' ); // ショップを限定する場合、そのショップのスラッグ

	$tab_labels = [
		'amazon'  => 'Amazonで検索',
		'rakuten' => '楽天市場で検索',
		'yahoo'   => 'Yahooショッピングで検索',
	];

	$tabs = [];
	if ( $only ) {
		$tabs[ \POCHIPP::TABKEYS[ $only ] ] = $tab_labels[ $only ];
	} else {
		if ( 'editor' === $at ) {
			// 投稿エディターからモーダルが開かれた時だけ追加
			$tabs[ \POCHIPP::TABKEYS['registerd'] ] = '登録済み商品を検索';
		}
		$tabs[ \POCHIPP::TABKEYS['amazon'] ]  = $tab_labels['amazon'];
		$tabs[ \POCHIPP::TABKEYS['rakuten'] ] = $tab_labels['rakuten'];
		$tabs[ \POCHIPP::TABKEYS['yahoo'] ]   = $tab_labels['yahoo'];
	}

	return $tabs;
}
