<?php
/**
 * 登録済み商品検索フォーム
 */

// 商品カテゴリー取得
$item_terms = get_terms( \POCHIPP::TAXONOMY_SLUG, [ 'fields' => 'id=>name' ] );
?>
<select id="term_select" name="term_id">
	<option value="0">商品カテゴリーを選択</option>
	<?php if ( ! is_wp_error( $item_terms ) ) : ?>
		<?php foreach ( $item_terms as $term_id => $term_name ) : ?>
			<option value="<?=esc_attr( $term_id )?>"><?=esc_html( $term_name )?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>
<?php echo $common_parts; // phpcs:ignore ?>
