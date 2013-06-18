<?php
$this->renderPartial('../_settingTop');
$tabs = array(
    'storehouse' => array('title' => '仓库设置', 'url' => $this->createUrl('/erp/setting/storehouse'), 
            'view' => 'tabViews/storehouse', 'data'=>array()),
    
    'product' => array('title' => '产品设置', 'url' => $this->createUrl('/erp/setting/product&type=1'), 
           'view' => 'tabViews/product', 'data'=>array()),
    
    'approval' => array('title' => '审批设置', 'url' => $this->createUrl('/erp/setting/approval'), 
           'view' => 'tabViews/approval', 'data'=>array()),
);

$this->widget('system.web.widgets.CTabView', array(
    'tabs' => $tabs,
    'viewData' => $this->tabViewData,
    'activeTab' => $this->action->id,
)); ?>

