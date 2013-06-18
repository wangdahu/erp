<?php

function isActive() {
    return  in_array(Yii::app()->getController()->route,  func_get_args());
}
$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'bigTab radius-top'), 'items' => array(
    array('label' => '货品仓库', 'url' => array('/pss/stock/index')),
    array('label' => '货品入库', 'url' => array('/pss/stockin/index'),
          'active' => isActive('pss/stockin/index', 'pss/stockin/item', 'pss/stockin/back')),
    array('label' => '货品出库', 'url' => array('/pss/stockout/index'),
          'active' => isActive('pss/stockout/index', 'pss/stockout/item', 'pss/stockout/back')),
    array('label' => '产品调拨', 'url' => array('/pss/stockallocate/index'),
          'active' => isActive('pss/stockallocate/index', 'pss/stockallocate/item')),
    array('label' => '产品目录', 'url' => array('/pss/product/list')),
)));

