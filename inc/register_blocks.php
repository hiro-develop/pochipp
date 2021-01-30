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
		'pid'                => 0,
		'box_class'          => '',
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

	$box_class   = $pdata['className'];
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
	if ( apply_filters( 'pochipp_show_amazon_btn', ! $pdata['hideAmazon'] ) ) {
		// $pdata['amazon_custom_url']
		$amazon_url = $asin ? 'https://www.amazon.co.jp/dp/' . $asin : \POCHIPP::get_amazon_searched_url( $keywords );
		$amazon_url = \POCHIPP::get_amazon_affi_url( $pdata['amazon_affi_url'], $amazon_url, $amazon_aid );
	}

	// 楽天ボタンURL
	if ( apply_filters( 'pochipp_show_rakuten_btn', ! $pdata['hideRakuten'] ) ) {
		// $pdata['rakuten_custom_url']
		$rakuten_url = $pdata['rakuten_detail_url'] ?: \POCHIPP::get_rakuten_searched_url( $keywords );
		$rakuten_url = \POCHIPP::get_rakuten_affi_url( $rakuten_url, $rakuten_aid );
	}

	// YahooボタンURL
	if ( apply_filters( 'pochipp_show_yahoo_btn', ! $pdata['hideYahoo'] ) ) {
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

	// スタイル
	$btn_style = \POCHIPP::get_setting( 'btn_style' );
	if ( 'default' === $btn_style ) {
		$btn_style = 'dflt';
	}

	// セール通知テキスト
	$amazon_sale_text  = apply_filters( 'pochipp_amazon_sale_text', '' );
	$rakuten_sale_text = apply_filters( 'pochipp_rakuten_sale_text', '' );
	$yahoo_sale_text   = apply_filters( 'pochipp_yahoo_sale_text', '' );

	ob_start();

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
		<div class="pochipp-box<?php if ( $box_class ) echo ' ' . $box_class; ?>"
			data-id="<?=esc_attr( $pdata['pid'] )?>"
			data-img="<?=esc_attr( \POCHIPP::get_setting( 'img_position' ) )?>"
			data-lyt-pc="<?=esc_attr( \POCHIPP::get_setting( 'box_layout_pc' ) )?>"
			data-lyt-mb="<?=esc_attr( \POCHIPP::get_setting( 'box_layout_mb' ) )?>"
			data-btn-style="<?=esc_attr( $btn_style )?>"
			data-btn-radius="<?=esc_attr( \POCHIPP::get_setting( 'btn_radius' ) )?>"
			data-sale-effect="<?=esc_attr( \POCHIPP::get_setting( 'sale_text_effect' ) )?>"
		>
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
			</div>
			<div class="pochipp-box__btns"
				data-maxclmn-pc="<?=esc_attr( \POCHIPP::get_setting( 'max_column_pc' ) )?>"
				data-maxclmn-mb="<?=esc_attr( \POCHIPP::get_setting( 'max_column_mb' ) )?>"
			>
				<?php if ( $amazon_url ) : ?>
					<div class="pochipp-box__btnwrap -amazon<?php if ($amazon_sale_text) echo ' -on-sale'; ?>">
						<?php if ( $amazon_sale_text ) : ?>
							<div class="pochipp-box__saleInfo -top">＼<?=esc_html( $amazon_sale_text )?>／</div>
						<?php endif; ?>
						<a href="<?=esc_url( $amazon_url )?>" class="pochipp-box__btn" <?=$rel_target?>>
							<span>
								<?php echo esc_html( \POCHIPP::get_setting( 'amazon_btn_text' ) ); ?>
							</span>
							<?php echo \POCHIPP::get_amazon_imptag( $amazon_aid ); ?>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( $rakuten_url ) : ?>
					<div class="pochipp-box__btnwrap -rakuten<?php if ($rakuten_sale_text) echo ' -on-sale'; ?>">
						<?php if ( $rakuten_sale_text ) : ?>
							<div class="pochipp-box__saleInfo -top">＼<?=esc_html( $rakuten_sale_text )?>／</div>
						<?php endif; ?>
						<a href="<?=esc_url( $rakuten_url )?>" class="pochipp-box__btn" <?=$rel_target?>>
							<span>
								<?php echo esc_html( \POCHIPP::get_setting( 'rakuten_btn_text' ) ); ?>
							</span>
							<?php echo \POCHIPP::get_rakuten_imptag( $rakuten_aid ); ?>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( $yahoo_url ) : ?>
					<?php
						// yahooは文字列の長さに注意する
						$yahoo_text = \POCHIPP::get_setting( 'yahoo_btn_text' );
						$length     = mb_strwidth( $yahoo_text, 'UTF-8' );

						$add_class                          = '';
						if ( 14 < $length )  $add_class    .= ' -long-text';
						if ( $yahoo_sale_text ) $add_class .= ' -on-salse';
					?>
					<div class="pochipp-box__btnwrap -yahoo<?php if ($add_class) echo $add_class; ?>">
						<?php if ( $yahoo_sale_text ) : ?>
							<div class="pochipp-box__saleInfo -top">＼<?=esc_html( $yahoo_sale_text )?>／</div>
						<?php endif; ?>
						<a href="<?=esc_url( $yahoo_url )?>" class="pochipp-box__btn" <?=$rel_target?>>
							<span>
								<?php echo esc_html( $yahoo_text ); ?>
							</span>
							<?php echo \POCHIPP::get_yahoo_imptag( $yahoo_aid ); ?>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( apply_filters( 'pochipp_show_custom_btn', (bool) ( $pdata['custom_btn_url'] && $pdata['custom_btn_text'] ) ) ) : ?>
					<?php
						// カスタムボタンも文字列の長さに注意する
						$custom_btn_text = $pdata['custom_btn_text'];
						$length          = mb_strwidth( $custom_btn_text, 'UTF-8' );
					?>
					<div class="pochipp-box__btnwrap -custom<?php if (14 < $length) echo ' -long-text'; ?>">
						<a href="<?=esc_url( $pdata['custom_btn_url'] )?>" class="pochipp-box__btn" <?=$rel_target?>>
							<span>
								<?php echo esc_html( $custom_btn_text ); ?>
							</span>
						</a>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( 1 ) : ?>
				<div class="pochipp-box__logo">
					<img src="<?=esc_url( POCHIPP_URL )?>assets/img/pochipp-logo-t1.png" alt="" width="32" height="32">
					<span>ポチップ</span>
				</div>
			<?php endif; ?>
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
