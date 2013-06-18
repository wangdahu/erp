<?php $this->renderPartial('../_detailTool', array('model' => $model));?>
<div class="main-title-big  radius-top">
	<h3>出库单</h3>
</div>

<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'sales-order-form',
    'action'=>Yii::app()->createUrl($this->route, array('order_id' => $model->sales_order_id)),
)); 
    ?>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'no');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->no ?>
                </div>
                <?php echo $form->error($model,'no'); ?>
            </div>
        </div>
        <div class="cell span10">
            <?php echo $form->label($model, '填单日期');?>
            <div class="item">
                <div class="main">
                    <?php echo Yii::app()->format->datetime($model->created); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->label($model, 'sales_order_id');?>
            <div class="item">
                <div class="main">
                    <?php
                    if ($model->isBindOrder){ 
                        echo $model->salesOrder->no."&nbsp;&nbsp;";
                    }else{
                        echo '无';
                    }
                    ?>
                </div>
                <?php echo $form->error($model,'sales_order_id'); ?>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'corp_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->corp_name?>
                </div>
            </div>
        </div>
        <div class="cell span14">
            <?php echo $form->label($model, 'customer_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->customer_name?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'address');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->address ?>
                </div>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->label($model, 'customer_address');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->customer_address ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->linkman?>
                </div>
            </div>
        </div>
        <div class="cell span10">
            <?php echo $form->label($model, 'customer_linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->customer_linkman ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'phone');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->phone ?>
                </div>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->label($model, 'customer_phone');?>
            <div class="item">
                <div class="main">
                    <?php echo Yii::app()->format->ntext($model->customer_phone);?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->label($model, 'remark');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->remark; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->label($model, 'items');?>
            <div class="item">
                <?php $this->renderPartial('_item', array('form' => $form, 'items' => $items, 'model'=> $model));?>
            </div>
        </div>
        
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->label($model, 'total_price');?>
            <div class="item span8">
                <div class="main">
                    <?php echo Yii::app()->format->number($model->total_price); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->label($model, 'out_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->out_name; ?>
                </div>
            </div>
        </div>
    </div>
    
<!-- 审批流程start -->
<?php $this->renderPartial('../_approve', array('model' => $model));?>
<!-- 审批流程start -->

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>
function populatecustomerData(data){
    $("#<?=Chtml::activeId($model, 'customer_id')?>").val(data.id);
    $("#<?=Chtml::activeId($model, 'customer_name')?>").val(data.name);
    $("#<?=Chtml::activeId($model, 'customer_linkman')?>").val(data.linkman);
    $("#<?=Chtml::activeId($model, 'customer_address')?>").val(data.address);
    $("#<?=Chtml::activeId($model, 'customer_phone')?>").val(data.phone);
}
</script>