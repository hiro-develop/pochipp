<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pochippブロック
 */
add_action( 'init', function() {

	register_block_type_from_metadata(
		POCHIPP_PATH . 'src/blocks/linkbox',
		[
			'render_callback'  => '\POCHIPP\cb_pochipp_block',
		]
	);

	// 設定用ブロック
	// $metadata = json_decode( file_get_contents( POCHIPP_PATH . 'src/blocks/setting/block.json' ), true );
	// register_block_type( $metadata['name'], [
	// 	'attributes'      => $metadata['attributes'],
	// 	'editor_script'   => 'pochipp-setting-block',
	// 	'render_callback' => '\POCHIPP\cb_pochipp_setting',
	// ] );
} );


function cb_pochipp_block( $attrs, $content ) {

	$pid      = $attrs['pid'] ?? 0;
	$title    = $attrs['title'] ?? '';
	$metadata = [];

	// メタデータ取得
	if ( $pid ) {
		$title    = $title ?: get_the_title( $pid );
		$metadata = get_post_meta( $pid, \POCHIPP::META_SLUG, true );
		$metadata = json_decode( $metadata, true ) ?: [];
	}

	// echo '<pre style="margin-left: 100px;">';
	// var_dump( $metadata );
	// echo '</pre>';

	// 商品未選択時
	if ( ! $title ) {
		if ( defined( 'REST_REQUEST' ) ) {
			return '<p class="__nullText"></p>';
		} else {
			return '';
		}
	}

	// 空情報を削除
	$attrs = array_filter( $attrs, function ( $elem ) {
		return ! empty( $elem );
	} );

	// $attr > $metadata の優先度
	$render_args = array_merge( $metadata, $attrs );

	return \POCHIPP\render_pochipp_block( $title, $render_args );

}

