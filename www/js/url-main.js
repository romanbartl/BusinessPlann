$(function() {
	if(window.location.search == '')
		window.history.replaceState('', '', '?day');
});

$('#viewDayButton').click(function(){
	window.history.replaceState('', '', '?day');
});

$('#viewWeekButton').click(function(){
	window.history.replaceState('', '', '?week');
});

$('#viewMonthButton').click(function(){
	window.history.replaceState('', '', '?month');
});

$('#viewAgendaButton').click(function(){
	window.history.replaceState('', '', '?agenda');
});

function viewDayChange(button){
	if(window.location.search.indexOf('&') > -1) {
		var url = window.location.search;
		var view = url.substring(url.indexOf('?'), url.indexOf('&'));
		window.history.replaceState('', '', view + '&date=' + button.value);
	} else
		window.history.replaceState('', '', window.location.search + '&date=' + button.value);
};
