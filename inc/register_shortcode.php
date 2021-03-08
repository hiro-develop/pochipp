<?php
namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'pochipp', '\POCHIPP\sc_pochipp' );
function sc_pochipp( $atts ) {

	if ( ! isset( $atts['id'] ) ) return '';

	$args = [ 'pid' => $atts['id'] ];
	if ( isset( $atts['title'] ) ) {
		$args['title'] = $atts['title'];
	}
	if ( isset( $atts['info'] ) ) {
		$args['info'] = $atts['info'];
	}

	return \POCHIPP\cb_pochipp_block( $args, null );
}
