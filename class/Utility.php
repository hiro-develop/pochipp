<?php

namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Utility {

	/**
	 * Amazon 検索結果ページを取得
	 */
	public static function get_amazon_searched_url( $keywords = '' ) {
		if ( ! $keywords ) return '';
		$url = 'https://www.amazon.co.jp/gp/search?ie=UTF8&keywords=' . rawurlencode( $keywords );

		// $url = apply_filters( 'pochipp_amazon_searched_url', $url, $keywords );
		return $url;
	}


	/**
	 * 楽天 検索結果ページを取得
	 */
	public static function get_rakuten_searched_url( $keywords = '' ) {
		if ( ! $keywords ) return '';
		$url = 'https://search.rakuten.co.jp/search/mall/' . rawurlencode( $keywords );
		// $url .= '/?f=1&grp=product'; // ?f=1&grp=product は必要？

		// $url = apply_filters( 'pochipp_rakuten_searched_url', $url, $keywords );
		return $url;
	}


	/**
	 * yahooショッピング 検索結果ページを取得
	 */
	public static function get_yahoo_searched_url( $keywords = '' ) {
		if ( ! $keywords ) return '';
		$url = 'https://shopping.yahoo.co.jp/search?p=' . rawurlencode( $keywords );

		// return apply_filters( 'pochipp_yahoo_searched_url', $url, $keywords );
		return $url;
	}


	/**
	 * Amazon アフィリンクを生成
	 *
	 * @param string $affi_url 商品検索時に保存されたアフィリンク。
	 * @param string $url 非アフィリンク。商品詳細ページ or 検索結果ページが渡ってくる。
	 * @param string $a_id もしも用a_id
	 */
	public static function get_amazon_affi_url( $affi_url = '', $url = '', $a_id = '' ) {

		// もしもリンクにする場合
		if ( $a_id ) {
			return \POCHIPP::get_moshimo_url( 'amazon', $url, $a_id );
		}

		// amazonはアフィリンクをデータとして保存しているので、それがあればそのまま返す
		if ( ! $affi_url ) {
			$traccking_id = \POCHIPP::get_setting( 'amazon_traccking_id' );

			if ( ! $traccking_id ) {
				$affi_url = '';
			} elseif ( $url ) {
				$connecter = false === strpos( $url, '?' ) ? '?' : '&';
				$affi_url  = $url . $connecter . 'tag=' . $traccking_id;
			}
		}

		return apply_filters( 'pochipp_amazon_affi_url', $affi_url, $url );
	}


	/**
	 * 楽天 アフィリンクを生成
	 */
	public static function get_rakuten_affi_url( $url = '', $a_id = '' ) {

		if ( ! $url ) return '';

		// もしものリンクにする場合
		if ( $a_id ) {
			return \POCHIPP::get_moshimo_url( 'rakuten', $url, $a_id );
		}

		$affi_url = '';
		$affi_id  = \POCHIPP::get_setting( 'rakuten_affiliate_id' );

		if ( false !== strpos( $url, 'hb.afl.rakuten.co.jp' ) ) {
			// すでにアフィリンク化されていればそのまま -> 仕様上あり得ないけど一応
			$affi_url = $url;

		} elseif ( ! $affi_id ) {
			// アフィリエイトDがないとき
			$affi_url = '';

		} else {
			// 通常時
			$encoded_url = rawurlencode( $url );
			$affi_url    = 'https://hb.afl.rakuten.co.jp/hgc/' . $affi_id . '/?pc=' . $encoded_url . '&m=' . $encoded_url;
		}

		return apply_filters( 'pochipp_rakuten_affi_url', $affi_url, $url );
	}


	/**
	 * yahooショッピング アフィリンクを生成
	 */
	public static function get_yahoo_affi_url( $url = '', $a_id = '' ) {
		if ( ! $url ) return '';

		// もしものリンクにする場合
		if ( $a_id ) {
			return \POCHIPP::get_moshimo_url( 'yahoo', $url, $a_id );
		}

		// idあれば
		// if ( \POCHIPP::get_setting( 'id' ) ) {
		// 	$url = 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid=' . $sid . '&pid=' . $pid . '&vc_url=' . rawurlencode( $url );
		// }

		if ( empty( \POCHIPP::get_setting( 'yahoo_linkswitch' ) ) ) {
			// LinkSwitch の設定がない場合
			$affi_url = '';
		} else {
			// LinkSwitch の設定があればそのままURL返す
			$affi_url = $url;
		}

		return apply_filters( 'pochipp_yahoo_affi_url', $affi_url, $url );
	}


	/**
	 * もしもリンクを作成する
	 */
	public function get_moshimo_url( $shop_type, $url, $a_id = '' ) {

		if ( ! $url ) return '';

		$query = '';
		if ( 'amazon' === $shop_type ) {
			$a_id  = $a_id ?: \POCHIPP::get_setting( 'moshimo_amazon_aid' );
			$query = '?a_id=' . $a_id . '&p_id=170&pc_id=185&pl_id=4062';
		} elseif ( 'rakuten' === $shop_type ) {
			$a_id  = $a_id ?: \POCHIPP::get_setting( 'moshimo_rakuten_aid' );
			$query = '?a_id=' . $a_id . '&p_id=54&pc_id=54&pl_id=616';
		} elseif ( 'yahoo' === $shop_type ) {
			$a_id  = $a_id ?: \POCHIPP::get_setting( 'moshimo_yahoo_aid' );
			$query = '?a_id=' . $a_id . '&p_id=1225&pc_id=1925&pl_id=18502';
		} else {
			return $url;
		}

		$moshimo_url = 'https://af.moshimo.com/af/c/click' . $query . '&url=' . rawurlencode( $url );
		return apply_filters( 'pochipp_moshimo_url', $moshimo_url, $shop_type, $url );

	}


	/**
	 * Amazonボタン用のインプレッション計測タグ
	 */
	public function get_amazon_imptag( $amazon_aid = '' ) {
		if ( $amazon_aid ) {
			return '<img src="https://i.moshimo.com/af/i/impression?a_id=' . $amazon_aid . '&p_id=170&pc_id=185&pl_id=4062" width="1" height="1" style="border:none;">';
		}

		return '';
	}


	/**
	 * 楽天ボタン用のインプレッション計測タグ
	 */
	public function get_rakuten_imptag( $rakuten_aid = '' ) {
		if ( $rakuten_aid ) {
			return '<img src="https://i.moshimo.com/af/i/impression?a_id=' . $rakuten_aid . '&p_id=54&pc_id=54&pl_id=616" width="1" height="1" style="border:none;">';
		}

		return '';
	}


	/**
	 * Yahooボタン用のインプレッション計測タグ
	 */
	public function get_yahoo_imptag( $yahoo_aid = '' ) {

		if ( $yahoo_aid ) {
			return '<img src="https://i.moshimo.com/af/i/impression?a_id=' . $yahoo_aid . '&p_id=1225&pc_id=1925&pl_id=18502" width="1" height="1" style="border:none;">';
		}

		// '<img src="https://ad.jp.ap.valuecommerce.com/servlet/gifbanner?sid='.$sid.'&pid='.$pid.'" width="1" height="1" border="0">';
		return '';

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
		$sort     = $args['sort'] ?? 'new';

		$query_args = [
			'post_type'         => \POCHIPP::POST_TYPE_SLUG,
			'posts_per_page'    => $count,
			'post_status'       => [ 'publish' ],
		];

		if ( $keywords ) {
			$query_args['s'] = $keywords;
		}

		// 商品カテゴリ
		if ( 0 < intval( $term_id ) ) {
			$query_args['tax_query'] = [
				[
					'taxonomy'  => \POCHIPP::TAXONOMY_SLUG,
					'terms'     => $term_id,
				],
			];
		}

		// 並び順
		if ( 'old' === $sort ) {
			$query_args['order']   = 'ASC';
			$query_args['orderby'] = 'date';
		} elseif ( 'count' === $sort ) {

			$query_args['order']    = 'DESC';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'used_count';
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
