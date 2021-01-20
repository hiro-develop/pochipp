<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pochippブロック
 */

register_block_type_from_metadata(
	POCHIPP_PATH . 'src/blocks/linkbox',
	[
		'render_callback'  => '\POCHIPP\cb_blog_card',
	]
);


function cb_blog_card( $attrs, $content ) {

	$pid    = $attrs['pid'] ?? 0;
	$ptitle = $attrs['title'] ?? '';
	$pmeta  = [];

	// メタデータ取得
	if ( $pid ) {
		$ptitle = $ptitle ?: get_the_title( $pid );
		$pmeta  = get_post_meta( $pid, \POCHIPP::META_SLUG, true );
		$pmeta  = json_decode( $pmeta, true ) ?: [];
	}

	// 商品未選択時
	if ( ! $ptitle ) {
		if ( defined( 'REST_REQUEST' ) ) {
			return '<p>商品がまだ選択されていません</p>';
		} else {
			return '';
		}
	}

	// 以下、 $attr > $pmeta の優先度で各情報を取得していく
	$keywords           = $attrs['keywords'] ?? $pmeta['keywords'] ?? '';
	$searched_at        = $attrs['searched_at'] ?? $pmeta['searched_at'] ?? '';
	$amazon_detail_url  = $attrs['amazon_detail_url'] ?? $pmeta['amazon_detail_url'] ?? '';
	$rakuten_detail_url = $attrs['rakuten_detail_url'] ?? $pmeta['rakuten_detail_url'] ?? '';
	$brand              = $attrs['brand'] ?? $pmeta['brand'] ?? '';
	$contributors       = $attrs['contributors'] ?? $pmeta['contributors'] ?? '';
	$shop_name          = $attrs['shop_name'] ?? $pmeta['shop_name'] ?? '';
	$price              = $attrs['price'] ?? $pmeta['price'] ?? 0;
	$price_at           = $attrs['price_at'] ?? $pmeta['price_at'] ?? '';
	$l_image_url        = $attrs['l_image_url'] ?? $pmeta['l_image_url'] ?? '';
	$m_image_url        = $attrs['m_image_url'] ?? $pmeta['m_image_url'] ?? '';
	$s_image_url        = $attrs['s_image_url'] ?? $pmeta['s_image_url'] ?? '';

	$amazon_link  = \POCHIPP::get_amazon_searched_affi_link( $keywords );
	$rakuten_link = \POCHIPP::get_rakuten_searched_affi_link( $keywords );
	$yahoo_link   = \POCHIPP::get_yahoo_searched_affi_link( $keywords );

	// Amazon詳細ページを優先する場合
	if ( 1 && $amazon_detail_url ) {
		$amazon_link = \POCHIPP::get_amazon_detail_affi_link( $amazon_detail_url );
	}

	// 楽天詳細ページを優先する場合
	if ( 1 && $rakuten_detail_url ) {
		$rakuten_link = \POCHIPP::get_rakuten_detail_affi_link( $rakuten_detail_url );
	}

	// 画像とかタイトル部分のリンク先
	$main_link = ( 'rakuten' === $searched_at ) ? $rakuten_link : $amazon_link;

	// どのサイズの画像使うかは設定で？
	$image_src = $l_image_url ?: $m_image_url ?: $s_image_url ?: '';

	// 商品メタ情報
	$meta_info = '';
	if ( 'rakuten' === $searched_at && $shop_name ) {
		$meta_info = $shop_name;
	} elseif ( $brand ) {
		$meta_info = $brand;
	} elseif ( $contributors ) {
		$meta_info = $contributors;
	}

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
						<?=esc_html( $ptitle )?>
					</a>
				</div>

				<?php if ( $meta_info ) : ?>
					<div class="pochipp-box__meta"><?=esc_html( $meta_info )?></div>
				<?php endif; ?>

				<?php if ( $price ) : ?>
					<div class="pochipp-box__price">
						¥<?=esc_html( number_format( (int) $price ) )?>
						<span>（<?=esc_html( $price_at )?>時点）</span>
					</div>
				<?php endif; ?>

				<div class="pochipp-box__btns">
					<?php if ( $amazon_link ) : ?>
						<a href="<?=esc_url( $amazon_link )?>" class="pochipp-box__btn -amazon" rel="nofollow" target="_blank">
							Amazon
						</a>
					<?php endif; ?>
					<?php if ( $rakuten_link ) : ?>
						<a href="<?=esc_url( $rakuten_link )?>" class="pochipp-box__btn -rakuten" rel="nofollow" target="_blank">
							楽天市場
						</a>
					<?php endif; ?>
					<?php if ( $yahoo_link ) : ?>
						<a href="<?=esc_url( $yahoo_link )?>" class="pochipp-box__btn -yahoo" rel="nofollow" target="_blank">
							Yahooショッピング
						</a>
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
