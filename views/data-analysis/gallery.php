<?php

//******************************************************************************
//                                 gallery.php
// PHIS-SILEX
// Copyright © INRA 2018
// Creation date: 21 feb. 2019
// Contact: arnaud.charleroy@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use yii\helpers\Html;
use app\models\yiiModels\DataAnalysisAppSearch;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\yiiModels\DataAnalysisAppSearch */
/* @var $dataProvider array */

$this->title = Yii::t('app', 
        '{n, plural, =1{Statistical/Visualization Application} other{Statistical/Visualization Applications}}',
        ['n' => 2]
        );
$this->params['breadcrumbs'][] = $this->title;

//var_dump($dataProvider);exit;
echo Html::beginTag("div", ["class" => "data-analysis-index"]);
echo Html::beginTag("div", ["class" => "row"]);
// HtmlPurifier::process($post->text)
//echo $this->renderFile($galleryFilePath . 'spatial/mapField/mapField.html');
// each thumbnail (R application vignette)
foreach ($dataProvider as $category => $categoryInfo) {
    echo Html::tag("h2", Yii::t('app',$categoryInfo["label"]));
    foreach ($categoryInfo['items'] as $categoryItem => $categoryItemInfo) {

        echo Html::beginTag("div", ["class" => "col-sm-5 col-md-4"]);
        echo Html::beginTag("div", ["class" => "thumbnail"]);
        $image = Html::img('RGallery/'. $category . '/'. $categoryItemInfo['vignette'], [
                    "class" => "img-responsive",
                    "alt" => $categoryItem
        ]);
        $exampleUrl = Url::to(
                        [
                            'data-analysis/view-gallery-item',
                            'htmlDescriptionFilePath' => $galleryFilePath . '/'. $category . '/'. $categoryItemInfo['htmlDescriptionFilePath'],
                            'RfunctionPath' => $galleryFilePath . '/'. $category . '/'. $categoryItemInfo['RfunctionPath']
                        ]
        );
        echo Html::a($image, $exampleUrl);
        echo Html::beginTag("center");
        echo Html::tag("strong", Yii::t('app/messages', $categoryItem));
        echo Html::endTag("center");
        echo Html::endTag("div");
        echo Html::endTag("div");
    }
}

echo Html::endTag("div");
echo Html::endTag("div");