function render_pochipp_block( $title = '', $pdata = [] ) {

	// ※ 定期的に、データの再取得も行う
	$pdata = array_merge([
		'keywords'           => '',
		'searched_at'        => '',
		'amazon_affi_url'    => '',
		'rakuten_detail_url' => '',
		'info'               => '',
		'price'              => 0,
		'price_at'           => '',
		'image_url'          => '',
		// 'image_url_s'        => '',
		'custom_btn_url'     => '',
		'custom_btn_text'    => '',
		'hideInfo'           => false,
		'hidePrice'          => false,
		'hideAmazon'         => false,
		'hideRakuten'        => false,
		'hideYahoo'          => false,
	], $pdata );

	$keywords    = $pdata['keywords'];
	$searched_at = $pdata['searched_at'];
	$asin        = $pdata['asin'];
	$image_url   = $pdata['image_url'];

	$main_url    = '';
	$amazon_url  = '';
	$rakuten_url = '';
	$yahoo_url   = '';

	// もしも用aid
	$amazon_aid  = \POCHIPP::get_setting( 'moshimo_amazon_aid' );
	$rakuten_aid = \POCHIPP::get_setting( 'moshimo_rakuten_aid' );
	$yahoo_aid   = \POCHIPP::get_setting( 'moshimo_yahoo_aid' );

	// AmazonボタンURL
	if ( ! $pdata['hideAmazon'] ) {
		// $pdata['amazon_custom_url']
		$amazon_url = $asin ? 'https://www.amazon.co.jp/dp/' . $asin : \POCHIPP::get_amazon_searched_url( $keywords );
		$amazon_url = \POCHIPP::get_amazon_affi_url( $pdata['amazon_affi_url'], $amazon_url, $amazon_aid );
	}

	// 楽天ボタンURL
	if ( ! $pdata['hideRakuten'] ) {
		// $pdata['rakuten_custom_url']
		$rakuten_url = $pdata['rakuten_detail_url'] ?: \POCHIPP::get_rakuten_searched_url( $keywords );
		$rakuten_url = \POCHIPP::get_rakuten_affi_url( $rakuten_url, $rakuten_aid );
	}

	// YahooボタンURL
	if ( ! $pdata['hideYahoo'] ) {
		// $pdata['yahoo_custom_url']
		$yahoo_url = \POCHIPP::get_yahoo_searched_url( $keywords );
		$yahoo_url = \POCHIPP::get_yahoo_affi_url( $yahoo_url, $yahoo_aid );
	}

	// 画像とかタイトル部分のリンク先
	if ( 'rakuten' === $searched_at ) {
		$main_url = $rakuten_url ?: $amazon_url ?: $yahoo_url;
	} else {
		$main_url = $amazon_url ?: $rakuten_url ?: $yahoo_url;
	}

	// 商品画像
	if ( $image_url ) {
		if ( 'rakuten' === $searched_at ) $image_url .= '?_ex=400x400';
		if ( 'amazon' === $searched_at ) $image_url   = str_replace( '.jpg', '._SL400_.jpg', $image_url );
	}

	$is_blank = \POCHIPP::get_setting( 'show_amazon_normal_link' );

	if ( $is_blank ) {
		$rel_target = 'rel="nofollow noopener" target="_blank"';
	} else {
		$rel_target = 'rel="nofollow"';
	}

	$price_memo = $pdata['price_at'] . '時点';
	if ( 'rakuten' === $searched_at ) {
		$price_memo .= ' | 楽天市場調べ';
	} elseif ( 'amazon' === $searched_at ) {
		$price_memo .= ' | Amazon調べ';
	}

	ob_start();

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
		<div class="pochipp-box">
			<?php if ( $image_url ) : ?>
				<div class="pochipp-box__image">
					<a href="<?=esc_url( $main_url )?>" <?=$rel_target?>>
						<img src="<?=esc_url( $image_url )?>" alt="" />
					</a>
				</div>
			<?php endif; ?>
			<div class="pochipp-box__body">
				<div class="pochipp-box__title">
					<a href="<?=esc_url( $main_url )?>" <?=$rel_target?>>
						<?=esc_html( $title )?>
					</a>
				</div>

				<?php if ( ! $pdata['hideInfo'] && $pdata['info'] ) : ?>
					<div class="pochipp-box__info"><?=esc_html( $pdata['info'] )?></div>
				<?php endif; ?>

				<?php if ( ! $pdata['hidePrice'] && $pdata['price'] ) : ?>
					<div class="pochipp-box__price">
						¥<?=esc_html( number_format( (int) $pdata['price'] ) )?>
						<span>（<?=esc_html( $price_memo )?>）</span>
					</div>
				<?php endif; ?>

				<div class="pochipp-box__btns">
					<?php if ( $amazon_url ) : ?>
						<div class="pochipp-box__btnwrap">
							<a href="<?=esc_url( $amazon_url )?>" class="pochipp-box__btn -amazon" <?=$rel_target?>>
								<?php
									echo esc_html( \POCHIPP::get_setting( 'amazon_btn_text' ) );
									echo \POCHIPP::get_amazon_imptag( $amazon_aid );
								?>
							</a>
						</div>
					<?php endif; ?>
					<?php if ( $rakuten_url ) : ?>
						<div class="pochipp-box__btnwrap">
							<a href="<?=esc_url( $rakuten_url )?>" class="pochipp-box__btn -rakuten" <?=$rel_target?>>
								<?php
									echo esc_html( \POCHIPP::get_setting( 'rakuten_btn_text' ) );
									echo \POCHIPP::get_rakuten_imptag( $rakuten_aid );
								?>
							</a>
						</div>
					<?php endif; ?>
					<?php if ( $yahoo_url ) : ?>
						<div class="pochipp-box__btnwrap">
							<a href="<?=esc_url( $yahoo_url )?>" class="pochipp-box__btn -yahoo" <?=$rel_target?>>
								<?php
									echo esc_html( \POCHIPP::get_setting( 'yahoo_btn_text' ) );
									echo \POCHIPP::get_yahoo_imptag( $yahoo_aid );
								?>
							</a>
						</div>
					<?php endif; ?>
					<?php if ( $pdata['custom_btn_url'] && $pdata['custom_btn_text'] ) : ?>
						<div class="pochipp-box__btnwrap">
							<a href="<?=esc_url( $pdata['custom_btn_url'] )?>" class="pochipp-box__btn -custom" <?=$rel_target?>>
								<?php
									echo esc_html( $pdata['custom_btn_text'] );
								?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
			</div>
	<?php

	return ob_get_clean();
}


/**
 * ブロックカテゴリー追加
 */
// add_filter( 'block_categories', '\POCHIPP\add_block_categories' );
// function add_block_categories( $categories ) {

// 	$my_category = [
// 		[
// 			'slug'  => 'useful - blocks',  // ブロックカテゴリーのスラッグ
// 			'title' => 'ポチップ',
// 		],
// 	];
// 	return array_merge( $categories, $my_category );
// }
