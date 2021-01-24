<?php
/**
 * 楽天商品検索フォーム
 */
// const RAKUTEN_APP_ID = '1098412079780620197';
if ( ! \POCHIPP::get_setting( 'rakuten_app_id' ) ) {
	$pochipp_setting_url = admin_url( 'edit.php?post_type=pochipps&page=pochipp_settings&tab=rakuten' );
	echo '<a href="' . esc_url( $pochipp_setting_url ) . '" target="_blank">ポチップ設定ページ</a>から、楽天APIの「アプリID」の設定を行ってください。';
	return;
}
?>
<select id="sort_select" name="sort">
	<option value="0">--並び順--</option>
	<?php foreach ( \POCHIPP::$rakuten_sorts as $sort_id => $values ) : ?>
		<option value="<?=esc_attr( $sort_id )?>"><?=esc_html( $values['label'] )?></option>
	<?php endforeach; ?>
</select>
<?php echo $common_parts; // phpcs:ignore ?>
