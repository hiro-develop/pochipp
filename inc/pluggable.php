<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * セール情報をセット
 */
if ( ! function_exists( '\POCHIPP\set_sale_data' ) ) {
	function set_sale_data() {
		$date = (int) wp_date( 'YmdGi' );

		// amazon
		$startline = (int) preg_replace( '/[^0-9]/', '', \POCHIPP::get_setting( 'amazon_sale_startline' ) );
		$deadline  = (int) preg_replace( '/[^0-9]/', '', \POCHIPP::get_setting( 'amazon_sale_deadline' ) );
		if ( $startline <= $date && $date <= $deadline ) {
			\POCHIPP::$sale_text['amazon'] = \POCHIPP::get_setting( 'amazon_sale_text' );
		}

		// rakuten
		$startline = (int) preg_replace( '/[^0-9]/', '', \POCHIPP::get_setting( 'rakuten_sale_startline' ) );
		$deadline  = (int) preg_replace( '/[^0-9]/', '', \POCHIPP::get_setting( 'rakuten_sale_deadline' ) );
		if ( $startline <= $date && $date <= $deadline ) {
			\POCHIPP::$sale_text['rakuten'] = \POCHIPP::get_setting( 'rakuten_sale_text' );
		}

		// yahoo
		$startline = (int) preg_replace( '/[^0-9]/', '', \POCHIPP::get_setting( 'yahoo_sale_startline' ) );
		$deadline  = (int) preg_replace( '/[^0-9]/', '', \POCHIPP::get_setting( 'yahoo_sale_deadline' ) );
		if ( $startline <= $date && $date <= $deadline ) {
			\POCHIPP::$sale_text['yahoo'] = \POCHIPP::get_setting( 'yahoo_sale_text' );
		}
	}
}

if ( ! function_exists( '\POCHIPP\set_campaign_data' ) ) {
	function set_campaign_data() {
		$day = (int) wp_date( 'd' );

		if ( ! \POCHIPP::$sale_text['rakuten'] ) {
			// 0と5のつく日
			$is_5campaign = \POCHIPP::get_setting( 'show_rakuten_5campaign' ) && ( 0 === $day % 5 );
			if ( $is_5campaign ) {
				\POCHIPP::$sale_text['rakuten'] = \POCHIPP::get_setting( 'rakuten_5campaign_text' );
			}
		}
		if ( ! \POCHIPP::$sale_text['yahoo'] ) {
			// 5のつく日
			$is_5campaign = \POCHIPP::get_setting( 'show_yahoo_5campaign' ) && false !== strpos( (string) $day, '5' );
			if ( $is_5campaign ) {
				\POCHIPP::$sale_text['yahoo'] = \POCHIPP::get_setting( 'yahoo_5campaign_text' );
			}
		}
	}
}
