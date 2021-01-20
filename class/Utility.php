<?php

namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Utility {

	/**
	 * Amazon 検索結果アフィリンクを取得
	 */
	public static function get_amazon_searched_affi_link( $keywords = '' ) {
		if ( ! $keywords ) return '';
		$affi_link = 'https://www.amazon.co.jp/gp/search?ie=UTF8&keywords=' . rawurlencode( $keywords );
		$affi_link = $affi_link . '&tag=' . \POCHIPP::get_setting( 'amazon_traccking_id' );

		return apply_filters( 'pochipp_amazon_searched_affi_link', $affi_link, $keywords );
	}

	/**
	 * Amazon 商品詳細アフィリンクを取得
	 */
	public static function get_amazon_detail_affi_link( $detail_url = '' ) {
		if ( ! $detail_url ) return '';

		// もしものリンクにするかどうか
		if ( 0 ) { // phpcs:ignore
			// $url = $this->generate_moshimo_link( self::SHOP_TYPE_AMAZON, $original_url );
		} else {
			if ( false !== strpos( $detail_url, 'tag=' ) ) {
				$affi_link = $detail_url; // すでにtag付いていればそのまま
			} else {
				$affi_link = $detail_url . '?tag=' . \POCHIPP::get_setting( 'amazon_traccking_id' );
			}
		}

		return apply_filters( 'pochipp_amazon_detail_affi_link', $affi_link, $detail_url );
	}


	/**
	 * 楽天の検索結果アフィリンクを取得
	 */
	public static function get_rakuten_searched_affi_link( $keywords = '' ) {
		if ( ! $keywords ) return '';
		$url = 'https://search.rakuten.co.jp/search/mall/' . $keywords;
		// $url .= '/?f=1&grp=product'; // ?f=1&grp=product は必要？

		$encoded_url = rawurlencode( $url );

		$affi_link  = 'https://hb.afl.rakuten.co.jp/hgc/' . \POCHIPP::get_setting( 'rakuten_affiliate_id' );
		$affi_link .= '/?pc=' . $encoded_url . '&m=' . $encoded_url;

		return apply_filters( 'pochipp_rakuten_searched_affi_link', $affi_link, $keywords );
	}


	/**
	 * 楽天の商品詳細アフィリンクを取得
	 */
	public static function get_rakuten_detail_affi_link( $detail_url = '' ) {
		if ( ! $detail_url ) return '';

		// もしものリンクにするかどうか
		if ( 0 ) { // phpcs:ignore
			// $url = $this->generate_moshimo_link( self::SHOP_TYPE_AMAZON, $original_url );
		} else {
			if ( false !== strpos( $detail_url, 'hb.afl.rakuten.co.jp' ) ) {
				$affi_link = $detail_url; // すでにアフィリンク化されていればそのまま
			} else {
				$encoded_url = rawurlencode( $detail_url );
				$affi_link   = 'https://hb.afl.rakuten.co.jp/hgc/' . \POCHIPP::get_setting( 'rakuten_affiliate_id' );
				$affi_link  .= '/?pc=' . $encoded_url . '&m=' . $encoded_url;
			}
		}

		return apply_filters( 'pochipp_rakuten_detail_affi_link', $affi_link, $detail_url );
	}


	/**
	 * yahooショッピング用の検索結果アフィリンクを作成
	 */
	public static function get_yahoo_searched_affi_link( $keywords = '' ) {
		if ( ! $keywords ) return '';
		$url = 'https://shopping.yahoo.co.jp/search?p=' . rawurlencode( $keywords );
		return $url;

		// LinkSwitch使うかどうか
		if ( \POCHIPP::get_setting( 'LinkSwitch使うかどうか' ) ) {
			$url = 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=' . $this->yahoo_sid . '&pid=' . $this->yahoo_pid . '&vc_url=' . urlencode( $original_url );
		} else {
			// LinkSwitch使用時はURLそのまま
			$url = $original_url;
		}

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
