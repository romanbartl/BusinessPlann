$('.dhx_cal_tab').click(viewChange);
$('.dhx_arrow').click(viewDayChange);

function viewChange(){
	setTimeout(function(){
		var url = window.location.pathname;
		var url2 = url.substring(url.indexOf('/app/') + 5);
		var date = '';
		
		if(url2.indexOf('/') > -1)
			date = url2.substring(url2.indexOf('/'));
		url = url.substring(0, url.indexOf('/app/') + 5);
		window.history.replaceState('', '', url + scheduler.getState().mode + date);
	}, 1);
}

function viewDayChange(){
	setTimeout(function(){
		var url = window.location.pathname;
		var url2 = url.substring(url.indexOf('/app/') + 5);
		var date = scheduler.getState().date;
		date = date.getFullYear() + '-' + addZero((date.getMonth()+1)) + '-' + addZero(date.getDate());
		if(url2.indexOf('/') > -1)
			window.history.replaceState('', '', date);
		else
			window.history.replaceState('', '', scheduler.getState().mode + '/' + date);
	}, 1);	
}

function addZero(i) {
    if (i < 10) 
        i = "0" + i;
    return i;
}

