<?php
/**
 * 汎用的な関数
 * とりあえず namespace だけで class なし。
 */
namespace POCHIPP;
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Amazon オリジナルリンクを取得します
 * @param $keywords
 */
function generate_amazon_original_link( $keywords = '' ) {
	$url = 'https://www.amazon.co.jp/gp/search?ie=UTF8&keywords=' . urlencode( $keywords );
	return $url;
}

/**
 * 楽天の検索ページを返す アフィリエイトIDなし
 * @param $keywords
 */
function generate_rakuten_original_link( $keywords = '' ) {
	$url  = 'https://search.rakuten.co.jp/search/mall/';
	$url .= urlencode( $keywords ) . '/?f=1&grp=product';
	return $url;
}


/**
 * yahooショッピング用のリンクを作成します アフィリエイトIDなし
 * @param $keywords
 */
function generate_yahoo_original_link( $keywords = '' ) {
	$url = 'https://shopping.yahoo.co.jp/search?p=' . urlencode( $keywords );
	return $url;
}


// yyi: とりあえずRinkerのやつほぼそのまま
function array_get( $array, $key = null, $default = null ) {
	if ( null === $key ) return $array;

	if ( isset( $array[ $key ] ) ) return $array[ $key ];

	foreach ( explode( '.', $key ) as $segment ) {
		if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
			return $default;
		}
		$array = $array[ $segment ];
	}

	return $array;
}


// 登録済みのものを取得するやつ
function get_search_itemlist( $term_id, $keywords, $numberposts = 20 ) {
	$args = [
		'post_type'			=> \POCHIPP::POST_TYPE_SLUG,
		'posts_per_page'	=> $numberposts,
		'numberposts'		=> $numberposts,
		'post_status'		=> array( 'publish' ),
		's'					=> $keywords,
	];
	if ( intval( $term_id ) > 0 ) {
		$args[ 'tax_query' ] = [
			[
				'taxonomy'	=> \POCHIPP::TAXONOMY_SLUG,
				'terms'		=> $term_id,
			]
		];
	}
	$the_query = new \WP_Query( $args );
	$datas = [];
	while ( $the_query->have_posts() ) : $the_query->the_post();
		$data = [];
		$data[ 'post_id' ] = get_the_ID();
		$data[ 'title'] = get_the_title();

		// その他情報
		// $data[ 'free_title_url' ]  	= get_post_meta( get_the_ID(), $this->add_prefix( 'free_title_url' ), true );
		// $data[ 's_image_url' ]			= get_post_meta( get_the_ID(), $this->add_prefix( 's_image_url' ), true );
		// $data[ 'm_image_url' ]			= get_post_meta( get_the_ID(), $this->add_prefix( 'm_image_url' ), true );
		// $data[ 'l_image_url' ]			= get_post_meta( get_the_ID(), $this->add_prefix( 'l_image_url' ), true );
		// $data[ 'amazon_title_url' ]	= get_post_meta( get_the_ID(), $this->add_prefix( 'amazon_title_url' ), true );
		// $data[ 'rakuten_title_url' ]	= get_post_meta( get_the_ID(), $this->add_prefix( 'rakuten_title_url' ), true );
		// $data[ 'amazon_url' ]		= get_post_meta( get_the_ID(), $this->add_prefix( 'amazon_url' ), true );
		// $data[ 'rakuten_url' ]		= get_post_meta( get_the_ID(), $this->add_prefix( 'rakuten_url' ), true );
		// $data[ 'yahoo_url' ]			= get_post_meta( get_the_ID(), $this->add_prefix( 'yahoo_url' ), true );

		// $search_shop_value = $this->get_search_shop_value( $data[ 'post_id' ] );
		// $is_search_from_rakuten = $this->is_search_from_rakuten( $search_shop_value );
		// $is_search_from_freelink = $this->is_search_from_freelink( $search_shop_value );

		// if ( $is_search_from_freelink ) {
		// 	$data[ 'text_url' ]	= esc_url( $data[ 'free_title_url' ] );
		// } elseif ( $is_search_from_rakuten ) {
		// 	if ($this->is_moshimo(self::SHOP_TYPE_RAKUTEN)) {
		// 		$rakuten_title_url =  $data[ 'rakuten_url' ];
		// 	} else {
		// 		$rakuten_title_url =  $data[ 'rakuten_title_url' ];
		// 	}
		// 	$data[ 'text_url' ]	= $this->generate_rakuten_title_link_with_aid( $rakuten_title_url, $data[ 'post_id' ] );
		// } else {
		// 	$data[ 'text_url' ]	= $this->generate_amazon_title_link_with_aid( $data[ 'amazon_title_url' ], $data[ 'post_id' ] );
		// }
		$datas[] = $data;
	endwhile;

	return $datas;
}