function textAreaAdjust(o) {
    o.style.height = '1px';
    o.style.height = (25 + o.scrollHeight) + 'px';

    if(o.value == "") {
		o.style.height = '75px';
	}
}

function textAreaCommentAdjust(o) {
	o.style.height = '1px';
    o.style.height = (1 + o.scrollHeight) + 'px';

    if(o.value == "") {
		o.style.height = '25px';
	}
}

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

$(function() {
	$("#comments").scrollTop($("#comments")[0].scrollHeight);
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
	$("#share_dialog").fadeToggle(120);
});

$('#share_event').click(function() {
	$("#dark").fadeToggle(120);
	$("#share_dialog").fadeToggle(120);
});

$('#comment_submit').click(function() {
	$('#textarea_comment').value = "";
});

$.nette.ext('#eventsLabelsChooser', {
    load: function() {
        $('select').change(function (e) {
            $(this).closest('form').submit();
            return false;
        });
    }
});

$.nette.ext('#event_submit_button', {
    load: function() {
        $('#event_submit_button').click(function (e) {
            $('#event_name_input').closest('form').submit();
            return false;
        });
    }
});