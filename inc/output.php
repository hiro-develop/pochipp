<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

// カスタムスタイル
function get_custom_style() {
	$custom_btn_color   = \POCHIPP::get_setting( 'custom_btn_color' );
	$custom_btn_color_2 = \POCHIPP::get_setting( 'custom_btn_color_2' );

	$style = ':root{' .
		"--pchpp-color-custom: {$custom_btn_color};" .
		"--pchpp-color-custom-2: {$custom_btn_color_2};" .
	'};';

	return $style;
}


/**
 * フロントに出力するコード
 */
add_action( 'wp_head', function() {

	echo '<!-- Pochipp -->' . PHP_EOL;

	// ボタン用style
	echo '<style id="pchpp_custom_style">' . \POCHIPP\get_custom_style() . '</style>' . PHP_EOL; // phpcs:ignore

	$linkswitch_code = \POCHIPP::get_setting( 'yahoo_linkswitch' );
	if ( is_numeric( $linkswitch_code ) ) {
		echo '<script type="text/javascript" language="javascript">' .
			'var vc_pid = "' . esc_html( $linkswitch_code ) . '";' .
		'</script>' . PHP_EOL .
		'<script type="text/javascript" src="//aml.valuecommerce.com/vcdal.js" async></script>' . PHP_EOL; // phpcs:ignore
	} else {
		echo $linkswitch_code . PHP_EOL; // phpcs:ignore
	};

	echo '<!-- / Pochipp -->' . PHP_EOL;
} );


/**
 * 管理画面に出力するコード
 */
add_action( 'admin_head', function() {

	// ボタン用style
	echo '<style id="pchpp_custom_style">' . \POCHIPP\get_custom_style() . '</style>' . PHP_EOL; // phpcs:ignore

	// ajax用のグローバル変数
	$script  = 'window.pchppVars = {};';
	$script .= 'window.pchppVars.adminUrl = "' . esc_js( admin_url() ) . '";';
	$script .= 'window.pchppVars.ajaxUrl = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";';
	$script .= 'window.pchppVars.ajaxNonce = "' . esc_js( wp_create_nonce( \POCHIPP::NONCE_KEY ) ) . '";';

	// ボタン表示するかどうか
	$script .= 'window.pchppVars.hasAffi = {' .
		'amazon: "' . esc_js( \POCHIPP::$has_affi['amazon'] ) . '",' .
		'rakuten: "' . esc_js( \POCHIPP::$has_affi['rakuten'] ) . '",' .
		'yahoo: "' . esc_js( \POCHIPP::$has_affi['yahoo'] ) . '",' .
	'};';

	// ボタンテキスト
	$script .= 'window.pchppVars.btnStyle = "' . esc_js( \POCHIPP::get_setting( 'btn_style' ) ) . '";';
	$script .= 'window.pchppVars.btnRadius = "' . esc_js( \POCHIPP::get_setting( 'btn_radius' ) ) . '";';
	$script .= 'window.pchppVars.imgPosition = "' . esc_js( \POCHIPP::get_setting( 'img_position' ) ) . '";';
	$script .= 'window.pchppVars.boxLayoutPC = "' . esc_js( \POCHIPP::get_setting( 'box_layout_pc' ) ) . '";';
	$script .= 'window.pchppVars.boxLayoutMB = "' . esc_js( \POCHIPP::get_setting( 'box_layout_mb' ) ) . '";';

	echo '<script id="pchpp_admin_vars">' . $script . '</script>' . PHP_EOL; // phpcs:ignore

} );


/**
 * セール情報
 */
add_action( 'wp', '\POCHIPP\set_sale_text' );
function set_sale_text() {

	// セール情報をセット
	set_sale_data();
	set_campaign_data();

	// Amazon
	if ( \POCHIPP::$sale_text['amazon'] ) {
		add_filter( 'pochipp_amazon_sale_text', function() {
			return \POCHIPP::$sale_text['amazon'];
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
			add_filter( 'pochipp_show_custom_btn_2', '__return_false' );
		}
	}

	// 楽天
	if ( \POCHIPP::$sale_text['rakuten'] ) {
		add_filter( 'pochipp_rakuten_sale_text', function() {
			return \POCHIPP::$sale_text['rakuten'];
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
			add_filter( 'pochipp_show_custom_btn_2', '__return_false' );
		}
	}

	// Yahoo
	if ( \POCHIPP::$sale_text['yahoo'] ) {
		add_filter( 'pochipp_yahoo_sale_text', function() {
			return \POCHIPP::$sale_text['yahoo'];
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
			add_filter( 'pochipp_show_custom_btn_2', '__return_false' );
		}
	}

}
