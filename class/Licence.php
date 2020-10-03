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

		$data    = ['license_id' => $licence_key ];
		$headers = ['Content-Type: application/json' ];

		if ( function_exists( 'curl_version' ) ) {
			$data = ['license_id' => $licence_key ];

			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, \POCHIPP::IS_VALID_LICENSE_URL );
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

			$response = curl_exec( $curl );

		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
		} elseif ( '1' == ini_get( 'allow_url_fopen' ) ) {
			$params = [
				'http' => [
					'header'        => implode( "\r\n", $headers ),
					'method'        => 'POST',
					'content'       => json_encode( $data ),
					'ignore_errors' => true,
				],
			];

			$stream = stream_context_create( $params );

			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$response = @file_get_contents( \POCHIPP::IS_VALID_LICENSE_URL, false, $stream );
		} else {
			return [
				'valid'   => false,
				'error'   => true,
				'message' => 'php.iniのallow_url_fopenをONにするかcURLインストールしてください',
			];
		}

		if ( ! $response ) {
			return [
				'valid'   => false,
				'error'   => true,
				'message' => '【parser Error】JSONの取得に失敗しました',
			];
		}

		$json = json_decode( $response, true );
		if ( ! $json && ! is_array( $json ) ) {
			return [
				'valid'   => false,
				'error'   => true,
				'message' => 'APIから正しいデータが返ってきません',
			];
		}

		return [
			'valid'   => $json['valid'],
			'error'   => false,
			'message' => '',
		];
	}

}
