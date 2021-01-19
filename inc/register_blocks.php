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

	$pid    = $attrs['pid'];
	$ptitle = $attrs['title'];

	ob_start();

	// メタデータ取得
	$pmeta = get_post_meta( $pid, \POCHIPP::META_SLUG, true );
	$pmeta = json_decode( $pmeta, true );

	// foreach ( $pmeta as $key => $value ) {
	// 	echo $key . ' : ' . $value . '<br>'; //phpcs:ignore
	// }

	$keywords    = $pmeta['keywords'];
	$searched_at = $pmeta['searched_at'];

	$amazon_link  = \POCHIPP::get_amazon_searched_link( $keywords );
	$rakuten_link = \POCHIPP::get_rakuten_searched_link( $keywords );
	$yahoo_link   = \POCHIPP::get_yahoo_searched_link( $keywords );

	// 詳細ページを優先する場合
	if ( 1 && $pmeta['amazon_detail_url'] ) {
		$amazon_link = $pmeta['amazon_detail_url'];
	}

	$main_link = ( 'rakuten' === $searched_at ) ? $rakuten_link : $amazon_link;

	$brand        = $pmeta['brand'] ?? '';
	$contributors = $pmeta['contributors'] ?? '';
	$price        = $pmeta['price'] ?? 0;
	$price_at     = $pmeta['price_at'] ?? 0;

	// どのサイズの画像使うかは設定で？
	$image_src = $pmeta['l_image_url'] ?? $pmeta['m_image_url'] ?? '';

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

				<?php if ( $brand ) : ?>
					<div class="pochipp-box__meta"><?=esc_html( $brand )?></div>
				<?php elseif ( $contributors ) : ?>
					<div class="pochipp-box__meta"><?=esc_html( $contributors )?></div>
				<?php endif; ?>

				<?php if ( $price ) : ?>
					<div class="pochipp-box__price">
						¥<?=esc_html( number_format( $price ) )?>
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
