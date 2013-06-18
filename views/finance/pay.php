<?php
$this->renderPartial('../_financeTop');

$form = $this->beginWidget('ActiveForm', array(
      'action' => Yii::app()->createUrl($this->route),
      'method' => 'get',
      'id' => 'paySearch',
      'htmlOptions' => array('class' => 'main-search-form'),
  ));
?>

    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?php echo $form->label($model, '付款时间');?>
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
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入供应商名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>
    
<? $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'receive',
    'dataProvider'=>$model->search(),
    'emptyText'=>'无应付记录',
     'columns'=>array(
        array('name'=>'供应商', 'value'=>'$data->name', 'headerHtmlOptions'=>array('class'=>'span2')),
        array('name'=>'buyTotalPrice', 'value'=>'$data->buyTotalPrice', 'headerHtmlOptions'=>array('class'=>'span3')),
        array('name'=>'paidPrice', 'value'=>'$data->paidPrice', 'headerHtmlOptions'=>array('class'=>'span3')),
        array('header'=>'未付金额', 'value'=>'$data->notPaidPrice', 'headerHtmlOptions'=>array('class'=>'span3')),
        array('header'=>'最近付款时间', 'value'=>'$data->lastPayTime ? date("Y-m-d H:i", $data->lastPayTime) : "--"', 'headerHtmlOptions'=>array('class'=>'span4')),
        array('header'=>'操作', 'type'=>'raw', 'value'=>'CHtml::link("结算", array("/pss/finance/chargeout", "id"=>"$data->id"), array("class" => "js-dialog-link"))', 'headerHtmlOptions'=>array('class'=>'span1')),
    ),
));?>
