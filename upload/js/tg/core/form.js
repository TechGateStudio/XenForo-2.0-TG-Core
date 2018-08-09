!function($, window, document, _undefined)
{
	"use strict";

	// ################################## TIME INPUT HANDLER ###########################################

	XF.TGCTimeInput = XF.Element.newHandler({
		init: function()
		{
			this.$target.bootstrapMaterialDatePicker({ 
				date: false,
				format : 'HH:mm'
			});
		}
	});

	// ################################## --- ###########################################

	XF.Element.register('time-input', 'XF.TGCTimeInput');
}
(window, document);