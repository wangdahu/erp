<?php $is_update = Yii::app()->controller->action->id == "update";
$is_view = Yii::app()->controller->action->id == "view";?>
<div class="main-title-big  radius-top">
	<h3><?php echo $is_update ? "编辑采购退货" : "新增采购退货"?></h3>
</div>

<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'back-buy-form',
    //'action'=>Yii::app()->createUrl($this->route, array('order_id' => $model->order_id)),
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
                    <?php echo $model->created ? Yii::app()->format->datetime($model->created) : Yii::app()->format->datetime(time()); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'order_id');?>
            <div class="item">
                <div class="main">
                    <?php
                    if ($model->isBindOrder){ 
                        echo $relatedOrder->no."&nbsp;&nbsp;";
                        echo $form->hiddenField($model,'order_id');
                        if(!$is_update) echo CHtml::link("删除", array('create'))."&nbsp;&nbsp;";
                    }
                    if(!$is_update){
                        echo CHtml::link("选择已有采购单", array('/pss/buy/popup'), array('class'=>'js-dialog-link'));
                    }
                    ?>
                </div>
                <?php echo $form->error($model,'order_id'); ?>
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
            <?php echo $form->labelEx($model, 'supplier_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'supplier_name', array('readonly' => true)); ?>
                    <?php echo $form->hiddenField($model,'supplier_id'); ?>
                </div>&nbsp;
                <?php echo CHtml::htmlButton('选择已有供应商', array('class' => 'js-dialog-link button', 'data-id'=>'select-supplier',
                        'disabled' => $model->isBindOrder, 'data-href' => $this->createUrl('/pss/supplier/popup')));?>
                &nbsp;
                <?php echo CHtml::htmlButton('添加供应商', array('class' => 'js-dialog-link button', 
                        'disabled' => $model->isBindOrder, 'data-href' => $this->createUrl('/pss/supplier/create')));?>
                <?php echo $form->error($model,'supplier_name'); ?>&nbsp;&nbsp;
                
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
            <?php echo $form->labelEx($model, 'supplier_address');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'supplier_address'); ?>
                </div>
                <?php echo $form->error($model,'supplier_address'); ?>
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
            <?php echo $form->labelEx($model, 'supplier_linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'supplier_linkman'); ?>
                </div>
                <?php echo $form->error($model,'supplier_linkman'); ?>
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
            <?php echo $form->labelEx($model, 'supplier_phone');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textArea($model,'supplier_phone'); ?>
                </div>
                <?php echo $form->error($model,'supplier_phone'); ?>
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
                <?php $this->renderPartial('_items', array('form' => $form, 'model'=> $model));?>
                
            </div>
        </div>
    </div>

    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'total_price');?>
            <div class="item span8">
                <div class="main">
                    <?php echo $form->textField($model,'total_price', array('readonly'=>true)); ?>
                </div>
                <?php echo $form->error($model,'total_price'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'buyer');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'buyer', $model->isBindOrder ? array('readonly'=>true) : array('class' => 'js-complete')); ?>
                    <?php echo $form->hiddenField($model,'buyer_id'); ?>
                </div>
                <?php echo $form->error($model,'buyer_id'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'back_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $form->textField($model,'back_name', !PssPrivilege::stockCheck(PssPrivilege::STOCK_ADMIN) ? array('readonly'=>true) : array('class' => 'js-complete')); ?>
                    <?php echo $form->hiddenField($model,'back_id'); ?>
                </div>
                <?php echo $form->error($model,'back_id'); ?>
            </div>
        </div>
    </div>
    
    <!-- 审批流程start -->
    <?php if(Yii::app()->controller->action->id == 'create'){
            $this->renderPartial('../_approve_choose', array('model' => $model, 'form' => $form));
        }?>
    <!-- 审批流程start -->
    
	<div class="actions">
		<?php echo CHtml::htmlButton('确定', array('class' => 'highlight', 'type'=>'submit')); ?>
		<?php echo CHtml::htmlButton('取消'); ?>
	</div>

<?php $this->endWidget(); ?>

<script>
function populateSupplierData(data){
    $("#<?=Chtml::activeId($model, 'supplier_id')?>").val(data.id);
    $("#<?=Chtml::activeId($model, 'supplier_name')?>").val(data.name);
    $("#<?=Chtml::activeId($model, 'supplier_linkman')?>").val(data.linkman);
    $("#<?=Chtml::activeId($model, 'supplier_address')?>").val(data.address);
    $("#<?=Chtml::activeId($model, 'supplier_phone')?>").val(data.phone);
}
</script>