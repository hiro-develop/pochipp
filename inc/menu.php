<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add menu
 */
add_action( 'admin_menu', '\POCHIPP\add_admin_menu' );
function add_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=pochipps',
		'ポチップ設定',
		'ポチップ設定',
		'manage_options',
		'pochipp_settings',
		'\POCHIPP\setting_page'
	);
}

function setting_page() {

	$SETTING_TABS = [
		'basic'     => '基本設定',
		'amazon'    => 'Amazon',
		'rakuten'   => '楽天市場',
		'yahoo'     => 'Yahooショッピング',
		'moshimo'   => 'もしも',
		'sale'      => 'セール情報',
		// 'licence'   => 'ライセンス',
	];

	include POCHIPP_PATH . 'inc/menu/setting_page.php';
}


/**
 * Add Sections & Fields
 */
add_action( 'admin_init', '\POCHIPP\add_menu_init' );
function add_menu_init() {

	register_setting( \POCHIPP::SETTING_GROUP, \POCHIPP::DB_NAME );

	\POCHIPP\add_settings( [
		'section_title' => '基本設定',
		'section_key'   => 'basic',
		'page_name'     => \POCHIPP::MENU_PAGE_PREFIX . '_basic',
	] );
	\POCHIPP\add_settings( [
		'section_title' => 'Amazon設定',
		'section_key'   => 'amazon',
		'page_name'     => \POCHIPP::MENU_PAGE_PREFIX . '_amazon',
	] );
	\POCHIPP\add_settings( [
		'section_title' => '楽天設定',
		'section_key'   => 'rakuten',
		'page_name'     => \POCHIPP::MENU_PAGE_PREFIX . '_rakuten',
	] );
	\POCHIPP\add_settings( [
		'section_title' => 'Yahoo(バリューコマース)設定',
		'section_key'   => 'yahoo',
		'page_name'     => \POCHIPP::MENU_PAGE_PREFIX . '_yahoo',
	] );
	\POCHIPP\add_settings( [
		'section_title' => 'もしもアフィリエイト設定',
		'section_key'   => 'moshimo',
		'page_name'     => \POCHIPP::MENU_PAGE_PREFIX . '_moshimo',
	] );
	\POCHIPP\add_settings( [
		'section_title' => 'セール情報の設定',
		'section_key'   => 'sale',
		'page_name'     => \POCHIPP::MENU_PAGE_PREFIX . '_sale',
	] );
	// \POCHIPP\add_settings( [
	// 	'section_title' => 'ライセンス',
	// 	'section_key'   => 'licence',
	// 	'page_name'     => \POCHIPP::MENU_PAGE_PREFIX . '_licence',
	// ] );
}

function add_settings( $args ) {

	$section_title = $args['section_title'] ?? '';
	$section_key   = $args['section_key'] ?? '';
	$section_cb    = $args['section_cb'] ?? '';
	$page_name     = $args['page_name'] ?? '';
	$section_name  = 'pochipp_' . $section_key . '_section';

	add_settings_section( $section_name, $section_title, $section_cb, $page_name );
	add_settings_field(
		$section_name . '_fields',
		'',
		function( $args ) {
			include POCHIPP_PATH . 'inc/menu/fields/' . $args['key'] . '.php';
		},
		$page_name,
		$section_name,
		[ 'key' => $section_key ]
	);
}
