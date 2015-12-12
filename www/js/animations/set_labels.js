$('#add_label_button').click(function() {
	$('#add_label_button').fadeToggle(120);
	setTimeout(function() {
        $('#add_label_form').fadeToggle(120);
        $('#add_label_name_input').select();
    }, 170);
});

$('#storno_icon').click(function() {
	$('#add_label_form').fadeToggle(120);
	$('#add_label_name_input').val('');
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
		$('#edit_label_storno_' + elementId).fadeToggle(100);
		$('#edit_label_submit_div_' + elementId).fadeToggle(100);
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
	var elementId = this.id.substr(19, this.id.length);
	var href = '&do=editLabelColor';

	for (var i = 1; i <= 20; i++) {
		$('#color_id_' + i).attr('href', '?labelId=' + elementId + '&colorId=' + i + href);
	};

	$('#choose_color_window').fadeToggle(120);
	$('#dark').fadeToggle(120);
});

$('#add_label_choose_color_button').click(function() {
	$('.color_cell').attr('href', '#');
	$('#choose_color_window').fadeToggle(120);
	$('#dark').fadeToggle(120);
});

$('#cancel_choosing_label_color').click(function() {
	$('#choose_color_window').fadeToggle(120);
	$('#dark').fadeToggle(120);
});

$('#dark').click(function() {
	$('#choose_color_window').fadeToggle(120);
	$('#dark').fadeToggle(120);	
});

$('.color_cell').click(function() {
	$('#choose_color_window').fadeToggle(120);
	$('#dark').fadeToggle(120);
	
	if(this.href.includes('#')) {
		var elementId = this.id.substr(9, this.id.length)
		$('#add_label_color_id_hidden').val(elementId);
		$('#add_label_form').css('border-left-color', $('#cell_color_' + elementId).css('background'));
		return false;
	}
});
