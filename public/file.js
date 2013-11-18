jQuery(document).ready(function() {
	$('.bree-file-remove').click(function() {

		var $input = $(this).siblings('input[name="'+$(this).data("field")+'"]');

		// var $replace = $input.clone().removeAttr('type').attr('type','file');

		var $replace = $('<input type="file">');
		var attributes = $input.prop("attributes");

		// Loop through the hidden input's attributes and copy to file input
		$.each(attributes, function() {
			if(this.name != 'type')
				$replace.attr(this.name, this.value);
		});

		$replace.insertAfter($input);

		$input.remove();
		$(this).siblings('.bree-file-view').remove();
		$(this).remove();
	});
});