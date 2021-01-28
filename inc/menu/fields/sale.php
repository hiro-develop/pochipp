<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/*
<h3 class="pchpp-setting__h3">セール情報の表示位置</h3>
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>タブレット・PCサイズ</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'sale_position_pc',
					'choices' => [
						'top'   => 'ボタン上部',
						'inner' => 'ボタン内部',
					],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>モバイルサイズ</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'sale_position_mb',
					'choices' => [
						'top'   => 'ボタン上部',
						'inner' => 'ボタン内部',
					],
				]);
			?>
		</dd>
	</dl>
</div>
*/
?>

<h3 class="pchpp-setting__h3">Amazonセール情報</h3>
<!-- <p class="pchpp-setting__p"></p> -->
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>表示テキスト</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'amazon_sale_text',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>いつまでか</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'amazon_sale_deadline',
				]);
			?>
			<p class="pchpp-setting__desc">
				<code>Y/m/d G:i</code>の形式で入力してください。 例 : <code>2020/01/05 10:00</code>, <code>2020/11/25 20:00</code>
			</p>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>セール中に隠すボタン</dt>
		<dd class="-flex">
			<?php
				\POCHIPP::output_checkbox([
					'key'   => 'hide_rakuten_at_amazon_sale',
					'label' => '楽天ボタン',
				]);
				\POCHIPP::output_checkbox([
					'key'   => 'hide_yahoo_at_amazon_sale',
					'label' => 'Yahooボタン',
				]);
				\POCHIPP::output_checkbox([
					'key'   => 'hide_custom_at_amazon_sale',
					'label' => 'カスタムボタン',
				]);
			?>
		</dd>
	</dl>
</div>

<h3 class="pchpp-setting__h3">楽天セール情報</h3>
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>楽天市場セール情報</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'rakuten_sale_text',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>いつまでか</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'rakuten_sale_deadline',
				]);
			?>
			<p class="pchpp-setting__desc">
				<code>Y/m/d G:i</code>の形式で入力してください。 例 : <code>2020/01/05 10:00</code>, <code>2020/11/25 20:00</code>
			</p>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>セール中に隠すボタン</dt>
		<dd class="-flex">
			<?php
				\POCHIPP::output_checkbox([
					'key'   => 'hide_amazon_at_rakuten_sale',
					'label' => 'Amazonボタン',
				]);
				\POCHIPP::output_checkbox([
					'key'   => 'hide_yahoo_at_rakuten_sale',
					'label' => 'Yahooボタン',
				]);
				\POCHIPP::output_checkbox([
					'key'   => 'hide_custom_at_rakuten_sale',
					'label' => 'カスタムボタン',
				]);
			?>
		</dd>
	</dl>
</div>

<h3 class="pchpp-setting__h3">Yahooセール情報</h3>
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>Yahooショッピングセール情報</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'yahoo_sale_text',
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>いつまでか</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'yahoo_sale_deadline',
				]);
			?>
			<p class="pchpp-setting__desc">
				<code>Y/m/d G:i</code>の形式で入力してください。 例 : <code>2020/01/05 10:00</code>, <code>2020/11/25 20:00</code>
			</p>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>セール中に隠すボタン</dt>
		<dd class="-flex">
			<?php
				\POCHIPP::output_checkbox([
					'key'   => 'hide_amazon_at_yahoo_sale',
					'label' => 'Amazonボタン',
				]);
				\POCHIPP::output_checkbox([
					'key'   => 'hide_rakuten_at_yahoo_sale',
					'label' => '楽天ボタン',
				]);
				\POCHIPP::output_checkbox([
					'key'   => 'hide_custom_at_yahoo_sale',
					'label' => 'カスタムボタン',
				]);
			?>
		</dd>
	</dl>
</div>

<h3 class="pchpp-setting__h3">セール情報の表示エフェクト</h3>
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<!-- <dt>アクセント</dt> -->
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'sale_text_effect',
					'choices' => [
						'none'  => 'なし',
						'flash' => '点滅',
					],
				]);
			?>
		</dd>
	</dl>
</div>
