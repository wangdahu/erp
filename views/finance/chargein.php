<div class="form" style='height: 400px; width: 750px; overflow: auto;'>
<?php 
$form = $this->beginWidget('ActiveForm', array(
	'id'=>'charge-form',
)); 
?>
    <div class="clearfix">
        <div class="cell span6">
            <?php echo $form->label($customer, 'name');?>
            <div class="item">
                <div class="main">
                    <?php echo $customer->name; ?>
                </div>
            </div>
        </div>
        <div class="cell span12">
            <?php echo CHtml::label('记账员', 'operator');?>
            <div class="item">
                <div class="main">
                    <?php echo CHtml::textField('operator', Yii::app()->user->name, array('readonly' => true)); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->label($customer, 'salesTotalPrice');?>
            <div class="item">
                <div class="main">
                    <?php echo $customer->salesTotalPrice; ?>
                </div>
            </div>
        </div>
    </div>
<?php
$content = $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'record',
        'dataProvider'=>new CArrayDataProvider($customer->receiveItems, array('pagination' => array('pageSize' => count($customer->receiveItems)))),
        'ajaxUpdate'=>null,
        'enablePagination'=>false,
        'emptyText'=>'暂无收款信息',
        'template'=>'{items}',
        'columns'=>array(
                array('header' => '收款销售单', 'value' => '$data->salesOrder->no', 'headerHtmlOptions'=>array('class'=>'span4'), ),
                array('header' => '实收金额', 'value' => '$data->price', 'headerHtmlOptions'=>array('class'=>'span3'), ),
                array('header' => '记账人', 'value' => '$data->operator', 'headerHtmlOptions'=>array('class'=>'span2'), ),
                array('header' => '记账时间', 'type' => 'datetime', 'value' => '$data->created', 'headerHtmlOptions'=>array('class'=>'span3'), ),
                array('header' => '备注', 'value' => '$data->remark'),
        ),
), true);

$this->widget('system.web.widgets.CTabView', array(
    'id' => 'charge',
    'tabs' => array(
        'tab1' => array('title' => '收款', 'view' => 'tabViews/inform'),
        'tab2' => array('title' => '收款记录', 'content' => $content),
    ),
    'viewData' => array('models' => $models, 'form' => $form, 'customer' => $customer),
));

$this->endWidget();
?>
</div>
