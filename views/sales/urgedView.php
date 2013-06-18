<?php 
$urged = $data;
?>
<div style='width: 450px; height: 400px; overflow: auto;'>
   <div style='border-bottom-style: solid; border-bottom-color: #C0C0C0; margin-bottom: 20px; padding: 5px;'>
       <label style='margin-right: 7px;'><?php echo Account::user($urged->user_id)->name?></label>
       <label><?=Yii::app()->format->datetime($urged->created);?>&nbsp;&nbsp;&nbsp;采购催办</label><br>
       <label style='margin-right: 15px; color: #C0C0C0; padding-left: 20px;'>催办产品</label>
       <label><?php echo SalesOrderItem::model()->findByPk($_GET['item_id'])->product_name;?></label><br>
       <label style='margin-right: 15px; color: #C0C0C0; padding-left: 20px;'>催办事由</label>
       <label><?=Yii::app()->format->ntext($urged['content'])?></label><br>
   </div>
   
      <!-- 回复内容start -->
       <div style="margin-bottom: 5px; margin-left: 20px; margin-right: 20px;">
           <?php $urged_reply = BuyUrgedReply::model()->findAll("urged_id = $urged->id");
               if(!empty($urged_reply)){
                   foreach ($urged_reply as $urged_row){
                       echo Account::user($urged_row->user_id)->name."&nbsp;&nbsp;".Yii::app()->format->datetime($urged_row->created)."&nbsp;&nbsp;回复<br>".
                       "<div style='margin-bottom: 5px;'>回复内容：" . Yii::app()->format->ntext($urged_row->content) ."</div><hr style='95%;'><br>";
                   }
               }else{
                   echo "<label'>无</label>";
               }
           ?>
       </div>
       <!-- 回复内容end -->

<?php
$form = $this->beginWidget('ActiveForm', array('id' => 'BuyUrgedReply', 'enableClientValidation' => true));
$model = new BuyUrgedReply;
?>
    <table>
        <tr>
            <td style='vertical-align: top; padding: 3px 10px 0 0; width: 70px; text-align: right;'>
                <label style='color: #C0C0C0;'><?php echo $form->label($model, 'content');?></label>
            </td>
            <td>
                <?php echo $form->textArea($model, 'content', array('rows' => 5, 'cols' => 40));?>
                <!-- 隐藏域 urged_id -->
                <?php echo $form->hiddenField($model, 'urged_id', array('value' => $urged->id));?>
                <?php echo $form->error($model, 'content');?>
            </td>
        </tr>
    </table>
    <div style='text-align: right; padding-right: 5px;'>
        <?php echo CHtml::button('确定', array('type' => 'submit', 'class' => 'highlight button'));?>
        <?php echo CHtml::button('取消', array('type' => 'reset', 'class' => 'js-dialog-close button'));?>
    </div>

    <div style='text-align: right; margin: 20px 10px 0 0;'>
    <?php $this->endWidget();?>
    </div>
</div>