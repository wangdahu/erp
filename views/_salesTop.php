<?php
$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'bigTab radius-top'), 'items' => array(
    array('label' => '进行中销售单', 'url' => array('/erp/sales/index')),
    array('label' => '历史销售单', 'url' => array('/erp/sales/history')),
    array('label' => '销售退货', 'url' => array('/erp/backsales/index')),
)));

