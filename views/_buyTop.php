<?php if (ErpPrivilege::buyCheck(ErpPrivilege::BUY_ADMIN)):?>
<div style='position: relative; top: -32px; right: 10px; float: right'>
    <?=CHtml::link('采购产品分配', array('/erp/assign/index'), array('class' => 'button js-dialog-link'));?>
</div>
<?php endif;?>

<?php
$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'bigTab radius-top'), 'items' => array(
    array('label' => '采购计划', 'url' => array('/erp/buy/plan')),
    array('label' => '进行中采购单', 'url' => array('/erp/buy/index')),
    array('label' => '历史采购单', 'url' => array('/erp/buy/history')),
    array('label' => '采购退货', 'url' => array('/erp/backbuy/index')),
)));?>


