<?php
//******************************************************************************
//                         EventButtonWidget.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 05 March, 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\icons\Icon;

/**
 * A widget used to generate an event button.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventButtonWidget extends Widget {

    const ADD_EVENT_LABEL = 'Add event';
    const CONCERNED_ITEMS_NOT_SET_LABEL = 'The concerned items are not set';
    const CONCERNED_ITEM_LIST_NOT_A_ARRAY = 'The concerned items list is not an array';
    const CONCERNED_ITEM_LIST_EMPTY = 'The concerned items list is empty';
        
    /**
     * Defines if button is displayed as a button (false) or as a link (true)
     * @var boolean
     */    
    public $asLink = false;
    const AS_LINK = "asLink";
           
    /**
     * Defines the items which will be annotated.
     * @var array
     */
    public $concernedItemsUris;
    const CONCERNED_ITEMS_URIS = "concernedItemsUris";

    /**
     * Renders the event button.
     * @return string the string rendered
     */
    public function run() {
        //SILEX:conception
        // Maybe create a widget bar and put buttons in it to use the same style
        //\SILEX:conception
        $uriArray = [
                'event/create',
                'concernedItemsUris' => $this->concernedItemsUris,
                'returnUrl' => Url::current()
            ];
        
        $linkClasses = [];
        if (!$this->asLink) {
            $linkLabel = Icon::show('flag', [], Icon::FA) . " " . Yii::t('app', self::ADD_EVENT_LABEL);
            $linkAttributes = ['class' => 'btn btn-default'];
        } else {
            $linkLabel = '<span class="fa fa-flag"></span>';
        }
        $linkAttributes["title"] = Yii::t('app', self::ADD_EVENT_LABEL);
        $linkAttributes["id"] = $this->id;
                
        return Html::a(
                    $linkLabel,
                    $uriArray, 
                    $linkAttributes
                );
    }

}
