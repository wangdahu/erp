<?php
$this->renderPartial('../_stockTop');

$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'simpleTab'), 'items' => array(
    array('label' => '调拨单', 'url' => array('/pss/stockallocate/index')),
    array('label' => '调拨产品', 'url' => array('/pss/stockallocate/item')),
)));

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'stockAllocateSearch',
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
        <?=$form->label($model, '转入仓库');?>
        <?=$form->dropDownList($model, 'storehouse_id', array(''=>'请选择')+$model->storehouselist, array('class' => 'medium'));?>
        </div>
    </div>
    
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
</div>
<div class="clearfix">
    <div class="cell span11">
        <div class="main">
        <?=$form->label($model, '关键词');?>
        <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入调拨单号/备注'));?>
        <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
        </div>
    </div>
</div>
<?php $this->endWidget();?>

<div class="main-panel">
<?php
if (PssPrivilege::stockCheck(PssPrivilege::STOCK_ADD)){
    echo CHtml::link('新添调拨单', array('/pss/stockallocate/create'), array('class' => 'button'));
}
?>
</div>

<?php 
$this->widget('zii.widgets.grid.CGridView',array(
    'id' => 'Allocate',
    'dataProvider' => $model->search(),
    'emptyText' => '暂无调拨单信息！',
    'columns'=>array(
        //array('name'=>'id', 'type'=>'raw', 'headerHtmlOptions' => array('style' => 'width: 45px;')),
        array('name'=>'单号', 'type'=>'raw', 'htmlOptions'=>array('style'=>'position:relative;'),
              'value'=>
                  'CHtml::link($data->no, array("view", "id"=>$data->id))
                   .CHtml::tag("i", 
                        array(
                            "class"=>"arrow-open",
                            "data-id" => $data->id,
                            "style"=>"position: absolute; top:7px; right:7px;"
                        )
                    )
        ', 'headerHtmlOptions'=>array('class'=>'span4')),
        array('name'=>'转入仓库', 'value'=>'$data->storehouse->name'),
        array('name'=>'remark', 'value'=>'$data->remark'),
        array('name'=>'allocate_name', 'value'=>'$data->allocate_name'),
        array('name'=>'created', 'value'=>'$data->created', 'type'=>'datetime',),
        array('name'=>'approval_id', 'type'=>'raw', 'value'=>'$data->approveStatusText', 'headerHtmlOptions'=>array('class'=>'span2')),
    ),
));
?>

<script>
$(function(){
    $("#Allocate .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('items')?>&id="+this.dataset.id);
        },
        function () {
            $(this).closeView();
        }
    );
});
</script>