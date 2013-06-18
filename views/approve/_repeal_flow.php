<?php
    $title = '撤销事务申请';
    $priority = array('一般', '重要', '加急', '加急重要');
    $selfId = Yii::app()->user->id;
?>
<style>
    .clearfix textarea {border: 1px solid #EFEFEF;}
    h4 {border:none;}
    form .actions {margin-left:98px;}
</style>
    <?php $form=$this->beginWidget('ActiveForm', array('id'=>'task-repeal-form', 'enableClientValidation'=>true)); ?>
    
    <div >
        <div class="clearfix">
            <div class="cell">
                <label for="DepartmentForm_desc">
                    <h4><?php echo (4 == $status) ? "撤销事由" : "作废事由" ?><span class="red">*</span></h4>
                </label>
                <div class="item">
                    <div class="main">
                        <?php echo $form->textArea($task, "comment", array("class" => "xlarge", "rows" => 6)); ?>
                        <?php echo $form->error($task, 'comment');?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if(Privilege::check('approve', 'default', 'sms_remind')): ?>
        <div class="clearfix">
            <div class="cell">
                <label>&nbsp;</label>
                <div class="item span8">
                    <div class="main">
                        <label >
                            <?php echo $form->checkBox($task, "sms_notice", array()); ?><?php echo $form->label($task, "sms_notice"); ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php echo $form->hiddenField($task, "status", array("value" => $status)); ?>
        
        <div class="actions">
            <button type="submit" class="button highlight">确定</button>
            <button type="button" class="js-dialog-close">取消</button>
        </div>
        
    </div>
    <?php $this->endWidget();?>
