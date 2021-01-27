<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'pochipp', '\POCHIPP\sc_pochipp' );
function sc_pochipp( $atts ) {

	if ( ! isset( $atts['id'] ) ) return '';

	$pid = $atts['id'];

	return \POCHIPP\cb_pochipp_block( ['pid' => $pid ], null );
}


add_shortcode( 'download_counter', '\POCHIPP\sc_counter' );
function sc_counter( $atts ) {
	$the_id = get_the_ID();

	if ( isset( $_POST['counter'] ) ) {
		wp_safe_redirect( 'http://shop.wp/wp-content/uploads/2020/09/pochipp_block_default.png' );
	}

	ob_start();
	?>
	<form action="" method="post">
		<button type="submit" name="counter">ダウンロード</button>
	</form>
	<?php

	return ob_get_clean();
}
