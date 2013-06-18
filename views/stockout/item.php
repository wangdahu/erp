<?php
$this->renderPartial('../_stockTop');

$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'simpleTab'),
                                         // 'activeParents' => true,
                                         'items' => array(
        array('label' => '出库单', 'url' => array('/erp/stockout/index')),
        array('label' => '出库产品', 'url' => array('/erp/stockout/item')),
        array('label' => '销售退货', 'url' => array('/erp/stockout/back')),
)));

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'stockOutItemSearch',
    'method'=>'get',
    'action' => Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>

    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($model, 'out_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('out_dept_id', 'out_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
            </div>
        </div>
        <div class="cell span4">
            <div class="main">
            <?=$form->label($model, 'approval_id');?>
            <?=$form->dropDownList($model, 'approval_id', BillForm::getStaticApproveSelectValue(), array('class' => 'small'));?>
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
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入出库单号/客户名称/产品名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget();?>

<div class="main-panel">
<?php
if (ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD)){
    echo CHtml::link('新添出库单', array('/erp/stockout/create'), array('class' => 'button'));
}
?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'product-grid',
    'dataProvider' => $model->search(),
    'emptyText' => '暂无录入产品数据！',
    'selectableRows' => 2,
    'columns'=>array(
        //array('name'=>'id', 'value' => '$data->id', 'headerHtmlOptions' => array('style' => 'width: 45px;')),
        array('name'=>'产品信息', 'type'=>'raw', 'value'=>'"名称：".$data->product_name."<br>型号：".$data->product_cate."<br>品牌：".$data->product_brand', 'htmlOptions' => array('style' => 'text-align: left; padding-left: 5px;')),
        array('name'=>'form.no', 'value'=>'$data->form->no'),
        array('header'=>'关联销售单号', 'value'=>'$data->form->isBindOrder ? $data->form->salesOrder->no : ""'),
        array('name'=>'form.customer_name', 'value'=>'$data->form->customer_name'),
        array('name'=>'product_unit', 'value'=>'$data->product_unit'),
        array('name'=>'quantity', 'value'=>'$data->quantity'),
        array('name'=>'price', 'value'=>'$data->price'),
        array('header'=>'销售额', 'value'=>'$data->totalPrice'),
        array('name'=>'出库人', 'value'=>'$data->form->out_name'),
        array('name'=>'审批状态', 'type'=>'raw', 'value'=>'$data->form->approveStatusText'),
    ),
));

