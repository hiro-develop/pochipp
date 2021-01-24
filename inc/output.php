<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

function get_custom_style() {
	$custom_btn_color = \POCHIPP::get_setting( 'custom_btn_color' );

	$style = '.pochipp-box__btn.-custom{background-color:' . $custom_btn_color . '};';

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

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<script id="pchpp_admin_vars">' . $script . '</script>' . PHP_EOL;

} );
