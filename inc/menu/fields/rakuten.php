<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<h3 class="pcpp-setting__h3">アフィリエイトID</h3>
<p>
	楽天のアフィリエイトIDは<a href="https://webservice.rakuten.co.jp/account_affiliate_id/" target="_blank" rel="noopener noreferrer">楽天のアフィリエイトID</a>からアフィリエイトIDを調べて登録してください。<br>アプリIDの詳しい取得方法は<a href=" https://oyakosodate.com/rinker/getrakutenapplicationid/" target="_blank" rel="noopener noreferrer">楽天のアフィリエイトIDの取得方法</a>にも記載しています。
</p>
<?php
	\POCHIPP::output_text_field([
		'key'   => 'rakuten_affiliate_id',
		'label' => 'アフィリエイトID',
	]);
?>
<h3 class="pcpp-setting__h3">楽天ボタンのリンク先</h3>
<?php
	\POCHIPP::output_radio([
		'key'     => 'rakuten_btn_target',
		'choices' => [
			'searched' => '検索結果ページ',
			'detail'   => '商品の詳細ページ',
		],
	]);
?>
