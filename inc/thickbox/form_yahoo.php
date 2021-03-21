<?php
/**
 * 楽天商品検索フォーム
 */
if ( ! \POCHIPP::get_setting( 'yahoo_app_id' ) ) {
	$pochipp_setting_url = admin_url( 'edit.php?post_type=pochipps&page=pochipp_settings&tab=yahoo' );
	echo '<a href="' . esc_url( $pochipp_setting_url ) . '" target="_blank">ポチップ設定ページ</a>から、Yahoo APIの「アプリケーションID」の設定を行ってください。';
	return;
}

echo $common_parts; // phpcs:ignore
