<?php $form = $this->beginWidget('ActiveForm', array('id' => 'Buy_urged', 'enableClientValidation' => true));?>
<div style='min-width: 400px; min-height: 110px;'>
    <table>
        <tr style='height: 30px; vertical-align:middle;'>
            <td colspan="2"><?php echo $item->product_name;?> 采购催办</td>
        </tr>
        <tr>
            <td style='vertical-align: top; padding: 3px 0 0 20px; width: 70px;'>
                <label style='color: #C0C0C0;'><?php echo $form->label($model, 'content');?></label>
            </td>
            <td>
                <?php echo $form->textArea($model, 'content', array('rows' => 5, 'cols' => 40));?>
                <?php echo $form->error($model, 'content');?>
            </td>
        </tr>
    </table>
    <div style='text-align: right; padding-right: 5px;'>
        <?php echo CHtml::button('确定', array('type' => 'submit', 'class' => 'highlight button'));?>
        <?php echo CHtml::button('取消', array('type' => 'reset', 'class' => 'js-dialog-close button'));?>
    </div>
</div>
<?php $this->endWidget();?>
