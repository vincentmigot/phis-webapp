//******************************************************************************
//                         actions-on-selection-widget.js
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
var selectionCountAlertDiv;
var checkboxes;
var checkedCount;
var checkedSelector = "input[type='checkbox']:checked";
var tableId = "w0";
    
function displaySelectedCount (count) {
    $('#' + selectionCountValueId).html(count);
    selectionCountAlertDiv.show();
}

window.onload = function() {
    selectionCountAlertDiv = $("#" + selectionCountAlertId);
    checkboxes = $("input[type='checkbox']");
    selectionEventButton = $("#" + selectionEventButtonId);
    
    selectionCountAlertDiv.on('close.bs.alert', function() {
        checkboxes.each(function(i) {
            $(this).prop("checked", false);
        });
        $(this).hide();
        return false;
    });
    
    checkboxes.change(function() {
        checkedCount = $(checkedSelector).length;
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
    
    checkedCount = $(checkedSelector).length;
    if(checkedCount > 0) {
        displaySelectedCount(checkedCount);
    }
    else {
        selectionCountAlertDiv.hide();
    }
    
    var columnNumberWhoseValueIsToSendToActions;
    $("#" + tableId  + " th").each(function(index) {
        if($(this).html() === columnNameToSendToActions) {
            columnNumberWhoseValueIsToSendToActions = index;
        }
    });
    alert("e " + $("#" + tableId  + " th").length);
    alert(columnNumberWhoseValueIsToSendToActions);
    
    selectionEventButton.click(function() {
        var concernedItemsUris;
        alert($('#' + tableId).yiiGridView('getSelectedRows'));
        
//        $(checkedSelector).each(function (index) {
//            concernedItemsUris.push($(this).) = 
//        });
        return false;
    });
};


