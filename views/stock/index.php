<?php
$this->renderPartial('../_stockTop');

$form = $this->beginWidget('ActiveForm', array(
      'action' => Yii::app()->createUrl($this->route),
      'method' => 'get',
      'id' => 'stockSearch',
      'htmlOptions' => array('class' => 'main-search-form'),
  ));
?>

<div class="clearfix">
    <div class="cell span6">
        <div class="main">
        <?=$form->label($model, '选择仓库');?>
        <?=$form->dropDownList($model, 'storehouse_id', array(''=>'请选择')+$model->storehouselist, array('class' => 'medium'));?>
        </div>
    </div>
    <div class="cell span6">
        <div class="main">
            <?=$form->label($model, '产品类别');?>
            <?=$form->dropDownList($model,'cate_id', array(''=>'请选择') + Product::getCateListData(), array('class' => 'medium')); ?>
        </div>
    </div>
    <div class="cell span12">
        <div class="main">
            <?=$form->label($model, 'storeman_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('dept_id', 'storeman_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell span11">
        <div class="main">
        <?=$form->label($model, '关键词');?>
        <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入产品名称/型号/品牌'));?>
        <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
        </div>
    </div>
</div>
<?php $this->endWidget(); ?>

<div class="main-panel">
<?php
if (PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD)){
    echo CHtml::link('新添入库单', array('/pss/stockin/create'), array('class' => 'button'));
    echo CHtml::link('新添出库单', array('/pss/stockout/create'), array('class' => 'button'));
    echo CHtml::link('新添调拨单', array('/pss/stockallocate/create'), array('class' => 'button'));
}
?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'buy-order',
    'dataProvider'=>$model->search(),
    'emptyText'=>'暂无货品仓库信息',
    'columns'=>array(
        //array('name'=>'id', 'value'=>'$data->id', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name'=>'storehouse_id', 'value'=>'$data->storehouse->name'),
        array('name'=>'product.name', 'value'=>'$data->product->name'),
        array('name'=>'型号', 'value'=>'$data->product->no', 'headerHtmlOptions' => array('class' => 'span3')),
        array('name'=>'product.unit_id', 'value'=>'$data->product->unit->name'),
        array('name'=>'product.brand_id', 'value'=>'isset($data->product->brand->name) ? $data->product->brand->name : ""'),
        array('name'=>'产品类别', 'value'=>'$data->product->cate->name'),
        array('name'=>'可用库存', 'value'=>'$data->quantity', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name'=>'待入库数量', 'value'=>'$data->bideInQuantity', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name'=>'待出库数量', 'value'=>'$data->bideOutQuantity', 'headerHtmlOptions' => array('class' => 'span2')),
        array('name'=>'storeman_id', 'value'=>'Account::user($data->storehouse->storeman_id)->name', 'headerHtmlOptions' => array('class' => 'span2')),
    ),
));
?>

