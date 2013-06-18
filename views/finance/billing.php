<?php
$this->renderPartial('../_financeTop');

$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'simpleTab'), 'items' => array(
        array('label' => '收入报表', 'url' => array('/pss/finance/billing'), 'active'=> $model->type == 0),
        array('label' => '支出报表', 'url' => array('/pss/finance/billing', 'type'=>1), 'active'=> $model->type == 1),
)));

$form = $this->beginWidget('ActiveForm', array(
      'action' => Yii::app()->createUrl($this->route),
      'method' => 'get',
      'id' => 'billingSearch',
      'htmlOptions' => array('class' => 'main-search-form'),
  ));
?>

    <div class="clearfix">
        <div class="cell span8">
            <div class="main">
                <?=$form->label($model, 'operator_id');?>
                <?php $this->widget('ext.Selector', array('model' => $model,
                                                          'names' => array('dept_id', 'operator_id'),
                                                          'htmlOptions' => array(array('class' => 'medium'), array('class' => 'small'))
                                                      )); ?>
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
            <?=$form->textField($model, 'keyword', array('class' => 'xlarge', 'placeholder' => '请输入单号/客户(供应商)名称'));?>
            <?=$form->hiddenField($model, 'type', array('name' => 'type'));?>
            <?php echo CHtml::htmlButton('查找', array('type'=>'submit')); ?>
            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>

<div class="main-panel">
<?=CHtml::link('添加收支', array('/pss/finance/addbilling', 'type'=>$model->type), array('class' => 'button js-dialog-link'));?>
<?php //echo CHtml::htmlButton('添加收支', array('data-href' => $this->createUrl('/pss/finance/addbilling'),
      //           'class' => 'js-dialog-link', 'type' => $model->type));?>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'billing',
    'dataProvider'=>$model->search(),
    'emptyText'=>'暂无收支信息',
    'itemsCssClass' => 'td-center',
    'showTableOnEmpty' => true,
     'columns'=>array(
        array('name'=>'no', 'htmlOptions'=>array('style'=>'position:relative;'), 
                'value'=>'$data->no.CHtml::tag("i", array(
                "class"=>"arrow-open", 
                "data-id"=>$data->id,
                "style"=>"position: absolute; top:7px; right:7px;"))', 'type'=>'raw'),
        array('header'=>'客户/供应商', 'value'=>'$data->partner_name'),
        array('name'=>'cheque', 'value'=>'$data->cheque'),
        array('name'=>$model->type == '0' ? '收入金额' : '支出金额', 'value'=>'$data->totalPrice'),
        array('header'=>'结算方式', 'value'=>'$data->balanceTypeOptions[$data->balance_type]'),
        array('name'=>'operator', 'value'=>'$data->operator'),
        array('name'=>'created', 'value'=>'date("Y-m-d H:i", $data->created)'),
    ),
));

?>

<script>
$(function(){
    $("#billing .arrow-open").toggle(
        function() {
            $(this).openView("<?=$this->createUrl('items')?>&id="+this.dataset.id);
        },
        function () {
            $(this).closeView();
        }
    );
});
</script>