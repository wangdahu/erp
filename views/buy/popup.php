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
            </div>
            <div class="cell">
                <?php echo $form->label($model, '关键词');?>
                <div class="item" style='margin-right: 10px;'>
                    <div class="main">
                        <?php echo $form->textField($model,'no', array('class' => 'span6', 'placeholder' => '采购单号/供应商名称')); ?>
                    </div>
                </div>
                <?php echo CHtml::htmlButton('搜索', array('type'=>'submit')); ?>
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
            'no',
            'supplier_name',
            'total_price',
            'buyer',
            array('name'=>'created', 'type'=>'datetime'),
        ),
    ));  ?>
</div>
<?php
Yii::app()->clientScript->registerScript('search', <<<SCRIPT
$(function(){
 	$('#search-order form').submit(function(){
		$('#order-grid').yiiGridView('update', {data: $(this).serialize()});
		return false;
	});

    $('#order-grid tbody tr').attr('style', 'cursor:pointer;').live('click',function(){
        var href = location.href, id = $.fn.yiiGridView.getKey('order-grid', this.rowIndex-1);
        location.href = /\border_id=\d+/.test(href) ? href.replace(/(order_id=)\d+/, '$1' + id) : href +'&order_id=' + id ;
    });
})
SCRIPT
);