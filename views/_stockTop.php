<?php

function isActive() {
    return  in_array(Yii::app()->getController()->route,  func_get_args());
}
$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'bigTab radius-top'), 'items' => array(
    array('label' => '货品仓库', 'url' => array('/erp/stock/index')),
    array('label' => '货品入库', 'url' => array('/erp/stockin/index'),
          'active' => isActive('erp/stockin/index', 'erp/stockin/item', 'erp/stockin/back')),
    array('label' => '货品出库', 'url' => array('/erp/stockout/index'),
          'active' => isActive('erp/stockout/index', 'erp/stockout/item', 'erp/stockout/back')),
    array('label' => '产品调拨', 'url' => array('/erp/stockallocate/index'),
          'active' => isActive('erp/stockallocate/index', 'erp/stockallocate/item')),
    array('label' => '产品目录', 'url' => array('/erp/product/list')),
)));

