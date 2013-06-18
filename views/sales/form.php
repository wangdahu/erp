<?php $is_update = Yii::app()->controller->action->id == "update";?>
<div class="main-title-big  radius-top">
	<h3><?php echo $is_update ? "编辑销售单" : "新增销售单"?></h3>
</div>

<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'sales-order-form',
)); 
?>
    
    <div class="clearfix">
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
            <?php echo $form->label($model, '填单日期');?>
            <div class="item">
                <div class="main">
                    <?php echo Yii::app()->format->datetime($model->created ? $model->created : time()); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'corp_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'corp_name', array('readonly'=>true)); ?>
                </div>
                <?php echo $form->error($model,'corp_name'); ?>
            </div>
        </div>
        <div class="cell span13">
            <?php echo $form->labelEx($model, 'customer_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'customer_name', array('readonly'=>true)); ?>
                    <?php echo $form->hiddenField($model,'customer_id'); ?>
                </div>&nbsp;
                <?php echo CHtml::link('选择已有客户', array('/erp/customer/popup'), array('class' => 'js-dialog-link button', 'data-id'=>'select-customer'));?>
                &nbsp;
                <?php 
                if (ErpPrivilege::customerCheck(ErpPrivilege::CUSTOMER_CREATE)){
                    echo CHtml::link('添加客户', array('/erp/customer/create'), array('class' => 'js-dialog-link button'));
                }
                ?>
                <?php echo $form->error($model,'customer_name'); ?>&nbsp;&nbsp;
                
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'address');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'address'); ?>
                </div>
                <?php echo $form->error($model,'address'); ?>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'customer_address');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'customer_address'); ?>
                </div>
                <?php echo $form->error($model,'customer_address'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'linkman'); ?>
                </div>
                <?php echo $form->error($model,'linkman'); ?>
            </div>
        </div>
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'customer_linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'customer_linkman'); ?>
                </div>
                <?php echo $form->error($model,'customer_linkman'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'phone');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'phone'); ?>
                </div>
                <?php echo $form->error($model,'phone'); ?>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'customer_phone');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textArea($model,'customer_phone'); ?>
                </div>
                <?php echo $form->error($model,'customer_phone'); ?>
            </div>
        </div>
    </div>

    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'delivery_date');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'delivery_date', array('class' => 'js-datepicker')); ?>
                </div>
                <?php echo $form->error($model,'delivery_date'); ?>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'balance_date');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'balance_date', array('class' => 'js-datepicker')); ?>
                </div>
                <?php echo $form->error($model,'balance_date'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'remark');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textArea($model,'remark', array('class' => 'xxlarge', 'rows'=>6)); ?>
                </div>
                <?php echo $form->error($model,'remark'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'items');?>
            <div class="item">
                <?php echo $form->error($model,'items'); ?>
                <?php $this->renderPartial('_items', array('model'=>$model));?>
            </div>
        </div>


    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'total_price');?>
            <div class="item span8">
                <div class="main">
                    <?php echo $form->textField($model,'total_price', array('readonly'=>'readonly')); ?>
                </div>
                <?php echo $form->error($model,'total_price'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'salesman');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'salesman', ErpPrivilege::salesCheck(ErpPrivilege::SALES_ADMIN) ? array('class' => 'js-complete') : array('readonly' => true)); ?>
                    <?php echo $form->hiddenField($model,'salesman_id'); ?>
                </div>
                <?php echo $form->error($model,'salesman'); ?>
            </div>
        </div>
    </div>

    <!-- 审批流程start -->
    <?php if($this->action->id == 'create'){
        $this->renderPartial('../_approve_choose', array('model' => $model, 'form' => $form));
    }
    ?>
    <!-- 审批流程start -->

	<div class="actions">
		<?php echo CHtml::htmlButton('确定', array('class' => 'highlight', 'type'=>'submit')); ?>
		<?php echo CHtml::htmlButton('取消', array('onclick' => 'history.back()')); ?>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
function populateCustomerData(data){
    $("#<?=Chtml::activeId($model, 'customer_id')?>").val(data.id);
    $("#<?=Chtml::activeId($model, 'customer_name')?>").val(data.name);
    $("#<?=Chtml::activeId($model, 'customer_linkman')?>").val(data.linkman);
    $("#<?=Chtml::activeId($model, 'customer_address')?>").val(data.address);
    $("#<?=Chtml::activeId($model, 'customer_phone')?>").val(data.phone);
}
</script>
