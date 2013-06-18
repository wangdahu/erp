<?php
$this->renderPartial('../_stockTop');

$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'simpleTab'), 'items' => array(
    array('label' => '调拨单', 'url' => array('/erp/stockallocate/index')),
    array('label' => '调拨产品', 'url' => array('/erp/stockallocate/item')),
)));

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'stockAllocateItemSearch',
    'method'=>'get',
    'action' => Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>

<div class="clearfix">
    <div class="cell span8">
        <div class="main">
                <?=$form->label($model, 'allocate_man_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('allocate_dept_id', 'allocate_man_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
        </div>
    </div>
    
    <div class="cell span6">
        <div class="main">
        <?=$form->label($model, '产品类别');?>
        <?=$form->dropDownList($model, 'cate_id', array(''=>'请选择')+Product::getCateListData(), array('class' => 'medium'));?>
        </div>
    </div>
    
    <div class="cell span6">
        <div class="main">
        <?=$form->label($model, '转出仓库');?>
        <?=$form->dropDownList($model, 'from_storehouse_id', array(''=>'请选择')+$model->storehouselist, array('class' => 'medium'));?>
        </div>
    </div>
    
    <div class="cell span6">
        <div class="main">
        <?=$form->label($model, '转入仓库');?>
        <?=$form->dropDownList($model, 'to_storehouse_id', array(''=>'请选择')+$model->storehouselist, array('class' => 'medium'));?>
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
        <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入产品名称/型号/品牌'));?>
        <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
        </div>
    </div>
</div>
<?php $this->endWidget();?>

<div class="main-panel">
<?php
if (ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADD)){
    echo CHtml::link('新添调拨单', array('/erp/stockallocate/create'), array('class' => 'button'));
}
?>
</div>

<?php 
$this->widget('zii.widgets.grid.CGridView',array(
    'id' => 'Allocate',
    'dataProvider' => $model->search(),
    'emptyText' => '暂无调拨单信息！',
    'columns'=>array(
        array('name'=>'产品名称', 'value'=>'empty($data->product_name) ? "" : $data->stock->product->name'),
        array('name'=>'产品型号', 'value'=>'$data->stock->product->no'),
        array('name'=>'品牌名称', 'value'=>'$data->stock->product->brand->name'),
        array('name'=>'产品类别', 'value'=>'$data->stock->product->cate->name'),
        array('name'=>'转出仓库', 'value'=>'Storehouse::model()->findByPk($data->from_storehouse_id)->name'),
        array('name'=>'转入仓库', 'value'=>'Storehouse::model()->findByPk($data->to_storehouse_id)->name'),
        array('name'=>'数量', 'value'=>'$data->quantity'),
        array('name'=>'单号', 'value'=>'$data->allocate->no'),
        array('name'=>'调拨人', 'value'=>'$data->allocate->allocate_name'),
        array('name'=>'填单时间', 'type'=>'datetime', 'value'=>'$data->allocate->created'),
        array('name'=>'审批状态', 'type'=>'raw', 'value'=>'$data->allocate->approveStatusText'),
    ),
));
?>
