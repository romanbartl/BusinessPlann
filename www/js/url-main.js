function viewChange(button){
	var url = window.location.pathname;
	var date = '';
	var url2 = url.substring(url.indexOf('/app/') + 5);
	if(url2.indexOf('/') > -1)
		date = url2.substring(url2.indexOf('/'));
	url = url.substring(0, url.indexOf('/app/') + 5);
	window.history.replaceState('', '', url + button.id + date);
};

function viewDayChange(button){
	var url = window.location.pathname;
	url = url.substring(url.indexOf('/app/') + 5);
	if(url.indexOf('/') > -1) {
		url = url.substring(0, url.indexOf('/'));
		window.history.replaceState('', '', button.value);
	} else
		window.history.replaceState('', '', url + '/' + button.value);
};

function showDay(button){
	var url = window.location.pathname;
	url = url.substring(0, url.indexOf('/app/') + 5);
	window.history.replaceState('', '', url + 'day/' + button.id);
}
