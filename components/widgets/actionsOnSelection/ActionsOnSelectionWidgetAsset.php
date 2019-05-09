<?php
//******************************************************************************
//                       ActionsOnSelectionWidgetAsset.php
// SILEX-PHIS
// Copyright © INRA 2019
// Creation date: 5 May 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets\actionsOnSelection;

use yii\web\AssetBundle;

/**
 * Asset for the handsontable input widget.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class ActionsOnSelectionWidgetAsset extends AssetBundle {
    
    public $js = [
        'js/actions-on-selection-widget.js'
    ];

    public $css = [
        'css/actions-on-selection-widget.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
    
    public function init()
    {
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}
