$('#add_label_button').click(function() {
	$('#add_label_button').fadeToggle(120);
	setTimeout(function() {
        $('#add_label_form').fadeToggle(120);
        $('#add_label_name_input').select();
    }, 170);
});

$('#storno_icon').click(function() {
	$('#add_label_form').fadeToggle(120);
	setTimeout(function() {
        $('#add_label_button').fadeToggle(120);
    }, 170);
});

$('.label_edit').click(function() {
	var elementId = this.id;
	$('#edit_label_name_' + elementId).prop("disabled", false);
	$('#edit_label_name_' + elementId).select();
	$('#' + elementId).fadeToggle(120);
	$('#label_color_change_' + elementId).fadeToggle(120);
	$('#label_remove_' + elementId).fadeToggle(120);
	setTimeout(function() {
		$('#edit_label_storno_' + elementId).fadeToggle(120);
		$('#edit_label_submit_div_' + elementId).fadeToggle(120);
	}, 200);
});

$('.edit_label_storno').click(function() {
	var elementId = this.id.substr(18, 19);
	$('#edit_label_name_' + elementId).prop("disabled", true);
	
	$('#edit_label_storno_' + elementId).fadeToggle(120);
	$('#edit_label_submit_div_' + elementId).fadeToggle(120);
	setTimeout(function() {
		$('#' + elementId).fadeToggle(120);
		$('#label_color_change_' + elementId).fadeToggle(120);
		$('#label_remove_' + elementId).fadeToggle(120);
	}, 200);
});

$('.label_choose_color').click(function() {
	$('#dark').fadeToggle(120);
});
