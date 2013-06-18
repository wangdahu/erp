<?php $this->renderPartial('../_detailTool', array('model' => $model));?>
<div class="main-title-big  radius-top">
	<h3>采购单：<?=$model->no?></h3>
</div>

<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'sales-order-form',
)); ?>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'no');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->no?>
                </div>
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
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'corp_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->corp_name; ?>
                </div>
            </div>
        </div>
        <div class="cell span12">
            <?php echo $form->labelEx($model, 'supplier_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->supplier_name; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'address');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->address; ?>
                </div>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'supplier_address');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->supplier_address; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->linkman; ?>
                </div>
            </div>
        </div>
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'supplier_linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->supplier_linkman; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'phone');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->phone; ?>
                </div>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'supplier_phone');?>
            <div class="item">
                <div class="main">
                    <?php echo Yii::app()->format->ntext($model->supplier_phone); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'delivery_date');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->delivery_date; ?>
                </div>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->labelEx($model, 'balance_date');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->balance_date; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'remark');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->remark; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell">
            <?php echo $form->labelEx($model, 'items');?>
            <div class="item">
                <?php $this->renderPartial('_items', array('model'=>$model));?>
                
            </div>
        </div>
        
        <div class="clearfix">
            <div class="cell">
                <?php echo $form->labelEx($model, 'total_price');?>
                <div class="item span8">
                    <div class="main">
                        <?php echo Yii::app()->format->number($model->total_price); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="clearfix">
            <div class="cell">
                <?php echo $form->labelEx($model, 'buyer');?>
                <div class="item">
                    <div class="main">
                        <?php echo $model->buyer; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 审批流程start -->
        <?php $this->renderPartial('../_approve', array('model' => $model));?>
        <!-- 审批流程start -->
        
<?php $this->endWidget(); ?>
</div><!-- form -->
