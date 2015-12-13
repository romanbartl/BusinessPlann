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

$("#menu-button-index").click(function() { 
	$("nav").toggle('slide', {direction: 'left'}, 400);
});

$("#user-photo-small").click(function() {
	$("#user-preferences").fadeToggle(120);
});

$('#main_add_button').click(function() {
	$("#dark").fadeToggle(120);
});

$('#dark').click(function() {
	$("#dark").fadeToggle(120);
});


/* IDEA
$(document).click(
function(element) {
if((!$(element.target).is('#search-div-icon, #')) && ($("#search-div-input").css('display') == 'block'))
$("#search-div-input").toggle('slide', {direction: 'right'}, 300);
}
);*/