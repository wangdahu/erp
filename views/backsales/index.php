<?php $this->renderPartial('../_salesTop');

$form = $this->beginWidget('ActiveForm', array(
    'id'=>'backSalesSearch',
    'method'=>'get',
    'action'=>Yii::app()->createUrl($this->route),
    'htmlOptions' => array('class' => 'main-search-form'),
));
?>

    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?=$form->label($model, 'salesman_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('salesman_dept_id', 'salesman_id'),
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
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入单号/客户名称/产品名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget();?>

<!--<div class="main-panel">-->
<?php
//if (PssPrivilege::stockCheck(PssPrivilege::SALES_BACK)){
//    echo CHtml::link('新添退货', array('/pss/backSales/create'), array('class' => 'button'));
//}
?>
<!--</div>-->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'backsales-grid',
    'dataProvider' => $model->search(),
    'emptyText' => '暂无销售退货信息！',
    'columns'=>array(
        //array('name'=>'id', 'value' => '$data->id', 'headerHtmlOptions' => array('style' => 'width: 45px;')),
        array('name'=>'no', 'type'=>'raw', 'htmlOptions'=>array('style'=>'position:relative;'),
            'value'=>
                    'CHtml::link($data->no, array("view", "id" => $data->id))
                    .CHtml::tag("i", 
                         array(
                             "class"=>"arrow-open",
                             "data-id" => $data->id,
                             "style"=>"position: absolute; top:7px; right:7px;"
                         )
                     )
             '),
        array('name'=>'order_id', 'value'=>'$data->isBindOrder ? $data->salesOrder->no : ""'),
        array('name'=>'customer_name', 'value'=>'$data->customer_name'),
        array('name'=>'total_price', 'value'=>'$data->total_price'),
        array('name'=>'salesman', 'value'=>'$data->salesman'),
        array('name'=>'back_name', 'value'=>'$data->back_name'),
        array('name'=>'created','type'=>'datetime', 'value'=>'$data->created'),
        array('name'=>'approval_id', 'type'=>'raw', 'value'=>'$data->approveStatusText', 'headerHtmlOptions'=>array('class'=>'span2')),
    ),
));
?>

<script>
$(function(){
    $("#backsales-grid .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('items')?>&id="+this.dataset.id);
        },
        function () {
            $(this).closeView();
        }
    );
});
</script>