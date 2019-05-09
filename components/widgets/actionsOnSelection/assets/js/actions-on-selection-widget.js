//******************************************************************************
//                         actions-on-selection-widget.js
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

window.onload = function() {
    var selectionCountAlertDiv = $("#selection-count-alert");
    var checkboxes = $("input[type='checkbox']");
    selectionCountAlertDiv.hide();
    selectionCountAlertDiv.on('close.bs.alert', function() {
        checkboxes.each(function(i) {
            $(this).prop("checked", false);
        });
        $(this).hide();
        return false;
    });
    checkboxes.change(function() {
        var checkedCount = $("input[type='checkbox']:checked").length;
        $('#selection-count-value').html(checkedCount);
        if ($(this).is(':checked')) {
            selectionCountAlertDiv.show();
        } 
        else {
            if(checkedCount === 0) {
                selectionCountAlertDiv.hide();
            }
        }
    });
};


