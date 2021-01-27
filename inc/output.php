<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

// カスタムスタイル
function get_custom_style() {
	$custom_btn_color = \POCHIPP::get_setting( 'custom_btn_color' );

	$style = ':root{--pchpp-color-custom:' . $custom_btn_color . '};';

	return $style;
}


/**
 * フロントに出力するコード
 */
add_action( 'wp_head', function() {

	// ボタン用style
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<style id="pchpp_custom_style">' . \POCHIPP\get_custom_style() . '</style>' . PHP_EOL;

	if ( \POCHIPP::get_setting( 'yahoo_linkswitch' ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo PHP_EOL . \POCHIPP::get_setting( 'yahoo_linkswitch' ) . PHP_EOL;
	};
} );


/**
 * 管理画面に出力するコード
 */
add_action( 'admin_head', function() {

	// ボタン用style
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<style id="pchpp_custom_style">' . \POCHIPP\get_custom_style() . '</style>' . PHP_EOL;

	// ajax用のグローバル変数
	$script  = 'window.pchppVars = {};';
	$script .= 'window.pchppVars.ajaxUrl = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";';
	$script .= 'window.pchppVars.ajaxNonce = "' . esc_js( wp_create_nonce( \POCHIPP::NONCE_KEY ) ) . '";';

	// ボタンテキスト
	$script .= 'window.pchppVars.amazonBtnText = "' . esc_js( \POCHIPP::get_setting( 'amazon_btn_text' ) ) . '";';
	$script .= 'window.pchppVars.rakutenBtnText = "' . esc_js( \POCHIPP::get_setting( 'rakuten_btn_text' ) ) . '";';
	$script .= 'window.pchppVars.yahooBtnText = "' . esc_js( \POCHIPP::get_setting( 'yahoo_btn_text' ) ) . '";';
	$script .= 'window.pchppVars.btnStyle = "' . esc_js( \POCHIPP::get_setting( 'btn_style' ) ) . '";';
	$script .= 'window.pchppVars.btnRadius = "' . esc_js( \POCHIPP::get_setting( 'btn_radius' ) ) . '";';
	$script .= 'window.pchppVars.maxColumns = "' . esc_js( \POCHIPP::get_setting( 'max_columns' ) ) . '";';

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<script id="pchpp_admin_vars">' . $script . '</script>' . PHP_EOL;

} );


/**
 * セール情報
 */
add_action( 'wp', '\POCHIPP\set_sale_text' );
function set_sale_text() {

	// 現在時刻
	$date = (int) wp_date( 'YmdGi' );

	// Amazon
	$deadline_amazon = \POCHIPP::get_setting( 'amazon_sale_deadline' );
	$deadline_amazon = (int) preg_replace( '/[^0-9]/', '', $deadline_amazon );
	if ( $deadline_amazon >= $date ) {
		add_filter( 'pochipp_amazon_sale_text_top', function() {
			return \POCHIPP::get_setting( 'amazon_sale_text' );
		});
		add_filter( 'pochipp_amazon_sale_text_inner', function() {
			return \POCHIPP::get_setting( 'amazon_sale_text' );
		});

		// セール中に他のボタンを隠すかどうか
		if ( \POCHIPP::get_setting( 'hide_rakuten_at_amazon_sale' ) ) {
			add_filter( 'pochipp_show_rakuten_btn', '__return_false' );
		}
		if ( \POCHIPP::get_setting( 'hide_yahoo_at_amazon_sale' ) ) {
			add_filter( 'pochipp_show_yahoo_btn', '__return_false' );
		}
		if ( \POCHIPP::get_setting( 'hide_custom_at_amazon_sale' ) ) {
			add_filter( 'pochipp_show_custom_btn', '__return_false' );
		}
	}

	// 楽天
	$deadline_rakuten = \POCHIPP::get_setting( 'rakuten_sale_deadline' );
	$deadline_rakuten = (int) preg_replace( '/[^0-9]/', '', $deadline_rakuten );
	if ( $deadline_rakuten >= $date ) {
		add_filter( 'pochipp_rakuten_sale_text_top', function() {
			return \POCHIPP::get_setting( 'rakuten_sale_text' );
		});
		add_filter( 'pochipp_rakuten_sale_text_inner', function() {
			return \POCHIPP::get_setting( 'rakuten_sale_text' );
		});

		// セール中に他のボタンを隠すかどうか
		if ( \POCHIPP::get_setting( 'hide_amazon_at_rakuten_sale' ) ) {
			add_filter( 'pochipp_show_amazon_btn', '__return_false' );
		}
		if ( \POCHIPP::get_setting( 'hide_yahoo_at_rakuten_sale' ) ) {
			add_filter( 'pochipp_show_yahoo_btn', '__return_false' );
		}
		if ( \POCHIPP::get_setting( 'hide_custom_at_rakuten_sale' ) ) {
			add_filter( 'pochipp_show_custom_btn', '__return_false' );
		}
	}

	// Yahoo
	$deadline_yahoo = \POCHIPP::get_setting( 'rakuten_sale_deadline' );
	$deadline_yahoo = (int) preg_replace( '/[^0-9]/', '', $deadline_yahoo );
	if ( $deadline_yahoo >= $date ) {
		add_filter( 'pochipp_yahoo_sale_text_top', function() {
			return \POCHIPP::get_setting( 'yahoo_sale_text' );
		});
		add_filter( 'pochipp_yahoo_sale_text_inner', function() {
			return \POCHIPP::get_setting( 'yahoo_sale_text' );
		});

		// セール中に他のボタンを隠すかどうか
		if ( \POCHIPP::get_setting( 'hide_amazon_at_yahoo_sale' ) ) {
			add_filter( 'pochipp_show_amazon_btn', '__return_false' );
		}
		if ( \POCHIPP::get_setting( 'hide_rakuten_at_yahoo_sale' ) ) {
			add_filter( 'pochipp_show_rakuten_btn', '__return_false' );
		}
		if ( \POCHIPP::get_setting( 'hide_custom_at_yahoo_sale' ) ) {
			add_filter( 'pochipp_show_custom_btn', '__return_false' );
		}
	}

}
