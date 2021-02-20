<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- <p>
	[LinkSwitch]か[アフィリエイトID]どちらかを設定してください。両方設定しても動作します。
</p> -->
<div class="pchpp-setting__section_help">
	<p>
		※ ご利用前にYahoo!ショッピングの公式ドキュメントをご確認ください。<br>
		<a href="https://developer.yahoo.co.jp/webapi/shopping/api_contract.html" target="_blank" rel="noopener noreferrer">Yahoo!ショッピング出品API 利用約款</a>
	</p>
	<div class="__helpLink">
		アプリケーションIDやLinkSwitchの設定方法は<a href="https://pochipp.com/189/" target="_blank" rel="noopener noreferrer" class="dashicons-before dashicons-book-alt">こちらのページ</a>で解説しています。
	</div>
</div>


<h3 class="pchpp-setting__h3">アプリケーションIDの設定</h3>
<div class="pchpp-setting__div">
	<p class="pchpp-setting__p">
		Yahoo!ショッピングWeb APIを使って商品検索をするためには、<a href="https://developer.yahoo.co.jp/" target="_blank" rel="noopener noreferrer">Yahoo!デベロッパーネットワーク</a>から発行可能な「アプリケーションID」（アプリケーションの「Client ID」）の設定が必要です。
	</p>
	<dl class="pchpp-setting__dl">
		<dt>アプリケーションID<br> ( Client ID )</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'yahoo_app_id',
				]);
			?>
		</dd>
	</dl>
</div>

<h3 class="pchpp-setting__h3">LinkSwitchの設定</h3>
<div class="pchpp-setting__div">
	<p class="pchpp-setting__p">
		バリューコマースの「LinkSwitch」を使用したい場合は、
		<a href="https://aff.valuecommerce.ne.jp/ad/AutoMyLink/getTag" target="_blank" rel="noopener noreferrer">LinkSwitch設定</a>ページの「LinkSwitchタグ」に記載されている<code>vc_pid</code>を設定してください。
	</p>
	<p>※「LinkSwitchタグ」を丸ごとペーストすると自動で<code>vc_pid</code>が抽出されます</p>
	<dl class="pchpp-setting__dl">
		<dt>LinkSwitch vc_pid</dt>
		<dd>
			<?php
				\POCHIPP::output_textarea([
					'key'  => 'yahoo_linkswitch',
					'rows' => '3',
				]);
			?>
		</dd>
	</dl>
</div>

<script>
	(function() {
		function trimTags() {
			document.querySelector('#yahoo_linkswitch').addEventListener('input', function(e) {
				var inputVal = e.target.value;
				this.value = inputVal.replace(/[^0-9]/g, '');
			});
		}

		trimTags();
	})();
</script>


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
