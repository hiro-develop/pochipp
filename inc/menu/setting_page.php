<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

// メッセージ
$green_message = '';
if ( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] ) {
	$green_message = '設定を保存しました。';
}

if ( $green_message ) {
	echo '<div class="notice updated is-dismissible"><p>' . esc_html( $green_message ) . '</p></div>';
}

// タブ情報
$setting_tabs = [
	'basic'  => '基本設定',
	'design' => 'デザイン',
];

// 現在のタブ
$now_tab = $_GET['tab'] ?? 'basic';
?>
<div id="pochipp_setting" class="pcpp-setting">
	<h1 class="pcpp-setting__title">
		Pochipp設定
	</h1>
	<div class="pcpp-setting__tabs">
		<div class="nav-tab-wrapper">
			<?php
				// タブ出力
				foreach ( $setting_tabs as $key => $val ) :

					$setting_url = admin_url( 'edit.php?post_type=pochipps&page=pochipp_settings' );
					$tab_url     = $setting_url . '&tab=' . $key;
					$nav_class   = ( $now_tab === $key ) ? 'nav-tab is-active' : 'nav-tab';

					echo '<a href="' . esc_url( $tab_url ) . '" class="' . esc_attr( $nav_class ) . '">' . esc_html( $val ) . '</a>';
				endforeach
			?>
		</div>
	</div>
	<div class="pcpp-setting__body">
		<form method="POST" action="options.php">
		<?php
			foreach ( $setting_tabs as $key => $val ) {

			$tab_class = ( $now_tab === $key ) ? 'tab-contents is-active' : 'tab-contents';

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<div id="' . $key . '" class="' . $tab_class . '">';

			// タブコンテンツの読み込み
			if ( file_exists( POCHIPP_PATH . '/inc/menu/tabs/' . $key . '.php' ) ) {

				include_once POCHIPP_PATH . '/inc/menu/tabs/' . $key . '.php';

			} else {
				// ファイルなければ単純に do_settings_sections
				do_settings_sections( \POCHIPP::MENU_PAGE[ $key ] );
				submit_button( '', 'primary large', 'submit_' . $key );
			}

			echo '</div>';
			}

			// nonce出力
			settings_fields( \POCHIPP::SETTING_GROUP );
		?>
		</form>
	</div>
</div>
