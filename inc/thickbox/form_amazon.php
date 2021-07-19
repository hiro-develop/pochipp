<?php
/**
 * Amazon商品検索フォーム
 */
$can_use_amazon_search = apply_filters(
	'pochipp_can_use_amazon_search',
	\POCHIPP::get_setting( 'amazon_access_key' ) && \POCHIPP::get_setting( 'amazon_secret_key' )
);

if ( ! $can_use_amazon_search ) {
	$pochipp_setting_url = admin_url( 'edit.php?post_type=pochipps&page=pochipp_settings&tab=amazon' );
	?>
	<p><a href="<?php echo esc_url( $pochipp_setting_url ); ?>">ポチップ設定ページ</a>から、Amazon APIの「アクセスキー」と「シークレットキー」の設定を行ってください。</p>
	<p>もしくは、<a href="https://pochipp.com/pochipp-assist/">Pochipp Assist</a>をご利用ください。</p>
	<?php
	return;
}

echo $common_parts; // phpcs:ignore
