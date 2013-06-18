<div id="charge-item" class="grid-view">
<table class="items">
    <thead>
    <tr>
        <th class="span5">付款采购单</th>
        <th class="span3">未付金额</th>
        <th class="span3">实付金额</th>
        <th>备注</th>
    </tr>
    </thead>
    <tbody>
    <?php $listCount = count($models); ?>
    <?php if($listCount){
        foreach ($models as $i => $model):?>
    <tr class="<?= ($i+1)%2 ? "odd" : "even"?>">
        <td><?=$supplier->buyOrders[$i]->no?></td>
        <td><?=$supplier->buyOrders[$i]->notPaidPrice?></td>
        <td>
            <?=$form->textField($model, "[{$i}]price", array('class' => 'small'))?>
        </td>
        <td>
            <?=$form->textField($model, "[{$i}]remark", array('class' => 'span6'))?>
            <?=$form->error($model, "[{$i}]remark")?>
        </td>
    </tr>
        <?php endforeach;
    }else{?>
        <tr class="even">
            <td colspan="4">暂无付款记录</td>
        </tr>
    <?}?>
    </tbody>
</table>
</div>

<?php if($listCount){?>
<div class="actions" style="margin: 5px 5px 0 0; float: right;">
	<?php echo CHtml::htmlButton('确定', array('class' => 'highlight', 'type'=>'submit')); ?>
	<?php echo CHtml::htmlButton('取消', array('class' => 'js-dialog-close')); ?>
</div>
<?php }?>