<?php 
$this->renderPartial('../_buyTop');

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'backBuySearch',
    'method'=>'get',
    'action' => Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>
<span style="color:darksalmon">提示：系统将根据产品的销售量及库存量智能生成当前您需要采购的产品（生成条件：  产品当前可用库存低于预销售数量或库存报警数目）</span> 
    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($model, 'buyer_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('dept_id', 'buyer_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        
        <div class="cell span11">
            <div class="main">
            <?=$form->label($model, '关键词');?>
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入产品名称/型号/品牌'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget();?>

<div class="main-panel">
<?php
if (PssPrivilege::buyCheck(PssPrivilege::BUY_ORDER_CREATE)){
    echo CHtml::htmlButton('生成采购单', array('id' => 'btn_buy', 'class' => 'button', 'disabled' => 'disabled', 
            'onclick'=>'location.href="'.$this->createUrl('/pss/buy/create').'&"+$("#buy-plan :checked").serialize()'));
}
?>

</div>
<?php  $gridView = $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'buy-plan',
    'dataProvider'=>$model->search(),
    'emptyText'=>'无销采购计划信息',
    'selectableRows' => 2,
    'selectionChanged' => 'function(id){
        $("#btn_buy").prop("disabled", !$("#" + id + " :checked").length);
    }',
    'columns'=>array(
        array('class'=>'CCheckBoxColumn', 'checkBoxHtmlOptions'=>array('name'=>'id[]')),
        //array('name' => 'id', 'headerHtmlOptions' => array('class' => 'span2')), 
        'name', 'no', 
        array('name'=>'brand_id', 'value'=>'$data->brand->name', 'headerHtmlOptions' => array('class' => 'span3')),
        array('name'=>'cate_id', 'value'=>'$data->cate->name', 'headerHtmlOptions' => array('class' => 'span3')),
        array('name'=>'salesQuantity', 'type'=>'raw', 'htmlOptions'=>array('style'=>'position:relative;'), 'headerHtmlOptions' => array('class' => 'span3'),
                'value'=>'$data->preSalesQuantity.CHtml::tag("i", array(
                "class"=>"arrow-open", 
                "style"=>"position: absolute; top:7px; right:7px;",
            ))'
        ),
        array('header' => '当前可用库存', 'value' => '$data->totalStock','headerHtmlOptions' => array('class' => 'span3')),
        array('header' => '库存报警', 'value' => '$data->safe_quantity','headerHtmlOptions' => array('class' => 'span2')),
        array('name' => 'buyer_id', 'value' => '"<span title={$data->buyerText}>$data->buyerText</span>"', 'htmlOptions' => array('class'=>'ellipsis'), 'type'=> 'raw'),
        //采购计划列表中新增“采购中数量”，显示进行采购单中尚未入库的采购产品数量 暂不做
        //array('header' => '采购中数量', 'value' => '$data->buyQuantity'),
        array('header' => '采购催办记录', 'type' => 'raw', 'headerHtmlOptions' => array('class' => 'span3'),
        	'value' => '$data->urgedCount == 0 ? "已催办".$data->urgedCount."次" : CHtml::link("已催办".$data->urgedCount."次", array("/pss/buy/buyUrged", "product_id" => $data->id), array("class" => "js-dialog-link", "data-title" => "催办查看"))'),
    ), 
));
?>

<script>
$(function(){
    $("#buy-plan .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('salesorders')?>&product_id="+$(this).parents("tr").first().find(":checkbox").val());
        },
        function () {
        	$(this).closeView();
        }
    );
});
</script>
