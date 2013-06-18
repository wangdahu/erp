<?php $is_view = Yii::app()->controller->action->id == "view"?>
<div class="main-title-big  radius-top">
	<h3><?php echo Yii::app()->controller->action->id == "update" ? "编辑出库单" : "新增出库单"?></h3>
</div>

<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'buy-order-form',
    //'action'=>Yii::app()->createUrl($this->route, array('order_id' => $model->sales_order_id)),
)); 
    ?>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'no');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'no', array('disabled' => Yii::app()->controller->action->id == "update")); ?>
                </div>
                <?php echo $form->error($model,'no'); ?>
            </div>
        </div>
        <div class="cell span10">
            <?php echo $form->label($model, '填单日期');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->created ? Yii::app()->format->datetime($model->created) : Yii::app()->format->datetime(time()); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'buy_order_id');?>
            <div class="item">
                <div class="main">
                    <?php
                    if ($model->isBindOrder){ 
                        echo $relatedOrder->no."&nbsp;&nbsp;";
                        echo $form->hiddenField($model,'sales_order_id');
                        if(Yii::app()->controller->action->id == "create") echo CHtml::link("删除", array('create'))."&nbsp;&nbsp;";
                    }
                    if(Yii::app()->controller->action->id == "create"){
                        echo CHtml::link("选择已有销售单", array('/pss/sales/popup'), array('class'=>'js-dialog-link', 'data-title' => '导入销售单',));
                    }
                    ?>
                </div>
                <?php echo $form->error($model,'sales_order_id'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'corp_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'corp_name', array('readonly' => true)); ?>
                </div>
                <?php echo $form->error($model,'corp_name'); ?>
            </div>
        </div>
        <div class="cell span14">
            <?php echo $form->labelEx($model, 'customer_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'customer_name', array('readonly' => true)); ?>
                    <?php echo $form->hiddenField($model,'customer_id'); ?>
                </div>&nbsp;
                <?php echo CHtml::htmlButton('选择已有客户', array('class' => 'js-dialog-link button', 'data-id'=>'select-customer',
                        'disabled' => $model->isBindOrder || $is_view, 'data-href' => $this->createUrl('/pss/customer/popup')));?>
                &nbsp;
                <?php 
                if (PssPrivilege::customerCheck(PssPrivilege::CUSTOMER_CREATE)){
                    echo CHtml::htmlButton('添加客户', array('class' => 'js-dialog-link button', 
                        'disabled' => $model->isBindOrder || $is_view, 'data-href' => $this->createUrl('/pss/customer/create')));
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
                <?php //echo "<pre>";print_r($form);?>
                <?php $this->renderPartial('_item', array('form' => $form, 'items' => $items, 'model'=> $model));?>
                
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
            <?php echo $form->labelEx($model, 'out_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'out_name', PssPrivilege::stockCheck(PssPrivilege::STOCK_ADMIN) ? array('class' => 'js-complete') : array('readonly' => true)); ?>
                    <?php echo $form->hiddenField($model,'out_id'); ?>
                </div>
                <?php echo $form->error($model,'out_name'); ?>
            </div>
        </div>
    </div>
    
    <!-- 审批流程start -->
    <?php if(Yii::app()->controller->action->id == "create"){
        $this->renderPartial('../_approve_choose', array('model' => $model, 'form' => $form));
    }?>
    <!-- 审批流程start -->
    
	<div class="actions">
		<?php echo CHtml::htmlButton('确定', array('class' => 'highlight', 'type'=>'submit')); ?>
		<?php echo CHtml::htmlButton('取消'); ?>
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