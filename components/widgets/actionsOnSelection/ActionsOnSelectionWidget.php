<?php
//******************************************************************************
//                            ActionsOnSelectionWidget.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 05 March 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\actionsOnSelection;

use yii\base\Widget;
use yii\web\View;

/**
 * A widget used to generate a customizable concerned item GridView interface
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class ActionsOnSelectionWidget extends Widget {

    const SELECTION_COUNT_VALUE_ID = "selection-count-value";
    const SELECTION_COUNT_ALERT_ID = "selection-count-alert";
    const ADD_ROW_BUTTON_ID = "handsontable-add-row-button";
    const REMOVE_ROW_BUTTON_ID = "handsontable-delete-row-button";
    
    public $inputName;
    public $title;
    
    public function run()
    {        

        $htmlRendered = $this->render('_actions-on-selection', [
        ]);
        
        $this->getView()->registerJs(""
            . "var selectionCountValueId = '" . self::SELECTION_COUNT_VALUE_ID . "';"
            . "var selectionCountAlertId = '" . self::SELECTION_COUNT_ALERT_ID . "';"
            . "", View::POS_HEAD);
        
        ActionsOnSelectionWidgetAsset::register($this->getView());

        return $htmlRendered;
    }
}