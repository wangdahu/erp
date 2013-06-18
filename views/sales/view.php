<?php $this->renderPartial('../_detailTool', array('model' => $model));?>
<div class="main-title-big  radius-top">
	<h3>销售单</h3>
</div>

<?php $form=$this->beginWidget('ActiveForm', array(
	'id'=>'sales-order-form',
)); ?>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'no');?>
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
            <?php echo $form->label($model, 'corp_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->corp_name; ?>
                </div>
            </div>
        </div>
        <div class="cell span12">
            <?php echo $form->label($model, 'customer_name');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->customer_name; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'address');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->address; ?>
                </div>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->label($model, 'customer_address');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->customer_address; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->linkman; ?>
                </div>
            </div>
        </div>
        <div class="cell span10">
            <?php echo $form->label($model, 'customer_linkman');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->customer_linkman; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'phone');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->phone; ?>
                </div>
            </div>
        </div>
        
        <div class="cell">
            <?php echo $form->label($model, 'customer_phone');?>
            <div class="item">
                <div class="main">
                    <?php echo Yii::app()->format->ntext($model->customer_phone); ?>
                    <?php //echo $model->customer_phone; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="clearfix">
        <div class="cell span10">
            <?php echo $form->label($model, 'delivery_date');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->delivery_date; ?>
                </div>
            </div>
        </div>
        
        <div class="cell span10">
            <?php echo $form->label($model, 'balance_date');?>
            <div class="item">
                <div class="main">
                    <?php echo $model->balance_date; ?>
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
                <?php $this->renderPartial('_items', array('model'=>$model));?>
                
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
                <?php echo $form->label($model, 'salesman');?>
                <div class="item">
                    <div class="main">
                        <?php echo $model->salesman; ?>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- 审批流程start -->
    <?php $this->renderPartial('../_approve', array('model' => $model));?>
    <!-- 审批流程start -->
    
<?php $this->endWidget(); ?>
</div><!-- form -->
