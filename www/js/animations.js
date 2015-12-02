//MAIN

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


$("#labels_heading").click(function() {
	$("#labels_list").toggle('slide', {direction: 'up'}, 200);
});

$("#groups_heading").click(function() {
	$("#groups_list").toggle('slide', {direction: 'up'}, 200);
});


/* IDEA
$(document).click(
function(element) {
if((!$(element.target).is('#search-div-icon, #')) && ($("#search-div-input").css('display') == 'block'))
$("#search-div-input").toggle('slide', {direction: 'right'}, 300);
}
);*/

//USER

$('#change_name_but').click(function() {
    $('#name_input').prop("disabled", false);
    $('#name_input').select();
    $('#change_name_but').hide();
    $('#change_name_but_save').show();
	$('#storno_name_but').show();
});

$('#storno_name_but').click(function() {
	$('#name_input').prop("disabled", true);
    $('#change_name_but').show();
    $('#change_name_but_save').hide();
	$('#storno_name_but').hide();
});

/*$('#change_name_but_save').click(function() {
    $('#name_input').prop("disabled", true);
    $('#change_name_but_save').hide();
    $('#change_name_but').show();
});*/

$('#change_surname_but').click(function() {
    $('#surname_input').prop("disabled", false);
    $('#surname_input').select();
    $('#change_surname_but').hide();
    $('#change_surname_but_save').show();
    $('#storno_surname_but').show();
});

$('#storno_surname_but').click(function() {
    $('#surname_input').prop("disabled", true);
    $('#change_surname_but').show();
    $('#change_surname_but_save').hide();
    $('#storno_surname_but').hide();
});
