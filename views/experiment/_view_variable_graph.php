<?php

//******************************************************************************
//                                       _view_sensor_graph.php
// SILEX-PHIS
// Copyright © INRA 2018
// Creation date: 13th November 2018
// Contact: vincent.migot@inra.fr, anne.tireau@inra.fr, pascal.neveu@inra.fr
//******************************************************************************

use miloschuman\highcharts\Highcharts;

$id = str_replace("=", "", base64_encode($graphName));
?>
<script>
    for (var i in Highcharts.charts) {
        Highcharts.charts[i].destroy();
    }
    var isDisabled = false;
    function disableSeries(element) {
        console.log($(element), element, isDisabled);
        if (isDisabled) {
            $(element).html("Disable all series");
        } else {
            $(element).html("Enable all series");
        }
        isDisabled = !isDisabled;
        for (var i in Highcharts.charts) {
            var chart = Highcharts.charts[i];
            if (chart != undefined) {
                $(chart.series).each(function(){
                    this.setVisible(!isDisabled, false);
                });
                chart.redraw();
                break;
            }
        }
    };
</script>
<?php

// Create data serie for HighChart
/**
 * @example
 * $serie = [
 *      "name" => "TRAIT_METHOD_UNIT",
 *      "data" => [
 *          [1497516660000, 1.25],
 *          [1497516720000, 1.33]
 *      ]
 * ]
 */
    // Display Hightchart widget
    echo Highcharts::widget([
        // Create a unique ID for each graph based on variable URI
        'id' => $id,
        'options' => [
            'title' => $graphName,
            'chart' => [
                "zoomType" => "x",
                "type" => "spline",
                "width" => 800
            ],
            'xAxis' => [
                'type' => 'datetime',
                'title' => 'Date',
            ],
            'yAxis' => [
                'title' => "Value",
                'labels' => [
                    'format' => '{value:.2f}'
                ]
            ],
            'tooltip' => [
               'xDateFormat' => '%Y-%m-%d %H:%M'
             ],
            'series' => array_values($series),
        ]
    ]);
    
    echo '<button onclick="disableSeries(this)">Disable all series</button>'
?>
