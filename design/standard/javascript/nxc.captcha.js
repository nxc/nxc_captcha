var NXC = NXC || {};

(function ($, NXC) {
	'use strict';

	NXC.Captcha = {
		hideTips: function () {
			$('div.nxc-captcha-tip').css('display', 'none');
		},

		installSliders: function () {
			$('input.nxc-captcha-slider-input').css('display', 'none');
			$('div.nxc-captcha-slider').each(function (index, el) {
				var $el = $(el),
					$parent = $el.parent(),
					$input = $('input.nxc-captcha-slider-input', $parent),
					options = {
						min: 0,
						max: 100,
						step: 1,
						value: $input.val(),
						slide: function (event, ui) {
							var $parent = $(this).parent();

							$('input.nxc-captcha-slider-input', $parent).val(ui.value);
							$('span.nxc-captcha-slider-value', $parent).html(ui.value);
						}
					};

				$.each(['min', 'max'], function (optionIndex, option) {
					var $optionInput = $('input.nxc-captcha-slider-' + option, $parent);

					if ($optionInput.length === 1) {
						options[option] = parseInt($($optionInput[0]).val(), 10);
					}
				});

				$input.after(
					$('<span />', {
						'class': 'nxc-captcha-slider-value',
						'html': $input.val()
					})
				);

				$el.slider(options);
			});
		},

		installColorSelectors: function () {
			$('input.nxc-captcha-color-selector-input').css('display', 'none');
			$('div.nxc-captcha-color-selector').each(function (index, el) {
				var $el = $(el),
					$parent = $el.parent(),
					$input = $('input.nxc-captcha-color-selector-input', $parent);

				$el.ColorPicker({
					color: '#' + $input.val(),
					onShow: function (colorPicker) {
						$(colorPicker).show();
					},
					onHide: function (colorPicker) {
						$(colorPicker).hide();
					},
					onChange: function (hsb, hex, rgb) {
						var $el = $(this.data('colorpicker').el);

						$('input.nxc-captcha-color-selector-input', $el.parent()).val(hex);
						$('div.nxc-captcha-color-selector-bg', $el).css(
							'backgroundColor',
							'#' + hex
						);
					}
				});
			});
		},

		installRegenerateLink: function () {
			var $links = $('a.nxc-captcha-regenerate');

			$links.show();

			$links.each(function (index, el) {
				var $el = $(el),
					tmp = $el.attr('id').split('-'),
					id = tmp[tmp.length - 1],
					$image = $('#nxc-captcha-' + id);

				if ($image.length > 0) {
					$el.data('captcha', $image);
					$el.click(function (e) {
						var $el = $(this);

						e.preventDefault();
						$el.data('captcha').attr('src', false);
						$el.data('captcha').attr(
							'src',
							$el.attr('href') + '/' + (1000000 * (Math.random() % 1))
						);
					});
				}
			});
		}
	};
}(jQuery, NXC));

if (jQuery) {
	jQuery(document).ready(function ($) {
		'use strict';

		// Content class edit
		NXC.Captcha.hideTips();
		NXC.Captcha.installSliders();
		NXC.Captcha.installColorSelectors();
		// Object edit
		NXC.Captcha.installRegenerateLink();
	});
}
