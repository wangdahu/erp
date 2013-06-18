<?php $is_update = Yii::app()->controller->action->id == 'update';?>
<div class="main-title-big  radius-top">
	<h3><?php echo $is_update ? "编辑调拨单" : "新添调拨单"?></h3>
</div>

<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'stockallocate_form',
)); 
?>
<div class="clearfix" style='margin-top: 10px;'>
    <div class="cell span10">
        <?php echo $form->labelEx($model, 'no');?>
        <div class="item">
            <div class="main">
                <?php echo $form->textField($model,'no', array('disabled' => $is_update)); ?>
            </div>
            <?php echo $form->error($model,'no'); ?>
        </div>
    </div>
    <div class="cell span10">
        <?php echo $form->label($model, '填单时间');?>
        <div class="item">
            <div class="main">
                <?php echo Yii::app()->format->datetime($model->created ? $model->created : time()); ?>
            </div>
        </div>
    </div>
</div>
    
<div class="clearfix" style='margin-top: 10px;'>
    <div class="cell span30">
        <?php echo $form->label($model, '需要调拨产品');?>
        <div class="item">
            <div class="main">
                <?php $this->renderPartial('_item', array('model' => $model)); ?>
            </div>
        </div>
    </div>
</div>
    
<div class="clearfix" style='margin-top: 10px;'>
    <div class="cell span10">
        <?php echo $form->label($model, '选择转入仓库');?>
        <div class="item">
            <div class="main">
                <?php echo $form->dropDownList($model, 'storehouse_id', array('' => '选择仓库')+CHtml::listData(Storehouse::model()->findAll(), 'id', 'name')); ?>
            </div>
            <?php echo $form->error($model, 'storehouse_id');?>
        </div>
    </div>
</div>
    
<div class="clearfix" style='margin-top: 10px;'>
    <div class="cell span25">
        <?php echo $form->label($model, 'remark');?>
        <div class="item">
            <div class="main">
                <?php echo $form->textArea($model, 'remark', array('cols' => '100', 'rows' => '4')); ?>
            </div>
        </div>
    </div>
</div>
    
<div class="clearfix" style='margin-top: 10px;'>
    <div class="cell span10">
        <?php echo $form->labelEx($model, 'allocate_name');?>
        <div class="item">
            <div class="main">
                <?php echo $form->textField($model,'allocate_name', !ErpPrivilege::stockCheck(ErpPrivilege::STOCK_ADMIN) ? array('readonly'=>true) : array('class' => 'js-complete')); ?>
            </div>
        </div>
    </div>
</div>

<!-- 审批流程start -->
<?php if(!$is_update) $this->renderPartial('../_approve_choose', array('model' => $model, 'form' => $form));?>
<!-- 审批流程start -->
    
<div class="actions">
    <?php echo CHtml::htmlButton('确定', array('class' => 'highlight', 'type'=>'submit')); ?>
    <?php echo CHtml::htmlButton('取消', array('type' => 'reset')); ?>
</div>
    
<?php $this->endWidget(); ?>

