<?php
/**
 * 商品検索部分の中身
 */



// iframeのURLから受け取るパラメータ
$tab    = \POCHIPP\array_get( $_GET, 'tab' ) ?: \POCHIPP::TABKEY_AMAZON;
$cid    = \POCHIPP\array_get( $_GET, 'blockid' ) ?: 0;
$postid = \POCHIPP\array_get( $_GET, 'postid' ) ?: 0;
$at     = \POCHIPP\array_get( $_GET, 'at' ) ?: '';

$terms = get_terms( \POCHIPP::TAXONOMY_SLUG, [ 'fields' => 'id=>name' ] );
?>
<div id="pochipp_tb_content" class="pcpp-tb">
	<?php media_upload_header(); // タブ呼び出し ?>
	<div class="pcpp-tb__body">
		<div id="search_area" class="pcpp-tb__search">
			<form id="search_form" method="GET" action="<?=admin_url( 'admin-ajax.php' )?>">
				<input type="hidden" nama="tab" value="<?=esc_attr( $tab )?>"/>

				<input type="hidden" nama="action" value="search_amazon"> <!-- yyi: 使われてない？ -->

				<!-- ブロックエディターから呼び出された時用 -->
				<input type="hidden" nama="blockid" value="<?=esc_attr( $cid )?>">

				<!-- どこから呼び出されたか -->
				<input type="hidden" nama="at" value="<?=esc_attr( $at )?>">

				<!-- テスト用 -->
				<input type="hidden" nama="date" value="<?=date( 'gis' );?>"> 

				<!-- Amazonタブ -->
				<?php if ( \POCHIPP::TABKEY_AMAZON === $tab ) : ?>
					<select id="search_index" name="search_index">
						<?php foreach ( \POCHIPP::$search_indexes as $key => $val ) : ?>
							<option value="<?=esc_attr( $key )?>"><?=esc_html( $val )?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>


				<!-- 楽天タブ -->
				<?php if ( \POCHIPP::TABKEY_RAKUTEN === $tab ) : ?>
					<select id="sort_select" name="sort">
						<option value="0">--並び順--</option>
						<?php foreach ( \POCHIPP::$rakuten_sorts as $id => $values ) : ?>
							<option value="<?=esc_attr( $id )?>"><?=esc_html( $values['label'] )?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>

				<!-- 登録済み商品タブ -->
				<?php if ( \POCHIPP::TABKEY_REGISTERD === $tab ) : ?>
					<select id="term_select" name="term_id">
					<option value="0">--カテゴリー選択--</option>
					<?php if ( ! is_wp_error( $terms ) ) : ?>
						<?php foreach ( $terms as $id => $the_term ) : ?>
							<option value="<?=esc_attr( $id )?>"><?=esc_html( $the_term )?></option>
						<?php endforeach; ?>
					<?php endif; ?>
					</select>
				<?php endif; ?>

				<input id="keywords" type="text" name="keywords" placeholder="キーワードを入力してください">
				<!-- <input class="button" type="submit" value="検索"> -->
				<button id="submit" class="button" type="submit" >
					<img src="<?=POCHIPP_URL?>assets/img/search-solid.svg" alt="" width="20" height="20" >
				</button>
			</form>
		</div>
		<div id="result_area" class="pcpp-tb__result">
			<!-- 検索結果がここに描画される -->
		</div>
		<div id="loading_image" style="display:none">
			<img src="<?=POCHIPP_URL?>assets/img/loading.gif" alt="">
		</div>
	</div>
</div>
