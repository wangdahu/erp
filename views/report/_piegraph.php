<?php
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/js/highcharts/highcharts.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/js/highcharts/modules/exporting.js', CClientScript::POS_END);
$types = array('user' => '人员', 'customer' => '客户', 'product' => '产品');
$categories = $data = array();
foreach ($dataProvider->getData() as $model){
    $data[] = array(
        'name' => $model->name,
        'y' => floatval($model->salesTotalPrice),
    );
}
$json = CJSON::encode(array(
    'title' => $types[$search->type] . '销售统计表',
    'series' => array(
        array("name" => "销售额", "data" => $data)
    ),
    //'series' => CJSON::decode('[{"name": "销售额", "data":[{"name":"aa","y": 23}, {"name":"bb","y": 13, "x": "ss"}, {"name":"cc","y": 33}, {"name":"dd","y": 43, "sliced": true}]}]'),
));
?>
<div id="container" style="height: 400px;"></div>
<script>
$(function() {
    var chart, json = <?php echo $json ?>;
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
            type: 'pie'
        },
        title: { text: json.title },
        series: json.series
    });
});
</script>
