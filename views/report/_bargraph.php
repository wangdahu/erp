<?php
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/js/highcharts/highcharts.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/js/highcharts/modules/exporting.js', CClientScript::POS_END);

$categories = $data1 = $data2 = array();
foreach ($dataProvider->getData() as $model){
    $categories[] = $model->name;
    if ($search->type == 'product'){
        $data1[] = floatval($model->salesTotalPrice);
        $data2[] = 0.00;
    }else{
        $data1[] = floatval($model->notReceivedPrice);
        $data2[] = floatval($model->receivedPrice);
    }
}
$types = array('user' => '人员', 'customer' => '客户', 'product' => '产品');
$json = CJSON::encode(array(
    'title' => $types[$search->type] . '销售统计表',
    'categories' => $categories,
    'series' => array(
        array('name' => '未收金额', 'data' => $data1),
        array('name' => '已收金额', 'data' => $data2),
    ), 
        //CJSON::decode('[{ "name": "未收金额", "data": [49.9, 71.5, 106.4] },{ "name": "已收金额", "data": [48.9, 38.8, 39.3] }]'),
));
?>
<div id="container" style="height: 400px;"></div>
<script>
$(function() {
    var chart, json = <?=$json?>;
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
            type: 'column'
        },
        title: { text: json.title },
        xAxis: {
            categories: json.categories
        },
        yAxis: {
            min: 0, title: { text: '' }
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + '：' + this.y + '<br/>' + '应收金额：' + this.point.stackTotal;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },
        series: json.series
    });
});
</script>
