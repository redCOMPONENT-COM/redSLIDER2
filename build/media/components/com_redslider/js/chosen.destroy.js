(function($) {
	'use strict';
	$.fn.chosenDestroy = function () {
		var $element = $(this);
		var timer;

		if (typeof $.fn.chosen === 'undefined') {
			return $(this);
		}

		timer = setTimeout(function(){
			if (typeof $element.data('chosen') === 'object') {
				$element.chosen('destroy').removeClass('chzn-done').show();

				clearTimeout(timer);
			}

		}, 100);

		return $(this);
	}
})(jQuery);
