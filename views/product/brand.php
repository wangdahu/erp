<div class="form">
<?php
$form = $this->beginWidget('ActiveForm', array('id' => 'product-brand-form'));

if (isset($_GET['popup']) && $_GET['popup'] == 1){
    $form->clientOptions['afterValidate'] = "js:function(form, data, hasError) {
        $.post(form.attr('action'), form.serialize(), function(json){
            if(json.status){
                voluationBrand(json.data);
                //关闭弹出层
                $('#product-brand-form .js-dialog-close').click();
            }
        }, 'json');
        return false;
    }";
}
?>


    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model,'name'); ?>
            <div class="item span6">
                <div class="main">
               <?php echo $form->textField($model,'name'); ?>
                </div>
                <?php echo $form->error($model,'name'); ?>
            </div>
        </div>
    </div>

    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model,'remark'); ?>
            <div class="item span6">
                <div class="main">
    		    <?php echo $form->textArea($model,'remark'); ?>
    		    </div>
    		    <?php echo $form->error($model,'remark'); ?>
    	    </div>
	    </div>
	</div>


    <div class="actions">
		<?php echo CHtml::htmlButton('确定', array('type' => 'submit', 'class' => 'highlight')); ?>
		<?php echo CHtml::htmlButton('取消', array('class' => 'js-dialog-close')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
<!-- form -->