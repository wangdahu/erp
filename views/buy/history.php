<?php 

$this->renderPartial('../_buyTop');


$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'buy-order',
    'dataProvider'=>$model->search(),
    'emptyText'=>'无历史采购单信息',
    'columns'=>array(
        array('name'=>'id', 'value'=>'$data->id', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name'=>'no', 'type'=>'raw', 'htmlOptions'=>array('style'=>'position:relative;'),
            'value'=>'CHtml::link($data->no, array("view", "id" => $data->id))
            .CHtml::tag("i", array(
            "class"=>"arrow-open",
            "style"=>"position: absolute; top:7px; right:7px;"))', 'headerHtmlOptions' => array('class' => 'span3')),
        'supplier_name', 'total_price',
        //array('name' => '已付金额', 'value' => 'sprintf("%.2f", $data->total_price-$data->paidPrice)'),
        //array('name' => '已付金额', 'value' => 'sprintf("%.2f", $data->total_price-$data->paidPrice)'),
        'buyer',
        'created',
        'status',
        array('name' => '操作', 'type'=>'raw', 'value' => 'CHtml::linkButton("", array(
            "class"=>"statement",
            "title"=>"结单",
            "submit"=>"",
            "params"=>array("command"=>"statement", "id"=>"$data->id"),
            "confirm"=>"确定要结单“ $data->no ”？"))', 'headerHtmlOptions' => array('class' => 'span2')),
    ),
));
?>
<script>
$(function(){
    $("#buy-order .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('items')?>&id="+$(this).parents("tr").children().first().text());
        },
        function () {
            $(this).closeView();
        }
    );
});
</script>
