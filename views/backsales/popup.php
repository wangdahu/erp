<?php
Yii::app()->clientScript->scriptMap['jquery.js'] = false;
?>
<div style="width: 720px; max-height: 400px;">
    <div class="wide form" id="search-order">
    <?php $form=$this->beginWidget('CActiveForm', array(
    	'action'=>Yii::app()->createUrl($this->route),
    	'method'=>'get',
    )); ?>
        <div class="clearfix">
            <div class="cell">
                <?php echo $form->label($model, 'no');?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textField($model,'keyword', array('class' => 'span6', 'placeholder' => '销售单号/客户名称')); ?>
                    </div>
                </div>
            </div>
            <div class="cell">
                <?php echo $form->label($model, 'created');?>
                <div class="item">
                    <div class="main">
                        <?php echo $form->label($model, 'created');?>
                        <?php $dates = $model->datePatternOptions;
                        $dates[3] .= ' '.$form->textField($model, 'start_date', array('class' => 'js-datepicker', 'data-group' => 'sales_date', 'data-type' => 'start')). ' ';
                        $dates[3] .= $form->textField($model, 'end_date', array('class' => 'js-datepicker', 'data-group' => 'sales_date'));
                        ?>
                        <?php echo $form->radioButtonList($model,'date_pattern', $dates, array('separator'=>'&nbsp;')); ?>
                    </div>
                </div>
                <div class="item" style='margin-left: 10px;'>
                    <div class="main">
                        <?php echo CHtml::htmlButton('搜索', array('type'=>'submit')); ?>
                    </div>
                </div>
            </div>
        </div>
    
    <?php $this->endWidget(); ?>
    </div>
    
    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'order-grid',
        'dataProvider'=>$model->search(),
        'selectableRows'=>1,
        'ajaxUpdate'=>null,
        'emptyText'=>'无客户信息',
        'columns'=>array(
            array('name' => 'no', 'value' => '$data->no'),
            array('name' => '客户', 'value' => '$data->customer_name'),
            array('name' => '应收金额', 'value' => '$data->total_price'),
            array('name' => '已收', 'value' => '0'),
            array('name' => '未收', 'value' => '0'),
            array('name' => 'salesman', 'value' => '$data->salesman'),
            array('name' => 'created', 'type' => 'datetime', 'value' => '$data->created', 'headerHtmlOptions' => array('class' => 'span3')),
        ),
    ));  ?>
</div>
<?php
Yii::app()->clientScript->registerScript('search', "
$(function(){
    $('#search-order form').submit(function(){
        $('#order-grid').yiiGridView('update', {data: $(this).serialize()});
        return false;
    });
    
    $('#order-grid tbody tr').attr('style', 'cursor:pointer;').live('click', function(){
        location.href = '".$this->createUrl('/erp/backsales/create')."&order_id='+$.fn.yiiGridView.getKey('order-grid', this.rowIndex-1);
    });
})");
