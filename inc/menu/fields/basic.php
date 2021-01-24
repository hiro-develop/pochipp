<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

?>
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
