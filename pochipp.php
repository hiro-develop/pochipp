<?php
/**
 * Plugin Name: Pochipp
 * Plugin URI: https://pochipp.com/
 * Description: Amazon・楽天市場・Yahooショッピングなどのアフィリエイトリンクを簡単に作成・管理できる、ブロックエディターに最適化されたプラグインです。
 * Author: ひろ
 * Version: 0.1.5
 * Author URI: https://pochipp.com/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'register_block_type' ) ) return;

/**
 * Ver.
 */
define( 'POCHIPP_VERSION', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? date( 'mdGis' ) : '0.1.5' );

/**
 * Define path, url
 */
define( 'POCHIPP_URL', plugins_url( '/', __FILE__ ) );
define( 'POCHIPP_PATH', plugin_dir_path( __FILE__ ) );
define( 'POCHIPP_BASENAME', plugin_basename( __FILE__ ) );


/**
 * class autoload
 */
spl_autoload_register(
	function( $classname ) {

		if ( false === strpos( $classname, 'POCHIPP' ) ) return;

		$file_name = str_replace( 'POCHIPP\\', '', $classname );
		$file_name = str_replace( '\\', '/', $file_name );
		$file      = POCHIPP_PATH . 'class/' . $file_name . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

require_once POCHIPP_PATH . 'inc/register_rest.php';

/**
 * POCHIPP main
 */
class POCHIPP extends \POCHIPP\Data {
	use \POCHIPP\Form_Output, \POCHIPP\Utility, \POCHIPP\Licence;

	public function __construct() {

		// Amazon API v5
		if ( ! class_exists( '\AwsV4' ) ) {
			require_once POCHIPP_PATH . 'inc/api/paapiv5.php';
		}

		add_action( 'init', [ $this, 'set_setting_data' ], 1 );

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
	 * set_setting_data
	 */
	public function set_setting_data() {

		// 設定されたデータを取得
		$setting_data = get_option( self::DB_NAME ) ?: [];

		// デフォルト値があるものはそれとマージしてセット
		self::$setting_data = array_merge( self::$default_data, $setting_data );
	}


	/**
	 * get_setting
	 */
	public static function get_setting( $key = null ) {

		if ( null !== $key ) {
			// if ( ! isset( self::$setting_data[ $key ] ) ) return '';
			return self::$setting_data[ $key ] ?? '';
		}
		return self::$setting_data;
	}

	/**
	 * set_setting
	 */
	// public static function set_setting( $key = null, $value ) {
	// 	if ( null !== $key ) {
	// 		self::$setting_data[ $key ] = $value;
	// 	}
	// }
}


/**
 * Start
 */
add_action( 'plugins_loaded', function() {

	new POCHIPP();

	/**
	 * アップデートチェック
	 */
	if ( ! class_exists( 'Puc_v4_Factory' ) ) {
		require POCHIPP_PATH . 'inc/updater/plugin-update-checker.php';
	}
	Puc_v4_Factory::buildUpdateChecker(
		'https://pochipp.com/plugin_versions/version.json',
		POCHIPP_PATH . 'pochipp.php',
		'pochipp'
	);
}, 11 );
