<?php
/**
 * Plugin Name: Pochipp
 * Plugin URI: https://pochipp.com/
 * Description: ぽちっぷ！
 * Author: ぽち
 * Version: 0.0.1
 * Author URI: https://pochipp.com/
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'register_block_type' ) ) return;

/**
 * Ver.
 */
define( 'POCHIPP_VERSION', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? date( 'mdGis' ) : '0.0.1' );

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
		require_once POCHIPP_PATH . 'inc/register_pt.php';
		require_once POCHIPP_PATH . 'inc/register_tax.php';
		require_once POCHIPP_PATH . 'inc/ajax.php';

		if ( is_admin() ) {
			// require_once POCHIPP_PATH . 'inc/add_metabox.php';
			require_once POCHIPP_PATH . 'inc/register_meta.php';
			require_once POCHIPP_PATH . 'inc/thickbox.php';
			require_once POCHIPP_PATH . 'inc/menu.php';
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
}

/**
 * Start
 */
add_action( 'plugins_loaded', function() {

	new POCHIPP();

	/**
	 * 翻訳
	 */
	// $locale = apply_filters( 'plugin_locale', determine_locale(), POCHIPP_DOMAIN );
	// load_textdomain( POCHIPP_DOMAIN, POCHIPP_PATH . 'languages/useful-blocks-' . $locale . '.mo' );

	/**
	 * アップデートチェック
	 */
	// if ( ! class_exists( 'Puc_v4_Factory' ) ) {
	// 	require POCHIPP_PATH . 'inc/updater/plugin-update-checker.php';
	// }
	// Puc_v4_Factory::buildUpdateChecker(
	// 	'https://pochipp.com/pochipp-plugin/update.json',
	// 	POCHIPP_PATH,
	// 	'pochipp'
	// );
}, 11 );
