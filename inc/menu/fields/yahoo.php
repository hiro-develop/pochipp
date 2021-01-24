<?php
namespace POCHIPP;

// バリューコマースでYahoo!ショッピングのsidとpidを取得する方法
// https://wp-cocoon.com/valuecommerce-yahoo-sid-pid/

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- <p>
	[LinkSwitch]か[アフィリエイトID]どちらかを設定してください。両方設定しても動作します。
</p> -->

<h3 class="pchpp-setting__h3">LinkSwitchの設定</h3>
<div class="pchpp-setting__div">
	<p class="pchpp-setting__p">
		バリューコマースの「LinkSwitch」を使用したい場合は、
		<a href="https://aff.valuecommerce.ne.jp/ad/AutoMyLink/getTag" target="_blank" rel="noopener noreferrer">LinkSwitch設定</a>ページの「LinkSwitchタグ」に記載されているコードを設定してください。
	</p>
	<dl class="pchpp-setting__dl">
		<dt>LinkSwitchタグ</dt>
		<dd>
			<?php
				\POCHIPP::output_textarea([
					'key'  => 'yahoo_linkswitch',
					'rows' => '5',
				]);
			?>
		</dd>
	</dl>
</div>

<?php
/*
<h3 class="pchpp-setting__h3">アフィリエイトIDの設定</h3>
<div class="pchpp-setting__div">
	<p class="pchpp-setting__p">
		バリューコマースの「sid」と「pid」を設定することで、商品リンクがアフィリエイトリンクに自動変換されます。
		<br>
		「sid」と「pid」の確認方法は<a href="###" target="_blank" rel="noopener noreferrer">こちらのページ</a>で解説しています。
	</p>
	<dl class="pchpp-setting__dl">
		<dt>sid</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'yahoo_sid',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>pid</dt>
		<dd>
		<?php
				\POCHIPP::output_text_field([
					'key' => 'yahoo_pid',
				]);
			?>
		</dd>
	</dl>
</div>
*/
