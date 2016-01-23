function changeColor(id, color){
	hidden = document.getElementById('group_color_hidden')
	hidden.value = id;

	$('#chooseGroupColor').css('background', '#' + color);	
	$('#choose_color_window').fadeToggle(120);
	$('#dark').fadeToggle(120);
}

$('#chooseGroupColor').click(function() {
	$('#choose_color_window').fadeToggle(120);
	$('#dark').fadeToggle(120);
});