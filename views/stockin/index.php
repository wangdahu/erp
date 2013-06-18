<?php
$this->renderPartial('../_stockTop');
?>

<?$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'simpleTab'), 'items' => array(
        array('label' => '入库单', 'url' => array('/pss/stockin/index')),
        array('label' => '入库产品', 'url' => array('/pss/stockin/item')),
        array('label' => '采购退货', 'url' => array('/pss/stockin/back')),
)));
?>

<?php 
$form = $this->beginWidget('ActiveForm', array(
    'id'=>'stockInSearch',
    'method'=>'get',
    'action' => Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>
    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($model, 'in_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('in_dept_id', 'in_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        <div class="cell span4">
            <div class="main">
            <?=$form->label($model, 'approval_id');?>
            <?=$form->dropDownList($model, 'approval_id', $model->approveSelectValue, array('class' => 'small'));?>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?php echo $form->label($model, '录入时间');?>
                <?php 
                $dates = $model->datePatternOptions;
                $dates[3] .= ' '.$form->textField($model,'start_date', array('class' => 'js-datepicker', 'data-group' => 'created', 'data-type' => 'start')). ' ';
                $dates[3] .= $form->textField($model,'end_date', array('class' => 'js-datepicker', 'data-group' => 'created'));
                echo $form->radioButtonList($model,'date_pattern', $dates, array('separator'=>'&nbsp;')); ?>
            </div>
        </div>
        
        <div class="cell span11">
            <div class="main">
            <?=$form->label($model, '关键词');?>
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入单号/供应商名称/联系人'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget();?>

<div class="main-panel">
<?php
if (PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD)){
    echo CHtml::link('新添入库单', array('/pss/stockin/create'), array('class' => 'button'));
}
?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'stock-in',
    'dataProvider'=>$model->search(),
    'emptyText'=>'暂无入库单信息',
    'columns'=>array(
        //array('name'=>'id', 'value'=>'$data->id', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name'=>'no', 'type'=>'raw', 'htmlOptions'=>array('style'=>'position:relative;'),
            'value'=>'CHtml::link($data->no, array("view", "id" => $data->id))
                .CHtml::tag("i", array(
                "class"=>"arrow-open",
                "data-id" => $data->id,
                "style"=>"position: absolute; top:7px; right:7px;"))'),
        array('name'=>'supplier_name', 'value'=>'$data->supplier_name'),
        array('name'=>'联系人', 'value'=>'$data->supplier_linkman'),
        array('name'=>'supplier_phone', 'type'=>'raw', 'value'=>'Yii::app()->format->ntext($data->supplier_phone)'),
        array('name'=>'created', 'type'=>'datetime', 'value'=>'$data->created'),
        array('name'=>'采购员', 'value'=>'$data->buyer'),
        array('name'=>'入库人', 'value'=>'$data->in_name'),
        array('name'=>'approval_id', 'type'=>'raw', 'value'=>'$data->approveStatusText', 'headerHtmlOptions'=>array('class'=>'span2')),
    ),
));
?>

<script>
$(function(){
    $("#stock-in .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('items')?>&id="+this.dataset.id);
        },
        function () {
            $(this).closeView();
        }
    );
});
</script>
