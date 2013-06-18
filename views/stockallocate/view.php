<?php $this->renderPartial('../_detailTool', array('model' => $model));?>
<div class="main-title-big  radius-top">
	<h3>调拨单</h3>
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
                <?php echo $model->no; ?>
            </div>
        </div>
    </div>
    <div class="cell span10">
        <?php echo $form->label($model, '填单时间');?>
        <div class="item">
            <div class="main">
                <?php echo Yii::app()->format->datetime($model->created); ?>
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
                <?php echo Storehouse::model()->findByPk($model->storehouse_id)->name; ?>
            </div>
        </div>
    </div>
</div>
    
<div class="clearfix" style='margin-top: 10px;'>
    <div class="cell span25">
        <?php echo $form->label($model, 'remark');?>
        <div class="item">
            <div class="main">
                <?php echo $model->remark?>
            </div>
        </div>
    </div>
</div>
    
<div class="clearfix" style='margin-top: 10px;'>
    <div class="cell span10">
        <?php echo $form->labelEx($model, 'allocate_name');?>
        <div class="item">
            <div class="main">
                <?php echo $model->allocate_name ?>
            </div>
        </div>
    </div>
</div>

<!-- 审批流程start -->
<?php $this->renderPartial('../_approve', array('model' => $model));?>
<!-- 审批流程start -->
<?php $this->endWidget(); ?>
