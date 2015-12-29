function updateClock() {
    var today = new Date();

    var hour = today.getHours();
    var mins = today.getMinutes();
    var secs = today.getSeconds();

    if (secs <= 9){
        secs = "0" + secs;
    } if (mins <= 9){
    	mins = "0" + mins;
    }

    var TotalTime = 'Čas: ' + today.getDate() + '.' + (today.getMonth()+1) + '.' + today.getFullYear() + " - " +
    				today.getHours() + ":" + mins + ":" + secs;

    document.getElementById("timer").innerHTML = TotalTime;

    setTimeout(updateClock, 1000);
}

$(function() {
	$.nette.init();
	window['updateClock']();
});


$("#user-photo-small").click(function() {
	$("#user-preferences").fadeToggle(120);
});