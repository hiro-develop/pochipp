<?php
/**
 * 登録済み商品検索フォーム
 */

$item_terms = get_terms( \POCHIPP::TAXONOMY_SLUG, [ 'fields' => 'id=>name' ] );
?>
<div class="pchpp-tb__selectbox">
	<label for="term_select">商品カテゴリー : </label>
	<select id="term_select" name="term_id">
		<option value="0">-- 選択してください --</option>
		<?php if ( ! is_wp_error( $item_terms ) ) : ?>
			<?php foreach ( $item_terms as $term_id => $term_name ) : ?>
				<option value="<?php echo esc_attr( $term_id ); ?>"><?php echo esc_html( $term_name ); ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>
</div>
<div class="pchpp-tb__selectbox">
	<label for="sort_select">並び順 : </label>
	<select id="sort_select" name="sort">
		<option value="new">登録が新しい順</option>
		<option value="old">登録が古い順</option>
		<option value="count">呼び出し回数が多い順</option>
	</select>
</div>
<?php echo $common_parts; // phpcs:ignore ?>
