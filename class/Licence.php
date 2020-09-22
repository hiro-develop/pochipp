<?php

namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Licence {

	/**
	 * ライセンスの照合をする関数
	 *
	 * @param string $licence_key ライセンスキー
	 * @return bool ライセンス通ったかどうか
	 */
	public static function check_licence( $licence_key = '' ) {

		// ライセンスキー
		$licence_key = $licence_key ?: \POCHIPP::get_setting( 'pochipp_licence_key' );

	}

}
