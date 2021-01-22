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


// function cb_pochipp_setting( $attrs, $content ) {
// 	$title       = $attrs['title'] ?? '';
// 	$render_args = json_decode( $attrs['meta'], true ) ?: [];
// 	return \POCHIPP\render_pochipp_block( $title, $render_args );
// }

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

	// 商品未選択時
	if ( ! $title ) {
		if ( defined( 'REST_REQUEST' ) ) {
			return '<p class="__nullText"></p>';
		} else {
			return '';
		}
	}

	// $attr > $metadata の優先度
	$render_args = array_merge( $metadata, $attrs );

	return \POCHIPP\render_pochipp_block( $title, $render_args );

}

function render_pochipp_block( $title = '', $pdata = [] ) {

	$pdata = array_merge([
		'keywords'           => '',
		'searched_at'        => '',
		'amazon_detail_url'  => '',
		'rakuten_detail_url' => '',
		'info'               => '',
		// 'brand'              => '',
		// 'contributors'       => '',
		// 'shop_name'          => '',
		'price'              => 0,
		'price_at'           => '',
		'l_image_url'        => '',
		'm_image_url'        => '',
		's_image_url'        => '',
	], $pdata );

	$amazon_link  = \POCHIPP::get_amazon_searched_affi_link( $pdata['keywords'] );
	$rakuten_link = \POCHIPP::get_rakuten_searched_affi_link( $pdata['keywords'] );
	$yahoo_link   = \POCHIPP::get_yahoo_searched_affi_link( $pdata['keywords'] );

	// Amazon詳細ページを優先する場合
	if ( 1 && $pdata['amazon_detail_url'] ) {
		$amazon_link = \POCHIPP::get_amazon_detail_affi_link( $pdata['amazon_detail_url'] );
	}

	// 楽天詳細ページを優先する場合
	if ( 1 && $pdata['rakuten_detail_url'] ) {
		$rakuten_link = \POCHIPP::get_rakuten_detail_affi_link( $pdata['rakuten_detail_url'] );
	}

	// 画像とかタイトル部分のリンク先
	$main_link = ( 'rakuten' === $pdata['searched_at'] ) ? $rakuten_link : $amazon_link;

	// どのサイズの画像使うかは設定で？
	$image_src = $pdata['l_image_url'] ?: $pdata['m_image_url'] ?: $pdata['s_image_url'] ?: '';

	// 商品メタ情報
	$info = $pdata['info'];

	ob_start();

	?>
		<div class="pochipp-box">
			<div class="pochipp-box__image">
				<a href="<?=esc_url( $main_link )?>" rel="nofollow" target="_blank">
					<img src="<?=esc_url( $image_src )?>" alt="" />
				</a>
			</div>
			<div class="pochipp-box__body">
				<div class="pochipp-box__title">
					<a href="<?=esc_url( $main_link )?>" rel="nofollow" target="_blank">
						<?=esc_html( $title )?>
					</a>
				</div>

				<?php if ( $info ) : ?>
					<div class="pochipp-box__info"><?=esc_html( $info )?></div>
				<?php endif; ?>

				<?php if ( $pdata['price'] ) : ?>
					<div class="pochipp-box__price">
						¥<?=esc_html( number_format( (int) $pdata['price'] ) )?>
						<span>（<?=esc_html( $pdata['price_at'] )?>時点）</span>
					</div>
				<?php endif; ?>

				<div class="pochipp-box__btns">
					<?php if ( $amazon_link ) : ?>
						<div class="pochipp-box__btnwrap">
							<a href="<?=esc_url( $amazon_link )?>" class="pochipp-box__btn -amazon" rel="nofollow" target="_blank">
								Amazon
							</a>
						</div>
					<?php endif; ?>
					<?php if ( $rakuten_link ) : ?>
						<div class="pochipp-box__btnwrap">
							<a href="<?=esc_url( $rakuten_link )?>" class="pochipp-box__btn -rakuten" rel="nofollow" target="_blank">
								楽天市場
							</a>
						</div>
					<?php endif; ?>
					<?php if ( $yahoo_link ) : ?>
						<div class="pochipp-box__btnwrap">
							<a href="<?=esc_url( $yahoo_link )?>" class="pochipp-box__btn -yahoo" rel="nofollow" target="_blank">
								Yahooショッピング
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
// 			'slug'  => 'useful-blocks',  // ブロックカテゴリーのスラッグ
// 			'title' => 'ポチップ',
// 		],
// 	];
// 	return array_merge( $categories, $my_category );
// }
