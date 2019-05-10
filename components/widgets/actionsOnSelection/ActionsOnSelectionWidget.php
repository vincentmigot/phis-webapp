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

    const SELECTION_COUNT_ALERT_ID = "selection-count-alert";
    const SELECTION_COUNT_VALUE_ID = "selection-count-value";
    const SELECTION_EVENT_BUTTON_ID = "selection-event-button";
    
    public $columnNameToSendToActions;
    const COLUMN_NAME_TO_SEND_TO_ACTIONS = "columnNameToSendToActions";
    
    public function run()
    {        

        $htmlRendered = $this->render('_actions-on-selection', [
            "selectionCountAlertId" => self::SELECTION_COUNT_ALERT_ID,
            "selectionCountValueId" => self::SELECTION_COUNT_VALUE_ID,
            "selectionEventButtonId" => self::SELECTION_EVENT_BUTTON_ID,
        ]);
        
        $this->getView()->registerJs(""
            . "var selectionCountValueId = '" . self::SELECTION_COUNT_VALUE_ID . "';"
            . "var selectionCountAlertId = '" . self::SELECTION_COUNT_ALERT_ID . "';"
            . "var selectionEventButtonId = '" . self::SELECTION_EVENT_BUTTON_ID . "';"
            . "var columnNameToSendToActions = '{$this->columnNameToSendToActions}';"
            . "", View::POS_HEAD);
        
        ActionsOnSelectionWidgetAsset::register($this->getView());

        return $htmlRendered;
    }
}