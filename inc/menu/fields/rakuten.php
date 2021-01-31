<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="pchpp-setting__section_help">
	ご利用前に楽天の公式ドキュメントをご確認ください。<br>
	<a href="https://webservice.faq.rakuten.net/hc/ja/categories/900000158383-%E3%81%94%E5%88%A9%E7%94%A8%E3%83%AB%E3%83%BC%E3%83%AB" target="_blank" rel="noopener noreferrer">Rakuten Devekipers ご利用ルール契約</a>
</div>


<h3 class="pchpp-setting__h3">アプリIDの設定</h3>
<div class="pchpp-setting__div">
	<p class="pchpp-setting__p">
		楽天APIを使って商品検索をするためには、<a href="https://webservice.rakuten.co.jp/" target="_blank" rel="noopener noreferrer">Rakuten Developers</a>から発行可能な「アプリID」の設定が必要です。
		<br>
		<!-- アプリIDの詳しい取得方法については、<a href="###" target="_blank" rel="noopener noreferrer">こちらのページ</a>で解説しています。 -->
	</p>
	<dl class="pchpp-setting__dl">
		<dt>アプリID<br>( デベロッパーID )</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key'   => 'rakuten_app_id',
				]);
			?>
		</dd>
	</dl>
</div>


<h3 class="pchpp-setting__h3">楽天アフィリエイトIDの設定</h3>
<div class="pchpp-setting__div">
	<p class="pchpp-setting__p">
		楽天の「アフィリエイトID」を設定することで、商品リンクがアフィリエイトリンクに自動変換されます。
		<br>
		<a href="https://webservice.rakuten.co.jp/account_affiliate_id/" target="_blank" rel="noopener noreferrer">アフィリエイトIDの確認ページ</a>からIDを調べて登録してください。
		<br>
		<!-- アプリIDの詳しい取得方法については、<a href="###" target="_blank" rel="noopener noreferrer">こちらのページ</a>で解説しています。 -->
	</p>
	<dl class="pchpp-setting__dl">
		<dt>アフィリエイトID</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key'   => 'rakuten_affiliate_id',
				]);
			?>
		</dd>
	</dl>
</div>

<?php
/*
<h3 class="pchpp-setting__h3">楽天ボタンのリンク先</h3>
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<!-- <dt></dt> -->
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'rakuten_btn_target',
					'choices' => [
						'searched' => '検索結果ページ',
						'detail'   => '商品の詳細ページ',
					],
				]);
			?>
		</dd>
	</dl>
</div>
*/
