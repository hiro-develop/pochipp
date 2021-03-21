/**
 * https://xdsoft.net/jqplugins/datetimepicker/
 */
(function ($) {
	window.setDatetimepicker = () => {
		$.datetimepicker.setLocale('ja');
		$('.pochipp-datepicker--start').datetimepicker({
			defaultTime: '00:00',
			// timepickerScrollbar: false,
			// scrollTime: false,
			scrollMonth: false,
			scrollInput: false,
		});

		$('.pochipp-datepicker--end').datetimepicker({
			defaultTime: '23:59',
			// timepickerScrollbar: false,
			// scrollTime: false,
			scrollMonth: false,
			scrollInput: false,
			allowTimes: [
				'00:59',
				'01:59',
				'02:59',
				'03:59',
				'04:59',
				'05:59',
				'06:59',
				'07:59',
				'08:59',
				'09:59',
				'10:59',
				'11:59',
				'12:59',
				'13:59',
				'14:59',
				'15:59',
				'16:59',
				'17:59',
				'18:59',
				'19:59',
				'20:59',
				'21:59',
				'22:59',
				'23:59',
			],
			// minDate
		});
	};

	$(function () {
		window.setDatetimepicker();
	});
})(jQuery);
