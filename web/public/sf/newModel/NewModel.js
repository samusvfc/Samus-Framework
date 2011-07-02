$(document).ready(function() {
    addLine(1);
});


function addLine(lineNum , element) {
    $.post('ajax/newModelLine/'+lineNum, {}, function(data) {

        var num = new Number(lineNum);
        num = num + 1;

        $("#model-linhas").append(data);
        $(element).remove();
        $("#name-"+num).focus();

        $("#line-btn-"+lineNum).html('<a href=\'#removeLine\' onclick="if(confirm(\'Remove this line?\')) { removeLine('+lineNum+') }; return false;" class=\'line-removeline\'>[-]</a>');
    });
}
function removeLine(lineNum) {
    $("#linha-"+lineNum).remove();
}