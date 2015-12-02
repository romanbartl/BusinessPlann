$('#change_name_but').click(function() {
    $('#name_input').prop("disabled", false);
    $('#change_surname_but').prop("disabled", true);
    $('#change_email_but').prop("disabled", true);
    $('#change_passwd_but').prop("disabled", true);
    $('#name_input').select();
    $('#change_name_but').fadeToggle(120);
    setTimeout(function() {
        $('#change_name_but_save').fadeToggle(120);
	    $('#storno_name_but').fadeToggle(120);
    }, 150);
});

$('#storno_name_but').click(function() {
	$('#name_input').prop("disabled", true);
    $('#change_surname_but').prop("disabled", false);
    $('#change_email_but').prop("disabled", false);
    $('#change_passwd_but').prop("disabled", false);
    $('#approve_name_change').fadeToggle(120);
    $('#change_name_but_save').fadeToggle(120);
    $('#storno_name_but').fadeToggle(120);
    setTimeout(function() {
        $('#change_name_but').fadeToggle(155);
    }, 150);
});

$('#change_surname_but').click(function() {
    $('#surname_input').prop("disabled", false);
    $('#change_name_but').prop("disabled", true);
    $('#change_email_but').prop("disabled", true);
    $('#change_passwd_but').prop("disabled", true);    
    $('#surname_input').select();
    $('#change_surname_but').fadeToggle(120);
    setTimeout(function() {
        $('#change_surname_but_save').fadeToggle(120);
        $('#storno_surname_but').fadeToggle(120);
    }, 150);
});

$('#storno_surname_but').click(function() {
    $('#surname_input').prop("disabled", true);
    $('#change_name_but').prop("disabled", false);
    $('#change_email_but').prop("disabled", false);
    $('#change_passwd_but').prop("disabled", false);   
    $('#change_surname_but_save').fadeToggle(120);
    $('#storno_surname_but').fadeToggle(120);
    setTimeout(function() {
        $('#change_surname_but').fadeToggle(155);
    }, 150);
});


$('#change_email_but').click(function() {
    $('#email_input').prop("disabled", false);
    $('#change_name_but').prop("disabled", true);
    $('#change_surname_but').prop("disabled", true);
    $('#change_passwd_but').prop("disabled", true);  
    $('#email_input').select();
    $('#change_email_but').fadeToggle(120);
    setTimeout(function() {
        $('#change_email_but_save').fadeToggle(120);
        $('#storno_email_but').fadeToggle(120);
    }, 150);
});

$('#storno_email_but').click(function() {
    $('#email_input').prop("disabled", true);
    $('#change_name_but').prop("disabled", false);
    $('#change_surname_but').prop("disabled", false);
    $('#change_passwd_but').prop("disabled", false);
    $('#change_email_but_save').fadeToggle(120);
    $('#storno_email_but').fadeToggle(120);
    setTimeout(function() {
        $('#change_email_but').fadeToggle(155);
    }, 150);
});

$('#change_passwd_but').click(function() {
    $('#change_passwd_but').fadeToggle(120);
    $('#change_name_but').prop("disabled", true);
    $('#change_surname_but').prop("disabled", true);
    $('#change_email_but').prop("disabled", true); 
    $('#change_passwd_div').toggle('slide', {direction: 'up'}, 300);
});

$('#storno_passwd_but').click(function() {
    $('#change_passwd_div').toggle('slide', {direction: 'up'}, 300);
    $('#change_passwd_but').fadeToggle(120);
    $('#change_name_but').prop("disabled", false);
    $('#change_surname_but').prop("disabled", false);
    $('#change_email_but').prop("disabled", false); 
});