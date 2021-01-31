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

	$tabs = [];

	// エディターからモーダルが開かれた時、タブを追加
	$at = \POCHIPP::array_get( $_GET, 'at', '' );
	if ( 'editor' === $at ) {
		$tabs[ \POCHIPP::TABKEYS['registerd'] ] = '登録済み商品を検索';
	}

	// 共通
	$tabs[ \POCHIPP::TABKEYS['amazon'] ]  = 'Amazonで検索';
	$tabs[ \POCHIPP::TABKEYS['rakuten'] ] = '楽天市場で検索';
	$tabs[ \POCHIPP::TABKEYS['yahoo'] ]   = 'Yahooショッピングで検索';

	return $tabs;
}
