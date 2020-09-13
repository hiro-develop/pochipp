<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * for Front
 */
add_action( 'wp_enqueue_scripts', 'POCHIPP\front_scripts', 12 );
function front_scripts() {
	wp_enqueue_style(
		'pochipp-front',
		POCHIPP_URL . 'dist/css/style.css',
		[],
		POCHIPP_VERSION
	);

	// yyi: イベントトラッキング
	// $is_tracking = !!get_option( $this->option_column_name( 'is_tracking' ) , false );
	// if ( $is_tracking ) {
	// wp_enqueue_script(
	// 	'pochipp-block',
	// 	POCHIPP_URL .'js/event-tracking.js',
	// 	['jquery],
	// 	POCHIPP_VERSION,
	// 	true
	// );
	// }
}


/**
 * for Admin
 */
add_action( 'admin_enqueue_scripts', 'POCHIPP\admin_scripts' );
function admin_scripts( $hook_suffix ) {

	$is_pochipp_menu = false !== strpos( $hook_suffix, 'pochipp' );
	$is_editor_page  = 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix;

	// 編集画面 or 設定ページでのみ読み込む
	if ( $is_editor_page || $is_pochipp_menu ) {

		wp_enqueue_media();
		wp_enqueue_script( 'media-editor' );
		wp_enqueue_script( 'media-upload' );

		// wp_enqueue_script( 'pochipp-media', POCHIPP_URL . '/dist/js/media.js', ['jquery'], POCHIPP_VERSION, true );

		wp_enqueue_style(
			'pochipp-admin',
			POCHIPP_URL . 'dist/css/admin.css',
			[],
			POCHIPP_VERSION
		);
	}

	// 設定ページにだけ読み込むファイル
	if ( $is_pochipp_menu ) {

		// カラーピッカー
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// メディアアップローダー

		// CSS
		// wp_enqueue_style(
		// 	'pochipp-menu',
		// 	POCHIPP_URL . 'dist/css/my_menu.css',
		// 	[],
		// 	POCHIPP_VERSION
		// );

		// JS
		// wp_enqueue_script(
		// 	'pochipp-menu',
		// 	POCHIPP_URL . 'dist/js/my_menu.js',
		// 	['jquery', 'wp-color-picker', 'wp-i18n'],
		// 	POCHIPP_VERSION,
		// 	true
		// );

		// インラインで出力するグローバル変数
		// wp_localize_script( 'pochipp-menu', 'pbVars', [
		// 	'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		// 	'ajaxNonce' => wp_create_nonce( 'pb-ajax-nonce' ),
		// ] );

	}
}


/**
 * for Gutenberg
 */
add_action( 'enqueue_block_editor_assets', 'POCHIPP\block_assets' );
function block_assets() {

	// ブロック関係のCSS
	wp_enqueue_style(
		'pochipp-blocks',
		POCHIPP_URL . 'dist/css/blocks.css',
		[],
		POCHIPP_VERSION
	);

	// Pochippブロック
	$asset = include POCHIPP_PATH . 'dist/blocks/linkbox/index.asset.php';
	wp_enqueue_script(
		'pochipp-block',
		POCHIPP_URL . 'dist/blocks/linkbox/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);

	// Translations for JS
	// if ( function_exists( 'wp_set_script_translations' ) ) {
	// 	wp_set_script_translations(
	// 		'pochipp-block',
	// 		'pochipp',
	// 		POCHIPP_PATH . 'languages'
	// 	);
	// }

	global $post_type;
	// 商品登録ページでのみ読み込む
	if ( \POCHIPP::POST_TYPE_SLUG === $post_type ) {

		// Pochipp登録ブロック
		$asset = include POCHIPP_PATH . 'dist/blocks/setting/index.asset.php';
		wp_enqueue_script(
			'pochipp-setting-block',
			POCHIPP_URL . 'dist/blocks/setting/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
