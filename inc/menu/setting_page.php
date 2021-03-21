<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

$now_tab = $_GET['tab'] ?? 'basic';

// メッセージ
$green_message = '';
if ( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] ) {
	$green_message = '設定を保存しました。';
}

?>
<div id="pochipp_setting" class="wrap pchpp-setting">
	<hr class="wp-header-end">
	<?php if ( $green_message ) : ?>
		<div class="notice updated is-dismissible"><p><?=esc_html( $green_message )?></p></div>
	<?php endif; ?>
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
						do_settings_sections( \POCHIPP::MENU_PAGE_PREFIX . '_' . $key );
						submit_button( '', 'primary large', 'submit_' . $key );
					echo '</div>';
				endforeach;
				settings_fields( \POCHIPP::SETTING_GROUP );
			?>
		</form>
	</div>
</div>
