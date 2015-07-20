(function ($) {
	$('.hexcode').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb, el) {
			$(el).css('border', '2px solid #' + hex);
			$(el).val(hex);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
})(jQuery);