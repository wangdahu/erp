<?php
$this->renderPartial('../_stockTop');

$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'simpleTab'), 'items' => array(
        array('label' => '入库单', 'url' => array('/pss/stockin/index')),
        array('label' => '入库产品', 'url' => array('/pss/stockin/item')),
        array('label' => '采购退货', 'url' => array('/pss/stockin/back')),
)));

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'stockInItemSearch',
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
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入入库单号/供应商名称/产品名称'));?>
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
        'id' => 'product-grid',
        'dataProvider' => $model->search(),
        'emptyText' => '暂无入库产品数据！',
        'columns'=>array(
            //array('name'=>'id', 'value' => '$data->id', 'headerHtmlOptions' => array('class' => 'span2')),
            array('name'=>'产品信息', 'type'=>'raw', 'value'=>'"名称：".$data->product_name."<br>型号：".$data->product_cate."<br>品牌：".$data->product_brand', 'htmlOptions' => array('class' => 'td-left'), 'headerHtmlOptions' => array('style' => 'width: 205px;')),
            array('name'=>'form.no', 'value'=>'$data->form->no'),
            array('name'=>'关联采购单号', 'value'=>'$data->form->isBindOrder ? $data->form->buyOrder->no : ""'),
            array('name'=>'供应商', 'value'=>'$data->form->supplier_name'),
            array('name'=>'product_unit', 'value'=>'$data->product_unit', 'headerHtmlOptions' => array('class' => 'span2')),
            array('name'=>'quantity', 'value'=>'$data->quantity', 'headerHtmlOptions' => array('class' => 'span2')),
            array('name'=>'入库价', 'value'=>'$data->price', 'headerHtmlOptions' => array('class' => 'span2')),
            array('header'=>'入库额', 'value'=>'$data->totalPrice'),
            array('name'=>'仓库', 'value'=>'$data->storehouse->name'),
            array('name'=>'form.in_name', 'value'=>'$data->form->in_name'),
            array('name'=>'入库时间', 'type'=>'datetime', 'value'=>'$data->storehouse->created', 'headerHtmlOptions' => array('class' => 'span3')),
            array('name'=>'form.approval_id', 'type'=>'raw', 'value'=>'$data->form->approveStatusText', 'headerHtmlOptions' => array('class' => 'span3')),
        ),
));

