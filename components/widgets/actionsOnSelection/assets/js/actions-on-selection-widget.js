//******************************************************************************
//                         actions-on-selection-widget.js
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
var selectionCountAlertDiv;
var checkboxes;
    
function displaySelectedCount (count) {
    $('#' + selectionCountValueId).html(count);
    selectionCountAlertDiv.show();
}

window.onload = function() {
    var checkedCount = $("input[type='checkbox']:checked").length;
    selectionCountAlertDiv = $("#" + selectionCountAlertId);
    checkboxes = $("input[type='checkbox']");
    selectionCountAlertDiv.hide();
    selectionCountAlertDiv.on('close.bs.alert', function() {
        checkboxes.each(function(i) {
            $(this).prop("checked", false);
        });
        $(this).hide();
        return false;
    });
    checkboxes.change(function() {
        checkedCount = $("input[type='checkbox']:checked").length;
        $('#' + selectionCountValueId).html(checkedCount);
        if ($(this).is(':checked')) {
            displaySelectedCount(checkedCount);
        } 
        else {
            if(checkedCount === 0) {
                selectionCountAlertDiv.hide();
            }
        }
    });
    if($("input[type='checkbox']:checked").length > 0) {
        displaySelectedCount(checkedCount);
    }
};


