<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * for Front
 */
add_action( 'wp_enqueue_scripts', 'POCHIPP\front_scripts', 12 );
function front_scripts() {
	wp_enqueue_style( 'pochipp-front', POCHIPP_URL . 'dist/css/style.css', [], \POCHIPP::$version );
}


/**
 * for Admin
 */
add_action( 'admin_enqueue_scripts', 'POCHIPP\admin_scripts' );
function admin_scripts( $hook_suffix ) {

	global $post_type;

	$is_pochipp_menu = false !== strpos( $hook_suffix, 'pochipp' );
	$is_editor_page  = 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix;
	$is_columns_page = 'edit.php' === $hook_suffix && \POCHIPP::POST_TYPE_SLUG === $post_type;

	// 編集画面 or 設定ページでのみ読み込む
	if ( $is_editor_page || $is_pochipp_menu ) {

		// メディアアップローダー
		wp_enqueue_script( 'media-editor' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_media();

		// TthickBox
		add_thickbox();
	}

	// 管理画面用CSS
	if ( $is_pochipp_menu || $is_columns_page ) {
		wp_enqueue_style( 'pochipp-setting', POCHIPP_URL . 'dist/css/setting.css', [], \POCHIPP::$version );
		wp_enqueue_script( 'pochipp-setting', POCHIPP_URL . 'dist/js/setting.js', [ 'jquery' ], \POCHIPP::$version, true );
	}

	// 設定ページにだけ読み込むファイル
	if ( $is_pochipp_menu ) {

		// カラーピッカー
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'pochipp-color-picker', POCHIPP_URL . 'dist/js/colorpicker.js', [ 'jquery', 'wp-color-picker' ], \POCHIPP::$version, true );

		// デートピッカー
		wp_enqueue_style( 'datetimepicker', POCHIPP_URL . 'assets/datetimepicker/jquery.datetimepicker.min.css', [], \POCHIPP::$version );
		wp_enqueue_script( 'datetimepicker', POCHIPP_URL . 'assets/datetimepicker/jquery.datetimepicker.full.min.js', [ 'jquery' ], \POCHIPP::$version, true );
		wp_enqueue_script( 'pochipp-datetimepicker', POCHIPP_URL . 'dist/js/datepicker.js', [], \POCHIPP::$version, true );

	}
}


/**
 * for Gutenberg
 */
add_action( 'enqueue_block_editor_assets', 'POCHIPP\block_assets' );
function block_assets() {

	// ブロック関係のCSS
	wp_enqueue_style( 'pochipp-blocks', POCHIPP_URL . 'dist/css/blocks.css', [], \POCHIPP::$version );

	global $post_type;
	if ( \POCHIPP::POST_TYPE_SLUG === $post_type ) {
		wp_enqueue_style( 'pochipp-setting', POCHIPP_URL . 'dist/css/setting.css', [], \POCHIPP::$version );
	}
}
