
<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'cate_id'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->dropDownList($model, 'cate_id', array('' => '选择分类') + $model->cateListData, array('class'=>'small')); ?>
           <?php 
           if (PssPrivilege::otherCheck(PssPrivilege::SETTING)){
               echo CHtml::link('添加分类', array('cate', 'popup'=>1), array('class'=>'js-dialog-link'));
           }
           ?>
           <span class="hint">产品的所属类别</span>
            </div>
            <?php echo $form->error($model,'cate_id'); ?>
            
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'unit_id'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->dropDownList($model, 'unit_id', array('' => '选择单位') + $model->unitListData, array('class'=>'small')); ?>
           <?php
           if(PssPrivilege::otherCheck(PssPrivilege::SETTING)){ 
               echo CHtml::link('添加单位', array('unit', 'popup'=>1), array('class'=>'js-dialog-link'));
           }?>
           <span class="hint">产品度量单位，如“台、套、KG、CM”等</span>
            </div>
            <?php echo $form->error($model,'unit_id'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'brand_id'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->dropDownList($model, 'brand_id', array('' => '选择品牌') + $model->brandListData, array('class'=>'small')); ?>
           <?php
           if(PssPrivilege::otherCheck(PssPrivilege::SETTING)){ 
               echo CHtml::link('添加品牌', array('brand', 'popup'=>1), array('class'=>'js-dialog-link'));
           }?>
           <span class="hint">产品的品牌名称</span>
            </div>
            <?php echo $form->error($model,'brand_id'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'name'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model,'name'); ?>
           <span class="hint">产品名称，如“IBM V3000笔记本”</span>
            </div>
            <?php echo $form->error($model,'name'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'no'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model,'no'); ?>
           <span class="hint">产品型号，如“Presario V3000”</span>
            </div>
            <?php echo $form->error($model,'no'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'code'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model,'code'); ?>
           <span class="hint">产品的批准文号，如“ABC02-001”</span>
            </div>
            <?php echo $form->error($model,'code'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'jan_code'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model,'jan_code'); ?>
           <span class="hint">产品的条形码，如没有请留空</span>
            </div>
            <?php echo $form->error($model,'jan_code'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'producting_place'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model,'producting_place'); ?>
           <span class="hint">产品的产地，如没有请留空</span>
            </div>
            <?php echo $form->error($model,'producting_place'); ?>
        </div>
    </div>
</div>

<div class="clearfix">
    <div class="cell">
        <?php echo $form->labelEx($model,'remark'); ?>
        <div class="item span12">
            <div class="main">
           <?php echo $form->textField($model,'remark'); ?>
           <span class="hint">产品的说明备注</span>
            </div>
            <?php echo $form->error($model,'remark'); ?>
        </div>
    </div>
</div>
