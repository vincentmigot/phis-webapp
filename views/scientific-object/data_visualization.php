<?php

//******************************************************************************
//                                       data_visualization.php
// PHIS-SILEX
// Copyright © INRA 2019
// Creation date: 24 mai 2019
// Contact: morgane.vidal@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************
use Yii;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\helpers\Url;

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{n, plural, =1{Scientific Object} other{Scientific Objects}}', ['n' => 2]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


/**
 * @var model app\models\yiiModels\YiiScientificObjectModel
 * @var variables array
 * @var data array
 * @var this yii\web\View
 */

?>

<div class="scientific-object-data-visualization">
    <div class="data-visualization-form well">
        <?php $form = ActiveForm::begin(); 
        if (empty($variables)) {
            echo "<p>" . Yii::t('app/messages', 'No variables linked to the experiment of the scientific object.') . "</p>";
        } else {
            ?>
            <div class="row">
            <?php
            $selectedVariable = null;
            if (isset($data)) {
                $selectedVariable = $data["variable"];
            }
            echo \kartik\select2\Select2::widget([
                'name' => 'variable',
                'data' => $variables,
                'value' => $selectedVariable,
                'options' => [
                    'placeholder' => Yii::t('app/messages','Select a variable ...'),
                    'multiple' => false
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
            </div>
        <br/>
        
            <div class="row">
                <div class="col-md-6">
                    <?=
                    \kartik\date\DatePicker::widget([
                        'name' => 'dateStart',
                        'options' => ['placeholder' => Yii::t('app','Enter date start')],
                        'value' => isset($dateStart) ? $dateStart : null,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ])
                    ?>
                </div>
                <div class="col-md-6">
                    <?=
                    \kartik\date\DatePicker::widget([
                        'name' => 'dateEnd',
                        'value' => isset($dateEnd) ? $dateEnd : null,
                        'options' => ['placeholder' => Yii::t('app','Enter date end')],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ])
                    ?>
                </div>
            </div>
            <br/>
            <div class="form-group row">
                <?= Html::submitButton(Yii::t('app', 'Show data'), ['class' => 'btn btn-primary']) ?>
            </div>
<?php   }
        if (isset($data)) {
            if (empty($data)) {
                echo "<p>" . Yii::t('app/messages', 'No result found.') . "</p>";
            } else {                
                $series = [];
                $series[] = ['name' => $data["scientificObjectData"][0]["label"],
                             'data' => $data["scientificObjectData"][0]["data"]];
                
            
            $url2 = Url::to(['image/search-from-scientific-object']);
            $objectURI = $model->uri;
            echo Highcharts::widget([
                'id' => 'test',
                    'options' => [
                        'title' => ['text' => $variables[$data["variable"]]],
                        'xAxis' => [
                           'type' => 'datetime',
                           'title' => 'Date',
//                        ],
//                        'chart' => [
//                            'zoomType' => 'x',
//                            'events'=> [
//                                'click' =>  new JsExpression("function(event){  alert('TEST appel ajax'+\"$url2\");}")
//                                
//                            ]
                        ],
                        'yAxis' => [
                           'title' => null,
                            'labels' => [
                                 'format' => '{value:.2f}'
                            ]
                        ],
                        'series' => $series,
                        'tooltip' => [
                               'xDateFormat'=> '%Y-%m-%d %H:%M',
                           ] ,
                        'plotOptions' => [
                            'series' => [
                                'cursor' =>'pointer',
                                'point' => [
                                    'events' => [
                                        'click' => new JsExpression(" function() {"
                                               // . "alert ('la serie X en YYYY-MM-DDTHH:MM:SSZ : '+Highcharts.dateFormat('%a %d %b %H:%M:%S', this.x));"
                                                . "var searchFormData = new FormData();"
                                                . "console.log('URI :'+\"$objectURI\");"
                                                . "console.log('url :'+\"$url2\");"
                                                . "searchFormData.append('concernedItems[]', \"$objectURI\");"
                                                . "searchFormData.append('DataFileSearch[rdfType]','http://www.opensilex.org/vocabulary/oeso#HemisphericalImage');"
                                                . "searchFormData.append('startDate',Highcharts.dateFormat('%Y-%m-%dT00:00:00+0200', this.x));"
                                                . "searchFormData.append('endDate',Highcharts.dateFormat('%Y-%m-%dT23:59:00+0200', this.x));"
                                                . "$.ajax({url: \"$url2\","
                                                . "   type: 'POST',"
                                                . "   processData: false,"
                                                . "   datatype: 'json',"
                                                . "   contentType: false,"
                                                . "   data: searchFormData   "
                                                . "}).done(function (data) { $('#visualization-images').html(data);});"
                                                . "}")
                                             
                                    ]
                                ]
                            ]
                        ]
                    ]
                 ]);
            }
        }
    ?>
    
    <?php ActiveForm::end(); ?>
        <div id="visualization-images">
            test
        </div>
    </div>
</div>
<script> //Highcharts stuff
   
</script>
    
    