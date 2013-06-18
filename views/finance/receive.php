<?php
$this->renderPartial('../_financeTop');

$form = $this->beginWidget('ActiveForm', array(
      'action' => Yii::app()->createUrl($this->route),
      'method' => 'get',
      'id' => 'receiveSearch',
      'htmlOptions' => array('class' => 'main-search-form'),
  ));
?>

    <div class="clearfix">
        <div class="cell span12">
            <div class="main">
                <?php echo $form->label($model, '收款时间');?>
                    <?php 
                    $dates = $model->datePatternOptions;
                    $dates[3] .= ' '.$form->textField($model,'start_date', array('class' => 'js-datepicker', 'data-group' => 'receive_date', 'data-type' => 'start')). ' ';
                    $dates[3] .= $form->textField($model,'end_date', array('class' => 'js-datepicker', 'data-group' => 'receive_date'));
                    echo $form->radioButtonList($model,'date_pattern', $dates, array('separator'=>'&nbsp;')); 
                    ?>
            </div>
        </div>
        
        <div class="cell span11">
            <div class="main">
            <?=$form->label($model, '关键词');?>
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入客户名称'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>

<? $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'receive',
    'dataProvider'=>$model->search(),
    'emptyText'=>'无应收记录',
     'columns'=>array(
        array('name'=>'客户', 'value'=>'$data->name', 'headerHtmlOptions'=>array('class'=>'span2')),
        array('name'=>'salesTotalPrice', 'value'=>'$data->salesTotalPrice', 'headerHtmlOptions'=>array('class'=>'span3')),
        array('name'=>'receivedPrice', 'value'=>'$data->receivedPrice', 'headerHtmlOptions'=>array('class'=>'span3')),
        array('header'=>'未收金额', 'value'=>'$data->notReceivedPrice', 'headerHtmlOptions'=>array('class'=>'span3')),
        array('header'=>'最近收款时间', 'value'=>'$data->receiveItems ? date("Y-m-d H:i", $data->receiveItems[0]->created) : "--"', 'headerHtmlOptions'=>array('class'=>'span4')),
        array('header'=>'操作', 'type'=>'raw', 'value'=>'CHtml::link("结算", array("/erp/finance/chargein", "id"=>"$data->id"), array("class" => "js-dialog-link"))', 
              'headerHtmlOptions'=>array('class'=>'span1')),
    ),
));
