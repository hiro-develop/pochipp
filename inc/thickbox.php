<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * media_upload_{$action} フックで iframe 読み込み
 */
add_action( 'media_upload_pochipp', function() {
	wp_enqueue_style( 'pochipp-iframe', POCHIPP_URL . 'dist/css/iframe.css', [], POCHIPP_VERSION );
	wp_enqueue_script( 'pochipp-iframe', POCHIPP_URL . 'dist/js/search.js', [ 'jquery' ], POCHIPP_VERSION, true );
	wp_iframe( '\POCHIPP\load_search_fom_iframe' );
} );


/**
 * 商品選択iframe
 */
function load_search_fom_iframe() {

	// タブはフックで定義
	add_filter( 'media_upload_tabs', '\POCHIPP\media_upload_tabs', 999 );

	// body
	include POCHIPP_PATH . 'inc/thickbox/serach_items.php';
}


/**
 * 商品リンク追加画面のタブ設定
 */
function media_upload_tabs() {

	$at   = \POCHIPP::array_get( $_GET, 'at', '' );
	$only = \POCHIPP::array_get( $_GET, 'only', '' );

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
			// 投稿のエディターからモーダルが開かれた時だけ追加する
			$tabs[ \POCHIPP::TABKEYS['registerd'] ] = '登録済み商品を検索';
		}
		$tabs[ \POCHIPP::TABKEYS['amazon'] ]  = $tab_labels['amazon'];
		$tabs[ \POCHIPP::TABKEYS['rakuten'] ] = $tab_labels['rakuten'];
		$tabs[ \POCHIPP::TABKEYS['yahoo'] ]   = $tab_labels['yahoo'];
	}

	return $tabs;
}
