<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

?>
<h3 class="pcpp-setting__h3">APIの設定</h3>
<p>
	Rinkerを利用するためにはAmazonのAmazon Product Advertising APIの認証キーを取得する必要があります。<br>
	<a href="https://affiliate.amazon.co.jp/assoc_credentials/home" target="_blank" rel="noopener noreferrer">Amazon Product Advertising APIの認証キーを取得</a>からキーを取得して、アクセスキーID、シークレットキーを登録してください。<br>
	認証キーの詳しい取得方法は<a href="https://oyakosodate.com/rinker/getamazonapikey/" target="_blank" rel="noopener noreferrer">Amazon Product Advertising APIの認証キー取得方法</a>にも記載しています。
</p>
<?php
	\POCHIPP::output_text_field([
		'key'   => 'amazon_access_key',
		'label' => 'アクセスキー',
	]);

	\POCHIPP::output_text_field([
		'key'   => 'amazon_secret_key',
		'label' => 'シークレットキー',
	]);
?>
<h3 class="pcpp-setting__h3">アソシエイツのトラッキングID</h3>
<p>
	利用できるトラッキングIDは<a href="https://affiliate.amazon.co.jp/home/account/tag/manage" target="_blank" rel="noopener noreferrer">トラッキングIDの管理</a>から確認できます。
</p>
<?php
	\POCHIPP::output_text_field([
		'key'   => 'amazon_traccking_id',
		'label' => 'トラッキングID',
	]);
?>

<h3 class="pcpp-setting__h3">Amazonボタンのリンク先</h3>
<?php
	\POCHIPP::output_radio([
		'key'     => 'amazon_btn_target',
		'choices' => [
			'searched' => '検索結果ページ',
			'detail'   => '商品の詳細ページ',
		],
	]);
?>
