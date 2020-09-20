<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * media_upload_{$action} フックで iframe 読み込み
 */
add_action( 'media_upload_pochipp', function() {
	wp_enqueue_style( 'pochipp-iframe', POCHIPP_URL . 'dist/css/iframe.css', [], POCHIPP_VERSION );
	wp_enqueue_script( 'pochipp-iframe', POCHIPP_URL . 'dist/js/search.js', [], POCHIPP_VERSION, true );
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

	$tabs = [
		\POCHIPP::TABKEY_AMAZON  => 'Amazonから商品検索',
		\POCHIPP::TABKEY_RAKUTEN => '楽天市場から商品検索',
	];

	// エディターからモーダルが開かれた時、タブを追加
	$from = \POCHIPP::array_get( $_GET, 'from', '' );
	if ( 'editor' === $from ) {
		$tabs[ \POCHIPP::TABKEY_REGISTERD ] = '登録済み商品リンクから検索';
	}

	return $tabs;
}


/**
 * 登録済み商品リンクから検索タブの使用時デフォルトで商品を出しておく
 * yyi: 使われていない。 phpのhookでセットしておくためのものだと思うが、結局これもJSのajaxで取得 -> 描画 されている。
 */
function search_result_items( $tab ) {
	echo '登録済み商品の一覧';
	// if ($tab === self::TAB_ITEMLIST) {
	// 	$datas = $this->get_search_itemlist( 0, '' );
	// 	foreach ( $datas as $data ) {
	// 		echo '<li class="items"><div class="img">';
	// 		echo '<img src="' . esc_url( $data[ 's_image_url' ] ) . '"></div>';
	// 		echo '<div class="detail"><div class="title">' . esc_html( $data[ 'title' ] ). '</div>';
	// 		echo '<div class="links"><a class="button" href="' . esc_url( $data[ 'amazon_url' ] ) . '" rel="nofollow noopener" target="_blank">Amazon確認</a>';
	// 		echo '<a class="button" href="' . esc_url( $data[ 'rakuten_url' ] ) . '" rel="nofollow noopener" target="_blank">楽天確認</a>';
	// 		echo '<a class="button" href="' . esc_url( $data[ 'yahoo_url' ] ) . '" rel="nofollow noopener" target="_blank">Yahoo確認</a>';
	// 		echo '<a class="button" href="' . esc_url( admin_url() ) . 'post.php?post=' . esc_attr( $data[ 'post_id' ] ) . '&action=edit" rel="nofollow noopener" target="_blank">リンク編集</a></div>';
	// 		echo '<div class="button-box"><button class="button select add-items-from-list" data-item-post-id="' . esc_attr( $data[ 'post_id' ] ) . '" >商品リンクを追加</button></div>';
	// 		echo '</div>';
	// 		echo '</li>';
	// 	}
	// }
}
