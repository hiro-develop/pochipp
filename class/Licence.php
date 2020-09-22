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

		if ( function_exists( 'curl_version' ) ) {
			$curl_headers = ['Content-Type: application/json'];
			$data = ['license_id' => $licence_key];

			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, 'https://asia-northeast1-pochipp-84843.cloudfunctions.net/isValidLicense');
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode($data) );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $curl_headers );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

			$response = curl_exec( $curl );
			$json = json_decode($response, true);
		}
		return $json['valid'];
	}

}
