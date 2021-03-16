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

// 現在のタブ
$now_tab = $_GET['tab'] ?? 'basic';
?>
<div id="pochipp_setting" class="wrap pchpp-setting">
<hr class="wp-header-end">
	<header class="pchpp-setting__header">
		<h1 class="pchpp-setting__title">
			<img src="<?=esc_url( POCHIPP_URL )?>assets/img/pochipp-logo.png" alt="ポチップ設定" width="200" height="50">
		</h1>
		<button class="pchpp-setting__menubtn">
			<span class="dashicons dashicons-menu-alt"></span>
		</button>
		<div class="pchpp-setting__tabs">
			<div class="__tabs__wrap">
				<?php
					// タブ出力
					foreach ( $SETTING_TABS as $key => $val ) :
					$setting_url = admin_url( 'edit.php?post_type=pochipps&page=pochipp_settings' );
					$tab_url     = $setting_url . '&tab=' . $key;
					$nav_class   = ( $now_tab === $key ) ? '__tab is-active' : '__tab';

					echo '<a href="' . esc_url( $tab_url ) . '" class="' . esc_attr( $nav_class ) . '" data-key="' . esc_attr( $key ) . '">' . esc_html( $val ) . '</a>';
					endforeach;
				?>
			</div>
		</div>
	</header>

	<div class="pchpp-setting__body">
		<form method="POST" action="options.php">
			<?php
				foreach ( $SETTING_TABS as $key => $val ) :
				$tab_class = ( $now_tab === $key ) ? 'tab-contents is-active' : 'tab-contents';

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div id="' . $key . '" class="' . $tab_class . '">';

				// タブコンテンツの読み込み
				if ( file_exists( POCHIPP_PATH . '/inc/menu/tabs/' . $key . '.php' ) ) :
					include_once POCHIPP_PATH . '/inc/menu/tabs/' . $key . '.php';
					else :
						// ファイルなければ単純に do_settings_sections
						do_settings_sections( \POCHIPP::MENU_PAGE_PREFIX . '_' . $key );
						submit_button( '', 'primary large', 'submit_' . $key );
					endif;

					echo '</div>';
				endforeach;

				// nonce出力
				settings_fields( \POCHIPP::SETTING_GROUP );
			?>
		</form>
	</div>
</div>
