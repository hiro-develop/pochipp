/**
 * 設定値による動的な変化
 */
(function ($) {
	$(function () {
		const menuBtn = $('.pchpp-setting__menubtn');
		menuBtn.click(function () {
			menuBtn.toggleClass('is-open');
		});

		(function () {
			const $boxPreview = $('.pchpp-setting__preview');
			const $box = $boxPreview.find('.pochipp-box');
			const $btns = $boxPreview.find('.pochipp-box__btns');

			// レイアウト PC
			$('[name="pochipp_settings[box_layout_pc]"]').change(function () {
				const thisVal = $(this).val();
				$box.attr('data-lyt-pc', thisVal);
			});

			// レイアウト モバイル
			$('[name="pochipp_settings[box_layout_mb]"]').change(function () {
				const thisVal = $(this).val();
				$box.attr('data-lyt-mb', thisVal);
			});

			// img_position
			$('[name="pochipp_settings[img_position]"]').change(function () {
				const thisVal = $(this).val();
				$box.attr('data-img', thisVal);
			});

			// btn_style
			$('[name="pochipp_settings[btn_style]"]').change(function () {
				const thisVal = $(this).val();
				$box.attr('data-btn-style', thisVal);
			});

			// btn_radius
			$('[name="pochipp_settings[btn_radius]"]').change(function () {
				const thisVal = $(this).val();
				$box.attr('data-btn-radius', thisVal);
			});

			// max_column_pc
			$('[name="pochipp_settings[max_column_pc]"]').change(function () {
				const thisVal = $(this).val();
				$btns.attr('data-maxclmn-pc', thisVal);
			});

			// max_column_mb
			$('[name="pochipp_settings[max_column_mb]"]').change(function () {
				const thisVal = $(this).val();
				$btns.attr('data-maxclmn-mb', thisVal);
			});
		})();
	});
})(jQuery);
