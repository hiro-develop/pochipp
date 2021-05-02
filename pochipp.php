<?php
/**
 * Plugin Name: Pochipp
 * Plugin URI: https://pochipp.com/
 * Description: Amazon・楽天市場・Yahooショッピングなどのアフィリエイトリンクを簡単に作成・管理できる、ブロックエディターに最適化されたプラグインです。
 * Author: ひろ
 * Version: 1.3.0
 * Author URI: https://twitter.com/hiro_develop127
 * Text Domain: pochipp
 * License: GPL3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Define
 */
define( 'POCHIPP_URL', plugins_url( '/', __FILE__ ) );
define( 'POCHIPP_PATH', plugin_dir_path( __FILE__ ) );
define( 'POCHIPP_BASENAME', plugin_basename( __FILE__ ) );


/**
 * Autoload
 */
spl_autoload_register( function( $classname ) {

	if ( false === strpos( $classname, 'POCHIPP' ) ) return;

	$file_name = str_replace( 'POCHIPP\\', '', $classname );
	$file_name = str_replace( '\\', '/', $file_name );
	$file      = POCHIPP_PATH . 'class/' . $file_name . '.php';

	if ( file_exists( $file ) ) require $file;
});


/**
 * POCHIPP
 */
class POCHIPP extends \POCHIPP\Data {
	use \POCHIPP\Setting, \POCHIPP\Helper;

	public function __construct() {

		self::$version = get_file_data( __FILE__, [ 'Version' ] );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			self::$version = date( 'mdGis' );
		}

		if ( ! class_exists( '\AwsV4' ) ) {
			require_once POCHIPP_PATH . 'inc/api/paapiv5.php';
		}

		add_action( 'init', [ $this, 'set_setting_data' ], 1 );
		add_action( 'after_setup_theme', [ $this, 'load_pluggable' ], 99 );

		require_once POCHIPP_PATH . 'inc/enqueues.php';
		require_once POCHIPP_PATH . 'inc/output.php';
		require_once POCHIPP_PATH . 'inc/register_pt.php';
		require_once POCHIPP_PATH . 'inc/register_tax.php';
		require_once POCHIPP_PATH . 'inc/register_meta.php';
		require_once POCHIPP_PATH . 'inc/register_blocks.php';
		require_once POCHIPP_PATH . 'inc/register_shortcode.php';
		require_once POCHIPP_PATH . 'inc/ajax.php';

		if ( is_admin() ) {
			require_once POCHIPP_PATH . 'inc/thickbox.php';
			require_once POCHIPP_PATH . 'inc/menu.php';
			require_once POCHIPP_PATH . 'inc/manage_columns.php';
		}
	}


	/**
	 * load_pluggable
	 */
	public function load_pluggable() {
		require_once POCHIPP_PATH . 'inc/pluggable.php';
	}


	/**
	 * set_setting_data
	 */
	public function set_setting_data() {
		$setting_data       = get_option( self::DB_NAME ) ?: [];
		self::$setting_data = array_merge( self::$default_data, $setting_data );
		self::$has_affi     = [
			'amazon'  => (bool) self::$setting_data['amazon_traccking_id'] || self::$setting_data['moshimo_amazon_aid'],
			'rakuten' => (bool) self::$setting_data['rakuten_affiliate_id'] || self::$setting_data['moshimo_rakuten_aid'],
			'yahoo'   => (bool) self::$setting_data['yahoo_linkswitch'] || self::$setting_data['moshimo_yahoo_aid'],
		];
	}


	/**
	 * get_setting
	 */
	public static function get_setting( $key = null ) {
		if ( null !== $key ) {
			return self::$setting_data[ $key ] ?? '';
		}
		return self::$setting_data;
	}
}


/**
 * Start
 */
add_action( 'plugins_loaded', function() {
	new POCHIPP();
}, 11 );
