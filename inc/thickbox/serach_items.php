<?php
/**
 * 商品検索部分の中身
 */

// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited

// iframeのURLから受け取るパラメータ
$tab    = \POCHIPP::array_get( $_GET, 'tab' ) ?: \POCHIPP::TABKEY_AMAZON;
$cid    = \POCHIPP::array_get( $_GET, 'blockid' ) ?: 0;
$postid = \POCHIPP::array_get( $_GET, 'postid' ) ?: 0;
$at     = \POCHIPP::array_get( $_GET, 'at' ) ?: '';

// 各タブにおける共通パーツ
$pochipp_url  = esc_url( POCHIPP_URL . 'assets/img/search-solid.svg' );
$common_parts = <<<HTML
<div class="pchpp-tb__keywords">
	<input id="keywords" type="text" name="keywords" placeholder="キーワードを入力してください">
	<button id="submit" class="button" type="submit" >
		<img src="{$pochipp_url}" alt="" width="20" height="20" >
	</button>
</div>
HTML;
?>

<script type="text/javascript">
	window.pochippIframeVars = {
		adminUrl: "<?=esc_js( admin_url() )?>", // 管理画面URL
		ajaxUrl: "<?=esc_js( admin_url( 'admin-ajax.php' ) )?>", // Ajax用URL
		tabKey: "<?=esc_js( $tab )?>", // 現在のタブ種別
		blockId: "<?=esc_js( $cid )?>", // ブロックID
		calledAt: "<?=esc_js( $at )?>", // どこから呼び出されたか
	};
</script>
<div id="pochipp_tb_content" class="pchpp-tb -<?=esc_attr( $tab )?> wp-core-ui">
	<?php media_upload_header(); // タブ呼び出し ?>
	<div class="pchpp-tb__body">
		<div id="search_area" class="pchpp-tb__search">
			<form id="search_form" method="GET" action="<?=esc_url( admin_url( 'admin-ajax.php' ) ) ?>">
				<?php
					// Amazonタブ
					if ( \POCHIPP::TABKEY_AMAZON === $tab ) :
					include __DIR__ . '/form_amazon.php';
					endif;

					// 楽天タブ
					if ( \POCHIPP::TABKEY_RAKUTEN === $tab ) :
						include __DIR__ . '/form_rakuten.php';
					endif;

					// 登録済み商品タブ
					if ( \POCHIPP::TABKEY_REGISTERD === $tab ) :
						include __DIR__ . '/form_registerd.php';
					endif;
				?>
			</form>
		</div>
		<div id="result_area" class="pchpp-tb__result">
			<!-- 検索結果がここに描画される -->
		</div>
		<div id="loading_image">
			<img src="<?=esc_url( POCHIPP_URL )?>assets/img/loading.gif" alt="">
		</div>
	</div>
</div>
