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
