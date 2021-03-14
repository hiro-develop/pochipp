<?php

namespace POCHIPP;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Form_Output {

	/**
	 * テキストフィールドを出力する
	 */
	public static function output_text_field( $args ) {
		$key         = $args['key'] ?? '';
		$size        = $args['size'] ?? '40';
		$description = $args['description'] ?? '';
		$val         = $args['val'] ?? '';
		$name        = $args['name'] ?? \POCHIPP::DB_NAME;

		$name = $name . '[' . $key . ']';
		$val  = $val ?: \POCHIPP::get_setting( $key );
		?>
			<div class="pchpp-setting__field -text">
				<div class="pchpp-setting__item">
					<input type="text" id="<?=esc_attr( $key )?>" name="<?=esc_attr( $name )?>" value="<?=esc_attr( $val )?>" size="<?=esc_attr( $size )?>" />
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
		$class   = $args['class'] ?? '';

		$name = \POCHIPP::DB_NAME . '[' . $key . ']';
		$val  = \POCHIPP::get_setting( $key );

		?>
		<div class="pchpp-setting__field -radio <?=esc_attr( $class )?>">
			<?php
				foreach ( $choices as $value => $label ) :
				$radio_id = $key . '_' . $value;
				$checked  = checked( $val, $value, false );
			?>
					<label for="<?=esc_attr( $radio_id )?>">
						<input type="radio" id="<?=esc_attr( $radio_id )?>" name="<?=esc_attr( $name )?>" value="<?=esc_attr( $value )?>" <?=$checked?> >
						<span><?=wp_kses_post( $label )?></span>
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
			<label for="<?=esc_attr( $key )?>"><?=wp_kses_post( $label )?></label>
		</div>
		<?php
	}


	/**
	 * カラーピッカーを出力する
	 */
	public static function output_colorpicker( $args ) {
		$key         = $args['key'] ?? '';
		$default     = $args['default'] ?? '';
		$description = $args['description'] ?? '';

		$name = \POCHIPP::DB_NAME . '[' . $key . ']';
		$val  = \POCHIPP::get_setting( $key );
		?>
			<div class="pchpp-setting__field -color">
				<div class="pchpp-setting__item">
					<input type="text" class="pochipp-colorpicker __icon_color"
						id="<?=esc_attr( $key )?>"
						name="<?=esc_attr( $name )?>"
						value="<?=esc_attr( $val )?>"
						data-default-color="<?=esc_attr( $default )?>"
					/>
				</div>
			</div>
			<?php if ( $description ) : ?>
				<p class="pchpp-setting__desc"><?=wp_kses_post( $description )?></p>
			<?php endif; ?>
		<?php
	}


	/**
	 * デートピッカーフィールドを出力する
	 */
	public static function output_datepicker( $args ) {
		$key           = $args['key'] ?? '';
		$size          = $args['size'] ?? '20';
		$name          = $args['name'] ?? \POCHIPP::DB_NAME;
		$val_startline = $args['val_startline'] ?? '';
		$val_deadline  = $args['val_deadline'] ?? '';

		$key_start     = $key . 'startline';
		$key_end       = $key . 'deadline';
		$name_start    = $name . '[' . $key_start . ']';
		$name_end      = $name . '[' . $key_end . ']';
		$val_startline = $val_startline ?: \POCHIPP::get_setting( $key_start );
		$val_deadline  = $val_deadline ?: \POCHIPP::get_setting( $key_end );
		?>
			<div class="pchpp-setting__field -date">
				<div class="pchpp-setting__item">
					<input type="text" id="<?=esc_attr( $key_start )?>" class="pochipp-datepicker--start" name="<?=esc_attr( $name_start )?>" value="<?=esc_attr( $val_startline )?>" size="<?=esc_attr( $size )?>" autocomplete="off"/>
					<span class="__nami">~</span>
					<input type="text" id="<?=esc_attr( $key_end )?>" class="pochipp-datepicker--end" name="<?=esc_attr( $name_end )?>" value="<?=esc_attr( $val_deadline )?>" size="<?=esc_attr( $size )?>" autocomplete="off"/>
				</div>
			</div>
		<?php
	}


}
