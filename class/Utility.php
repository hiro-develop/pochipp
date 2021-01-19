<?php

namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Utility {

	/**
	 * Amazon 検索結果リンクを取得
	 */
	public static function get_amazon_searched_link( $keywords = '' ) {
		$url = 'https://www.amazon.co.jp/gp/search?ie=UTF8&keywords=' . rawurlencode( $keywords );
		return $url;
	}

	/**
	 * 楽天の検索ページを返す （アフィリエイトIDなし
	 */
	public static function get_rakuten_searched_link( $keywords = '' ) {
		$url  = 'https://search.rakuten.co.jp/search/mall/';
		$url .= rawurlencode( $keywords ) . '/?f=1&grp=product'; // ?f=1&grp=product は必要？
		return $url;
	}


	/**
	 * yahooショッピング用のリンクを作成 (アフィリエイトIDなし
	 */
	public static function get_yahoo_searched_link( $keywords = '' ) {
		$url = 'https://shopping.yahoo.co.jp/search?p=' . rawurlencode( $keywords );
		return $url;
	}


	/**
	 * GETなどの処理に使う
	 */
	public static function array_get( $array, $key = null, $default = null ) {
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


	// 登録済みの商品を取得
	// function get_search_itemlist( $term_id, $keywords, $numberposts = 20 ) {
	public static function get_registerd_items( $args = [] ) {

		if ( ! \is_array( $args ) ) $args = [];

		$term_id  = $args['term_id'] ?? 0;
		$keywords = $args['keywords'] ?? '';
		$count    = $args['count'] ?? 20;

		$query_args = [
			'post_type'         => \POCHIPP::POST_TYPE_SLUG,
			'posts_per_page'    => $count,
			'post_status'       => [ 'publish' ],
		];

		if ( $keywords ) {
			$query_args['s'] = $keywords;
		}

		if ( 0 < intval( $term_id ) ) {
			$query_args['tax_query'] = [
				[
					'taxonomy'  => \POCHIPP::TAXONOMY_SLUG,
					'terms'     => $term_id,
				],
			];
		}

		// 最終的に返すデータ
		$datas = [];

		// new クエリ
		$the_query = new \WP_Query( $query_args );
		while ( $the_query->have_posts() ) :
			$the_query->the_post();

			// metaデータ
			$metadata = get_post_meta( get_the_ID(), \POCHIPP::META_SLUG, true );
			$metadata = json_decode( $metadata, true );

			// マージ
			$metadata['post_id'] = get_the_ID();
			$metadata['title']   = get_the_title();

			$datas[] = $metadata;
		endwhile;

		return $datas;
	}

}
