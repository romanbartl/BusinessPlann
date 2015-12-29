$(function() {
	if(window.location.hash == '') {
		window.location.hash = 'day';
	}
});

$("#search-div-icon").click(function() { 
	$("#search-div-input").toggle('slide', {direction: 'right'}, 300);
	$(".search-input").select();
	if($(".search-input").val() != '') $(".search-input").val('');
});

$(document).keyup(function(e) {
	if ((e.keyCode == 27) && ($('#search-div-input').css('display') === 'block')) { 
		$("#search-div-input").toggle('slide', {direction: 'right'}, 300);
		$(".search-input").val('');
	}
});

$('#main_add_button').click(function() {
	$("#dark").fadeToggle(120);
	$('#add_event').fadeToggle(120);
});

$('#dark').click(function() {
	$("#dark").fadeToggle(120);
	$('#add_event').fadeToggle(120);
});


$(function() {
	$('.datepicker').datetimepicker({
    	format: 'DD.MM.YYYY',
    	viewMode: 'days'
    });
});

$(function() {
	$('.timepicker').datetimepicker({
   		format: 'HH:mm'
    });
});