<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

$btn_style = \POCHIPP::get_setting( 'btn_style' );
	if ( 'default' === $btn_style ) {
	$btn_style = 'dflt';
	}
?>

<h3 class="pchpp-setting__h3">ボックスのデザイン設定</h3>
<div class="pchpp-setting__div">
	<div class="pchpp-setting__preview">
		<div class="__wrap">
			<!-- <div class="__label">プレビュー</div> -->
			<div class="__inner">
				<div class="pochipp-box"
					data-img="<?php echo esc_attr( \POCHIPP::get_setting( 'img_position' ) ); ?>"
					data-lyt-pc="<?php echo esc_attr( \POCHIPP::get_setting( 'box_layout_pc' ) ); ?>"
					data-lyt-mb="<?php echo esc_attr( \POCHIPP::get_setting( 'box_layout_mb' ) ); ?>"
					data-btn-style="<?php echo esc_attr( $btn_style ); ?>"
					data-btn-radius="<?php echo esc_attr( \POCHIPP::get_setting( 'btn_radius' ) ); ?>"
					data-sale-effect="<?php echo esc_attr( \POCHIPP::get_setting( 'sale_text_effect' ) ); ?>"
				>
					<div class="pochipp-box__image">
						<a href="###" rel="nofollow">
							<img src="<?php echo esc_url( POCHIPP_URL ); ?>assets/img/box_preview_img.png" alt="">
						</a>
					</div>
					<div class="pochipp-box__body">
						<div class="pochipp-box__title">
							<a href="###" rel="nofollow">
							Lorem Ipsum Watch 商品タイトル 腕時計 ABC-Z3 最新 防水 ソーラー</a>
						</div>
						<div class="pochipp-box__info">Lorem Ipsum</div>
						<div class="pochipp-box__price">¥10,000 <span>（2021/01/01 11:11時点 | 〇〇調べ）</span></div>
					</div>
					<div class="pochipp-box__btns"
						data-maxclmn-pc="<?php echo esc_attr( \POCHIPP::get_setting( 'max_column_pc' ) ); ?>"
						data-maxclmn-mb="<?php echo esc_attr( \POCHIPP::get_setting( 'max_column_mb' ) ); ?>"
					>
						<div class="pochipp-box__btnwrap -amazon">
							<a href="###" class="pochipp-box__btn" rel="nofollow">Amazon</a>
						</div>
						<div class="pochipp-box__btnwrap -rakuten">
							<a href="###" class="pochipp-box__btn" rel="nofollow">楽天市場</a>
						</div>
						<div class="pochipp-box__btnwrap -yahoo">
							<a href="###" class="pochipp-box__btn" rel="nofollow">Yahooショッピング</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<dl class="pchpp-setting__dl">
		<dt>レイアウト（PC）</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'box_layout_pc',
					'class'   => '-flex',
					'choices' => [
						'dflt' => '標準',
						'big'  => 'ビッグ',
					],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>レイアウト（モバイル）</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'box_layout_mb',
					'class'   => '-flex',
					'choices' => [
						'vrtcl' => '縦並び',
						'flex'  => '画像とタイトル横並び',
					],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>画像の配置</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'img_position',
					'class'   => '-flex',
					'choices' => [
						'l' => '左',
						'r' => '右',
					],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>ボタンスタイル</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'btn_style',
					'class'   => '-flex',
					'choices' => [
						'dflt'    => '標準',
						'outline' => 'アウトライン',
					],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>ボタンの丸み</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'btn_radius',
					'class'   => '-flex',
					'choices' => [
						'off' => '四角',
						'on'  => '丸め',
					],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>ボタン幅（PC）</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'max_column_pc',
					'class'   => '-flex',
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
	<dl class="pchpp-setting__dl">
		<dt>ボタン幅（モバイル）</dt>
		<dd>
			<?php
				\POCHIPP::output_radio([
					'key'     => 'max_column_mb',
					'class'   => '-flex',
					'choices' => [
						'1'  => '1列幅',
						'2'  => '2列幅 <small>（※ セール情報表示中のボタンは1列幅に広がります。）</small>',
					],
				]);
			?>
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
	<dl class="pchpp-setting__dl">
		<dt>PayPayモールボタン</dt>
		<dd>
			<?php
				\POCHIPP::output_text_field([
					'key' => 'paypay_btn_text',
				]);
			?>
			<p class="pchpp-setting__desc">
				Yahooショッピングから検索した商品リンクがPayPayモールのURLだった時に、「Yahooショッピングボタン」の代わりに表示されます。
			</p>
		</dd>
	</dl>
</div>


<h3 class="pchpp-setting__h3">カスタムボタンの色</h3>
<!-- <p class="pchpp-setting__p"></p> -->
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dt>カスタムボタン1</dt>
		<dd>
			<?php
				\POCHIPP::output_colorpicker([
					'key'     => 'custom_btn_color',
					'default' => \Pochipp::$default_data['custom_btn_color'],
				]);
			?>
		</dd>
	</dl>
	<dl class="pchpp-setting__dl">
		<dt>カスタムボタン2</dt>
		<dd>
			<?php
				\POCHIPP::output_colorpicker([
					'key'     => 'custom_btn_color_2',
					'default' => \Pochipp::$default_data['custom_btn_color_2'],
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


<h3 class="pchpp-setting__h3">商品情報の定期更新</h3>
<div class="pchpp-setting__div">
	<dl class="pchpp-setting__dl">
		<dd>
			<?php
				\POCHIPP::output_checkbox([
					'key'   => 'auto_update',
					'label' => '商品情報を自動更新する',
				]);
			?>
			<p class="pchpp-setting__desc">
				チェックをオンにすると、定期的（約1週間ごと）に商品情報を更新します。<br>
				<small>※ 更新される情報は 価格・画像・商品URL のみです。</small><br>
				<small>※ 更新対象の商品は、ポチップ管理に登録済みで、かつ Amazon API または 楽天 APIで検索された商品のみです。</small>
			</p>
		</dd>
	</dl>
</div>
