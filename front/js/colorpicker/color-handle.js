/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function(){

	$('#cpicker_fld').ColorPicker({
		color: '#d9411a',
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelector div').css('backgroundColor', '#' + hex);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
	$('#colorSelector').ColorPicker({
		color: '#d9411a',
		onSubmit: function(hsb, hex, rgb, el) {
			$('#cpicker_fld').val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			if($('#cpicker_fld').attr('value') != '') {
				$(this).ColorPickerSetColor('#' + $('#cpicker_fld').attr('value'));
			}
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelector div').css('backgroundColor', '#' + hex);
		}
	});

});

