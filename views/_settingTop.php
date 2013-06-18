<?php
$action_name = Yii::app()->getController()->getAction()->getId();
$approve_path = $action_name == 'index' ? 'index' : ($action_name == 'createflow' ? 'createflow' : 'updateflow');

$this->widget('zii.widgets.CMenu', array('htmlOptions' => array('class' => 'bigTab radius-top'), 'items' => array(
    array('label' => '仓库设置', 'url' => array('/erp/setting/storehouse')),
    array('label' => '产品设置', 'url' => array('/erp/setting/product', 'type' => 1), 
            'active' => Yii::app()->getController()->route == 'erp/setting/product'),
    array('label' => '审批设置', 'url' => array('/erp/approve/index', 'form_name'=>'SalesOrder'),
            'active' => Yii::app()->getController()->route == 'erp/approve/'.$approve_path),
)));
