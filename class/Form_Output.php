<?php

namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Form_Output {

	/**
	 * テキストフィールドを出力する
	 */
	public static function output_text_field( $args ) {
		$key         = $args['key'] ?? '';
		$size        = $args['size'] ?? '';
		$description = $args['description'] ?? '';

		$name = \POCHIPP::DB_NAME . '[' . $key . ']';
		$val  = \POCHIPP::get_setting( $key );
		?>
			<div class="pchpp-setting__field -text">
				<div class="pchpp-setting__item">
					<input type="text" id="<?=esc_attr( $key )?>" name="<?=esc_attr( $name )?>" value="<?=esc_attr( $val )?>" size="40" />
				</div>
			</div>
			<?php if ( $description ) : ?>
				<p class="pchpp-setting__desc"><?=wp_kses_post( $description )?></p>
			<?php endif; ?>
		<?php
	}


	/**
	 * テキストエリアを出力する
	 */
	public static function output_textarea( $args ) {
		$key  = $args['key'] ?? '';
		$rows = $args['rows'] ?? '4';

		$name = \POCHIPP::DB_NAME . '[' . $key . ']';
		$val  = \POCHIPP::get_setting( $key );

		?>
			<div class="pchpp-setting__field -textarea">
				<div class="pchpp-setting__item">
					<?php //phpcs:ignore ?>
					<textarea id="<?=esc_attr( $key )?>" name="<?=esc_attr( $name )?>" rows="<?=esc_attr($rows)?>"><?=$val?></textarea>
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
		<div class="pchpp-setting__field -radio">
			<?php
				foreach ( $choices as $value => $label ) :
				$radio_id = $key . '_' . $value;
				$checked  = checked( $val, $value, false );
			?>
					<label for="<?=esc_attr( $radio_id )?>">
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
		<div class="pchpp-setting__field -checkbox">
			<input type="hidden" name="<?=esc_attr( $name )?>" value="">
			<input type="checkbox" id="<?=esc_attr( $key )?>" name="<?=esc_attr( $name )?>" value="1" <?=$checked?> />
			<label for="<?=esc_attr( $key )?>"><?=esc_html( $label )?></label>
		</div>
		<?php
	}
}
