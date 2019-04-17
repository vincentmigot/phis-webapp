<?php
//******************************************************************************
//                               EventPut.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 06 March 2019
// Contact: andreas.garcia@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
namespace app\models\yiiModels;

use Yii;

/**
 * Event PUT model.
 * @author Andréas Garcia <andreas.garcia@inra.fr>
 */
class EventUpdate extends EventAction {
    
    /**
     * @return array the labels of the attributes
     */
    public function attributeLabels() {
        return array_merge(
            parent::attributeLabels(),
            [
                self::URI => Yii::t('app', 'URI')
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributesToArray() {
        return array_merge( 
            parent::attributesToArray(),[
            self::URI => $this->uri
        ]);
    }
}
