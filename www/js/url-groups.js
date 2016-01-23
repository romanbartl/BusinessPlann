function addGroup(){
	var url = window.location.pathname;
	url = url.substring(0, url.indexOf('/groups') + 7);
	window.history.replaceState('', '', url + '/add');
}

function editGroup(id) {
	var url = window.location.pathname;
	url = url.substring(0, url.indexOf('/groups') + 7);
	window.history.replaceState('', '', url + '/edit/' + id);
}

function stornoGroup() {
	var url = window.location.pathname;
	url = url.substring(0, url.indexOf('/groups') + 7);
	window.history.replaceState('', '', url);
}