$t = jQuery.noConflict();
$t(function() {

	$t('.ubicacion-options').each(function() {
	
		/* if the custom message input field has no message,
		 * then hide it. */
		if($t(this).children('input[type=text]').val() === '') {
			$t(this).children('input[type=text]').hide();
			$t('.coord_label').hide();
		}
		
		/* when this widget's checkbox is clicked, determine if the input element
		 * is visible.
		 *
		 * if it is, then clear the element and hide it; otherwise, show it. */
		$t(this).children('input[type=checkbox]').click(function() {
			if($t(this).siblings('input[type=text]').is(':visible')) {
				$t(this).siblings('input[type=text]').val('').hide();
				$t('.coord_label').hide();
			} else {
				$t(this).siblings('input[type=text]').show();
				$t('.coord_label').show();
			}
		});
		
	});
	
});
jQuery = jQuery.noConflict();