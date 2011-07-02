$(document).ready(function() {

    $("#input-directory").change(function() {
        $("#input-name").val($(this).val()+"_Filter");
    });

});