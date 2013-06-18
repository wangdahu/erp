<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'buy_price'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'buy_price'); ?>
           <span class="hint">设定产品价格可参考采购价</span>
            </div>
            <?php echo $form->error($model,'buy_price'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'cost'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'cost'); ?>
           <span class="hint">设定产品价格可参考成本价</span>
            </div>
            <?php echo $form->error($model,'cost'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'sales_price'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'sales_price'); ?>
           <span class="hint">设定产品市场销售价</span>
            </div>
            <?php echo $form->error($model, 'sales_price'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'discount_price'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'discount_price'); ?>
           <span class="hint">设定产品活动折扣价</span>
            </div>
            <?php echo $form->error($model,'discount_price'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'wholesales_price'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'wholesales_price'); ?>
           <span class="hint">设定产品批发价</span>
            </div>
            <?php echo $form->error($model,'wholesales_price'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'low_price'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'low_price'); ?>
            </div>
            <?php echo $form->error($model,'low_price'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'safe_quantity'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'safe_quantity'); ?>
           <span class="hint">设定最低库存提醒警报</span>
            </div>
            <?php echo $form->error($model,'safe_quantity'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'min_quantity'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model, 'min_quantity'); ?>
           <span class="hint">设定该产品最少订货量</span>
            </div>
            <?php echo $form->error($model,'min_quantity'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model->detail,'size'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model->detail, 'size'); ?>
           <span class="hint">如100X100X50数量，只换算不为0的值</span>
            </div>
            <?php echo $form->error($model->detail, 'size'); ?>
        </div>
    </div>
</div>


