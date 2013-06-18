<?php
$this->renderPartial('../_stockTop');

$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'simpleTab'), 'items' => array(
        array('label' => '入库单', 'url' => array('/pss/stockin/index')),
        array('label' => '入库产品', 'url' => array('/pss/stockin/item')),
        array('label' => '采购退货', 'url' => array('/pss/stockin/back')),
)));

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'stockBackSearch',
    'method'=>'get',
    'action' => Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>

    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
            <?=$form->label($model, 'buyer_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('buyer_dept_id', 'buyer_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        
        <div class="cell span12">
            <div class="main">
                <?=$form->label($model, 'back_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('back_dept_id', 'back_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
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
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入单号/供应商名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget();?>

<div class="main-panel">
<?php
if (PssPrivilege::buyCheck(PssPrivilege::BUY_BACK)){
    echo CHtml::link('新添退货', array('/pss/backbuy/create'), array('class' => 'button'));
}
?>
</div>
<?php
$gridView = $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'buy-order',
    'dataProvider'=>$model->search(),
    'emptyText'=>'无销售单信息',
    'columns'=>array(
        //array('name'=>'id', 'value'=>'$data->id'),
        array('name'=>'no', 'type'=>'raw', 'value'=>'CHtml::link($data->no, array("/pss/backbuy/view", "id" => $data->id))'),
        array('name'=>'关联采购单号', 'value'=>'$data->buyOrder->no ? $data->buyOrder->no : "无"'),
        array('name'=>'supplier_name', 'value'=>'$data->supplier_name'),
        array('name'=>'total_price', 'value'=>'$data->total_price'),
        array('name'=>'采购员', 'value'=>'$data->buyer'),
        array('name'=>'back_name', 'value'=>'$data->back_name'),
        array('name'=>'created', 'value'=>'Yii::app()->format->datetime($data->created)'),
        array('name'=>'approval_id', 'type'=>'raw', 'value'=>'$data->approveStatusText'),
    ),
));
?>