<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

?>

<h3 class="pchpp-setting__h3">ポチップボックスのデザイン</h3>

<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>スタイル</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'box_style',
					'choices' => [
						'default' => '標準',
						'radius'  => '丸め',
					],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>ボタンサイズ（PC）</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'max_columns_pc',
					'choices' => [
						'fit'     => '自動フィット',
						'text'    => 'テキストに応じる',
						'3'       => '3列幅',
						'2'       => '2列幅',
					],
				]);
			?>
		</dd>
	</dl>
	<!-- <dl class="pchpp-setting__dl">
		<dt>ボタンの最大列数（SP）</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'max_columns_sp',
					'choices' => [
						'2'  => '2列',
						'1'  => '1列',
					],
				]);
			?>
		</dd>
	</dl> -->
</div>


<h3 class="pchpp-setting__h3">各ボタンの表示テキスト</h3>
<!-- <p class="pchpp-setting__p"></p> -->
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>Amazonボタン</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'amazon_btn_text',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>楽天市場ボタン</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'rakuten_btn_text',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>Yahooショッピングボタン</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'yahoo_btn_text',
				]);
			?>
		</dd>
	</dl>
</div>


<h3 class="pchpp-setting__h3">カスタムボタンの色</h3>
<!-- <p class="pchpp-setting__p"></p> -->
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>カラーコード</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'custom_btn_color',
				]);
			?>
		</dd>
	</dl>
</div>


<h3 class="pchpp-setting__h3">ボタンのリンクターゲット</h3>
<!-- <p class="pchpp-setting__p"></p> -->
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<!-- <dt></dt> -->
		<dd>
			<?php
				\POCHIPP::output_checkbox([
					'key'   => 'show_amazon_normal_link',
					'label' => 'リンク先を別ウィンドウで開く',
				]);
			?>
			<p class="pchpp-setting__desc">
				チェックをオンにすると、各ボタンに<code>target="_blank"</code>がつきます。
			</p>
		</dd>
	</dl>
</div>
