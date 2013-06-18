<?php

class ErpController extends Controller {
    public $layout = 'main';
    
    public function init(){
        parent::init();
        $this->breadcrumbs['进销存管理'] = array('/erp');
        $this->activeMenu = 'erp';
    }
    
    public function beforeAction($action){
        if (parent::beforeAction($action)){
            $request = Yii::app()->request;
            if ($request->isAjaxRequest){
                Yii::app()->clientScript->scriptMap['jquery.js'] = false;
            }
            $erpMenu = new ErpMenu($this);
            $this->navMenu = array(
                    array('label' => '销售单', 'url' => array('/erp/sales'), 'active'=>$erpMenu->isSalesOrder()),
                    array('label' => '采购单', 'url' => array('/erp/buy'), 'active'=>$erpMenu->isBuyOrder()),
                    array('label' => '货品库存', 'url' => array('/erp/stock'), 'active'=>$erpMenu->isStock()),
                    array('label' => '报表统计', 'url' => array('/erp/report'), 'active'=>$erpMenu->isReport()),
                    array('label' => '财务管理', 'url' => array('/erp/finance'), 'active'=>$erpMenu->isFinance()),
                    array('label' => '供应商管理', 'url' => array('/erp/supplier'), 'active'=>$erpMenu->isSupplier()),
                    array('label' => '客户管理', 'url' => array('/erp/customer'), 'active'=>$erpMenu->isCustomer()),
                    array('label' => '设置', 'url' => array('/erp/setting'), 'active'=>$erpMenu->isSetting()),
            );
            return true;
        }
        return false;
    }
    
    public function getDeptOptions(){
        return Account::departmentDropdown();
    }
    
    public function getUserOptions($deptId=0, $showAll=false){
        if ($deptId>0){
            return CHtml::listData(ErpUser::model()->department($deptId)->findAll(), 'id', 'name');
        }elseif ($showAll){
            return CHtml::listData(ErpUser::model()->findAll(), 'id', 'name');
        }else{
            return array();
        }
    }
}

?>
