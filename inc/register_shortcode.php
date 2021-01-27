<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'pochipp', '\POCHIPP\sc_pochipp' );
function sc_pochipp( $atts ) {

	if ( ! isset( $atts['id'] ) ) return '';

	$pid = $atts['id'];

	return \POCHIPP\cb_pochipp_block( ['pid' => $pid ], null );
}
