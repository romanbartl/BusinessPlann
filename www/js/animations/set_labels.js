$('#add_label').click(function() {
	$('#add_label').fadeToggle(120);
	setTimeout(function() {
        $('#add_label_div').fadeToggle(120);
        $('#add_label_name_input').select();
    }, 170);
});