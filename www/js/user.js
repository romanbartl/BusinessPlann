$('#change_name_but').click(function() {
    $('#name_input').prop("disabled", false);
    $('#name_input').select();
    $('#change_name_but').hide();
    $('#change_name_but_save').show();
});
