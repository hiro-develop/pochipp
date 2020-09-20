<?php

namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Form_Output {

	/**
	 * テキストフィールドを出力する
	 */
	public static function output_text_field( $args ) {
		$label = $args['label'] ?? '';
		$key   = $args['key'] ?? '';
		$size  = $args['size'] ?? '';

		$name = \POCHIPP::DB_NAME . '[' . $key . ']';
		$val  = \POCHIPP::get_setting( $key );
		?>
			<div class="pcpp-setting__field -text">
				<span class="pcpp-setting__label"><?=esc_html( $label )?></span>
				<div class="pcpp-setting__item">
					<input type="text" id="<?=esc_attr( $key )?>" name="<?=esc_attr( $name )?>" value="<?=esc_attr( $val )?>" size="40" />
				</div>
			</div>
		<?php
	}


	/**
	 * ラジオボタンを出力する
	 */
	public static function output_radio( $args ) {

		$key     = $args['key'] ?? '';
		$choices = $args['choices'] ?? '';

		$name = \POCHIPP::DB_NAME . '[' . $key . ']';
		$val  = \POCHIPP::get_setting( $key );

		?>
		<div class="pcpp-setting__field -radio">
			<?php
				foreach ( $choices as $value => $label ) :
					$radio_id = $key . '_' . $value;
					$checked  = checked( $val, $value, false );
			?>
					<label for="<?=esc_attr( $radio_id )?>" class="pcpp-setting__label">
						<input type="radio" id="<?=esc_attr( $radio_id )?>" name="<?=esc_attr( $name )?>" value="<?=esc_attr( $value )?>" <?=$checked?> >
						<span><?=esc_html( $label )?></span>
					</label>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * チェックボックスを出力する
	 */
	public static function output_checkbox( $args ) {

		$label = $args['label'] ?? '';
		$key   = $args['key'] ?? '';

		$name = \POCHIPP::DB_NAME . '[' . $key . ']';
		$val  = \POCHIPP::get_setting( $key );

		$checked = checked( (string) $val, '1', false );

		?>
		<div class="pcpp-setting__field -checkbox">
			<input type="hidden" name="<?=esc_attr( $name )?>" value="">
			<input type="checkbox" id="<?=esc_attr( $key )?>" name="<?=esc_attr( $name )?>" value="1" <?=$checked?> />
			<label for="<?=esc_attr( $key )?>"><?=esc_html( $label )?></label>
		</div>
		<?php
	}


}
