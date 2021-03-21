/**
 * カラーピッカーに関するスクリプト。
 * https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
 */
(function ($) {
	//通常（ウィジェット以外）
	$(function () {
		$('.pochipp-colorpicker').wpColorPicker({
			change(event, ui) {
				//チェンジイベントを発火させる setTimeoutでちょっと遅らせないと選択した色が反映されない
				const $this = $(this);
				setTimeout(function () {
					$this.trigger('change');
				}, 10);
			},
			clear() {
				// クリアクリック時にも changeイベントを発火させる。
				const $this = $(this);
				const $colorPicker = $this.prev().find('input');
				setTimeout(function () {
					$colorPicker.trigger('change');
				}, 10);
			},
		});
	});
})(jQuery);
