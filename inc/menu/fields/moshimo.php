<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="pchpp-setting__section_help">
	<div class="__helpLink">
		もしもアフィリエイトに関するの設定方法は<a href="https://pochipp.com/211/" target="_blank" rel="noopener noreferrer" class="dashicons-before dashicons-book-alt">こちらのページ</a>で解説しています。
	</div>
</div>

<h3 class="pchpp-setting__h3">もしもリンクのa_id</h3>
<p class="pchpp-setting__p">各ボタン用の<code>a_id</code>を設定すると、もしもアフィリエイトのリンクに自動変換されて出力されます。</p>
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>Amazonの<code>a_id</code></dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'moshimo_amazon_aid',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>楽天市場の<code>a_id</code></dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'moshimo_rakuten_aid',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>Yahooショッピングの<code>a_id</code></dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'moshimo_yahoo_aid',
				]);
			?>
		</dd>
	</dl>
</div>
