<div class="form">
<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'storehouse-form',
	'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
)); ?>
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'name');?>
            <div class="item span8">
                <div class="main">
                    <?php echo $form->textField($model,'name',array('class'=>'span5','maxlength'=>50)); ?>
                </div>
                <?php echo $form->error($model,'name'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'storeman_id');?>
            <div class="item span6">
                <div class="main">
                    <?=$form->searchField($model,'storeman', 
                            array('placeholder' => "请输入人员姓名或拼音", 'class' => 'span5', 'value' => !empty($model->storeman) ? $model->storeman->name : ""));?> 
                    <?=$form->hiddenField($model, 'storeman_id');?>
                </div>
                <?php echo $form->error($model,'storeman_id');?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span12">
            <?php echo $form->labelEx($model, 'description');?>
            <div class="item span8">
                <div class="main">
                    <?php echo $form->textArea($model,'description',array('maxlength'=>255, 'class' => 'xlarge', 'rows'=>6)); ?>
                </div>
                <?php echo $form->error($model,'description'); ?>
            </div>
        </div>
    </div>

	<div class="actions">
		<?php echo CHtml::htmlButton('确定', array('class' => 'highlight', 'type' => 'submit')); ?>
		<?php echo CHtml::htmlButton('取消', array('class' => 'js-dialog-close')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script>
$(function(){
    $("#Storehouse_storeman").complete();
});
</script>
